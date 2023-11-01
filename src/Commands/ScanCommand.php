<?php

namespace Kedniko\Vivy\Commands;

use Kedniko\Vivy\V;

final class ScanCommand
{
    public function handle(): void
    {
        $exportPath ??= '.tmp/ide-helper.rules.php';
        $registered = $this->getRegistered();
        $file = $this->generateFromRegistered($registered);
        file_put_contents($exportPath, $file);
    }

    private function getRegistered()
    {
        return V::$magicCaller->toArray();
    }

    private function generateFromRegistered($registered)
    {
        $file = new \Nette\PhpGenerator\PhpFile();

        foreach ($registered as $className => $methods) {
            $class = $file->addClass($className);
            foreach ($methods as $methodArr) {
                $fromClassName = $methodArr['function'][0];
                $fromMethodName = $methodArr['function'][1];

                $method = \Nette\PhpGenerator\Method::from([$fromClassName, $fromMethodName]);
                $method = $method->cloneWithName($methodArr['methodName']);
                if ($methodArr['returnType']) {
                    $method->setReturnType($methodArr['returnType']);
                }
                if ($className === V::class) {
                    $method->setStatic(true);
                }
                $class->addMember($method);
            }
        }

        return <<<PHP
        <?php
        
        {$this->getHeader()}
        {$this->removeFirstLineFromString($file)}
        PHP;
    }

    private function removeFirstLineFromString(\Nette\PhpGenerator\PhpFile $str): string
    {
        return substr($str, strpos($str, "\n") + 1);
    }

    private function getHeader(): string
    {
        return file_get_contents(__DIR__.'/../stubs/header.txt');
    }
}
