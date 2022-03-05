<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends Post
 */
final class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getPaginatedPosts(int $page): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('t')
            ->addSelect('u')
            ->join('p.category', 'c')
            ->join('p.tags', 't')
            ->join('p.user', 'u')
            ->orderBy('p.publishedAt', 'desc')
            ->setMaxResults(18)
            ->setFirstResult(($page - 1) * 18)
            ->groupBy('p.id');

        return new Paginator($queryBuilder);
    }
}
