<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table;

use AbterPhp\Admin\Grid\Factory\TableFactory;
use AbterPhp\Admin\Grid\Factory\Table\Header\UserGroup as HeaderFactory;

class UserGroup extends TableFactory
{
    /**
     * UserGroup constructor.
     *
     * @param HeaderFactory $headerFactory
     * @param BodyFactory   $bodyFactory
     */
    public function __construct(HeaderFactory $headerFactory, BodyFactory $bodyFactory)
    {
        parent::__construct($headerFactory, $bodyFactory);
    }
}
