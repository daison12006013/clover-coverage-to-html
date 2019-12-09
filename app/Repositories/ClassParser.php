<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\Repositories\Contracts\InterpreterContract;
use Daison\CloverToHtml\Repositories\Contracts\ParserContract;
use Daison\CloverToHtml\Repositories\Traits\ConfigSettler;

class ClassParser implements ParserContract
{
    use ConfigSettler;

    protected $interpret;
    protected $methods;

    public function __construct(InterpreterContract $interpreter)
    {
        $this->interpreter = $interpreter;
        $this->methods     = $interpreter->getMethods();
    }

    public function __call($name, $args)
    {
        $intMethods = [
            'valid',
            'getFile',
            'getClass',
            'getNamespace',
        ];

        if (in_array($name, $intMethods) !== false) {
            return call_user_func_array([$this->interpreter, $name], $args);
        }
    }

    public function parse()
    {
        $this->appendAnnotations();
        $this->appendCalculations();

        return $this;
    }

    protected function appendAnnotations()
    {
        $classPaths = $this->interpreter->getClass();

        if (!is_array($classPaths)) {
            $classPaths = [$classPaths];
        }

        foreach ($classPaths as $classPath) {
            $ae = new AnnotationExtractor($classPath);

            foreach ($ae->getMethods() as $name => $annotations) {
                // if the method at first does not exists,
                // this annotation probably part of the trait's method
                // or inheritance.
                if (!isset($this->methods[$name])) {
                    continue;
                }

                $this->methods[$name]['annotations'] = $annotations;
            }
        }

        return $this;
    }

    protected function appendCalculations()
    {
        $calculator = new SampleCalculator($this);
        $calculator->setConfig($this->config);

        $this->calculations = $calculator->handle();

        return $this;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getCalculations()
    {
        return $this->calculations;
    }
}
