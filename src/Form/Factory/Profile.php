<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Extra\DefaultButtons;

class Profile extends User
{
    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addCanLogin(Entity $entity): User
    {
        $this->form[] = new Input(
            'can_login',
            'can_login',
            '1',
            [],
            [Html5::ATTR_TYPE => Input::TYPE_HIDDEN]
        );

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $showUrl
     *
     * @return Base
     */
    protected function addDefaultButtons(string $showUrl): Base
    {
        $buttons = new DefaultButtons();

        $buttons->addSave();

        $this->form[] = $buttons;

        return $this;
    }
}
