<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\Repositories\Contracts\ParserContract;

class SampleCalculator
{
    use MakeableTrait;

    const ANNOTATION_GROUP = [
        'module',
        'feature',
    ];

    public function __construct(ParserContract $parser)
    {
        $this->parser = $parser;
    }

    public function handle()
    {
        $calculated = [
            'positive'    => 0, // overall class positives
            'negative'    => 0, // overall class negatives
            'percentage'  => 0, // overall class percentage
            'methods'     => [], // per method
            'annotations' => [], // per groups defined by @group
        ];

        foreach ($this->parser->getMethods() as $methodName => $methodInfo) {
            list($positive, $negative) = $this->extractLinesPosNeg($methodInfo['lines'] ?? []);

            // overall class
            $calculated['positive'] += $positive;
            $calculated['negative'] += $negative;
            $percentage = static::calculatePercetange($positive, $negative);

            // per method
            $calculated['methods'][$methodName] = $calcMethod = [
                'positive'    => $positive,
                'negative'    => $negative,
                'percentage'  => $percentage,
                'annotations' => [],
            ];

            // per group
            foreach ($methodInfo['annotations'] ?? [] as $annoName => $values) {
                if (in_array($annoName, static::ANNOTATION_GROUP) === false) {
                    continue;
                }

                foreach ($values as $v) {
                    $v = trim($v);

                    $calculated['methods'][$methodName]['annotations'][$v] = $v;

                    if (!isset($calculated['annotations'][$annoName][$v])) {
                        $calculated['annotations'][$annoName][$v] = [
                            'positive'   => 0,
                            'negative'   => 0,
                            'percentage' => null,
                            'methods'    => [],
                        ];
                    }

                    $calculated['annotations'][$annoName][$v]['methods'][$methodName] = sprintf(
                        '%s [%s/%s] %s%%',
                        $methodName,
                        $calcMethod['positive'],
                        $calcMethod['negative'],
                        $calcMethod['percentage']
                    );
                    $calculated['annotations'][$annoName][$v]['positive'] += $calcMethod['positive'];
                    $calculated['annotations'][$annoName][$v]['negative'] += $calcMethod['negative'];
                    $calculated['annotations'][$annoName][$v]['percentage'] = static::calculatePercetange(
                        $calculated['annotations'][$annoName][$v]['positive'],
                        $calculated['annotations'][$annoName][$v]['negative']
                    );
                }
            }
        }

        $percentage               = static::calculatePercetange($calculated['positive'], $calculated['negative']);
        $calculated['percentage'] = $percentage;

        return $calculated;
    }

    protected function extractLinesPosNeg(array $lines)
    {
        $positive = count(array_filter($lines, function ($val) {
            return $val > 0;
        }));

        $negative = count($lines) - $positive;

        return [$positive, $negative];
    }

    public static function calculatePercetange($positive, $negative)
    {
        return number_format(
            ($positive + $negative !== 0)
            ? $positive / ($positive + $negative) * 100
            : 0,
            2
        );
    }
}
