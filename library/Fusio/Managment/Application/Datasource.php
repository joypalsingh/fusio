<?php

namespace Fusio\Managment\Application;

use PSX\Controller\HandlerApiAbstract;

class Datasource extends HandlerApiAbstract
{
	protected function getDefaultHandler()
	{
		return $this->getDoctrineManager()
			->getHandler(function($manager){
				return $manager->createQueryBuilder()
					->from('Fusio\Entity\Datasource', 'datasource');
			});
	}
}
