<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

interface IConditional
{
    /**
     * @param int $additionalData
     */
    public function setAdditionalData(int $additionalData): void;
}
