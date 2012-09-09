<?php
namespace BehatViewer\BehatViewerWorkerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration,
    BehatViewer\BehatViewerBundle\Entity,
    BehatViewer\BehatViewerBundle\DBAL\Type\EnumProjectTypeType,
	BehatViewer\BehatViewerBundle\Controller\BehatViewerController;

/**
 * @Configuration\Route("/worker")
 */
class DefaultController extends BehatViewerController
{
    /**
     * @return array
     *
     * @Configuration\Route("/", name="behatviewer.workers")
     * @Configuration\Template()
     */
    public function indexAction()
    {
		$rabbitHost = $this->container->getParameter('rabbitmq_host');
		$rabbitPort = $this->container->getParameter('rabbitmq_api_port');
		$rabbitUser = $this->container->getParameter('rabbitmq_user');
		$rabbitPassword = $this->container->getParameter('rabbitmq_password');

		$curl = curl_init('http://' . $rabbitHost . ':' . $rabbitPort . '/api/connections');
		curl_setopt($curl, CURLOPT_USERPWD, $rabbitUser . ':' . $rabbitPassword);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		curl_close($curl);

		return $this->getResponse(array(
			'items' => json_decode($result)
		));
	}
}