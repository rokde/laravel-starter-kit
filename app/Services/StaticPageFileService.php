<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FrontMatter;
use App\Models\StaticPage;
use Symfony\Component\Yaml\Yaml;

class StaticPageFileService
{
    public const PATTERN = '/^[\s\r\n]?---[\s\r\n]?$/sm';

    public function parseFile(string $path): StaticPage
    {
        return $this->parse(file_get_contents($path));
    }

    public function parse(string $content): StaticPage
    {
        $parts = preg_split(self::PATTERN, PHP_EOL.mb_ltrim($content));

        if (count($parts) < 3) {
            return new StaticPage($content, new FrontMatter);
        }

        $data = Yaml::parse(mb_trim($parts[1]));
        $body = implode(PHP_EOL.'---'.PHP_EOL, array_slice($parts, 2));

        return new StaticPage($body, new FrontMatter($data));
    }
}
