<?php

namespace App\Security;

class CheckTenantManager
{
    public function __construct()
    {

    }

    public function isTenantValid(string $tenantId): bool
    {
        // TODO: Eigene Logik ob der Tenant berechtigt ist auf die App zuzugreifen hier einfügen
        return true;
    }
}