<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use Opulence\Validation\IValidator;

class NewUser extends User
{
    /**
     * @param IValidator $validator
     */
    protected function addPasswordFields(IValidator $validator): void
    {
        $validator
            ->field('password')
            ->required();

        $validator
            ->field('password_confirmed')
            ->equalsField('password')
            ->required();
    }
}
