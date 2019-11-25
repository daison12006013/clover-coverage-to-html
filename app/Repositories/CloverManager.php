<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\Repositories\ParseToHtml;
use Daison\CloverToHtml\Repositories\SampleHtmlPrinter;
use Daison\CloverToHtml\Repositories\XmlParser;

class CloverManager
{
    use MakeableTrait;

    protected $xmlParser   = XmlParser::class;
    protected $parseToHtml = ParseToHtml::class;
    protected $htmlPrinter = SampleHtmlPrinter::class;

    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    public function handle()
    {
        $class    = $this->xmlParser;
        $instance = new $class($this->xml);
        $instance->setConfig($this->config);
        $xmlResponse = $instance->handle();

        // ---

        $class    = $this->parseToHtml;
        $instance = new $class($xmlResponse);
        $instance->setConfig($this->config);
        $htmlResponse = $instance->handle();

        // ---

        $class    = $this->htmlPrinter;
        $instance = new $class($xmlResponse, $htmlResponse);
        $instance->setConfig($this->config);
        $instance->handle();
    }

    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    public function setXmlParser(string $xmlParser = null)
    {
        $this->xmlParser = $xmlParser;

        return $this;
    }

    public function setParseToHtml(string $parseToHtml = null)
    {
        $this->parseToHtml = $parseToHtml;

        return $this;
    }

    public function setHtmlPrinter(string $htmlPrinter = null)
    {
        $this->htmlPrinter = $htmlPrinter;

        return $this;
    }
}
