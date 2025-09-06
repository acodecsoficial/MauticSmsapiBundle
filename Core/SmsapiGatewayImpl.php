<?php

namespace MauticPlugin\MauticSmsapiBundle\Core;

use MauticPlugin\MauticSmsapiBundle\DataObject\Profile;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Throwable;

class SmsapiGatewayImpl implements SmsapiGateway
{
    const MAUTIC = 'Mautic';
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function isConnected(): bool
    {
        $service = $this->connection->smsapiClient();

        try {
            $service->profileFeature()->findProfile();
        } catch (Throwable $apiErrorException) {
            return false;
        }

        return true;
    }

    public function getSendernames(): array
    {
        $sendernames = $this->connection->smsapiClient()->smsFeature()->sendernameFeature()->findSendernames();
        $array = [];

        foreach ($sendernames as $sendername) {
            if($sendername->status === 'ACTIVE') {
                $array[$sendername->sender] = $sendername->sender;
            }
        }

        return $array;
    }

    public function sendSms(string $phoneNumber, string $content, string $sendername)
{
    // Send through custom webhook-style API instead of SMSAPI SDK.
    $base = \MauticPlugin\MauticSmsapiBundle\MauticSmsapiConst::CUSTOM_API_BASE_URL;
    $idCampanha = \MauticPlugin\MauticSmsapiBundle\MauticSmsapiConst::CUSTOM_API_ID_CAMPANHA;
    $client = \MauticPlugin\MauticSmsapiBundle\MauticSmsapiConst::CUSTOM_API_CLIENT;

    $params = http_build_query([
        'id_campanha' => $idCampanha,
        'numero' => $phoneNumber,
        'mensagem' => $content,
        'client' => $client,
    ]);

    $url = rtrim($base, '?&') . '?' . $params;

    // Basic cURL GET
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $response = curl_exec($ch);
    $errNo = curl_errno($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errNo !== 0 || $status >= 400) {
        // Throw generic exception to be caught by the API wrapper.
        throw new \RuntimeException('SpaceDev SMS API error: HTTP ' . $status . ' cURL(' . $errNo . '): ' . $err . ' Response: ' . substr((string)$response, 0, 1024));
    }

    return $response ?: true;
}

    public function getProfile(): Profile
    {
        $service = $this->connection->smsapiClient();
        $profile = $service->profileFeature()->findProfile();

        return new Profile($profile->points);
    }
}
