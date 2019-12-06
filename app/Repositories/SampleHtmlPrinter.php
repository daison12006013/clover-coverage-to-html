<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\ConfigManager;

class SampleHtmlPrinter
{
    use MakeableTrait;

    public function __construct(
        array $xmlParsed,
        array $htmlParsed
    ) {
        $this->xmlParsed  = $xmlParsed;
        $this->htmlParsed = $htmlParsed;
    }

    public function handle()
    {
        $storePath = $this->config->get('storePath');
        // by index
        SampleHtmlPrinter\ByIndex::make($this->xmlParsed, $storePath);

        // by annotation
        SampleHtmlPrinter\ByAnnotation::make($this->xmlParsed, $storePath);

        // per file
        foreach ($this->htmlParsed as $idx => $html) {
            SampleHtmlPrinter\PerFile::make(
                $this->xmlParsed[$idx],
                $html,
                $storePath
            );
        }
    }

    public function setConfig(ConfigManager $config)
    {
        $this->config = $config;

        return $this;
    }
}
