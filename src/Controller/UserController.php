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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

class UserController extends AbstractController
{
    private $signUpValidator;
    private $userCreator;
    private $logger;
    
    private $jwtManager;
    protected $dispatcher;
    
    private $userInfo;
    
    public function __construct(
        SignUpValidator $signUpValidator,
        UserCreator $userCreator,
        LoggerInterface $logger,
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        UserInfo $userInfo
    )
    {
        $this->userCreator = $userCreator;
        $this->signUpValidator = $signUpValidator;
        $this->logger = $logger;
        $this->jwtManager = $jwtManager;
        $this->dispatcher = $dispatcher;
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
    #[Route('/sign-up-handler', name: 'sign-up-handler', methods: ['POST','OPTIONS'])]
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
    
    #[Route('/login', name: 'login', methods: ['POST','OPTIONS'])]
    public function loginHandler(#[CurrentUser] ?User $user): JsonResponse
    {
        return new JsonResponse();
    }
    
    #[Route('/userinfo', name: 'userinfo', methods: ['POST','OPTIONS'])]
    public function userInfo(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials for userinfo',
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $hellers = [];
        $helpers['orderCan'] = $this->userInfo->getOrdersHeplerCan();
        $helpers['orderWant'] = $this->userInfo->getOrdersHeplerWant();
        $helpers['orderNoes'] = $this->userInfo->getOrdersHeplerNoes();

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'userData' => $this->userInfo->getUserInfo($user),
            'helpers' => $helpers
        ]);
    }
    
    #[Route('/vk-login', name: 'vkontakte-start', methods: ['POST','OPTIONS','GET'])]
    public function loginVkHandler(Request $request, ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('vkontakte_client')
            ->redirect(['email'],['redirect_uri'=>$request->server->get('HTTP_X_FORWARDED_PROTO').'://'.$request->server->get('HTTP_HOST').'/vk-login-callback']);
    }
    
    #[Route('/vk-login-callback', name: 'connect_vkontakte_check', methods: ['GET'])]
    public function loginVkCheck(Request $request, ClientRegistry $clientRegistry): JsonResponse
    {
        return new JsonResponse();
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
