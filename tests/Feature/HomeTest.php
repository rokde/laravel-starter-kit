<?php

declare(strict_types=1);

test('home page can be visited', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});
