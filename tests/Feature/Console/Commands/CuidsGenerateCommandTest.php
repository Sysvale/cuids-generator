<?php

use Illuminate\Support\Facades\Artisan;
use Sysvale\CuidsGenerator\Blueprint\Builders\FormFieldBuilder;
use Illuminate\Support\Facades\File;

test('it can detect the command', function () {
    $commands = Artisan::all();
    expect($commands)->toHaveKey('cuids:generate');
});

test('it shows an error message when FormFieldBuilder fails', function () {

});