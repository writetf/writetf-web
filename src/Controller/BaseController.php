<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * BaseController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param $request
     * @return JsonResponse | void
     */
    public function validate($request)
    {
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($request);
        if (count($violations) !== 0) {
            $results = [];
            foreach ($violations as $violation) {
                $key = $violation->getPropertyPath();
                $results[$key] = $violation->getMessage();
            }
            return new JsonResponse(
                [
                    'status' => 'error',
                    'errors' => $results
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
