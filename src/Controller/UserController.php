<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request\SignUpRequest;
use App\Service\SignUpValidator;
use App\Service\UserCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Psr\Log\LoggerInterface;

class UserController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $signUpValidator;

    /**
     * @var SignUpValidator
     */
    private $userCreator;
    
    private $logger;

    public function __construct(
        SignUpValidator $signUpValidator,
        UserCreator $userCreator,
        LoggerInterface $logger
    )
    {
        $this->userCreator = $userCreator;
        $this->signUpValidator = $signUpValidator;
        $this->logger = $logger;
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/sign-up-handler', name: 'sign-up-handler', methods: ['POST'])]
    public function signUpHandler(Request $request): JsonResponse
    {
        $signUpRequest = $this->decodeRequest($request);
        if(!$this->signUpValidator->validate($signUpRequest)){
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $this->signUpValidator->getErrors()
            ]);
        }

        $user = $this->userCreator->createUser($signUpRequest);

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'entity' => $user->getId()
        ]);
    }
    
    private function decodeRequest(Request $request): SignUpRequest
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        
//        $data = json_decode($request->getContent(), true);
        $data = $request->getContent();

//        if (json_last_error() !== JSON_ERROR_NONE) {
            //throw new HttpException(400, 'invalid_json');
//            return false;
//        }
        
        $signUpRequest = $serializer->deserialize($data, SignUpRequest::class, 'json');
        return $signUpRequest;
    }
}
