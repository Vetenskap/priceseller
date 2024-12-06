<?php

namespace App\Enums;

enum ReportStatus
{
    case cancelled;
    case failed;
    case finished;
    case running;
    case pending;
}
