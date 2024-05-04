<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('that the login route at /auth/login returns a successful response', function () {
    $response = $this->get('/auth/login');

    $response->assertStatus(200);
});
