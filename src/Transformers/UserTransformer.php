<?php

namespace App\Transformers;

use App\Entity\User;

class UserTransformer extends BaseTransformer
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'user';
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAttributes($user)
    {
        return [
            'slug' => $user->getSlug(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'avatar' => $user->getAvatar() ? $this->imageFormat($user->getAvatar()) : null,
            'cover' => $user->getCover() ? $this->imageFormat($user->getCover()) : null,
            'created_at' => $this->dateFormat($user->getCreatedAt()),
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
