<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Url;

use Mvo\MeLike\Entity\Like;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ConfirmUrlGenerator
{
    private ?Request $request;

    private string $userTokenName;
    private string $confirmTokenName;

    public function __construct(RequestStack $requestStack, string $userTokenName, string $confirmTokenName)
    {
        $this->request = $requestStack->getCurrentRequest();

        $this->userTokenName = $userTokenName;
        $this->confirmTokenName = $confirmTokenName;
    }

    public function generateUrl(Like $like, string $basePath = null): string
    {
        if (null === $basePath) {
            if (null === $this->request || !$this->request->headers->has('referer')) {
                throw new \RuntimeException('A base path must be specified if no referer is present.');
            }

            $basePath = strtok($this->request->headers->get('referer') ?: '', '?');
        }

        $parameters = [
            $this->userTokenName => $like->getUserToken(),
        ];

        if (!$like->isConfirmed()) {
            $parameters[$this->confirmTokenName] = $like->getConfirmToken();
        }

        // we'll append the tokens as fragments ('#t1=..&t2=..') instead of using a query
        // string ('?t1=..&t2=..') as the data never needs to be sent to the server
        return $basePath.'#'.http_build_query($parameters);
    }
}
