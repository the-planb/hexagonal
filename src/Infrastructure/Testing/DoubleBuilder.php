<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Testing;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\MockObject\TestStubBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @template TValue of object
 */
abstract class DoubleBuilder
{
    final public function __construct(
        private readonly TestCase $testCase,
    ) {
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
            ->getMock()
        ;
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
            ->getStub()
        ;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>        $type
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
            ->getMock()
        ;
    }
}
