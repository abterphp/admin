<?php

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Framework\Form\IForm;
use Opulence\Orm\IEntity;

interface IFormFactory
{
    public const ERR_MSG_ENTITY_MISSING = 'Entity missing';

    /**
     * @param string       $action
     * @param string       $method
     * @param string       $showUrl
     * @param IEntity|null $entity
     *
     * @return IForm
     */
    public function create(string $action, string $method, string $showUrl, ?IEntity $entity = null): IForm;
}
