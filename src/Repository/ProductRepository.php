<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param array{
     *   q?: string|null,
     *   type?: string|null,
     *   min_price?: float|null,
     *   max_price?: float|null,
     *   sort?: string|null
     * } $filters
     *
     * @return Product[]
     */
    public function findForStore(array $filters): array
    {
        $qb = $this->createQueryBuilder('p');

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $tokens = preg_split('/[\s\-]+/', $q) ?: [];
            $tokens = array_values(array_filter($tokens, static fn ($token) => $token !== ''));

            foreach ($tokens as $index => $token) {
                $param = 'q' . $index;
                $qb
                    ->andWhere("(p.name LIKE :$param OR p.description LIKE :$param)")
                    ->setParameter($param, '%' . $token . '%');
            }
        }

        $type = trim((string) ($filters['type'] ?? ''));
        if ($type !== '') {
            $qb
                ->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }

        $minPrice = $filters['min_price'] ?? null;
        if (is_numeric($minPrice)) {
            $qb
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', (float) $minPrice);
        }

        $maxPrice = $filters['max_price'] ?? null;
        if (is_numeric($maxPrice)) {
            $qb
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', (float) $maxPrice);
        }

        $sort = (string) ($filters['sort'] ?? 'recent');
        $sortingMap = [
            'recent' => ['p.id', 'DESC'],
            'oldest' => ['p.id', 'ASC'],
            'price_asc' => ['p.price', 'ASC'],
            'price_desc' => ['p.price', 'DESC'],
            'name_asc' => ['p.name', 'ASC'],
            'name_desc' => ['p.name', 'DESC'],
        ];

        [$field, $direction] = $sortingMap[$sort] ?? $sortingMap['recent'];
        $qb->orderBy($field, $direction);

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
