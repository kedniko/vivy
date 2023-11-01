<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\Type;

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

        $middleware = new Rule('count', function (ContextInterface $c) use ($count): bool {
            $value = $c->value;

            return (is_countable($value['name']) ? count($value['name']) : 0) === $count &&
                (is_countable($value['full_path']) ? count($value['full_path']) : 0) === $count &&
                (is_countable($value['type']) ? count($value['type']) : 0) === $count &&
                (is_countable($value['tmp_name']) ? count($value['tmp_name']) : 0) === $count &&
                (is_countable($value['error']) ? count($value['error']) : 0) === $count &&
                (is_countable($value['size']) ? count($value['size']) : 0) === $count;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function minCount($minCount, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Numero di file troppo piccolo';

        $middleware = new Rule('minCount', function (ContextInterface $c) use ($minCount): bool {
            $value = $c->value;

            return (is_countable($value['name']) ? count($value['name']) : 0) >= $minCount &&
                (is_countable($value['full_path']) ? count($value['full_path']) : 0) >= $minCount &&
                (is_countable($value['type']) ? count($value['type']) : 0) >= $minCount &&
                (is_countable($value['tmp_name']) ? count($value['tmp_name']) : 0) >= $minCount &&
                (is_countable($value['error']) ? count($value['error']) : 0) >= $minCount &&
                (is_countable($value['size']) ? count($value['size']) : 0) >= $minCount;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    public function maxCount($maxCount, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Numero di file troppo grande';

        $middleware = new Rule('maxCount', function (ContextInterface $c) use ($maxCount): bool {
            $value = $c->value;

            return is_array($value) &&
                array_key_exists('name', $value) && (is_countable($value['name']) ? count($value['name']) : 0) <= $maxCount &&
                array_key_exists('full_path', $value) && (is_countable($value['full_path']) ? count($value['full_path']) : 0) <= $maxCount &&
                array_key_exists('type', $value) && (is_countable($value['type']) ? count($value['type']) : 0) <= $maxCount &&
                array_key_exists('tmp_name', $value) && (is_countable($value['tmp_name']) ? count($value['tmp_name']) : 0) <= $maxCount &&
                array_key_exists('error', $value) && (is_countable($value['error']) ? count($value['error']) : 0) <= $maxCount &&
                array_key_exists('size', $value) && (is_countable($value['size']) ? count($value['size']) : 0) <= $maxCount;
        }, $errormessage);

        $this->addRule($middleware, $options);

        return $this;
    }

    /**
     * @param  string  $unit `B`|`KB`|`MB`|`GB`
     */
    public function totalSize(mixed $totalSize, string $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensioni files non accettate';

        $totalSize = $this->convertUnit($totalSize, $unit, 'B');

        $middleware = new Rule('totalSize', function (ContextInterface $c) use ($totalSize): bool {
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
     * @param  string  $unit `B`|`KB`|`MB`|`GB`
     */
    public function minTotalSize(mixed $minTotalSize, string $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensioni files troppo piccole';

        $minTotalSize = $this->convertUnit($minTotalSize, $unit, 'B');

        $middleware = new Rule('minTotalSize', function (ContextInterface $c) use ($minTotalSize): bool {
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
     * @param  string  $unit `B`|`KB`|`MB`|`GB`
     */
    public function maxTotalSize(mixed $maxTotalSize, string $unit = 'B', Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: 'Dimensioni files troppo grandi';

        $maxTotalSize = $this->convertUnit($maxTotalSize, $unit, 'B');

        $middleware = new Rule('maxTotalSize', function (ContextInterface $c) use ($maxTotalSize): bool {
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
        $transformer = new Transformer('toJson', fn (ContextInterface $c): string => json_encode($c->value, JSON_THROW_ON_ERROR), $errormessage);
        $this->addTransformer($transformer, $options);

        return $this;
    }

    public function each(TypeFile $fileField, bool|callable $stopOnItemFailure = false, Options $options = null)
    {
        $options = Options::build($options, func_get_args());

        $rule = $this->getEachRule($fileField, $stopOnItemFailure, $options->getErrorMessage());
        $this->addRule($rule, $options);

        return $this;
    }

    private function getEachRule(Type $type, bool|callable $stopOnItemFailure, $errormessage): Rule
    {
        $ruleFn = function (ContextInterface $c) use ($type, $stopOnItemFailure): \Kedniko\Vivy\Core\Validated {
            if (! is_array($c->value)) {
                throw new \Exception('This is not an array. Got ['.gettype($c->value).']: '.json_encode($c->value, JSON_THROW_ON_ERROR), 1);
            }

            $arrayContext = new ArrayContext();

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
                        $arrayContext->setIndex($index);
                        $arrayContext->setFailsCount($failsCount);
                        $arrayContext->setSuccessCount($successCount);
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

            return new Validated($c->value, $c->errors);
        };

        if ($errormessage === null) {
            $errormessage = fn (ContextInterface $c) => $c->errors;
        }

        return new Rule(self::RULE_ID, $ruleFn, $errormessage);
    }

    /**
     * @param  mixed  $from `B`|`KB`|`MB`|`GB`
     * @param  mixed  $to `B`|`KB`|`MB`|`GB`
     */
    private function convertUnit(mixed $value, mixed $from, string $to)
    {
        $from = strtoupper((string) $from);
        $to = strtoupper($to);
        $value = (float) $value;
        $index = array_search($from, self::UNITS);
        $toIndex = array_search($to, self::UNITS);
        $diff = $toIndex - $index;
        if ($diff === 0) {
            return $value;
        }
        if ($diff > 0) {
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

    /**
     * @return mixed[][]
     */
    private function normalizeFileStructure(array $files): array
    {
        $newFiles = [];
        foreach ($files as $property => $value) {
            for ($i = 0, $count = is_countable($value) ? count($value) : 0; $i < $count; $i++) {
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
