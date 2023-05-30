<?php

namespace Kedniko\Vivy\Commands;

use Kedniko\PhpIdeHelper\IdeHelper;
use Kedniko\PhpIdeHelper\Register;
use Kedniko\Vivy\V;

final class ScanCommand
{
    public function handle($exportPath = null): void
    {
        $exportPath ??= 'ide-helper.rules.php';
        $registered = array_map(function ($item): Register {
            $r = new Register();

            $returnType = $item['returnType'];
            $availableForType = $item['availableForType'];
            $fn = $item['function'];
            if (is_array($fn)) {
                $classname = $item['function'][0];
                $methodname = $item['function'][1];
                $r->useMethod($classname, $methodname);
            } else {
                $r->useFunction($fn);
            }
            $name = $item['methodName'];
            $is_static = $availableForType === V::class;
            $r->name($name)->asStatic($is_static)->to($availableForType)->setReturn($returnType);

            return $r;
        }, V::$registeredMiddlewares);

        $ih = new IdeHelper($registered);
        $ih->setHeader($this->getHeader());
        $ih->generate($exportPath);
    }

    private function getHeader(): string
    {
        return <<<'HEADER'

        // @formatter:off
        /**
         * A helper file for your Vivy validators.
         *
         * @author @kedniko
         */
        
        HEADER;
    }
}
