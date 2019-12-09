<?php

namespace Daison\CloverToHtml\Repositories\Traits;

use Daison\CloverToHtml\ConfigManager;

trait ConfigSettler
{
    protected $config;

    public function setConfig(ConfigManager $config)
    {
        $this->config = $config;

        return $this;
    }
}
