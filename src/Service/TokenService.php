<?php

namespace App\Service;

use DateTime;
use Exception;
use App\Entity\Token;
use Firebase\JWT\JWT;
use App\Helpers\RequestHelper;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TokenService
{
    protected $entityManager;
    protected $parameterBag;
    protected $tokenRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag,
        TokenRepository $tokenRepository
    ) {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param $user
     * @return string
     * @throws Exception
     */
    public function createToken($user)
    {
        $token = new Token();
        $expiredAt = time() + (365 * 24 * 60 * 60);
        $token->setUser($user);

        $token->setUserAgent(RequestHelper::getUserAgent());
        $token->setIpAddress(RequestHelper::getRemoteIPAddress());
        $token->setCountry(RequestHelper::getCountry());
        $token->setExpiredAt((new DateTime())->setTimestamp($expiredAt));
        $this->entityManager->persist($token);
        $this->entityManager->flush();
        $payload = [
            'jti' => $token->getId(),
            "iss" => "https://writetf.com",
            "exp" => $expiredAt,
            'alg' => 'HS256'
        ];
        $key = $this->parameterBag->get('token_secret');

        return JWT::encode($payload, $key);
    }

    /**
     * @param $credentials
     * @return false | Token
     */
    public function validate($credentials)
    {
        $credentialsExploded = explode('Bearer ', $credentials);
        if (count($credentialsExploded) != 2) {
            return false;
        }
        $token = $credentialsExploded[1];
        $key = $this->parameterBag->get('token_secret');

        $header = JWT::decode($token, $key, ['HS256']);
        if ($header->exp < time()) {
            return false;
        }
        /** @var Token $token */
        $token = $this->tokenRepository->find($header->jti);
        if (empty($token)) {
            return false;
        }

        return $token;
    }
}
