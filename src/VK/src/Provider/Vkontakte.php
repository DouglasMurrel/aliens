<?php

namespace App\VK\Provider;

use App\Service\SHA256Helper;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Vkontakte extends AbstractProvider implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $baseOAuthUri = 'https://id.vk.com';
    protected $baseUri      = 'https://api.vk.com/method';
    protected $version      = '5.199';
    protected $language     = null;

    /**
     * @type array
     * @see https://vk.com/dev/permissions
     */
    public $scopes = [
//        'email',
//        'friends',
//        'offline',
        //'photos',
        //'wall',
        //'ads',
        //'audio',
        //'docs',
        //'groups',
        //'market',
        //'messages',
        //'nohttps',
        //'notes',
        //'notifications',
        //'notify',
        //'pages',
        //'stats',
        //'status',
        //'video',
    ];
    /**
     * @type array
     * @see https://new.vk.com/dev/fields
     */
    public $userFields = [
        'bdate',
        'city',
        'country',
        'domain',
        'first_name',
        'friend_status',
        'has_photo',
        'home_town',
        'id',
        'is_friend',
        'last_name',
        'maiden_name',
        'nickname',
        'photo_max',
        'photo_max_orig',
        'screen_name',
        'sex',
        //'about',
        //'activities',
        //'blacklisted',
        //'blacklisted_by_me',
        //'books',
        //'can_post',
        //'can_see_all_posts',
        //'can_see_audio',
        //'can_send_friend_request',
        //'can_write_private_message',
        //'career',
        //'common_count',
        //'connections',
        //'contacts',
        //'crop_photo',
        //'counters',
        //'deactivated',
        //'education',
        //'exports',
        //'followers_count',
        //'games',
        //'has_mobile',
        //'hidden',
        //'interests',
        //'is_favorite',
        //'is_hidden_from_feed',
        //'last_seen',
        //'military',
        //'movies',
        //'occupation',
        //'online',
        //'personal',
        //'photo_100',
        //'photo_200',
        //'photo_200_orig',
        //'photo_400_orig',
        //'photo_50',
        //'photo_id',
        //'quotes',
        //'relation',
        //'relatives',
        //'schools',
        //'site',
        //'status',
        //'timezone',
        //'tv',
        //'universities',
        //'verified',
        //'wall_comments',
    ];

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = (string)$language;

        return $this;
    }

    public function getBaseAuthorizationUrl()
    {
        return "$this->baseOAuthUri/authorize";
    }
    public function getBaseAccessTokenUrl(array $params)
    {
        return "$this->baseOAuthUri/oauth2/auth";
    }
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $params = [
            'fields'       => $this->userFields,
            'access_token' => $token->getToken(),
            'v'            => $this->version,
            'lang'         => $this->language
        ];
        $query  = $this->buildQueryString($params);
        $url    = "$this->baseUri/users.get?$query";

        return $url;
    }
    protected function getDefaultScopes()
    {
        return $this->scopes;
    }
    protected function checkResponse(ResponseInterface $response, $data)
    {
        // Metadata info
        $contentTypeRaw = $response->getHeader('Content-Type');
        $contentTypeArray = explode(';', reset($contentTypeRaw));
        $contentType = reset($contentTypeArray);
        // Response info
        $responseCode    = $response->getStatusCode();
        $responseMessage = $response->getReasonPhrase();
        // Data info
        $error            = !empty($data['error']) ? $data['error'] : null;
        $errorCode        = !empty($error['error_code']) ? $error['error_code'] : $responseCode;
        $errorDescription = !empty($data['error_description']) ? $data['error_description'] : null;
        $errorMessage     = !empty($error['error_msg']) ? $error['error_msg'] : $errorDescription;
        $message          = $errorMessage ?: $responseMessage;

        // Request/meta validation
        if (399 < $responseCode) {
            throw new IdentityProviderException($message, $responseCode, $data);
        }

        // Content validation
        if ('application/json' != $contentType) {
            throw new IdentityProviderException($message, $responseCode, $data);
        }
        if ($error) {
            throw new IdentityProviderException($errorMessage, $errorCode, $data);
        }
    }
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $response   = reset($response['response']);
        $additional = $token->getValues();
        if (!empty($additional['email'])) {
            $response['email'] = $additional['email'];
        }
        if (!empty($response['uid']) && 4 === floor($this->version)) {
            $response['id'] = $response['uid'];
        }
        if (!empty($additional['user_id'])) {
            $response['id'] = $additional['user_id'];
        }

        return new User($response, $response['id']);
    }

    /**
     * @see https://vk.com/dev/users.get
     *
     * @param integer[]        $ids
     * @param AccessToken|null $token Current user if empty
     * @param array            $params
     *
     * @return User[]
     */
    public function usersGet(array $ids = [], AccessToken $token = null, array $params = [])
    {
        if (empty($ids) && !$token) {
            throw new \InvalidArgumentException('Some of parameters usersIds OR access_token are required');
        }

        $default = [
            'user_ids'     => implode(',', $ids),
            'fields'       => $this->userFields,
            'access_token' => $token ? $token->getToken() : null,
            'v'            => $this->version,
            'lang'         => $this->language
        ];
        $params  = array_merge($default, $params);
        $query   = $this->buildQueryString($params);
        $url     = "$this->baseUri/users.get?$query";

        $response   = $this->getResponse($this->createRequest(static::METHOD_GET, $url, $token, []))['response'];
        $users      = !empty($response['items']) ? $response['items'] : $response;
        $array2user = function ($userData) {
            return new User($userData);
        };

        return array_map($array2user, $users);
    }
    /**
     * @see https://vk.com/dev/friends.get
     *
     * @param integer          $userId
     * @param AccessToken|null $token
     * @param array            $params
     *
     * @return User[]
     */
    public function friendsGet($userId, AccessToken $token = null, array $params = [])
    {
        $default = [
            'user_id'      => $userId,
            'fields'       => $this->userFields,
            'access_token' => $token ? $token->getToken() : null,
            'v'            => $this->version,
            'lang'         => $this->language
        ];
        $params  = array_merge($default, $params);
        $query   = $this->buildQueryString($params);
        $url     = "$this->baseUri/friends.get?$query";

        $response     = $this->getResponse($this->createRequest(static::METHOD_GET, $url, $token, []))['response'];
        $friends      = !empty($response['items']) ? $response['items'] : $response;
        $array2friend = function ($friendData) {
            if (is_numeric($friendData)) {
                $friendData = ['id' => $friendData];
            }

            return new User($friendData);
        };

        return array_map($array2friend, $friends);
    }
    
    protected function getPkceMethod()
    {
        return static::PKCE_METHOD_S256;
    }

    public function getAccessToken($grant, array $options = [])
    {
        $grant = $this->verifyGrant($grant);
        $this->redirectUri = preg_replace('/^http:\/\//','https://',$this->redirectUri);

        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
        ];

        $params   = $grant->prepareRequestParameters($params, $options);
        $request  = $this->getAccessTokenRequest($params);
        $this->logger->info($request->getUri());
        $this->logger->info($request->getBody());
        $response = $this->getParsedResponse($request);
        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }
        $prepared = $this->prepareAccessTokenResponse($response);
        $token    = $this->createAccessToken($prepared, $grant);

        return $token;
    }

    public function getResponse(RequestInterface $request)
    {
        $response = $this->getHttpClient()->send($request);
        $this->logger->info($response->getBody());
        return $response;
    }
    
    protected function getAuthorizationParameters(array $options)
    {
        if (empty($options['state'])) {
            $options['state'] = $this->getRandomState();
        }

        if (empty($options['scope'])) {
//            $options['scope'] = $this->getDefaultScopes();
        }

        $options += [
            'response_type'   => 'code',
//            'approval_prompt' => 'auto'
        ];

        if (isset($options['scope']) && is_array($options['scope'])) {
            $separator = $this->getScopeSeparator();
            $options['scope'] = implode($separator, $options['scope']);
        }

        // Store the state as it may need to be accessed later on.
        $this->state = $options['state'];

        $pkceMethod = $this->getPkceMethod();
        if (!empty($pkceMethod)) {
            $options['code_challenge_method'] = $pkceMethod;
        }

        // Business code layer might set a different redirect_uri parameter
        // depending on the context, leave it as-is
        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirectUri;
        }

        $options['client_id'] = $this->clientId;

        return $options;
    }

}
