<?php

namespace Daison\CloverToHtml\Repositories\SampleHtmlPrinter;

use Daison\CloverToHtml\Repositories\MakeableTrait;
use Illuminate\Filesystem\Filesystem;
use Daison\CloverToHtml\Repositories\SampleCalculator;
use Daison\CloverToHtml\ConfigManager;

class ByIndex
{
    use MakeableTrait;

    public function __construct($xmlParsed, $storePath)
    {
        $this->xmlParsed = $xmlParsed;
        $this->storePath = $storePath;
    }

    public function handle()
    {
        $this->reformatAnnotations();

        $fullPath = $this->getStorePath();

        if (!file_exists(dirname($fullPath))) {
            $this->fs()->makeDirectory(
                dirname($fullPath),
                0777,
                true,
                true
            );
        }

        $this->fs()->put(
            $fullPath,
            $this->getClassContent()
        );
    }

    protected function fs()
    {
        return new Filesystem;
    }

    protected function reformatAnnotations()
    {
        $this->positive   = 0;
        $this->negative   = 0;
        $this->percentage = null;
        $this->files      = [];

        foreach ($this->xmlParsed as $idx => $classParser) {
            $this->files[] = $classParser->getFile();

            $calc = $classParser->getCalculations();
            $this->positive += $calc['positive'];
            $this->negative += $calc['negative'];
            $this->percentage = SampleCalculator::calculatePercetange(
                $this->positive,
                $this->negative
            );
        }
    }

    protected function getClassContent()
    {
        ob_start();

        $files      = $this->files;
        $positive   = $this->positive;
        $negative   = $this->negative;
        $percentage = $this->percentage;

        require __DIR__ . '/../.tmp/indexTemplate.php';

        return ob_get_clean();
    }

    protected function getStorePath()
    {
        return $this->storePath . '/index.html';
    }
}
