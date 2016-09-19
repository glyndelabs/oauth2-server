<?php
/**
 * OAuth 2.0 Bearer Token Response.
 *
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\ResponseTypes;

use Lcobucci\JWT\Builder;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\TokenSigner\TokenSignerInterface;
use Psr\Http\Message\ResponseInterface;

class BearerTokenResponse extends AbstractResponseType
{
    /**
     * @var \League\OAuth2\Server\TokenSigner\TokenSignerInterface
     */
    private $tokenSigner;

    /**
     * @var callable
     */
    private $extraJsonResponseParams;

    /**
     * BearerTokenResponse constructor.
     *
     * @param \League\OAuth2\Server\TokenSigner\TokenSignerInterface $tokenSigner
     * @param callable                                               $extraJsonResponseParams
     */
    public function __construct(TokenSignerInterface $tokenSigner, callable $extraJsonResponseParams = null)
    {
        $this->tokenSigner = $tokenSigner;
        $this->extraJsonResponseParams = ($extraJsonResponseParams === null)
            ? function () { return []; }
            : $extraJsonResponseParams;
    }

    /**
     * {@inheritdoc}
     */
    public function generateHttpResponse(ResponseInterface $response)
    {
        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();

        $jwtAccessToken = (new Builder())
            ->setAudience($this->accessToken->getClient()->getIdentifier())
            ->setId($this->accessToken->getIdentifier(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->accessToken->getExpiryDateTime()->getTimestamp())
            ->setSubject($this->accessToken->getUserIdentifier())
            ->set('scopes', $this->accessToken->getScopes())
            ->sign($this->tokenSigner->getSigner(), $this->tokenSigner->getKey())
            ->getToken();

        $responseParams = [
            'token_type'   => 'Bearer',
            'expires_in'   => $expireDateTime - (new \DateTime())->getTimestamp(),
            'access_token' => (string) $jwtAccessToken,
        ];

        if ($this->refreshToken instanceof RefreshTokenEntityInterface) {

            $responseParams['refresh_token'] = (new Builder())
                ->setAudience($this->accessToken->getClient()->getIdentifier())
                ->setId($this->refreshToken->getIdentifier(), true)
                ->setIssuedAt(time())
                ->setNotBefore(time())
                ->setExpiration($this->refreshToken->getExpiryDateTime()->getTimestamp())
                ->setSubject($this->accessToken->getUserIdentifier())
                ->set('scopes', $this->accessToken->getScopes())
                ->sign($this->tokenSigner->getSigner(), $this->tokenSigner->getKey())
                ->getToken()
                ->__toString();

        }

        $responseParams = array_merge($this->getExtraParams($this->accessToken), $responseParams);

        $response = $response
            ->withStatus(200)
            ->withHeader('pragma', 'no-cache')
            ->withHeader('cache-control', 'no-store')
            ->withHeader('content-type', 'application/json; charset=UTF-8');

        $response->getBody()->write(json_encode($responseParams));

        return $response;
    }
}
