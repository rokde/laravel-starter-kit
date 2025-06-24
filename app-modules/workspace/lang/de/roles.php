<?php

declare(strict_types=1);

return [
    'admin' => [
        'name' => 'Administrator',
        'description' => 'Der Administrator hat alle Rechte.',
    ],
    'editor' => [
        'name' => 'Bearbeiter',
        'description' => 'Der normale Mitarbeiter kann mitarbeiten im Workspace.',
    ],
    'owner' => [
        'name' => 'Eigentümer',
        'description' => 'Der Eigentümer des Workspace.',
    ],
    'visitor' => [
        'name' => 'Besucher',
        'description' => 'Der Besucher kann nur lesend zugreifen.',
    ],
];
