<?php

namespace App\Security;

use Liondeer\Framework\Allocation\AbstractAllocation;

class RoleAllocation extends AbstractAllocation
{
    const ADMIN = 'ROLE_ADMIN';
    const USER = 'ROLE_USER';

    public static function getValues(): array
    {
        return self::getAllocationValues();
    }

    public static function getTranslationKey($constName): string
    {
        return self::getAllocationTranslationKey($constName);
    }
}