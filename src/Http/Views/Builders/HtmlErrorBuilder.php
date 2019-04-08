<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Views\Builders;

use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

/**
 * Defines the HTML error page view builder
 */
class HtmlErrorBuilder implements IViewBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IView $view): IView
    {
        $view->setVar('title', 'Error');

        return $view;
    }
}
