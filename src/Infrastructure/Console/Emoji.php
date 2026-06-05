<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Console;

final class Emoji
{
    public private(set) string $success = "\u{1F680}";
    public private(set) string $check = "\u{2705}";
    public private(set) string $ok = "\u{1F44C}";
    public private(set) string $error = "\u{274C}";
    public private(set) string $warning = "\u{26A0}\u{FE0F}";
    public private(set) string $info = "\u{2139}\u{FE0F}";
    public private(set) string $question = "\u{2753}";

    public private(set) string $building = "\u{1F3D7}\u{FE0F}";
    public private(set) string $running = "\u{2699}\u{FE0F}";
    public private(set) string $syncing = "\u{1F504}";
    public private(set) string $waiting = "\u{23F3}";
    public private(set) string $searching = "\u{1F50D}";
    public private(set) string $cleaning = "\u{1F9F9}";
    public private(set) string $fire = "\u{1F525}";

    public private(set) string $database = "\u{1F4D8}";
    public private(set) string $file = "\u{1F4C4}";
    public private(set) string $config = "\u{1F4DD}";
    public private(set) string $package = "\u{1F4E6}";

    public private(set) string $api = "\u{1F310}";
    public private(set) string $event_in = "\u{1F4E5}";
    public private(set) string $event_out = "\u{1F4E4}";
    public private(set) string $lock = "\u{1F512}";

    public private(set) string $test = "\u{1F9EA}";
    public private(set) string $debug = "\u{1F41B}";
    public private(set) string $timer = "\u{23F1}\u{FE0F}";
}
