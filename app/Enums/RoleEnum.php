<?php

namespace App\Enums;

enum RoleEnum : string
{
    case ADMIN ='admin';
    case TEACHER ='teacher';
    case STUDENT ='student';
}
