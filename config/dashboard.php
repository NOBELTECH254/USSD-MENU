<?php
return [
    'tiles' => [
        'charts' => [
            'refresh_interval_in_seconds' => 30, // Default: 300 seconds (5 minutes)
        ],
    ],


    /*
     * The dashboard supports these themes:
     *
     * - light: always use light mode
     * - dark: always use dark mode
     * - device: follow the OS preference for determining light or dark mode
     * - auto: use light mode when the sun is up, dark mode when the sun is down
     */
    'theme' => 'light',

    /*
     * When the dashboard uses the `auto` theme, these coordinates will be used
     * to determine whether the sun is up or down
     */
    'auto_theme_location' => [
        'lat' => 51.260197,
        'lng' => 4.402771,
    ],
];
