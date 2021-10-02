<?php

namespace App;

use App\Exception\MissingConfigOptionException;

class Config
{
    private array $settings;

    public function __construct()
    {
        $this->settings = array();
    }

    public static function load(string $path): static
    {
        $config = new static();

        if (file_exists($path)) {
            $config->settings = include $path;
        }

        return $config;
    }

    public function get(string $option): string
    {
        if (!isset($this->settings[$option])) {
            throw new MissingConfigOptionException('Option "' . $option . '" is not set.');
        }

        return $this->settings[$option];
    }
}
