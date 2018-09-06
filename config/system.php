<?php

return [
    "default_config" => [
        "color_theme" => "red-dark",
        "locale" => "en",
        "timezone" => "America/Argentina/Buenos_Aires",
        "notification_position" => "toast-bottom-right",
        "page_length" => 50,
        "company_name" => "Spala",
        "company_description" => "A modern lightweight CMS for Laravel and Vue developers",
        "contact_info" => "",
        "reset_password_token_lifetime" => 30,
        "lock_screen" => 0,
        "lock_screen_timeout" => 60,
        "activity_log" => 1,
        "reset_password" => 1,
        "registration" => 1,
        "footer_credit" => "Copyright © 2018 Alexey Kutalo",
        "multilingual" => 1,
        "post" => 1,
        "facebook_group" => "",
        "default_cover" => "uploads/images/cover-default.png",
        "maintenance_mode_message"  => "Briefly unavailable for scheduled maintenance. Check back in a minute.",
        "app_url" => config('app.url'),
        "public_login" => 1
    ],
    "default_role" => [
        "admin" => "admin",
        "author" => "author",
        "user" => "user",
    ],
    "default_category" => [
        "articles" => "Articles",
    ],
    "public_config" => [
        "color_theme",
        "locale",
        "timezone",
        "notification_position",
        "page_length",
        "company_name",
        "company_description",
        "contact_info",
        "maintenance_mode",
        "reset_password",
        "registration",
        "footer_credit",
        "two_factor_security",
        "https",
        "email_verification",
        "facebook_group",
        "default_cover",
        "maintenance_mode_message",
        "app_url",
        "public_login",
        "background",
        "logo",
    ],
    "upload_path" => [
        "config" => "uploads/config",
        "avatar" => "uploads/avatar",
        "images" => "uploads/images"
    ],
    "pagination" => [
        "5",
        "10",
        "25",
        "50"
    ],
    "default_permission" => [
        "access-configuration",
        "list-user",
        "create-user",
        "edit-user",
        "delete-user",
        "force-reset-user-password",
        "email-user",
        "change-status-user",
        "access-post",
        "enable-login",
        "access-category",
        "access-page"
    ],
    "color_themes" => [
        [
            "text" => "Blue",
            "value" => "blue"
        ],
        [
            "text" => "Blue with Dark Sidebar",
            "value" => "blue-dark"
        ],
        [
            "text" => "Red",
            "value" => "red"
        ],
        [
            "text" => "Red with Dark Sidebar",
            "value" => "red-dark"
        ],
        [
            "text" => "Default",
            "value" => "default"
        ],
        [
            "text" => "Default with Dark Sidebar",
            "value" => "default-dark"
        ],
        [
            "text" => "Green",
            "value" => "green"
        ],
        [
            "text" => "Green with Dark Sidebar",
            "value" => "green-dark"
        ],
        [
            "text" => "Megna",
            "value" => "megna"
        ],
        [
            "text" => "Megna with Dark Sidebar",
            "value" => "megna-dark"
        ],
        [
            "text" => "Purple",
            "value" => "purple"
        ],
        [
            "text" => "Purple with Dark Sidebar",
            "value" => "purple-dark"
        ]
    ]
];