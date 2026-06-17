<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Query;

use PlanB\Hexagonal\Core\Cqrs\Query\QueryBusInterface;
use PlanB\Hexagonal\Core\Cqrs\Query\QueryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @template TResult
 *
 * @implements QueryBusInterface<TResult>
 */
final readonly class QueryBus implements QueryBusInterface
{
    public function __construct(
        private MessageBusInterface $queryBus,
    ) {}

    public function ask(QueryInterface $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);

        /** @var null|HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        if ($handledStamp === null) {
            throw new \LogicException(sprintf('The query "%s" was not handled.', $query::class));
        }

        return $handledStamp->getResult();
    }
}
