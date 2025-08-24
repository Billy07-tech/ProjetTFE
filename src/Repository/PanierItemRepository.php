<?php
namespace App\Repository;

use App\Entity\PanierItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PanierItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PanierItem::class);
    }

    /**
     * Récupère tous les items d'un panier
     */
    public function findByPanier($panier)
    {
        return $this->createQueryBuilder('pi')
            ->andWhere('pi.panier = :panier')
            ->setParameter('panier', $panier)
            ->getQuery()
            ->getResult();
    }
}
