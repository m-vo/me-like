<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Token;

class TokenFactory
{
    public function __invoke(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
