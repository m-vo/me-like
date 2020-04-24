<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mvo\MeLike\Entity\Like;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function countConfirmedByEndpoint(string $endpoint): int
    {
        return $this->count([
            'endpoint' => $endpoint,
            'confirmToken' => null,
        ]);
    }

    public function findByEndpointAndEmailHash(string $endpoint, string $emailHash): ?Like
    {
        return $this->findOneBy([
            'endpoint' => $endpoint,
            'emailHash' => $emailHash,
        ]);
    }

    public function findByEndpointAndUserToken(string $endpoint, string $userToken): ?Like
    {
        return $this->findOneBy([
            'endpoint' => $endpoint,
            'userToken' => $userToken,
        ]);
    }

    public function findByConfirmToken(string $activationToken): ?Like
    {
        return $this->findOneBy([
            'confirmToken' => $activationToken,
        ]);
    }
}
