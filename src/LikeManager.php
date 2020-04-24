<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Mvo\MeLike\Endpoint\EndpointRegistry;
use Mvo\MeLike\Entity\Like;
use Mvo\MeLike\Exception\ResponseException;
use Mvo\MeLike\Notification\EmailNotification;
use Mvo\MeLike\Repository\LikeRepository;
use Mvo\MeLike\Token\EmailHashProvider;
use Mvo\MeLike\Token\TokenFactory;
use Mvo\MeLike\Url\ConfirmUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LikeManager
{
    private EndpointRegistry $endpointRegistry;
    private LikeRepository $likeRepository;
    private EmailHashProvider $emailHashGenerator;
    private TokenFactory $tokenFactory;
    private EmailNotification $emailNotification;
    private ConfirmUrlGenerator $confirmUrlGenerator;

    private UrlGeneratorInterface $urlGenerator;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(EndpointRegistry $endpointRegistry, LikeRepository $likeRepository, EmailHashProvider $emailHashGenerator, TokenFactory $tokenFactory, EmailNotification $emailNotification, ConfirmUrlGenerator $confirmUrlGenerator, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->endpointRegistry = $endpointRegistry;
        $this->likeRepository = $likeRepository;
        $this->emailHashGenerator = $emailHashGenerator;
        $this->tokenFactory = $tokenFactory;
        $this->emailNotification = $emailNotification;
        $this->confirmUrlGenerator = $confirmUrlGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws ResponseException
     */
    public function getLikeData(string $endpoint, $userToken): array
    {
        $this->validateEndpoint($endpoint);

        $status = null;

        if (null !== $userToken) {
            $this->validateToken($userToken);

            if (null !== ($like = $this->likeRepository->findByEndpointAndUserToken($endpoint, $userToken))) {
                $status = $like->isConfirmed();
            }
        }

        $likes = $this->likeRepository->countConfirmedByEndpoint($endpoint);
        $pathAdd = $this->urlGenerator->generate('mvo_like_add', ['endpoint' => $endpoint]);
        $newToken = (null === $userToken) ? ($this->tokenFactory)() : null;

        return [
            'status' => $status,
            'likes' => $likes,
            'pathAdd' => $pathAdd,
            'newToken' => $newToken,
        ];
    }

    /**
     * @throws ResponseException
     */
    public function addLike(string $endpoint, $userToken, $email): void
    {
        $this->validateEndpoint($endpoint);
        $this->validateToken($userToken);
        $this->validateEmail($email);

        // find existing or create new target
        $emailHash = $this->emailHashGenerator->generateHash($email, $endpoint);
        $like = $this->likeRepository->findByEndpointAndEmailHash($endpoint, $emailHash);

        // note: if timing attacks (time to response) become a problem, consider adding
        //       a random sleep time here to prevent guessing found vs. not found entries

        if (null === $like) {
            $confirmToken = ($this->tokenFactory)();

            $like = new Like($endpoint, $emailHash, $userToken, $confirmToken);
            $this->entityManager->persist($like);

            try {
                $this->entityManager->flush();
            } catch (UniqueConstraintViolationException $e) {
                throw new ResponseException(new Response('The given token for this endpoint was already consumed with another email address.', Response::HTTP_BAD_REQUEST));
            }
        }

        // send notification
        $confirmUrl = $this->confirmUrlGenerator->generateUrl($like);
        $this->emailNotification->send($email, $like, $confirmUrl);
    }

    /**
     * @throws ResponseException
     */
    public function confirmLike($confirmToken): void
    {
        $this->validateToken($confirmToken);

        // activate
        $like = $this->likeRepository->findByConfirmToken($confirmToken);

        if (null === $like) {
            throw new ResponseException(new Response('Invalid or expired token.', Response::HTTP_BAD_REQUEST));
        }

        $like->confirm();
        $this->entityManager->flush();
    }

    /**
     * @throws ResponseException
     */
    private function validateEndpoint(string $endpoint): void
    {
        if (!$this->endpointRegistry->isValidEndpoint($endpoint)) {
            throw new ResponseException(new Response('Unknown endpoint.', Response::HTTP_NOT_FOUND));
        }
    }

    /**
     * @throws ResponseException
     */
    private function validateToken(?string $token): void
    {
        if (null === $token) {
            throw new ResponseException(new Response('Missing token.', Response::HTTP_BAD_REQUEST));
        }

        $tokenConstraints = [
            new NotBlank(),
            new Length(['min' => 64, 'max' => 64]),
            new Type(['type' => 'alnum']),
        ];

        if (0 !== \count($this->validator->validate($token, $tokenConstraints))) {
            throw new ResponseException(new Response('Malformed token.', Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * @throws ResponseException
     */
    private function validateEmail(?string $email): void
    {
        if (null === $email) {
            throw new ResponseException(new Response('Missing email.', Response::HTTP_BAD_REQUEST));
        }

        $emailConstraints = [
            new NotBlank(),
            new Length(['max' => 254]),
            new Email(['mode' => Email::VALIDATION_MODE_LOOSE]),
        ];

        if (0 !== \count($this->validator->validate($email, $emailConstraints))) {
            throw new ResponseException(new Response('Malformed email.', Response::HTTP_BAD_REQUEST));
        }
    }
}
