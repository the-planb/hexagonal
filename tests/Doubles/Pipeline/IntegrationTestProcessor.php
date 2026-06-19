<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Pipeline;

use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\PipeInterface;
use PlanB\Hexagonal\Core\Pipeline\PipeStatus;
use PlanB\Hexagonal\Core\Pipeline\ProcessorInterface;

final class IntegrationTestProcessor implements ProcessorInterface
{
    public function process(Context $context, callable|PipeInterface ...$pipes): Context
    {
        foreach ($pipes as $pipe) {
            if ($context->status === PipeStatus::FAILED || $context->status === PipeStatus::CANCELLED) {
                break;
            }

            $context = $pipe($context);

            if ($context->notification->hasErrors()) {
                $context->status = PipeStatus::FAILED;

                break;
            }
        }

        if ($context->status === PipeStatus::PROCESSING) {
            $context->status = PipeStatus::COMPLETED;
        }

        return $context;
    }
}
