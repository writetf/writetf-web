<?php

namespace App\Transformers;

use App\Entity\Community;

class CommunityTransformer extends BaseTransformer
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'community';
    }

    /**
     * @param Community $community
     * @return array
     */
    public function getAttributes($community)
    {
        return [
            'slug' => $community->getSlug(),
            'name' => $community->getName(),
            'description' => $community->getDescription(),
            'avatar' => $community->getAvatar() ? $this->imageFormat($community->getAvatar()) : null,
            'cover' => $community->getCover() ? $this->imageFormat($community->getCover()) : null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getIncludes($entity)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRelationships($entity)
    {
        return [];
    }
}
