<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Endpoint;

use Mvo\MeLike\Entity\Like;

interface EndpointInterface
{
    public const VALID = true;
    public const INVALID = false;
    public const UNKNOWN = null;

    /**
     * Return VALID / INVALID if this handler is applicable, UNKNOWN else.
     */
    public function handle(string $domain, ?int $id): ?bool;

    /**
     * Return an array of key-value pairs that will be added as context. This
     * method will only be called if this handler declares the endpoint as VALID.
     */
    public function addContext(string $domain, ?int $id, Like $like): ?array;
}
