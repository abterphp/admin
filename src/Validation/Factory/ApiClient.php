<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\IValidator;

class ApiClient extends ValidatorFactory
{
    /**
     * @return IValidator
     */
    public function createValidator(): IValidator
    {
        $validator = parent::createValidator();

        $validator
            ->field('id')
            ->forbidden();

        $validator
            ->field('user_id')
            ->uuid()
            ->required();

        $validator
            ->field('description')
            ->required();

        $validator
            ->field('admin_resource_ids');

        return $validator;
    }
}
