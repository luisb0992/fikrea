<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable All Language Routes
    |--------------------------------------------------------------------------
    |
    | This option enable language route.
    |
    */
    'route'         => true,

    /*
    |--------------------------------------------------------------------------
    | Enable Language Home Route
    |--------------------------------------------------------------------------
    |
    | This option enable language route to set language and return
    | to url('/')
    |
    */
    'home'          => true,

    /*
    |--------------------------------------------------------------------------
    | Add Language Code
    |--------------------------------------------------------------------------
    |
    | This option will add the language code to the redirected url
    |
    */
    'url'          => false,

    /*
    |--------------------------------------------------------------------------
    | Set strategy
    |--------------------------------------------------------------------------
    |
    | This option will determine the strategy used to determine the back url.
    | It can be 'session' (default) or 'referer'
    |
    */
    'back'          => 'session',

    /*
    |--------------------------------------------------------------------------
    | Carbon Language
    |--------------------------------------------------------------------------
    |
    | This option the language of carbon library.
    |
    */
    'carbon'        => true,

    /*
    |--------------------------------------------------------------------------
    | Date Language
    |--------------------------------------------------------------------------
    |
    | This option the language of jenssegers/date library.
    |
    */
    'date'          => false,

    /*
    |--------------------------------------------------------------------------
    | Auto Change Language
    |--------------------------------------------------------------------------
    |
    | This option allows to change website language to user's
    | browser language.
    |
    */
    'auto'          => true,

    /*
    |--------------------------------------------------------------------------
    | Routes Prefix
    |--------------------------------------------------------------------------
    |
    | This option indicates the prefix for language routes.
    |
    */
    'prefix'        => 'languages',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | This option indicates the middleware to change language.
    |
    */
    'middleware'    => 'Akaunting\Language\Middleware\SetLocale',

    /*
    |--------------------------------------------------------------------------
    | Controller
    |--------------------------------------------------------------------------
    |
    | This option indicates the controller to be used.
    |
    */
    'controller'    => 'Akaunting\Language\Controllers\Language',

    /*
    |--------------------------------------------------------------------------
    | Flags
    |--------------------------------------------------------------------------
    |
    | This option indicates the flags features.
    |
    */

    'flags'         =>
        [
            'ul_class'  => 'language-selector',
            'li_class'  => '',
            'img_class' => 'language-flag'
        ],

    /*
    |--------------------------------------------------------------------------
    | Language code mode
    |--------------------------------------------------------------------------
    |
    | This option indicates the language code and name to be used, short/long
    | and english/native.
    | Short: language code (en)
    | Long: languagecode-COUNTRYCODE (en-GB)
    |
    */

    'mode'          => ['code' => 'short', 'name' => 'native'],

    /*
    |--------------------------------------------------------------------------
    | Allowed languages
    |--------------------------------------------------------------------------
    |
    | This options indicates the language allowed languages.
    |
    */

    'allowed'       => ['es', 'eu', 'ca', 'gl', 'en', 'fr', 'de', 'it', 'pt', 'ru', 'cn', 'ar'],

    /*
    |--------------------------------------------------------------------------
    | All Languages
    |--------------------------------------------------------------------------
    |
    | This option indicates the language codes and names.
    |
    */

    'all' => [
        ['short' => 'ar',       'long' => 'ar-SA',      'english' => 'Arabic',              'native' => '??????????????'],
        ['short' => 'bg',       'long' => 'bg-BG',      'english' => 'Bulgarian',           'native' => '??????????????????'],
        ['short' => 'bn',       'long' => 'bn-BD',      'english' => 'Bengali',             'native' => '???????????????'],
        ['short' => 'cn',       'long' => 'zh-CN',      'english' => 'Chinese (S)',         'native' => '????????????'],
        ['short' => 'cs',       'long' => 'cs-CZ',      'english' => 'Czech',               'native' => '??e??tina'],
        ['short' => 'da',       'long' => 'da-DK',      'english' => 'Danish',              'native' => 'Dansk'],
        ['short' => 'de',       'long' => 'de-DE',      'english' => 'German',              'native' => 'Deutsch'],
        ['short' => 'at',       'long' => 'de-AT',      'english' => 'Austrian',            'native' => '??sterreichisches Deutsch'],
        ['short' => 'fi',       'long' => 'fi-FI',      'english' => 'Finnish',             'native' => 'Suomi'],
        ['short' => 'fr',       'long' => 'fr-FR',      'english' => 'French',              'native' => 'Fran??ais'],
        ['short' => 'el',       'long' => 'el-GR',      'english' => 'Greek',               'native' => '????????????????'],
        ['short' => 'en',       'long' => 'en-AU',      'english' => 'English (AU)',        'native' => 'English (AU)'],
        ['short' => 'en',       'long' => 'en-GB',      'english' => 'English (GB)',        'native' => 'English (GB)'],
        ['short' => 'us',       'long' => 'en-US',      'english' => 'English (US)',        'native' => 'English (US)'],
        ['short' => 'es',       'long' => 'es-ES',      'english' => 'Spanish',             'native' => 'Espa??ol'],
        ['short' => 'eu',       'long' => 'es-EU',      'english' => 'Vasque',              'native' => 'Euskera'],
        ['short' => 'ca',       'long' => 'es-CA',      'english' => 'Catalan',             'native' => 'Catal??'],
        ['short' => 'gl',       'long' => 'es-GL',      'english' => 'Galician',            'native' => 'Gallego'],
        ['short' => 'et',       'long' => 'et-EE',      'english' => 'Estonian',            'native' => 'Eesti'],
        ['short' => 'he',       'long' => 'he-IL',      'english' => 'Hebrew',              'native' => '????????????????'],
        ['short' => 'hi',       'long' => 'hi-IN',      'english' => 'Hindi',               'native' => '??????????????????'],
        ['short' => 'hr',       'long' => 'hr-HR',      'english' => 'Croatian',            'native' => 'Hrvatski'],
        ['short' => 'hu',       'long' => 'hu-HU',      'english' => 'Hungarian',           'native' => 'Magyar'],
        ['short' => 'hy',       'long' => 'hy-AM',      'english' => 'Armenian',            'native' => '??????????????'],
        ['short' => 'id',       'long' => 'id-ID',      'english' => 'Indonesian',          'native' => 'Bahasa Indonesia'],
        ['short' => 'it',       'long' => 'it-IT',      'english' => 'Italian',             'native' => 'Italiano'],
        ['short' => 'ir',       'long' => 'fa-IR',      'english' => 'Persian',             'native' => '??????????'],
        ['short' => 'jp',       'long' => 'ja-JP',      'english' => 'Japanese',            'native' => '?????????'],
        ['short' => 'ka',       'long' => 'ka-GE',      'english' => 'Georgian',            'native' => '?????????????????????'],
        ['short' => 'ko',       'long' => 'ko-KR',      'english' => 'Korean',              'native' => '?????????'],
        ['short' => 'lt',       'long' => 'lt-LT',      'english' => 'Lithuanian',          'native' => 'Lietuvi??'],
        ['short' => 'lv',       'long' => 'lv-LV',      'english' => 'Latvian',             'native' => 'Latvie??u valoda'],
        ['short' => 'mk',       'long' => 'mk-MK',      'english' => 'Macedonian',          'native' => '???????????????????? ??????????'],
        ['short' => 'ms',       'long' => 'ms-MY',      'english' => 'Malay',               'native' => 'Bahasa Melayu'],
        ['short' => 'mx',       'long' => 'es-MX',      'english' => 'Mexico',              'native' => 'Espa??ol de M??xico'],
        ['short' => 'nb',       'long' => 'nb-NO',      'english' => 'Norwegian',           'native' => 'Norsk Bokm??l'],
        ['short' => 'ne',       'long' => 'ne-NP',      'english' => 'Nepali',              'native' => '??????????????????'],
        ['short' => 'nl',       'long' => 'nl-NL',      'english' => 'Dutch',               'native' => 'Nederlands'],
        ['short' => 'pl',       'long' => 'pl-PL',      'english' => 'Polish',              'native' => 'Polski'],
        ['short' => 'pt-BR',    'long' => 'pt-BR',      'english' => 'Brazilian',           'native' => 'Portugu??s do Brasil'],
        ['short' => 'pt',       'long' => 'pt-PT',      'english' => 'Portuguese',          'native' => 'Portugu??s'],
        ['short' => 'ro',       'long' => 'ro-RO',      'english' => 'Romanian',            'native' => 'Rom??n??'],
        ['short' => 'ru',       'long' => 'ru-RU',      'english' => 'Russian',             'native' => '??????????????'],
        ['short' => 'sr',       'long' => 'sr-RS',      'english' => 'Serbian (Cyrillic)',  'native' => '???????????? ??????????'],
        ['short' => 'sr',       'long' => 'sr-CS',      'english' => 'Serbian (Latin)',     'native' => '???????????? ??????????'],
        ['short' => 'sq',       'long' => 'sq-AL',      'english' => 'Albanian',            'native' => 'Shqip'],
        ['short' => 'sk',       'long' => 'sk-SK',      'english' => 'Slovak',              'native' => 'Sloven??ina'],
        ['short' => 'sl',       'long' => 'sl-SL',      'english' => 'Slovenian',           'native' => 'Sloven????ina'],
        ['short' => 'sv',       'long' => 'sv-SE',      'english' => 'Swedish',             'native' => 'Svenska'],
        ['short' => 'th',       'long' => 'th-TH',      'english' => 'Thai',                'native' => '?????????'],
        ['short' => 'tr',       'long' => 'tr-TR',      'english' => 'Turkish',             'native' => 'T??rk??e'],
        ['short' => 'tw',       'long' => 'zh-TW',      'english' => 'Chinese (T)',         'native' => '????????????'],
        ['short' => 'uk',       'long' => 'uk-UA',      'english' => 'Ukrainian',           'native' => '????????????????????'],
        ['short' => 'ur',       'long' => 'ur-PK',      'english' => 'Urdu (Pakistan)',     'native' => '????????'],
        ['short' => 'uz',       'long' => 'uz-UZ',      'english' => 'Uzbek',               'native' => 'O\'zbek'],
        ['short' => 'vi',       'long' => 'vi-VN',      'english' => 'Vietnamese',          'native' => 'Ti???ng Vi???t'],
    ],
];
