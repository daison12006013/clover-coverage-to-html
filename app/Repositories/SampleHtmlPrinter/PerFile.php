<?php

namespace Daison\CloverToHtml\Repositories\SampleHtmlPrinter;

use Daison\CloverToHtml\Repositories\Contracts\ParserContract;
use Daison\CloverToHtml\Repositories\MakeableTrait;
use Illuminate\Filesystem\Filesystem;

class PerFile
{
    use MakeableTrait;

    public function __construct(ParserContract $classParser, $html, $storePath)
    {
        $this->classParser = $classParser;
        $this->html        = $html;
        $this->storePath   = $storePath;
    }

    protected function handle()
    {
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

    protected function getShortClassNames()
    {
        return implode(', ', array_map(function ($val) {
            // get the class name, not the whole class path
            $exploded = explode('\\', $val);

            return end($exploded);
        }, is_array($class = $this->classParser->getClass()) ? $class : [$class]));
    }

    protected function getClassContent()
    {
        ob_start();

        $content      = $this->html['content'];
        $calculations = $this->classParser->getCalculations();
        $title        = $this->getShortClassNames();

        require __DIR__ . '/../.tmp/classTemplate.php';

        return ob_get_clean();
    }

    protected function getStorePath()
    {
        $path = $this->classParser->getFile();

        return str_replace('.php', '.html', $this->storePath . '/' . $path);
    }
}
