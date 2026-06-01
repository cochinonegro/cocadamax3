<?php

namespace App\Enums;

enum ProgramaSolicitudStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
