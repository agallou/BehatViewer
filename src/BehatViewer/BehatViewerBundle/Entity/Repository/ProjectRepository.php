<?php
namespace BehatViewer\BehatViewerBundle\Entity\Repository;

use BehatViewer\BehatViewerBundle\Entity;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{
    public function findByUser(Entity\User $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUsernameAndSlug($username, $slug)
    {
        $user = $this->getEntityManager()->getRepository('BehatViewerBundle:User')->findOneByUsername($username);
        if (null === $user) {
            return null;
        }

        return $this->findOneByUserAndSlug($user, $slug);
    }

    public function findOneByUserAndSlug(Entity\User $user, $slug)
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :user')
            ->andWhere('p.slug = :slug')
            ->setParameter('user', $user)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
