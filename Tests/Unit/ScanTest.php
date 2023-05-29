<?php

namespace Tests;

use Kedniko\Vivy\V;

uses()->group('scan');

test('scan', function () {
    V::registerPlugin(new \Kedniko\Vivy\Plugins\StandardLibrary\StandardLibrary());
    (new \Kedniko\Vivy\Commands\ScanCommand())->handle('_ide_helper_vivy.php');

    $fileExists = file_exists('_ide_helper_vivy.php');
    expect($fileExists)->toBeTrue();
});
