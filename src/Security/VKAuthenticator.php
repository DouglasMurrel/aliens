<?php

namespace App\Security;

use App\Service\UserCreator;
use App\Service\UserInfo;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Psr\Log\LoggerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class VKAuthenticator extends OAuth2Authenticator implements AuthenticatorInterface
{
    private $clientRegistry;
    private $router;
    private $userCreator;
    private $userInfo;
    private $logger;
    private $returnUrl;
    private $jwtManager;
    private $refreshTokenGenerator;
    private $refreshTokenManager;
    private $jwt;
    private $refreshToken;

    public function __construct(
            ClientRegistry $clientRegistry, 
            RouterInterface $router,
            UserCreator $userCreator,
            UserInfo $userInfo,
            LoggerInterface $logger,
            JWTTokenManagerInterface $jwtManager,
            RefreshTokenGeneratorInterface $refreshTokenGenerator,
            RefreshTokenManagerInterface $refreshTokenManager,
            string $returnUrl
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->userCreator = $userCreator;
        $this->userInfo = $userInfo;
        $this->logger = $logger;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenGenerator = $refreshTokenGenerator;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->returnUrl = $returnUrl;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_vkontakte_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('vkontakte_client');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                $vkUser = $client->fetchUserFromToken($accessToken);

                $user = $this->userInfo->getVkUser($vkUser);

                $this->jwt = $this->jwtManager->create($user);
                $this->refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, 2592000);
                $this->refreshTokenManager->save($this->refreshToken);
                
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->returnUrl . '?token=' . $this->jwt  . '&refreshToken=' . $this->refreshToken);
    
        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    
   /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/empty', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
