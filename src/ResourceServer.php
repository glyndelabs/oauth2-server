<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server;

use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenValidatorRepositoryInterface;
use League\OAuth2\Server\TokenSigner\TokenSignerInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResourceServer
{
    /**
     * @var AccessTokenValidatorRepositoryInterface
     */
    private $accessTokenRepository;

    /**
     * @var CryptKey
     */
    private $publicKey;

    /**
     * @var null|AuthorizationValidatorInterface
     */
    private $authorizationValidator;

    /**
     * New server instance.
     *
     * @param AccessTokenValidatorRepositoryInterface                $accessTokenRepository
     * @param \League\OAuth2\Server\TokenSigner\TokenSignerInterface $tokenSigner
     * @param null|AuthorizationValidatorInterface                   $authorizationValidator
     *
     * @internal param \League\OAuth2\Server\CryptKey|string $publicKey
     */
    public function __construct(
        AccessTokenValidatorRepositoryInterface $accessTokenRepository,
        TokenSignerInterface $tokenSigner,
        AuthorizationValidatorInterface $authorizationValidator = null
    ) {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->authorizationValidator = $authorizationValidator;
    }

    /**
     * @return AuthorizationValidatorInterface
     */
    protected function getAuthorizationValidator()
    {
        if ($this->authorizationValidator instanceof AuthorizationValidatorInterface === false) {
            $this->authorizationValidator = new BearerTokenValidator($this->accessTokenRepository);
        }

        $this->authorizationValidator->setPublicKey($this->publicKey);

        return $this->authorizationValidator;
    }

    /**
     * Determine the access token validity.
     *
     * @param ServerRequestInterface $request
     *
     * @throws OAuthServerException
     *
     * @return ServerRequestInterface
     */
    public function validateAuthenticatedRequest(ServerRequestInterface $request)
    {
        return $this->getAuthorizationValidator()->validateAuthorization($request);
    }
}
