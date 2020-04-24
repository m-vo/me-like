<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Controller;

use Mvo\MeLike\Exception\ResponseException;
use Mvo\MeLike\LikeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/_like")
 */
class LikeApiController extends AbstractController
{
    private LikeManager $manager;

    public function __construct(LikeManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Return the number of likes, status and meta information for this endpoint as json.
     *
     * Status:
     *  - no information          => null
     *  - liked but not confirmed => false
     *  - liked and confirmed     => true
     *
     * If no (valid) user token is specified, the status will always be null (no information).
     *
     * @Route("/l/{endpoint}", name="mvo_like_data", methods="GET")
     */
    public function data(string $endpoint, Request $request): Response
    {
        try {
            $data = $this->manager->getLikeData(
                $endpoint,
                $request->query->get('token', null)
            );

            return $this->json($data);
        } catch (ResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Like this endpoint.
     *
     * @Route("/l/{endpoint}", name="mvo_like_add", methods="POST")
     */
    public function add(string $endpoint, Request $request): Response
    {
        try {
            $this->manager->addLike(
                $endpoint,
                $request->get('token', null),
                $request->get('email', null)
            );

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (ResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Confirm a like.
     *
     * @Route("/confirm", name="mvo_like_confirm", methods="POST")
     */
    public function confirm(Request $request): Response
    {
        try {
            $this->manager->confirmLike(
                $request->get('token', null),
            );

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (ResponseException $e) {
            return $e->getResponse();
        }
    }
}
