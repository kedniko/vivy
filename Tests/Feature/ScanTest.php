<?php

namespace Tests;

use Kedniko\Vivy\V;

uses()->group('scan');

test('scan', function () {
    $filename = '_ide_helper_vivy.php';

    V::registerPlugin(new \Kedniko\VivyPluginStandard\StandardLibrary());
    (new \Kedniko\Vivy\Commands\ScanCommand())->handle($filename);

    $fileExists = file_exists($filename);
    expect($fileExists)->toBeTrue();
    if ($fileExists) {
        unlink($filename);
    }
});
