<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Cqrs\Query;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Cqrs\Query\QueryInterface;
use PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Query\QueryBus;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @internal
 */
#[CoversClass(QueryBus::class)]
final class QueryBusTest extends TestCase
{
    private MessageBusInterface $messageBus;
    private QueryBus $symfonyQueryBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->symfonyQueryBus = new QueryBus($this->messageBus);
    }

    #[Test]
    public function it_returns_result_when_query_is_handled(): void
    {
        // Arrange
        $query = $this->createStub(QueryInterface::class);
        $expectedResult = ['data' => 'some_value'];

        $handledStamp = new HandledStamp($expectedResult, 'HandlerName');
        $envelope = new Envelope($query, [$handledStamp]);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope)
        ;

        // Act
        $actualResult = $this->symfonyQueryBus->ask($query);

        // Assert
        $this->assertSame($expectedResult, $actualResult);
    }

    #[Test]
    public function it_throws_exception_when_query_is_not_handled(): void
    {
        // Arrange
        $query = $this->createStub(QueryInterface::class);
        $queryClass = $query::class;

        // Envoltura vacía, sin HandledStamp
        $envelope = new Envelope($query);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope)
        ;

        // Assert & Act
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf('The query "%s" was not handled.', $queryClass));

        $this->symfonyQueryBus->ask($query);
    }
}
