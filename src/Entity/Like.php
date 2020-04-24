<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mvo\MeLike\Repository\LikeRepository")
 * @ORM\Table(
 *     name="me_like",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"endpoint","email"}),
 *          @ORM\UniqueConstraint(columns={"endpoint","user_token"}),
 *     },
 *     indexes={
 *          @ORM\Index(columns={"endpoint"})
 *     }
 * )
 */
class Like
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="endpoint", type="string", length=64)
     */
    private string $endpoint;

    /**
     * @ORM\Column(name="email", type="string", length=64)
     */
    private string $emailHash;

    /**
     * @ORM\Column(name="user_token", type="string", length=64)
     */
    private string $userToken;

    /**
     * @ORM\Column(name="confirm_token", type="string", length=64, nullable=true, unique=true)
     */
    private ?string $confirmToken;

    /**
     * @ORM\Column(name="requested_at", type="datetime")
     */
    private \DateTimeInterface $requestedAt;

    /**
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $confirmedAt;

    public function __construct(string $endpoint, string $emailHash, string $userToken, string $confirmToken)
    {
        $this->endpoint = $endpoint;
        $this->emailHash = $emailHash;
        $this->userToken = $userToken;
        $this->confirmToken = $confirmToken;

        $this->requestedAt = new \DateTime();
    }

    public function confirm(): void
    {
        $this->confirmToken = null;
        $this->confirmedAt = new \DateTime();
    }

    public function isConfirmed(): bool
    {
        return null === $this->confirmToken;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getEmailHash(): string
    {
        return $this->emailHash;
    }

    public function getUserToken(): string
    {
        return $this->userToken;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function getRequestedAt(): \DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }
}
