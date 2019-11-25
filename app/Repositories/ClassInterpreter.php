<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\ConfigManager;
use Daison\CloverToHtml\Repositories\Contracts\InterpreterContract;

class ClassInterpreter implements InterpreterContract
{
    public function __construct(array $fileArray)
    {
        $this->fileArray = $fileArray;
    }

    public function setConfig(ConfigManager $config)
    {
        $this->config = $config;

        return $this;
    }

    public function valid()
    {
        return isset($this->fileArray['class']);
    }

    public function getFile()
    {
        return $this->fileArray['@attributes']['name'];
    }

    public function getClass()
    {
        if (isset($this->fileArray['class'][0])) {
            $ret = [];

            foreach ($this->fileArray['class'] as $cls) {
                $ret[] = $this->resolveClass($cls['@attributes']);
            }

            return $ret;
        }

        return $this->resolveClass($this->fileArray['class']['@attributes']);
    }

    protected function resolveClass($attr)
    {
        if ($attr['namespace'] === 'global') {
            return $attr['name'];
        }

        return sprintf(
            '%s\\%s',
            $attr['namespace'],
            ltrim(str_replace($attr['namespace'], '', $attr['name']), '\\')
        );
    }

    public function getNamespace()
    {
        return $this->fileArray['class']['@attributes']['namespace'];
    }

    public function getMethods()
    {
        $methods        = [];
        $previousMethod = null;

        $fileLines = explode("\n", file_get_contents($this->getFile()));

        // dd($this->fileArray['line']);

        foreach ($this->fileArray['line'] ?? [] as $line) {
            $lineAttr = $line['@attributes'];

            if ($lineAttr['type'] === 'method') {
                $previousMethod = $lineAttr['name'];

                $methods[$previousMethod] = [
                    'visibility' => $lineAttr['visibility'] ?? 'public',
                    'complexity' => (int) $lineAttr['complexity'],
                    'crap'       => (float) $lineAttr['crap'],
                    'num'        => (int) $lineAttr['num'],
                    'count'      => (int) $lineAttr['count'],
                    'lines'      => [],
                ];
            } elseif ($lineAttr['type'] === 'stmt') {
                if (isset($fileLines[$lineAttr['num'] - 1])) {
                    $lineContent = $fileLines[$lineAttr['num'] - 1];

                    if (
                        $this->shouldExactIgnore($lineContent) ||
                        $this->shouldRegexIgnore($lineContent)
                    ) {
                        continue;
                    }
                }

                $methods[$previousMethod]['lines'][$lineAttr['num']] = (int) $lineAttr['count'];
            }
        }

        return $methods;
    }

    protected function shouldExactIgnore($line)
    {
        return (in_array(trim($line), $this->config->get('ignores')['exact']) !== false) ? true : false;
    }

    protected function shouldRegexIgnore($line)
    {
        foreach ($this->config->get('ignores')['regex'] as $ignoreRegex) {
            preg_match("/{$ignoreRegex}/", $line, $matches);

            if (count($matches)) {
                return true;
            }
        }

        return false;
    }
}
