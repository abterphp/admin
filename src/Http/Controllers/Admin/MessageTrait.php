<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\I18n\ITranslator; // @phan-suppress-current-line PhanUnreferencedUseNormal

trait MessageTrait
{
    /**
     * @param string $messageType
     *
     * @return string
     */
    protected function getMessage(string $messageType)
    {
        /** @var ITranslator $translator */
        $translator = $this->translator; // @phan-suppress-current-line PhanUndeclaredProperty

        // @phan-suppress-next-line PhanUndeclaredConstant
        $entityName = $translator->translate(static::ENTITY_TITLE_SINGULAR);

        return $translator->translate($messageType, $entityName);
    }
}
