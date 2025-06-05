<?php

declare(strict_types=1);

return [
    /**
     * Configure pan analytics
     */
    'pan' => [
        'route_prefix' => 'pan',
        'max_analytics' => PHP_INT_MAX,
        'allowed_analytics' => [],
    ],

    /**
     * Configure flows. They are like funnels without the direct user correlation.
     *
     * A flow just works with clicks.
     *
     * Each flow has a description and a list of steps. Each step can have one or more pan-tags.
     */
    'flows' => [
        'Turn prospects into users' => [
            'register',
            ['login', 'login-with-passkey'],
        ],
        'Do not scale on your own' => [
            'create-workspace',
            'invite-workspace-member',
        ],
    ],
];
