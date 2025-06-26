<?php declare(strict_types=1);

namespace Smug\Core\Twig\Filter;

use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SmugMarkdownExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('SmugMarkdown', [$this, 'parseMarkdown'], ['is_safe' => ['html']])
        ];
    }

    public function parseMarkdown(string $content): string
    {
        return Parsedown::instance()->parse($content);
    }
}