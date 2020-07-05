<?php

namespace App\Service;

use App\Entity\User;
use App\Helpers\StringHelper;
use App\Helpers\RequestHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    protected $entityManager;
    protected $encoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function createUser($email, $username, $password)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $slug = StringHelper::slugify($user->getUsername());
        $user->setSlug($slug);
        $user->setRoles(['ROLE_USER']);
        $user->setCountry(RequestHelper::getCountry());
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
