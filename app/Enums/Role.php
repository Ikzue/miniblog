<?php
namespace App\Enums;

enum Role: string
{
    case MODERATOR = 'moderator';
    case WRITER = 'writer';
    case READER = 'reader';
}