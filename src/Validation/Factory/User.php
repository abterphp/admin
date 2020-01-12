<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use AbterPhp\Framework\Http\Service\Execute\IRepoService;
use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\IValidator;

class User extends ValidatorFactory implements IConditional
{
    use ConditionalTrait;

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

        $validator
            ->field('password');

        $validator
            ->field('password_confirmed')
            ->equalsField('password');

        $this->makePasswordRequired($validator);

        return $validator;
    }

    /**
     * @param IValidator $validator
     */
    protected function makePasswordRequired(IValidator $validator): void
    {
        if ($this->additionalData !== IRepoService::CREATE) {
            return;
        }

        $validator
            ->field('password')
            ->required();

        $validator
            ->field('password_confirmed')
            ->required();
    }
}
