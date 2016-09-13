<?php

namespace League\OAuth2\Server\Repositories;

interface AccessTokenValidatorRepositoryInterface extends RepositoryInterface
{
    /**
     * Check if the access token has been revoked or does not exist
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked or does not exist
     */
    public function isAccessTokenRevoked($tokenId);
}