<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Cqrs\Query;

/**
 * @template TResult
 */
interface QueryBusInterface
{
    /**
     * @return TResult
     */
    public function ask(QueryInterface $query): mixed;
}
