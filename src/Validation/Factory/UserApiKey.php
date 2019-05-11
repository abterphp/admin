<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\IValidator;

class UserApiKey extends ValidatorFactory
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
            ->field('description')
            ->required();

        $validator
            ->field('admin_resource_ids')
            ->required();

        return $validator;
    }
}
