<?php

namespace League\OAuth2\Server\TokenSigner;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;

class RsaKeyTokenSigner implements TokenSignerInterface
{
    /**
     * @var \Lcobucci\JWT\Signer
     */
    private $signer;

    /**
     * @var \League\OAuth2\Server\CryptKey
     */
    private $key;

    /**
     * TokenSignerInterface constructor.
     *
     * @param CryptKey             $privateKey
     */
    public function __construct(CryptKey $privateKey)
    {
        $this->signer = new Sha256();
        $this->key = new Signer\Key($privateKey->getKeyPath(), $privateKey->getPassPhrase());
    }

    /**
     * @return \Lcobucci\JWT\Signer
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * @return CryptKey
     */
    public function getKey()
    {
        return $this->key;
    }
}
