<?php

namespace BehatViewer\BehatViewerBundle\Entity\Repository;

use BehatViewer\BehatViewerBundle\Entity;

/**
 * DefinitionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DefinitionRepository extends EntityRepository
{
    public function truncateForProject(Entity\Project $project)
    {
        return $this->createQueryBuilder('d')
            ->delete()
            ->where('d.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->execute();
    }

    public function getContexts(Entity\Project $project)
    {
        return $this->createQueryBuilder('d')
            ->select('d.context')
            ->distinct(true)
            ->where('d.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();
    }
}
