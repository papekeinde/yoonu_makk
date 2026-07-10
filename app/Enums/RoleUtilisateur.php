<?php

namespace App\Enums;

enum RoleUtilisateur: string
{
    case Patient = 'patient';
    case Admin   = 'admin';
}
