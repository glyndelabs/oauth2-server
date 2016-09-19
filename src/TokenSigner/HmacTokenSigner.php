<?php

namespace League\OAuth2\Server\TokenSigner;

use Lcobucci\JWT\Signer;

class HmacTokenSigner implements TokenSignerInterface
{
    /**
     * @var \Lcobucci\JWT\Signer
     */
    private $signer;

    /**
     * @var string
     */
    private $key;

    /**
     * TokenSignerInterface constructor.
     *
     * @param string               $key
     */
    public function __construct($key)
    {
        $this->signer = new Signer\Hmac\Sha256();
        $this->key = $key;
    }

    /**
     * @return \Lcobucci\JWT\Signer
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
