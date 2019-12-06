<?php

namespace Daison\CloverToHtml;

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
        return $this->config[$key];
    }
}
