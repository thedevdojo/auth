<?php

test('that authentication URLs return a 200', function ($url) {
    $this->get($url)->assertOK();
})->with('urls');
