<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers;

use Opulence\Http\Requests\UploadedFile;

trait ApiDataTrait
{
    protected string $problemBaseUrl;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array $data
     *
     * @return UploadedFile[]
     */
    protected function getFileData(array $data): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCreateData(): array
    {
        return $this->getSharedData();
    }

    /**
     * @return array
     */
    public function getUpdateData(): array
    {
        return $this->getSharedData();
    }

    /**
     * @return array
     */
    public function getSharedData(): array
    {
        // @phan-suppress-next-line PhanUndeclaredProperty
        return $this->request->getJsonBody();
    }
}
