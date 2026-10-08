<?php

test('guests visiting the root url are redirected to the login page', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});
