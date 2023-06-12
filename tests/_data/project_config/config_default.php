<?php

use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Router\RouterConstants;
use Spryker\Shared\DocumentationGeneratorRestApi\DocumentationGeneratorRestApiConstants;
use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\CmsGui\CmsGuiConstants;
use Spryker\Shared\Translator\TranslatorConstants;
use Spryker\Shared\Monitoring\MonitoringConstants;

// ############################################################################
// ############################## PRODUCTION CONFIGURATION ####################
// ############################################################################

// ----------------------------------------------------------------------------
// ------------------------------ CODEBASE: TO REMOVE -------------------------
// ----------------------------------------------------------------------------

$sprykerBackendHost = getenv('SPRYKER_BE_HOST') ?: 'not-configured-host';
$sprykerFrontendHost = getenv('SPRYKER_FE_HOST') ?: 'not-configured-host';

$config[KernelConstants::SPRYKER_ROOT] = APPLICATION_ROOT_DIR . '/vendor/spryker';

$config[KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED] = true;
$config[KernelConstants::RESOLVED_INSTANCE_CACHE_ENABLED] = true;

$config[KernelConstants::PROJECT_NAMESPACE] = 'Pyz';
$config[KernelConstants::PROJECT_NAMESPACES] = [
    'Pyz',
];
$config[KernelConstants::CORE_NAMESPACES] = [
    'SprykerShop',
    'SprykerEco',
    'Spryker',
    'SprykerSdk',
];

// >>> ROUTER

$config[RouterConstants::YVES_SSL_EXCLUDED_ROUTE_NAMES] = [
    'healthCheck' => '/health-check',
];
$config[RouterConstants::ZED_SSL_EXCLUDED_ROUTE_NAMES] = [
    'healthCheck' => 'health-check/index',
];

// >>> DEV TOOLS

$config[ConsoleConstants::ENABLE_DEVELOPMENT_CONSOLE_COMMANDS] = (bool)getenv('DEVELOPMENT_CONSOLE_COMMANDS');
$config[DocumentationGeneratorRestApiConstants::ENABLE_REST_API_DOCUMENTATION_GENERATION] = true;

// >>> ERROR HANDLING

$config[ErrorHandlerConstants::YVES_ERROR_PAGE] = APPLICATION_ROOT_DIR . '/public/Yves/errorpage/5xx.html';
$config[ErrorHandlerConstants::ZED_ERROR_PAGE] = APPLICATION_ROOT_DIR . '/public/Backoffice/errorpage/5xx.html';
$config[ErrorHandlerConstants::ERROR_RENDERER] = WebHtmlErrorRenderer::class;

// >>> CMS

//$config[CmsGuiConstants::CMS_FOLDER_PATH] = '@Cms/templates/';
$config[CmsGuiConstants::CMS_PAGE_PREVIEW_URI] = '/en/cms/preview/%d';

// >>> TRANSLATOR

$config[TranslatorConstants::TRANSLATION_ZED_FALLBACK_LOCALES] = [
    'de_DE' => ['en_US'],
];

// >>> MONITORING

$config[MonitoringConstants::IGNORABLE_TRANSACTIONS] = [
    '_profiler',
    '_wdt',
];

// >>> TESTING

if (class_exists(TestifyConstants::class)) {
    $config[TestifyConstants::GLUE_OPEN_API_SCHEMA] = APPLICATION_SOURCE_DIR . '/Generated/Glue/Specification/spryker_rest_api.schema.yml';
    $config[TestifyConstants::BOOTSTRAP_CLASS_YVES] = YvesBootstrap::class;
    $config[TestifyConstants::BOOTSTRAP_CLASS_ZED] = ZedBootstrap::class;
}

// ----------------------------------------------------------------------------
// ------------------------------ SECURITY ------------------------------------
// ----------------------------------------------------------------------------

$trustedHosts
    = $config[HttpConstants::ZED_TRUSTED_HOSTS]
    = $config[HttpConstants::YVES_TRUSTED_HOSTS]
    = array_filter(explode(',', getenv('SPRYKER_TRUSTED_HOSTS') ?: ''));

$config[KernelConstants::DOMAIN_WHITELIST] = array_merge($trustedHosts, [
    $sprykerBackendHost,
    $sprykerFrontendHost,
    'threedssvc.pay1.de', // trusted Payone domain
    'www.sofort.com', // trusted Payone domain
]);
$config[KernelConstants::STRICT_DOMAIN_REDIRECT] = true;

$config[HttpConstants::ZED_HTTP_STRICT_TRANSPORT_SECURITY_ENABLED]
    = $config[HttpConstants::YVES_HTTP_STRICT_TRANSPORT_SECURITY_ENABLED]
    = $config[HttpConstants::GLUE_HTTP_STRICT_TRANSPORT_SECURITY_ENABLED]
    = true;
