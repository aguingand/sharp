<?php

return [

    // Required. The name of your app, as it will be displayed in Sharp.
    'name' => 'Sharp',

    // Optional. You can here customize the URL segment in which Sharp will live. Default in "sharp".
    'custom_url_segment' => 'sharp',

    // Optional. You can prevent Sharp version to be displayed in the page title. Default is true.
    'display_sharp_version_in_title' => true,

    // Optional. You can display a breadcrumb on all Sharp pages. Default is false.
    'display_breadcrumb' => false,

    // Optional. Handle extensions.
    //    'extensions' => [
    //        'assets' => [
    //            'strategy' => 'asset',
    //            'head' => [
    //                '/css/inject.css',
    //            ],
    //        ],
    //
    //        'activate_custom_fields' => false,
    //    ],

    // Required. Your entities list, as entityKey => \App\Sharp\Entities\SharpEntity implementation
    'entities' => [
        // 'my_entity' => \App\Sharp\Entities\MyEntity::class,
    ],

    // Optional. Your global filters list, which will be displayed in the main menu.
    'global_filters' => [
        // \App\Sharp\Filters\MyGlobalFilter::class
    ],

    // Optional. Your global search implementation.
    //    'search' => [
    //        'enabled' => true,
    //        'placeholder' => 'Search for anything...',
    //        'engine' => \App\Sharp\MySearchEngine::class,
    //    ],

    // Required. The main menu (left bar), which may contain links to entities, dashboards
    // or external URLs, grouped in categories.
    'menu' => null, //\App\Sharp\SharpMenu::class

    // These middleware will be assigned to Sharp routes
    'middleware' => [
        'common' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'web' => [
            \Code16\Sharp\Http\Middleware\InvalidateCache::class,
        ],
        'api' => [
            Code16\Sharp\Http\Middleware\Api\BindSharpValidationResolver::class,
            Code16\Sharp\Http\Middleware\Api\HandleSharpApiErrors::class,
            Code16\Sharp\Http\Middleware\Api\SetSharpLocale::class,
        ],
    ],

    // Optional. Your file upload configuration.
    'uploads' => [
        // Tmp directory used for file upload.
        'tmp_disk' => env('SHARP_UPLOADS_TMP_DISK', 'local'),
        'tmp_dir' => env('SHARP_UPLOADS_TMP_DIR', 'tmp'),

        // These two configs are used for thumbnail generation inside Sharp.
        'thumbnails_disk' => env('SHARP_UPLOADS_THUMBS_DISK', 'public'),
        'thumbnails_dir' => env('SHARP_UPLOADS_THUMBS_DIR', 'thumbnails'),

        'image_driver' => env('SHARP_UPLOADS_IMAGE_DRIVER', \Intervention\Image\Drivers\Gd\Driver::class),

        'transform_keep_original_image' => true,

        // Optional SharpUploadModel implementation class name
        // 'model_class' => null,
    ],

    // Optional. Options for form markdown editor (SharpFormMarkdownField)
    'markdown_editor' => [
        // If false, the UL tool will display a dropdown to choose between tight and normal lists
        'tight_lists_only' => true,
        // If false, simple carriage return will not be converted to <br> (in Sharp)
        'nl2br' => false,
    ],

    // Optional. Auth related configuration.
    'auth' => [
        // Optional custom login page to replace the default Sharp implementation.
        'login_page_url' => null,

        // Name of the login and password attributes of the User Model.
        'login_attribute' => 'email',
        'password_attribute' => 'password',

        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 5,
        ],

        '2fa' => [
            'enabled' => false,
            'handler' => 'notification', // "notification", "totp" or a class name in custom implementation case
        ],

        // Handle a "remember me" flag (with a checkbox on the login form)
        'suggest_remember_me' => false,

        // Name of the attribute used to display the current user in the UI.
        'display_attribute' => 'name',

        // Optional additional auth check.
        // 'check_handler' => \App\Sharp\Auth\MySharpCheckHandler::class,

        // Optional custom guard
        // 'guard' => 'sharp',
    ],

    // 'login_page_message_blade_path' => env('SHARP_LOGIN_PAGE_MESSAGE_BLADE_PATH', 'sharp/_login-page-message'),

    'theme' => [
        'primary_color' => '#004c9b',
        // 'favicon_url' => '',
        // 'logo_urls' => [
        //     'menu' => '/sharp-assets/menu-icon.png',
        //     'login' => '/sharp-assets/login-icon.png',
        // ],
    ],

];
