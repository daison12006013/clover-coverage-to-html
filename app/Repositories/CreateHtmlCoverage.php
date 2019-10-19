<?php

namespace App\Repositories;

use Illuminate\Filesystem\Filesystem;

class CreateHtmlCoverage
{
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function setLinkCoverage(LinkCoverage $linkCoverage)
    {
        $this->linkCoverage = $linkCoverage;

        return $this;
    }

    public function handle()
    {
        $this->link             = $this->linkCoverage->handle();
        $this->filePathHtml     = $this->filePath . '.html';
        $this->fullFilePathHtml = $this->basePath . '/' . $this->filePathHtml;

        $this->fs->makeDirectory(dirname($this->fullFilePathHtml), 0777, true, true);
        $this->fs->put($this->fullFilePathHtml, $this->getContent());
    }

    public function getContent()
    {
        ob_start();

        $title        = $this->filePath;
        $coverage     = $this->link['content'];
        $greenPercent = $this->link['positive'] > 0 ? number_format($this->link['positive'] / ($this->link['positive'] + $this->link['negative']) * 100, 2) : 0;
        $redPercent   = $this->link['negative'] > 0 ? number_format($this->link['negative'] / ($this->link['positive'] + $this->link['negative']) * 100, 2) : 0;

        require __DIR__ . '/../Html/Base.php';

        return ob_get_clean();
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getFilePathHtml()
    {
        return $this->filePathHtml;
    }

    public function getFullFilePathHtml()
    {
        return $this->fullFilePathHtml;
    }
}
