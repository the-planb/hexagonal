<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

enum PipeStatus: string
{
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
}
