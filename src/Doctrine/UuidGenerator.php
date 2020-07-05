<?php

namespace App\Doctrine;

use Exception;
use App\Helpers\StringHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Id\AbstractIdGenerator;

class UuidGenerator extends AbstractIdGenerator
{
    const LENGTH = 11;

    /**
     * Generate an identifier
     *
     * @param EntityManager $em
     * @param Entity $entity
     * @return string
     * @throws Exception
     */
    public function generate(EntityManager $em, $entity)
    {
        return StringHelper::generateRandomString(self::LENGTH);
    }
}
