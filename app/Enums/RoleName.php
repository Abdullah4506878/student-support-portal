<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'super_admin';
    case AdminOfficer = 'admin_officer';
    case Student = 'student';
}
