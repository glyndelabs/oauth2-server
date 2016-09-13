<?php

namespace League\OAuth2\Server\Entities;

interface ValidatedAccessTokenEntityInterface
{
    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get the token's expiry date time.
     *
     * @return \DateTime
     */
    public function getExpiryDateTime();

    /**
     * Get the token user's identifier.
     *
     * @return string|int
     */
    public function getUserIdentifier();

    /**
     * Get the client that the token was issued to.
     *
     * @return string
     */
    public function getClientIdentifier();

    /**
     * Return an array of scopes associated with the token.
     *
     * @return array - string[]
     */
    public function getScopes();

}
