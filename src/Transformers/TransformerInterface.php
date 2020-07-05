<?php

namespace App\Transformers;

use App\Entity\EntityInterface;

interface TransformerInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function getAttributes($entity);

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function getIncludes($entity);

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function getRelationships($entity);
}
