<?php
namespace BehatViewer\BehatViewerBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ConfigurationListener extends \Symfony\Component\DependencyInjection\ContainerAware
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        switch (true) {
            case $exception instanceof \BehatViewer\BehatViewerBundle\Exception\NoProjectConfiguredException:
                $event->setResponse(new \Symfony\Component\HttpFoundation\RedirectResponse($this->container->get('router')->generate('behatviewer.project.create')));
                break;
            default:
                break;
        }
    }
}
