<?php

namespace App\Twig;

use Twig\TwigFilter;
use App\Utils\Markdown;
use Twig\Extension\AbstractExtension;

/**
 * This Twig extension adds a new 'md2html' filter to easily transform Markdown
 * contents into HTML contents inside Twig templates.
 *
 * See https://symfony.com/doc/current/templating/twig_extension.html
 */
class AppExtension extends AbstractExtension
{
    private $parser;

    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
            new TwigFilter('shorten_number', [$this, 'numberFormatShort'], ['is_safe' => ['html']]),
        ];
    }


    /**
     * Transforms the given Markdown content into HTML content.
     */
    public function markdownToHtml(string $content): string
    {
        return $this->parser->toHtml($content);
    }

    public function numberFormatShort($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $result = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $result = number_format($n / 1000, $precision);
            $suffix = 'k';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $result = number_format($n / 1000000, $precision);
            $suffix = 'm';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $result = number_format($n / 1000000000, $precision);
            $suffix = 'b';
        } else {
            // 0.9t+
            $result = number_format($n / 1000000000000, $precision);
            $suffix = 't';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $result = str_replace($dotzero, '', $result);
        }
        return $result . $suffix;
    }
}
