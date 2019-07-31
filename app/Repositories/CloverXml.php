<?php

namespace App\Repositories;

use Illuminate\Filesystem\Filesystem;
use Exception;

/**
 * CloverXMl with memory efficient way to load the xml.
 *
 * @author Daison Carino <daison12006013@gmail.com>
 */
class CloverXml
{
    private $xmlPath;
    private $storePath;
    private $withCallback;
    private $renderedPaths = [];

    public function __construct($config, $xmlPath, $storePath, \Closure $withCallback = null)
    {
        $this->config       = $config;
        $this->fs           = new Filesystem;
        $this->xmlPath      = $xmlPath;
        $this->storePath    = $storePath;
        $this->withCallback = $withCallback;
        $this->xml          = simplexml_load_string(
            file_get_contents($xmlPath),
            'SimpleXMLElement',
            LIBXML_NOCDATA
        );
    }

    /**
     * If you would like to use this class as an html maker instead.
     */
    public static function html(array $config, $xmlPath, $storePath, \Closure $callback = null)
    {
        if ($callback === null) {
            $callback = function ($packageName, $filepath, $collection, $clover) {
                if (!file_exists($filepath)) {
                    return;
                }

                $clover->createHtmlCoverage($packageName, $filepath, $collection);
            };
        }

        $self = new static($config, $xmlPath, $storePath, $callback);

        $self->createHtmlReport();
    }

    /**
     * If you only like to reverse engineer the xml.
     *
     * @return array
     */
    public static function toArray($config, $xmlPath, $storePath)
    {
        return (new static($config, $xmlPath, $storePath))->processXml();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function createHtmlReport()
    {
        return (new CreateHtmlReport($this->fs, $this->config))
            ->setBasePath($this->storePath)
            ->setPaths($this->processXml())
            ->handle();
    }

    /**
     * Undocumented function
     *
     * @param [type] $packageName
     * @param [type] $filepath
     * @param [type] $collection
     * @return void
     */
    protected function createHtmlCoverage($packageName, $filepath, $collection)
    {
        $cov = new CreateHtmlCoverage($this->fs);

        $cov->setBasePath($this->storePath)
            ->setFilePath($filepath)
            ->setLinkCoverage($this->linkCoverage(file_get_contents($filepath), $collection))
            ->handle();

        $html = $cov->getFilePathHtml();

        $link = $cov->getLink();

        $this->renderedPaths[ltrim($html, '/')] = $report = $this->extractEssentialCoverage($link);

        return [
            $html,
            $link,
            $report,
        ];
    }

    /**
     * Extract essential keys without over populating the informations.
     *
     * @param  array $link
     * @return void
     */
    private function extractEssentialCoverage(array $link)
    {
        $keys = [
            'positive',
            'negative',
        ];

        return array_filter($link, function ($k) use ($keys) {
            return in_array($k, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Link the lines thru their respected lines.
     *
     * @param  string $content
     * @param  array $collection
     * @return \LinkCoverage
     */
    protected function linkCoverage(string $content, array $collection)
    {
        return (new LinkCoverage())
            ->setIgnoreExactValues($this->config['ignores']['exact'] ?? [])
            ->setIgnoreRegexValues($this->config['ignores']['regex'] ?? [])
            ->setContentCollection($collection)
            ->setContent(htmlspecialchars($content));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function processXml()
    {
        $parsed = [];

        foreach ($this->xml as $project) {
            if ($project->getName() !== 'project') {
                continue;
            }

            foreach ($project as $packageOrFile) {
                switch ($packageOrFile->getName()) {
                    case 'package':
                        $packageName = (string) $packageOrFile->attributes()['name'];

                        foreach ($packageOrFile as $file) {
                            $this->xmlFile($file, $packageName, $parsed);
                        }

                        break;
                    case 'file':
                        $this->xmlFile($packageOrFile, 'default', $parsed);
                        break;
                }
            }
        }

        if ($this->withCallback) {
            return $this->renderedPaths;
        }

        return $parsed;
    }

    /**
     * Undocumented function
     *
     * @param [type] $file
     * @param [type] $packageName
     * @param [type] $parsed
     * @return void
     */
    protected function xmlFile($file, $packageName, &$parsed)
    {
        if ($file->getName() !== 'file') {
            return;
        }

        $fileName = (string) $file->attributes()['name'];

        foreach ($file as $line) {
            if ($line->getName() !== 'line') {
                continue;
            }

            $lineAttr = array_first(((array) $line->attributes()));

            $parsed[$packageName][$fileName][$lineAttr['num']] = $lineAttr;
        }

        if (!isset($parsed[$packageName][$fileName])) {
            return;
        }

        if ($callback = $this->withCallback) {
            call_user_func_array($callback, [$packageName, $fileName, $parsed[$packageName][$fileName], $this]);

            // we need to unset to make it more efficient
            unset($parsed[$packageName][$fileName]);
        }
    }
}
