<?php

namespace App\Sanitizer\Extension\Image;

use HtmlSanitizer\Sanitizer\UrlSanitizerTrait;

class ImgSrcSanitizer
{
    use UrlSanitizerTrait;

    private $allowedHosts;
    private $allowDataUri;
    private $allowLocalUri;
    private $forceHttps;
    public function __construct(?array $allowedHosts, bool $allowDataUri, bool $allowLocalUri, bool $forceHttps)
    {
        $this->allowedHosts = $allowedHosts;
        $this->allowDataUri = $allowDataUri;
        $this->allowLocalUri = $allowLocalUri;
        $this->forceHttps = $forceHttps;
    }
    public function sanitize(?string $input): ?string
    {
        $allowedSchemes = ['http', 'https'];
        $allowedHosts = $this->allowedHosts;
        if ($this->allowDataUri && !$this->allowLocalUri) {
            $allowedSchemes = ['data'];
            if (null !== $allowedHosts) {
                $allowedHosts[] = null;
            }
        }
        if ($this->allowLocalUri) {
            $allowedSchemes[] = null;
            if (null !== $allowedHosts) {
                $allowedHosts[] = null;
            }
        }
        if (!$sanitized = $this->sanitizeUrl($input, $allowedSchemes, $allowedHosts, $this->forceHttps)) {
            return null;
        }
        if (0 === strpos($sanitized, 'data:') && 0 !== strpos($sanitized, 'data:image/')) {
            return null;
        }
        return $sanitized;
    }
}
