<?php

test('basic example', function () {
    visit('/')->assertPathIs('/');
});
