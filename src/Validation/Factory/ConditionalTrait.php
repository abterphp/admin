<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use AbterPhp\Framework\Http\Service\Execute\IRepoService;

trait ConditionalTrait
{
    /** @var int */
    protected $additionalData = IRepoService::MIXED;

    /**
     * @param int $additionalData
     */
    public function setAdditionalData(int $additionalData): void
    {
        $this->additionalData = $additionalData;
    }
}
