<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Exception;

use Symfony\Component\HttpFoundation\Response;

class ResponseException extends \Exception
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;

        parent::__construct('This exception has no message.');
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
