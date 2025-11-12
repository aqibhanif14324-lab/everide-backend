<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case SELLER = 'seller';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'User',
            self::SELLER => 'Seller',
            self::ADMIN => 'Admin',
        };
    }
}

