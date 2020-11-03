<?php

namespace App\Repository;

use App\Entity\Pasta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PastaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pasta::class);
    }

    public function findAllContaining($text){
        $em=$this->getEntityManager();
        $query=$em->createQuery("SELECT p FROM App\Entity\Pasta p WHERE p.stripped LIKE :match")->setParameter("match","%".$text."%");
        return $query->getResult();
    }

    public function findOrdered($activeUser="anon",$order="best",$page=0,$tag="",$author="",$search=""){
        $itemsPerPage=5;
        $qb=$this->createQueryBuilder("p")->join("p.author","au");
        //shadowbans
        $qb->andWhere("au.isShadowbanned = 0")->orWhere("au.username = :activeuser")->setParameter("activeuser",$activeUser);
        //order by
        if($order=="best") {
            $qb->leftJoin("p.likes","l")->leftJoin("p.author","a")->groupBy('p.id');
            $qb->orderBy("COUNT(l.id)","DESC");
        }
        $qb->addOrderBy("p.id","DESC");
        //tags
        if(!empty($tag) && $tag!="all"){
            $qb->join("p.tags","t")->andwhere("t.name LIKE :tagname")->setParameter("tagname",$tag);
        }
        //authors
        if(!empty($author) && $author!="all"){
            $qb->andwhere("au.username LIKE :authorname")->setParameter("authorname",$author);
        }
        //search
        if(!empty($search)){
            $qb->andwhere('p.content LIKE :searchquery')->setParameter("searchquery","%".$search."%");
        }
        $qb->andWhere("p.deleted = 0");
        //pagination
        $qb->setFirstResult($itemsPerPage*$page)->setMaxResults($itemsPerPage);
        $query=$qb->getQuery();
        return $query->execute();
    }
}