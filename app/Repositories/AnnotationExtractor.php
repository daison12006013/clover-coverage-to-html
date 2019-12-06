<?php

namespace Daison\CloverToHtml\Repositories;

use ReflectionClass;

class AnnotationExtractor
{
    public $annotation = [];
    public $methods = [];

    public function __construct($classPath)
    {
        $r = new ReflectionClass($classPath);

        $classAnnotations = $this->extract($r->getDocComment());

        foreach ($r->getMethods() as $method) {
            $this->methods[$method->getName()] = $this->extract($method->getDocComment());
        }

        // combine the annotations from the class
        foreach ($r->getMethods() as $method) {
            $this->methods[$method->getName()] = array_merge(
                $this->methods[$method->getName()],
                $classAnnotations
            );
        }
    }

    protected function extract(string $docBlock)
    {
        preg_match_all('/@([a-zA-z]+)\s(.*)/', $docBlock, $matches);

        $ret = [];

        foreach ($matches[1] as $idx => $label) {
            $ret[trim($label)][] = trim($matches[2][$idx]);
        }

        return $ret;
    }

    public function getMethods()
    {
        return $this->methods;
    }
}
