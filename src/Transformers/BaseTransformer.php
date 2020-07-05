<?php

namespace App\Transformers;

use DateTime;
use App\Helpers\ImageHelper;
use App\Entity\EntityInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

abstract class BaseTransformer implements TransformerInterface
{
    protected $request;

    /** @var FilesystemAdapter $cache */
    protected $cache;

    public function __construct(RequestStack $requestStack, CacheInterface $cache)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->cache = $cache;
    }

    /**
     * @param DateTime |null $datetime
     * @return false|string
     */
    protected function dateFormat($datetime)
    {
        if (!$datetime instanceof DateTime) {
            return $datetime;
        }

        return gmdate(DateTime::ATOM, $datetime->getTimestamp());
    }

    protected function imageFormat($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = $this->getAbsolutePath($url);
        }
        return array_merge(
            [
                'url' => $url
            ],
            ImageHelper::getSize($url)
        );
    }

    private function getAbsolutePath($relativePath)
    {
        return $this->getHost() . $relativePath;
    }

    private function getHost()
    {
        return $this->request->getSchemeAndHttpHost();
    }

    /**
     * @param EntityInterface $entity
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function transform($entity)
    {
        $cacheKey = $this->cacheKeyBuilder($entity);
        $attributesCache = $this->cache->getItem($cacheKey);
        $attributesCacheData = $attributesCache->get();
        if (!empty($attributesCacheData)) {
            $attributesData = $attributesCacheData;
        } else {
            $attributesData = $this->getAttributes($entity);
            $attributesCache->set($attributesData);
            $this->cache->save($attributesCache);
        }
        $result = [
            'id' => $entity->getId(),
            'type' => $this->getType(),
            'attributes' => $attributesData
        ];

        $relationshipsData = $this->getRelationships($entity);
        if (!empty($relationshipsData)) {
            $result['relationships'] = $relationshipsData;
        }

        $includesData = $this->getIncludes($entity);
        if (!empty($includesData)) {
            $result['includes'] = $includesData;
        }

        return $result;
    }

    protected function cacheKeyBuilder($entity)
    {
        return $this->getType() . '-' . hash('sha256', serialize($entity));
    }
}
