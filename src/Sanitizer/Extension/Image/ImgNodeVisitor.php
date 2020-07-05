<?php

namespace App\Sanitizer\Extension\Image;

use HtmlSanitizer\Model\Cursor;
use HtmlSanitizer\Node\NodeInterface;
use HtmlSanitizer\Visitor\AbstractNodeVisitor;
use HtmlSanitizer\Visitor\NamedNodeVisitorInterface;
use HtmlSanitizer\Visitor\IsChildlessTagVisitorTrait;

class ImgNodeVisitor extends AbstractNodeVisitor implements NamedNodeVisitorInterface
{
    use IsChildlessTagVisitorTrait;
    private $sanitizer;
    protected $config;
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->sanitizer = new ImgSrcSanitizer(
            $this->config['allowed_hosts'],
            $this->config['allow_data_uri'],
            $this->config['allow_local_uri'],
            $this->config['force_https']
        );
    }

    protected function getDomNodeName(): string
    {
        return 'img';
    }

    public function getDefaultAllowedAttributes(): array
    {
        return ['src', 'alt', 'title'];
    }

    public function getDefaultConfiguration(): array
    {
        return [
            'allowed_hosts'   => null,
            'allow_data_uri'  => false,
            'allow_local_uri' => true,
            'force_https'     => false
        ];
    }

    protected function createNode(\DOMNode $domNode, Cursor $cursor): NodeInterface
    {
        $node = new ImgNode($cursor->node);
        $node->setAttribute('src', $this->sanitizer->sanitize($this->getAttribute($domNode, 'src')));

        return $node;
    }
}
