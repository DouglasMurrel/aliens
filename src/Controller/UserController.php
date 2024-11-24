<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Http\Request\SignUpRequest;
use App\Service\SignUpValidator;
use App\Service\UserCreator;
use App\Service\UserInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Psr\Log\LoggerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

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
    
    private $jwtManager;
    
    private $userInfo;
    
    public function __construct(
        SignUpValidator $signUpValidator,
        UserCreator $userCreator,
        LoggerInterface $logger,
        JWTTokenManagerInterface $jwtManager,
        UserInfo $userInfo
    )
    {
        $this->userCreator = $userCreator;
        $this->signUpValidator = $signUpValidator;
        $this->logger = $logger;
        $this->jwtManager = $jwtManager;
        $this->userInfo = $userInfo;
    }
    
    #[Route('/', name: 'empty')]
    public function empty(): Response
    {
        return new Response();
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/sign-up-handler', name: 'sign-up-handler', methods: ['POST'])]
    public function signUpHandler(Request $request): JsonResponse
    {
        $signUpRequest = $this->decodeSignUpRequest($request);
        if(!$this->signUpValidator->validate($signUpRequest)){
            return $this->json([
                'errors' => $this->signUpValidator->getErrors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userCreator->createUser($signUpRequest);

        return $this->json([
            'entity' => $user->getId(),
        ]);
    }
    
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function loginHandler(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
    
    #[Route('/userinfo', name: 'userinfo', methods: ['GET','POST'])]
    public function userInfo(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials for userinfo',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'userData' => $this->userInfo->getUserInfo($user)
        ]);
    }
    
    #[Route('/login-vk', name: 'connect_vkontakte_check', methods: ['POST'])]
    public function loginVkHandler(Request $request, ClientRegistry $clientRegistry): JsonResponse
    {
    }
    
    private function decodeSignUpRequest(Request $request): SignUpRequest
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        
        $data = $request->getContent();

        $signUpRequest = $serializer->deserialize($data, SignUpRequest::class, 'json');
        return $signUpRequest;
    }
}
