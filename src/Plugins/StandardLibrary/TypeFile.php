<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;

final class TypeFile extends TypeCompound
{
    const MIME_PDF = 'application/pdf';
    /**
     * @var string[]
     */
    private const UNITS = ['B', 'KB', 'MB', 'GB'];

    public function mime($mime, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Mime non corretto';

        $middleware = new Rule('mime', function (Context $c) use ($mime) {
            $valueMime = $c->value['type'];

            return $valueMime === $mime;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function pdf(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Non è un pdf';

        $middleware = new Rule('pdf', function (Context $c) {
            $valueMime = $c->value['type'];

            return $valueMime === self::MIME_PDF;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  array  $extensions
     */
    public function extensionIn($extensions, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Estensione non corretta';

        $middleware = new Rule('extension', function (Context $c) use ($extensions) {
            return in_array(pathinfo($c->value['tmp_name'], PATHINFO_EXTENSION), (array) $extensions);
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function extension($extension, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Estensione non corretta';

        $middleware = new Rule('extension', function (Context $c) use ($extension) {
            return $extension === pathinfo($c->value['tmp_name'], PATHINFO_EXTENSION);
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function baseNameEquals($basename, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Lunghezza basename non valida';

        $middleware = new Rule('baseNameEquals', function (Context $c) use ($basename) {
            return $basename === pathinfo($c->value['basename'], PATHINFO_BASENAME);
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function baseNameLength($maxLength, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Lunghezza basename non valida';

        $middleware = new Rule('baseNameLength', function (Context $c) use ($maxLength) {
            return $maxLength === strlen(pathinfo($c->value['basename'], PATHINFO_BASENAME));
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function baseNameMinLength($maxLength, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Basename troppo corto';

        $middleware = new Rule('baseNameMinLength', function (Context $c) use ($maxLength) {
            return $maxLength >= strlen(pathinfo($c->value['basename'], PATHINFO_BASENAME));
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function baseNameMaxLength($maxLength, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Basename troppo lungo';

        $middleware = new Rule('baseNameMaxLength', function (Context $c) use ($maxLength) {
            return $maxLength <= strlen(pathinfo($c->value['basename'], PATHINFO_BASENAME));
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function fileNameLength($maxLength, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Lunghezza filename non valida';

        $middleware = new Rule('FileNameLength', function (Context $c) use ($maxLength) {
            return $maxLength === strlen(pathinfo($c->value['FILENAME'], PATHINFO_FILENAME));
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function fileNameMinLength($maxLength, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Filename troppo corto';

        $middleware = new Rule('fileNameMinLength', function (Context $c) use ($maxLength) {
            return $maxLength >= strlen(pathinfo($c->value['FILENAME'], PATHINFO_FILENAME));
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function fileNameMaxLength($maxLength, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Filename troppo lungo';

        $middleware = new Rule('fileNameMaxLength', function (Context $c) use ($maxLength) {
            return $maxLength <= strlen(pathinfo($c->value['FILENAME'], PATHINFO_FILENAME));
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function size($size, $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensione file non accettata';

        $size = $this->convertUnit($size, $unit, 'B');

        $middleware = new Rule('size', function (Context $c) use ($size) {
            $valueSize = $c->value['size'];

            return floatval($valueSize) === floatval($size);
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  mixed  $maxSize
     * @param  string  $unit `B` | `KB` | `MB` | `GB`,
     */
    public function maxSize($maxSize, $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensione file troppo grande';

        $maxSize = $this->convertUnit($maxSize, $unit, 'B');

        $middleware = new Rule('maxSize', function (Context $c) use ($maxSize) {
            $valueSize = $c->value['size'];

            return $valueSize <= $maxSize;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function minSize($minSize, $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensione file troppo piccola';

        $minSize = $this->convertUnit($minSize, $unit, 'B');

        $middleware = new Rule('minSize', function (Context $c) use ($minSize) {
            $valueSize = $c->value['size'];

            return $valueSize >= $minSize;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  mixed  $value
     * @param  mixed  $from `B`|`KB`|`MB`|`GB`
     * @param  mixed  $to `B`|`KB`|`MB`|`GB`
     * @return int|float
     */
    private function convertUnit($value, $from, $to)
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        $value = floatval($value);
        $index = array_search($from, self::UNITS);
        $toIndex = array_search($to, self::UNITS);
        $diff = $toIndex - $index;
        if ($diff === 0) {
            return $value;
        } elseif ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                $value /= 1024;
            }
        } else {
            for ($i = 0; $i < abs($diff); $i++) {
                $value *= 1024;
            }
        }

        return $value;
    }
}
