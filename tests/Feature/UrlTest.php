<?php

// uses(\Illuminate\Support\Facades\Artisan::class);

beforeAll(function () {
    // \Artisan::call('view:clear');
    // echo 'what cool!';
});

// beforeEach(function () {
//     echo 'ww';
// });


test('that authentication URLs return a 200', function ($url) {
    $this->get($url)->assertOK();
})->with('urls');
