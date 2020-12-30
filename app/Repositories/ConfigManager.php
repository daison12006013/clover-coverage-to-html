<?php

namespace Daison\CloverToHtml\Repositories;

class ConfigManager
{
    public $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        if (!isset($this->config[$key])) {
            return null;
        }

        return $this->config[$key];
    }
}
