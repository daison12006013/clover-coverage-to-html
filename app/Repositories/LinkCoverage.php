<?php

namespace App\Repositories;

class LinkCoverage
{
    public function setIgnoreExactValues(array $values)
    {
        $this->ignoresExact = $values;

        return $this;
    }

    public function setIgnoreRegexValues(array $values)
    {
        $this->ignoresRegex = $values;

        return $this;
    }

    public function setContentCollection($contentCollection)
    {
        $this->contentCollection = $contentCollection;

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function handle()
    {
        $positive        = 0;
        $negative        = 0;
        $contentToArray = explode("\n", $this->content);

        foreach ($this->contentCollection as $line => $attr) {
            if ($attr['type'] !== 'stmt') {
                continue;
            }

            if (!isset($contentToArray[$line - 1])) {
                continue;
            }

            $lineContent = $contentToArray[$line - 1];

            if ($this->shouldExactIgnore($lineContent)) {
                continue;
            }

            if ($this->shouldRegexIgnore($lineContent)) {
                continue;
            }

            if (((int) $attr['count']) > 0) {
                $contentToArray[$line - 1] = $this->appendCovered($lineContent);
                $positive++;

                continue;
            }

            $contentToArray[$line - 1] = $this->appendUncovered($lineContent);
            $negative++;
        }

        $contentToArray = $this->appendHtmlCode($contentToArray);

        return [
            'content'  => implode("\n", $contentToArray),
            'positive' => $positive,
            'negative' => $negative,
        ];
    }

    protected function shouldExactIgnore(string $content)
    {
        return (in_array(trim($content), $this->ignoresExact) !== false) ? true : false;
    }

    protected function shouldRegexIgnore(string $content)
    {
        foreach ($this->ignoresRegex as $ignoreRegex) {
            preg_match("/{$ignoreRegex}/", $content, $matches);

            if (count($matches)) {
                return true;
            }
        }

        return false;
    }

    protected function appendCovered($content)
    {
        return strtr('<i class="line-coverage-green">{content}</i>', [
            '{content}' => $content,
        ]);
    }

    protected function appendUncovered($content)
    {
        return strtr('<i class="line-coverage-red">{content}</i>', [
            '{content}' => $content,
        ]);
    }

    protected function appendHtmlCode(array $arr)
    {
        foreach ($arr as $idx => $content) {
            $arr[$idx] = strtr('<code>{content}</code>', ['{content}' => $content]);
        }

        return $arr;
    }
}
