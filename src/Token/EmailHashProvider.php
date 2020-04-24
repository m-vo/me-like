<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Token;

class EmailHashProvider
{
    private string $appSecret;

    public function __construct(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function generateHash(string $email, string $scopeIdentifier): string
    {
        // sign with app secret and an additional identifier to make sure the
        // resulting hashes cannot be associated across scopes or installations
        $signature = $this->appSecret.$scopeIdentifier;

        return hash_hmac('sha256', $email, $signature);
    }
}
