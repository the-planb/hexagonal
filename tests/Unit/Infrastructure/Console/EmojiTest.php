<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Console;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\Emoji;

/**
 * @internal
 */
#[CoversClass(Emoji::class)]
class EmojiTest extends TestCase
{
    #[Test]
    #[DataProvider('emojiDataProvider')]
    public function it_retuns_the_proper_code_for_a_emoji(string $method, string $expected): void
    {
        $emoji = new Emoji();
        self::assertEquals($expected, $emoji->{$method});
    }

    public static function emojiDataProvider()
    {
        return [
            'success' => ['success', "\u{1F680}"],
            'check' => ['check', "\u{2705}"],
            'ok' => ['ok', "\u{1F44C}"],
            'error' => ['error', "\u{274C}"],
            'warning' => ['warning', "\u{26A0}\u{FE0F}"],
            'info' => ['info', "\u{2139}\u{FE0F}"],
            'question' => ['question', "\u{2753}"],
            'building' => ['building', "\u{1F3D7}\u{FE0F}"],
            'running' => ['running', "\u{2699}\u{FE0F}"],
            'syncing' => ['syncing', "\u{1F504}"],
            'waiting' => ['waiting', "\u{23F3}"],
            'searching' => ['searching', "\u{1F50D}"],
            'cleaning' => ['cleaning', "\u{1F9F9}"],
            'fire' => ['fire', "\u{1F525}"],
            'database' => ['database', "\u{1F4D8}"],
            'file' => ['file', "\u{1F4C4}"],
            'config' => ['config', "\u{1F4DD}"],
            'package' => ['package', "\u{1F4E6}"],
            'api' => ['api', "\u{1F310}"],
            'event_in' => ['event_in', "\u{1F4E5}"],
            'event_out' => ['event_out', "\u{1F4E4}"],
            'lock' => ['lock', "\u{1F512}"],
            'test' => ['test', "\u{1F9EA}"],
            'debug' => ['debug', "\u{1F41B}"],
            'timer' => ['timer', "\u{23F1}\u{FE0F}"],
        ];
    }
}
