<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Console;

final class Emoji
{
    public const string SUCCESS = "\u{1F680}";
    public const string CHECK = "\u{2705}";
    public const string OK = "\u{1F44C}";
    public const string ERROR = "\u{274C}";
    public const string WARNING = "\u{26A0}\u{FE0F}";
    public const string INFO = "\u{2139}\u{FE0F}";
    public const string QUESTION = "\u{2753}";

    public const string BUILDING = "\u{1F3D7}\u{FE0F}";
    public const string RUNNING = "\u{2699}\u{FE0F}";
    public const string SYNCING = "\u{1F504}";
    public const string WAITING = "\u{23F3}";
    public const string SEARCHING = "\u{1F50D}";
    public const string CLEANING = "\u{1F9F9}";
    public const string FIRE = "\u{1F525}";

    public const string DATABASE = "\u{1F4D8}";
    public const string FILE = "\u{1F4C4}";
    public const string CONFIG = "\u{1F4DD}";
    public const string PACKAGE = "\u{1F4E6}";
    public const string API = "\u{1F310}";
    public const string EVENT_IN = "\u{1F4E5}";
    public const string EVENT_OUT = "\u{1F4E4}";
    public const string LOCK = "\u{1F512}";
    public const string TEST = "\u{1F9EA}";
    public const string DEBUG = "\u{1F41B}";
    public const string TIMER = "\u{23F1}\u{FE0F}";

    public private(set) string $success = self::SUCCESS;
    public private(set) string $check = self::CHECK;
    public private(set) string $ok = self::OK;
    public private(set) string $error = self::ERROR;
    public private(set) string $warning = self::WARNING;
    public private(set) string $info = self::INFO;
    public private(set) string $question = self::QUESTION;
    public private(set) string $building = self::BUILDING;
    public private(set) string $running = self::RUNNING;
    public private(set) string $syncing = self::SYNCING;
    public private(set) string $waiting = self::WAITING;
    public private(set) string $searching = self::SEARCHING;
    public private(set) string $cleaning = self::CLEANING;
    public private(set) string $fire = self::FIRE;
    public private(set) string $database = self::DATABASE;
    public private(set) string $file = self::FILE;
    public private(set) string $config = self::CONFIG;
    public private(set) string $package = self::PACKAGE;
    public private(set) string $api = self::API;
    public private(set) string $event_in = self::EVENT_IN;
    public private(set) string $event_out = self::EVENT_OUT;
    public private(set) string $lock = self::LOCK;
    public private(set) string $test = self::TEST;
    public private(set) string $debug = self::DEBUG;
    public private(set) string $timer = self::TIMER;
}
