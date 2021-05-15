<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Events;

use Opulence\Views\IView;

class AdminReady
{
    private IView $view;

    /**
     * AdminReady constructor.
     *
     * @param IView $view
     */
    public function __construct(IView $view)
    {
        $this->view = $view;
    }

    /**
     * @return IView
     */
    public function getView(): IView
    {
        return $this->view;
    }
}
