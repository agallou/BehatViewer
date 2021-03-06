<?php
namespace BehatViewer\BehatViewerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    BehatViewer\BehatViewerBundle\Entity,
    BehatViewer\BehatViewerBundle\Form\Type\ProjectType,
    JMS\SecurityExtraBundle\Annotation\Secure;

class ProjectController extends BehatViewerProjectController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/project/create", name="behatviewer.project.create")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->getRequest();
		$user = $this->getUser();

        $project = new Entity\Project();
        $project->setUser($user);

        $form = $this->get('form.factory')->create(new ProjectType(), $project);

        if ('POST' === $request->getMethod()) {
            $success = $this->save($form);

            if ($success) {
                return $this->redirect(
                    $this->generateUrl(
                        'behatviewer.project.edit',
                        array(
                            'username' => $user->getUsername(),
                            'project' => $form->getData()->getSlug(),
                            'success' => $success
                        )
                    )
                );
            }
        }

        return $this->getResponse(array(
            'form' => $form->createView(),
            'success' => false,
            'hasproject' => (null !== $this->getDoctrine()->getRepository('BehatViewerBundle:Project')->findOneByUser($this->getUser())),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{username}/{project}", name="behatviewer.project")
     * @Route("/{username}/{project}/{type}", requirements={"type" = "list|thumb"}, name="behatviewer.project.switch")
     * @Template()
     */
    public function indexAction(Entity\User $user, Entity\Project $project, $type = null)
    {
        return $this->forward(
            'BehatViewerBundle:History:entry',
            array(
                'username' => $project->getUser()->getUsername(),
                'project' => $project->getSlug(),
                'build' => $project->getLastBuild(),
                'type' => $type
            )
        );
    }

    /**
     * @return array
     *
     * @Route("/{username}/{project}/edit", name="behatviewer.project.edit")
     * @Secure(roles="ROLE_USER")
     * @Template("BehatViewerBundle:Project:edit.html.twig")
     */
    public function editAction(Entity\User $user, Entity\Project $project)
    {
        $request = $this->getRequest();
        $success = false;

        $form = $this->get('form.factory')->create(new ProjectType(), $project);

        if ('POST' === $request->getMethod()) {
            $success = $this->save($form);
        }

        return $this->getResponse(array(
            'form' => $form->createView(),
            'success' => $success || $this->getRequest()->get('success', false),
            'hasproject' => true,
        ));
    }

    /**
     * @param \BehatViewer\BehatViewerBundle\Entity\Build|null $build
     *
     * @return \Symfony\Component\HttpFoundation\Response|array
     *
     * @Route("/{username}/{project}/delete", name="behatviewer.project.delete")
     * @Secure(roles="ROLE_USER")
     */
    public function deleteAction(Entity\User $user, Entity\Project $project)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($project);
        $manager->flush();

        return $this->redirect($this->generateUrl('behatviewer.homepage'));
    }

    protected function save(\Symfony\Component\Form\Form $form)
    {
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($form->getData());
            $manager->flush();
        }

        return $form->isValid();
    }
}
