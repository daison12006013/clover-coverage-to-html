<?php

namespace Daison\CloverToHtml\Repositories;

use Daison\CloverToHtml\Repositories\Traits\ConfigSettler;

class ParseToHtml
{
    use ConfigSettler;
    use MakeableTrait;

    public function __construct(array $parsed)
    {
        $this->parsed = $parsed;
    }

    public function handle()
    {
        $ret = [];

        foreach ($this->parsed as $idx => $parse) {
            $ret[$idx] = $this->processColors($parse);
        }

        return $ret;
    }

    public function processColors($parse)
    {
        $methods = $parse->getMethods();
        $content = static::getContent($parse);

        foreach ($content as $line => $code) {
            foreach ($methods as $methodName => $methodBlock) {
                foreach ($methodBlock['lines'] as $bLine => $count) {
                    if ($bLine === $line + 1) {
                        $methods[$methodName]['c_lines'][$bLine]
                        = $content[$line]
                        = static::applyColors($bLine, $code, $count);
                    }
                }
            }
        }

        return [
            'methods' => $methods,
            'content' => implode("\n", $content),
        ];
    }

    public static function getContent($parse)
    {
        $filePath = $parse->getFile();
        // $filePath = str_replace('/Users/daisoncarino/Incube8/sa', '/var/www', $filePath);

        $content = file_get_contents($filePath);

        return array_map(function ($val) {
            return htmlspecialchars($val);
        }, explode("\n", $content));
    }

    public static function applyColors($line, $code, $hit)
    {
        return strtr('<i data-line-of-code="{line}" data-number-of-hit="{hit}" class="{class}">{code}</i>', [
            '{line}'  => $line,
            '{class}' => $hit > 0 ? 'positive' : 'negative',
            '{hit}'   => $hit,
            '{code}'  => $code,
        ]);
    }
}
