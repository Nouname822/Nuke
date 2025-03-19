<?php

namespace Auth\Enums;

enum RegisterEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
}
