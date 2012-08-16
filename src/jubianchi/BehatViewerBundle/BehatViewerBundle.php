<?php

namespace jubianchi\BehatViewerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
	Doctrine\DBAL\Types\Type;

/**
 *
 */
class BehatViewerBundle extends Bundle
{
	public function boot()
	{
		parent::boot();

		$manager = $this->container->get('doctrine')->getManager();
		$platform = $manager->getConnection()->getDatabasePlatform();
		$platform->registerDoctrineTypeMapping('ENUM', 'string');

		if(false === Type::hasType('status')) {
			Type::addType('status', 'jubianchi\BehatViewerBundle\DBAL\Type\EnumStatusType');
		}

		if(false === Type::hasType('step_status')) {
			Type::addType('step_status', 'jubianchi\BehatViewerBundle\DBAL\Type\EnumStepStatusType');
		}

		if(false === Type::hasType('project_type')) {
			Type::addType('project_type', 'jubianchi\BehatViewerBundle\DBAL\Type\EnumProjectTypeType');
		}
	}
}
