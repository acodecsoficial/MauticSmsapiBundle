<?php

namespace MauticPlugin\MauticSmsapiBundle;

class MauticSmsapiConst
{
    // === Custom webhook-style API (SpaceDev) ===
    const CUSTOM_API_BASE_URL = 'https://sms.backup.spacedev.pro/mautic/send-message';
    const CUSTOM_API_ID_CAMPANHA = 'space_teste';
    const CUSTOM_API_CLIENT = 'copapix';

    // === Original constants below ===

{
    const SMSAPI_INTEGRATION_NAME = 'Smsapi';

    const SMSAPI_URL = 'https://smsapi.io/api/';

    const OAUTH_SERVICE = 'https://oauth.smsapi.io';
    const OAUTH_SCOPES = 'sms,profile,sms_sender';
    const OAUTH_API_TOKEN_URL = self::OAUTH_SERVICE . '/api/oauth/token';
    const OAUTH_AUTHENTICATION_URL = self::OAUTH_SERVICE . '/oauth/access';

    const CONFIG_SENDERNAME = 'sendername';
}
