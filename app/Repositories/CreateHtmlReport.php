<?php

namespace App\Repositories;

use Illuminate\Filesystem\Filesystem;

class CreateHtmlReport
{
    public function __construct(Filesystem $fs, $config)
    {
        $this->fs     = $fs;
        $this->config = $config;
    }

    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function setPaths(array $paths)
    {
        $this->paths = $paths;

        return $this;
    }

    public function handle()
    {
        $this->fs->put($this->basePath . "/index.html", $this->getContent());
    }

    /**
     * Get the html content.
     *
     * @return string
     */
    protected function getContent()
    {
        list($listings, $green, $red) = static::parsePaths($this->paths);

        ob_start();

        $title         = $this->config['title'] ?? 'Code Coverage';
        $badges        = $this->config['badges'] ?? [];
        $contents      = implode("\n", $listings);
        $greenPercent  = $green > 0 ? number_format($green / ($green + $red) * 100, 2) : 0;
        $redPercent    = $red > 0 ? number_format($red / ($green + $red) * 100, 2) : 0;
        $numberOfFiles = count($this->paths);
        $greenLines    = $green;
        $redLines      = $red;

        require __DIR__ . '/../Html/Index.php';

        return ob_get_clean();
    }

    /**
     * Parse the paths and coverage values.
     *
     * @param  array $paths
     * @return array
     */
    public static function parsePaths(array $paths)
    {
        $listings   = [];
        $greenLines = 0;
        $redLines   = 0;

        foreach ($paths as $path => $attr) {
            $listings[] = strtr('
        <li class="file-coverage-path">
            <a href="{href}">{path}</a>
            <span data-line="{green-line}" class="badge badge-pill badge-success">{green}%</span>
            <span data-line="{red-line}" class="badge badge-pill badge-danger">{red}%</span>
        </li>', [
                '{href}'       => $path,
                '{path}'       => $path,
                '{green-line}' => $attr['positive'],
                '{red-line}'   => $attr['negative'],
                '{green}'      => $attr['positive'] > 0 ? number_format($attr['positive'] / ($attr['positive'] + $attr['negative']) * 100, 2) : 0,
                '{red}'        => $attr['negative'] > 0 ? number_format($attr['negative'] / ($attr['positive'] + $attr['negative']) * 100, 2) : 0,
            ]);

            $greenLines += $attr['positive'];
            $redLines += $attr['negative'];
        }

        return [$listings, $greenLines, $redLines];
    }
}
