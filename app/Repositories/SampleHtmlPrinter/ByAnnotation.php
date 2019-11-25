<?php

namespace Daison\CloverToHtml\Repositories\SampleHtmlPrinter;

use Daison\CloverToHtml\Repositories\MakeableTrait;
use Daison\CloverToHtml\Repositories\SampleCalculator;
use Illuminate\Filesystem\Filesystem;

class ByAnnotation
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
        $this->annotations = [];

        foreach ($this->xmlParsed as $idx => $classParser) {
            foreach ($classParser->getCalculations()['annotations'] as $flag => $values) {
                foreach ($values as $name => $value) {
                    if (!isset($this->annotations[$flag][$name])) {
                        $this->annotations[$flag][$name] = [
                            'positive'   => 0,
                            'negative'   => 0,
                            'percentage' => null,
                        ];
                    }

                    $this->annotations[$flag][$name]['positive'] += $value['positive'];
                    $this->annotations[$flag][$name]['negative'] += $value['negative'];
                    $this->annotations[$flag][$name]['percentage'] = SampleCalculator::calculatePercetange(
                        $this->annotations[$flag][$name]['positive'],
                        $this->annotations[$flag][$name]['negative']
                    );
                    $this->annotations[$flag][$name]['files'][$classParser->getFile()] = $value;
                }
            }
        }
    }

    protected function getClassContent()
    {
        ob_start();

        $annotations = $this->annotations;

        require __DIR__ . '/../.tmp/annotationsTemplate.php';

        return ob_get_clean();
    }

    protected function getStorePath()
    {
        return $this->storePath.'/annotations.html';
    }
}
