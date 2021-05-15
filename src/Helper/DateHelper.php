<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Helper;

use AbterPhp\Admin\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Helper\DateHelper as FrameworkDateHelper;

class DateHelper extends FrameworkDateHelper
{
    /**
     * @param \DateTime|null $dateTime
     *
     * @return string
     */
    public static function format(?\DateTime $dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        return $dateTime->format(Environment::getVar(Env::ADMIN_DATE_FORMAT));
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return string
     */
    public static function formatDateTime(?\DateTime $dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        return $dateTime->format(Environment::getVar(Env::ADMIN_DATETIME_FORMAT));
    }
}
