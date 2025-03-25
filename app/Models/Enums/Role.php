<?php
namespace App\Models\Enums;

enum Role: string
{
    case MODERATOR = 'moderator';
    case WRITER = 'writer';
    case READER = 'reader';
}