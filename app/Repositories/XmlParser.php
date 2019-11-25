<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\Repositories\Contracts\InterpreterContract;
use Daison\CloverToHtml\ConfigManager;

class XmlParser implements InterpreterContract
{
    use MakeableTrait;

    protected $interpreter;
    protected $parser;

    public function __construct($xmlPath)
    {
        $xml = simplexml_load_string(
            file_get_contents($xmlPath),
            "SimpleXMLElement",
            LIBXML_NOCDATA
        );

        $this->arr = json_decode(json_encode($xml), true);
    }

    public function handle()
    {
        $files = [];

        foreach ($this->indexFileFinder($this->arr) as $fileArray) {
            $interpreter = $this->getInterpreter($fileArray);

            if (!$interpreter->valid()) {
                continue;
            }

            $files[] = $this->getParser($interpreter)->parse();
        }

        return $files;
    }

    public function setConfig(ConfigManager $config)
    {
        $this->config = $config;

        return $this;
    }

    public function indexFileFinder($arr)
    {
        $ret = [];

        foreach ($arr as $key => $val) {
            if ($key === 'file') {
                // this means in 1 file, there are more than 1 class!
                if (isset($val[0])) {
                    foreach ($val as $v) {
                        $ret[] = $v;
                    }
                } else {
                    $ret[] = $val;
                }

                continue;
            }

            if (is_array($val)) {
                foreach ($this->indexFileFinder($val) as $mVal) {
                    $ret[] = $mVal;
                }
            }
        }

        return $ret;
    }

    protected function getInterpreter($fileArray)
    {
        if ($this->interpreter) {
            return call_user_func($this->interpreter, [$fileArray]);
        }

        $interpreter = new ClassInterpreter($fileArray);

        $interpreter->setConfig($this->config);

        return $interpreter;
    }

    protected function getParser(InterpreterContract $interpreter)
    {
        if ($this->parser) {
            return call_user_func($this->parser, [$interpreter]);
        }

        return new ClassParser($interpreter);
    }

    public function setInterpreter(Closure $interpreter)
    {
        $this->interpreter = $interpreter;

        return $this;
    }

    public function setParser(Closure $parser)
    {
        $this->parser = $parser;

        return $this;
    }
}
