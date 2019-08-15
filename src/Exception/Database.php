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
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $errorInfo = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = isset($errorInfo[2]) ? $errorInfo[2] : print_r($errorInfo, true);

        parent::__construct($message, $code, $previous);
    }
}
