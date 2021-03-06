<?php

namespace BehatViewer\BehatViewerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
	BehatViewer\BehatViewerBundle\Entity;

class DefinitionsController extends BehatViewerProjectController
{
    /**
     * @return array
     *
     * @Route("/{username}/{project}/definitions", name="behatviewer.definitions")
     * @Template()
     */
    public function indexAction(Entity\User $user, Entity\Project $project)
    {
        $definitions = array();
        $contexts = array();
        $repository = $this->getDoctrine()->getRepository('BehatViewerBundle:Definition');

        if ($project !== null) {
            $definitions = $repository->findByProject($project->getId());
            $contexts = $repository->getContexts($project);
        }

        return $this->getResponse(array(
            'items' => $definitions,
            'contexts' => $contexts
        ));
    }
}
