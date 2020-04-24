<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LikeWidgetController extends AbstractController
{
    private string $userTokenName;
    private string $confirmTokenName;

    public function __construct(string $userTokenName, string $confirmTokenName)
    {
        $this->userTokenName = $userTokenName;
        $this->confirmTokenName = $confirmTokenName;
    }

    /**
     * Render this controller in your twig template.
     *
     * Example:
     *  {{ render(controller('mvo.me_like.widget', { 'endpoint': 'my-endpoint' })) }}
     */
    public function __invoke(string $endpoint): Response
    {
        return $this->render('@MvoMeLike/Like/widget.html.twig', [
            'endpoint' => $endpoint,
            'token_key' => [
                'user' => $this->userTokenName,
                'confirm' => $this->confirmTokenName,
            ],
        ]);
    }
}
