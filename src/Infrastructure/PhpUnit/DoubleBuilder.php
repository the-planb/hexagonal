<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\PhpUnit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use PHPUnit\Framework\MockObject\TestStubBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @template TValue of object
 */
abstract class DoubleBuilder extends Assert
{
    final public function __construct(
        private readonly TestCase $testCase,
    )
    {
        $this->setUp();
    }

    abstract protected function setUp(): void;

    /**
     * @return TValue
     */
    abstract public function build(): object;

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return MockObject&T
     */
    protected function createMock(string $type): MockObject
    {
        return new MockBuilder($this->testCase, $type)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return Stub&T
     */
    protected function createStub(string $type): Stub
    {
        return new TestStubBuilder($type)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getStub();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     * @param list<non-empty-string> $methods
     *
     * @return MockObject&T
     */
    protected function createPartialMock(string $type, array $methods): MockObject
    {
        return new MockBuilder($this->testCase, $type)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * Replicación exacta del método protected 'once()'
     */
    protected function once(): InvokedCountMatcher
    {
        return new InvokedCountMatcher(1);
    }

    /**
     * Replicación exacta del método protected 'never()'
     */
    protected function never(): InvokedCountMatcher
    {
        return new InvokedCountMatcher(0);
    }

    /**
     * Replicación exacta del método protected 'exactly()'
     */
    protected function exactly(int $count): InvokedCountMatcher
    {
        return new InvokedCountMatcher($count);
    }

    /**
     * Replicación exacta del método protected 'any()'
     */
    protected function any(): AnyInvokedCountMatcher
    {
        return new AnyInvokedCountMatcher;
    }

    /**
     * Replicación exacta del método protected 'atLeastOnce()'
     */
    protected function atLeastOnce(): InvokedAtLeastOnceMatcher
    {
        return new InvokedAtLeastOnceMatcher;
    }

    /**
     * Replicación exacta del método protected 'throwException()'
     */
    protected function throwException(\Throwable $exception): ExceptionStub
    {
        return new ExceptionStub($exception);
    }
}