$config[HttpConstants::ZED_HTTP_STRICT_TRANSPORT_SECURITY_CONFIG]
    = $config[HttpConstants::YVES_HTTP_STRICT_TRANSPORT_SECURITY_CONFIG]
    = $config[HttpConstants::GLUE_HTTP_STRICT_TRANSPORT_SECURITY_CONFIG]
    = [
    'max_age' => 31536000,
    'include_sub_domains' => true,
    'preload' => true,
];

$config[CustomerConstants::CUSTOMER_SECURED_PATTERN] = '(^/login_check$|^(/en|/de)?/customer($|/)|^(/en|/de)?/wishlist($|/)|^(/en|/de)?/shopping-list($|/)|^(/en|/de)?/quote-request($|/)|^(/en|/de)?/comment($|/)|^(/en|/de)?/company(?!/register)($|/)|^(/en|/de)?/multi-cart($|/)|^(/en|/de)?/shared-cart($|/)|^(/en|/de)?/cart(?!/add)($|/)|^(/en|/de)?/checkout($|/))';
$config[CustomerConstants::CUSTOMER_ANONYMOUS_PATTERN] = '^/.*';
$config[CustomerPageConstants::CUSTOMER_REMEMBER_ME_SECRET] = 'hundnase';
$config[CustomerPageConstants::CUSTOMER_REMEMBER_ME_LIFETIME] = 31536000;

$config[LogConstants::LOG_SANITIZE_FIELDS] = [
    'password',
];

// ----------------------------------------------------------------------------
// ------------------------------ AUTHENTICATION ------------------------------
// ----------------------------------------------------------------------------

// >>> OAUTH

$config[OauthConstants::PRIVATE_KEY_PATH] = str_replace(
    '__LINE__',
    PHP_EOL,
    getenv('SPRYKER_OAUTH_KEY_PRIVATE') ?: '',
) ?: null;
$config[OauthConstants::PUBLIC_KEY_PATH]
    = $config[OauthCryptographyConstants::PUBLIC_KEY_PATH]
    = str_replace(
        '__LINE__',
        PHP_EOL,
        getenv('SPRYKER_OAUTH_KEY_PUBLIC') ?: '',
    ) ?: null;
$config[OauthConstants::ENCRYPTION_KEY] = getenv('SPRYKER_OAUTH_ENCRYPTION_KEY') ?: null;
$config[OauthConstants::OAUTH_CLIENT_IDENTIFIER] = getenv('SPRYKER_OAUTH_CLIENT_IDENTIFIER') ?: null;
$config[OauthConstants::OAUTH_CLIENT_SECRET] = getenv('SPRYKER_OAUTH_CLIENT_SECRET') ?: null;

// >> ZED REQUEST

$config[UserConstants::USER_SYSTEM_USERS] = [
    'yves_system',
];
$config[SecuritySystemUserConstants::AUTH_DEFAULT_CREDENTIALS] = [
    'yves_system' => [
        'token' => getenv('SPRYKER_ZED_REQUEST_TOKEN'),
    ],
];

// >> URL Signer

$config[HttpConstants::URI_SIGNER_SECRET_KEY] = getenv('SPRYKER_URI_SIGNER_SECRET_KEY') ?: null;

// ACL: Special rules for specific users
$config[AclConstants::ACL_DEFAULT_CREDENTIALS] = [
    'yves_system' => [
        'rules' => [
            [
                'bundle' => '*',
                'controller' => 'gateway',
                'action' => '*',
                'type' => 'allow',
            ],
        ],
    ],
];

// >> BACKOFFICE

// ACL: Allow or disallow of urls for Zed Admin GUI for ALL users
$config[AclConstants::ACL_DEFAULT_RULES] = [
    [
        'bundle' => 'security-gui',
        'controller' => '*',
        'action' => '*',
        'type' => 'allow',
    ],
    [
        'bundle' => 'acl',
        'controller' => 'index',
        'action' => 'denied',
        'type' => 'allow',
    ],
    [
        'bundle' => 'health-check',
        'controller' => 'index',
        'action' => 'index',
        'type' => 'allow',
    ],
    [
        'bundle' => 'api',
        'controller' => 'rest',
        'action' => '*',
        'type' => 'allow',
    ],
];
// ACL: Allow or disallow of urls for Zed Admin GUI
$config[AclConstants::ACL_USER_RULE_WHITELIST] = [
    [
        'bundle' => 'application',
        'controller' => '*',
        'action' => '*',
        'type' => 'allow',
    ],
];
