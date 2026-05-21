<?php

namespace App\Config;

use Symfony\Component\Yaml\Yaml;
use Tracy\Debugger;

final class Config
{
    private array $configValues = [];

    public function load(string $filePath): void
    {
        $newValues = Yaml::parseFile($filePath);
        $this->configValues = array_merge_recursive($this->configValues, $newValues);
    }

    public function get(string $name): int|float|array|string|bool|null
    {
        $keys = explode('.', $name);
        $values = $this->configValues;

        foreach ($keys as $key) {
            if (!is_array($values[$key]) &&
                !array_key_exists($key, $values)
            ) {
                throw new ConfigValueNotDefinedException(
                    "Config value '{$name}' ('{$key}') not defined."
                );
            }

            $values = $values[$key];
        }

        return $values;
    }
}
