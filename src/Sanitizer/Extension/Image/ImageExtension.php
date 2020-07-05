<?php

namespace App\Sanitizer\Extension\Image;

use HtmlSanitizer\Extension\ExtensionInterface;
use HtmlSanitizer\Visitor\NodeVisitorInterface;

class ImageExtension implements ExtensionInterface
{

    /**
     * Return this extension name, which will be used in the sanitizer configuration.
     */
    public function getName(): string
    {
        return 'custom-image';
    }

    /**
     * Return a list of node visitors to register in the sanitizer following the format tagName => visitor.
     * For instance: 'strong' => new StrongVisitor($config).
     *
     * @param array $config The configuration given by the user of the library.
     *
     * @return NodeVisitorInterface[]
     */
    public function createNodeVisitors(array $config = []): array
    {
        return [
            'img' => new ImgNodeVisitor($config['tags']['img'] ?? [])
        ];
    }
}
