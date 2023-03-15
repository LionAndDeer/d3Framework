<?php

namespace App\Manager;

use App\Security\RoleAllocation;

class PermissionManager
{
    public function __construct(
    ) {
    }

    public function getTenantPermissionsArray(string $tenantId, $groups): array
    {
        $groupArray = [];
        $roles = [];
        foreach ($groups as $group) {
            $groupArray[$group[1]] = $group[0];
            //Tenant-Admin
            if ('DC4885EF-A72C-4489-95A1-F37269D6E48D' == $group[1]) {
                $roles[] = RoleAllocation::ADMIN;
            }
        }

        //TODO: Make your own premission logic
        $roles[] = RoleAllocation::USER;

        return array_unique($roles);
    }
}