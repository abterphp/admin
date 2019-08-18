<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Exception;

use RuntimeException;
use Throwable;

class Database extends RuntimeException
{
    /**
     * Database constructor.
     *
     * @param array          $errorInfo
     * @param Throwable|null $previous
     */
    public function __construct(
        array $errorInfo = [],
        Throwable $previous = null
    ) {
        $message = isset($errorInfo[2]) ? $errorInfo[2] : print_r($errorInfo, true);
        $code    = isset($errorInfo[1]) ? $errorInfo[1] : 0;

        parent::__construct($message, $code, $previous);
    }
}
