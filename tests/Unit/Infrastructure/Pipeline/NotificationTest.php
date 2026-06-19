<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Pipeline\Error;
use PlanB\Hexagonal\Core\Pipeline\Notification;

/**
 * @internal
 */
#[CoversClass(Notification::class)]
final class NotificationTest extends TestCase
{
    public function test_it_is_empty_by_default(): void
    {
        $notification = new Notification();

        $this->assertFalse($notification->hasErrors());
        $this->assertEmpty($notification->errors);
    }

    public function test_it_can_add_and_retrieve_errors(): void
    {
        $notification = new Notification();
        $notification->addError('El formato es inválido.');
        $notification->addError('El dominio no existe.');
        $notification->addError('Demasiado corta.');

        $this->assertTrue($notification->hasErrors());

        $expectedErrors = [
            new Error(100, 'El formato es inválido.'),
            new Error(100, 'El dominio no existe.'),
            new Error(100, 'Demasiado corta.'),
        ];

        $this->assertEquals($expectedErrors, $notification->errors);
    }
}
