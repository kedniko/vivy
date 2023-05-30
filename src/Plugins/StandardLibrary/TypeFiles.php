<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\ContextProxy;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\Types\Type;

final class TypeFiles extends TypeCompound
{
    /**
     * @var string
     */
    private const RULE_ID = 'each';
    /**
     * @var string[]
     */
    private const UNITS = ['B', 'KB', 'MB', 'GB'];
    public function count($count, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Numero di file errato';

        $middleware = new Rule('count', function (Context $c) use ($count): bool {
            $value = $c->value;

            return count($value['name']) === $count &&
                count($value['full_path']) === $count &&
                count($value['type']) === $count &&
                count($value['tmp_name']) === $count &&
                count($value['error']) === $count &&
                count($value['size']) === $count;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function minCount($minCount, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Numero di file troppo piccolo';

        $middleware = new Rule('minCount', function (Context $c) use ($minCount): bool {
            $value = $c->value;

            return count($value['name']) >= $minCount &&
                count($value['full_path']) >= $minCount &&
                count($value['type']) >= $minCount &&
                count($value['tmp_name']) >= $minCount &&
                count($value['error']) >= $minCount &&
                count($value['size']) >= $minCount;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function maxCount($maxCount, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Numero di file troppo grande';

        $middleware = new Rule('maxCount', function (Context $c) use ($maxCount): bool {
            $value = $c->value;

            return is_array($value) &&
                array_key_exists('name', $value) && count($value['name']) <= $maxCount &&
                array_key_exists('full_path', $value) && count($value['full_path']) <= $maxCount &&
                array_key_exists('type', $value) && count($value['type']) <= $maxCount &&
                array_key_exists('tmp_name', $value) && count($value['tmp_name']) <= $maxCount &&
                array_key_exists('error', $value) && count($value['error']) <= $maxCount &&
                array_key_exists('size', $value) && count($value['size']) <= $maxCount;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  mixed  $totalSize
     * @param  int|float  $unit `B`|`KB`|`MB`|`GB`
     */
    public function totalSize($totalSize, $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensioni files non accettate';

        $totalSize = $this->convertUnit($totalSize, $unit, 'B');

        $middleware = new Rule('totalSize', function (Context $c) use ($totalSize): bool {
            $value = $c->value;
            $totalSize = 0;
            foreach ($value['size'] as $size) {
                $totalSize += $size;
            }

            return $totalSize === $totalSize;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  mixed  $minTotalSize
     * @param  int|float  $unit `B`|`KB`|`MB`|`GB`
     */
    public function minTotalSize($minTotalSize, $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensioni files troppo piccole';

        $minTotalSize = $this->convertUnit($minTotalSize, $unit, 'B');

        $middleware = new Rule('minTotalSize', function (Context $c) use ($minTotalSize): bool {
            $value = $c->value;
            $totalSize = 0;
            foreach ($value['size'] as $size) {
                $totalSize += $size;
            }

            return $totalSize >= $minTotalSize;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  mixed  $maxTotalSize
     * @param  int|float  $unit `B`|`KB`|`MB`|`GB`
     */
    public function maxTotalSize($maxTotalSize, $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensioni files troppo grandi';

        $maxTotalSize = $this->convertUnit($maxTotalSize, $unit, 'B');

        $middleware = new Rule('maxTotalSize', function (Context $c) use ($maxTotalSize): bool {
            $value = $c->value;
            $totalSize = 0;
            foreach ($value['size'] as $size) {
                $totalSize += $size;
            }

            return $totalSize <= $maxTotalSize;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  array  $args
     */
    public function toJson(Options $options = null)
    {
        $errormessage = $options->getErrorMessage() ?: 'TRANSFORMER: toJson';
        $transformer = new Transformer('toJson', function (Context $c) {
            return json_encode($c->value);
        }, $errormessage);
        $this->addTransformer($transformer, $options);

        return $this;
    }

    /**
     * @param  bool|callable  $stopOnItemFailure
     */
    public function each(TypeFile $fileField, $stopOnItemFailure = false, Options $options = null)
    {
        $options = Options::build($options, func_get_args());

        $rule = $this->getEachRule($fileField, $stopOnItemFailure, $options->getErrorMessage());
        $this->addRule($rule, $options);

        return $this;
    }

    private function getEachRule(Type $type, $stopOnItemFailure, $errormessage): \Kedniko\Vivy\Core\Rule
    {
        $ruleFn = function (Context $c) use ($type, $stopOnItemFailure): \Kedniko\Vivy\Core\Validated {
            if (! is_array($c->value)) {
                throw new \Exception('This is not an array. Got ['.gettype($c->value).']: '.json_encode($c->value), 1);
            }

            $contextProxy = new ContextProxy($c);

            $arrayContext = new ArrayContext();
            $arrayContextProxy = new ContextProxy($arrayContext);

            $failsCount = 0;
            $successCount = 0;

            $value = $c->value;

            $files = $this->normalizeFileStructure($value);

            foreach ($files as $index => $file) {
                $type->_extra = [
                    'isArrayContext' => true,
                    'index' => $index,
                    'failsCount' => $failsCount,
                ];

                $validated = $type->validate($file, $c);

                $c->value[$index] = $validated->value();

                if ($validated->fails()) {
                    $failsCount++;
                    $c->errors[$index] = $validated->errors();
                    if (is_callable($stopOnItemFailure)) {
                        $arrayContextProxy->setIndex($index);
                        $arrayContextProxy->setFailsCount($failsCount);
                        $arrayContextProxy->setSuccessCount($successCount);
                        if ($stopOnItemFailure($arrayContext)) {
                            break;
                        }
                    } elseif ($stopOnItemFailure) {
                        break;
                    }
                } else {
                    $successCount++;
                }
            }

            $validated = new Validated($c->value, $c->errors);

            return $validated;
        };

        if ($errormessage === null) {
            $errormessage = function (Context $c) {
                return $c->errors;
            };
        }

        return new Rule(self::RULE_ID, $ruleFn, $errormessage);
    }

    /**
     * @param  mixed  $value
     * @param  mixed  $from `B`|`KB`|`MB`|`GB`
     * @param  mixed  $to `B`|`KB`|`MB`|`GB`
     */
    private function convertUnit($value, $from, string $to)
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

    private function normalizeFileStructure(array $files)
    {
        foreach ($files as $property => $value) {
            for ($i = 0, $count = count($value); $i < $count; $i++) {
                $newFiles[$i][$property] = $value[$i];
            }
        }

        return $newFiles;
    }

    // public function group($setup, Options $options = null)
    // {
    // 	$options = Helpers::getOptions($options);

    // 	$type = new BasicGroup($setup, $options);

    // 	$type->addRule(Rules::notNull($options->getErrormessage() ?: RuleMessage::getErrorMessage('group.notNull')), $options);
    // 	$type->addRule(Rules::notEmptyString($options->getErrormessage() ?: RuleMessage::getErrorMessage('group.notEmptyString')), $options);
    // 	$type->addRule(Rules::array($options->getErrormessage()), $options);

    // 	/** @var LinkedList $types */
    // 	$types = (new TypeProxy($type))->getChildState()->getFields();
    // 	$type->group($types, true, $options);

    // 	// share state
    // 	$type->state = $this->state;
    // 	return $type;
    // }
}
