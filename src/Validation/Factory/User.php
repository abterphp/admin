<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\IValidator;

abstract class User extends ValidatorFactory
{
    /**
     * @return IValidator
     */
    public function createValidator(): IValidator
    {
        $validator = parent::createValidator();

        $validator
            ->field('id')
            ->uuid();

        $validator
            ->field('username')
            ->required();

        $validator
            ->field('email')
            ->email()
            ->required();

        $validator
            ->field('user_group_ids');

        $validator
            ->field('user_language_id')
            ->uuid()
            ->required();

        $this->addPasswordFields($validator);

        return $validator;
    }

    /**
     * @param IValidator $validator
     */
    abstract protected function addPasswordFields(IValidator $validator): void;
}
