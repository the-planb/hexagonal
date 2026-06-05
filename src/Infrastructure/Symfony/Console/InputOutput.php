<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Console;

use Laravel\Prompts\AutoCompletePrompt;
use Laravel\Prompts\Clear;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\DataTablePrompt;
use Laravel\Prompts\FormBuilder;
use Laravel\Prompts\Grid;
use Laravel\Prompts\MultiSearchPrompt;
use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\Note;
use Laravel\Prompts\NotifyPrompt;
use Laravel\Prompts\NumberPrompt;
use Laravel\Prompts\PasswordPrompt;
use Laravel\Prompts\PausePrompt;
use Laravel\Prompts\Progress;
use Laravel\Prompts\SearchPrompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\Spinner;
use Laravel\Prompts\Stream;
use Laravel\Prompts\SuggestPrompt;
use Laravel\Prompts\Table as LaravelTable;
use Laravel\Prompts\Task;
use Laravel\Prompts\TextareaPrompt;
use Laravel\Prompts\TextPrompt;
use Laravel\Prompts\Title;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InputOutput
{
    public private(set) SymfonyStyle $symfonyStyle;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
    }

    /**
     * @param array<int, string>|string $messages
     */
    public function block(array|string $messages, ?string $type = null, ?string $style = null, string $prefix = ' ', bool $padding = false, bool $escape = true): void
    {
        $this->symfonyStyle->block($messages, $type, $style, $prefix, $padding, $escape);
    }

    public function section(string $message): void
    {
        $this->symfonyStyle->section($message);
    }

    /**
     * @param array<int, string> $elements
     */
    public function listing(array $elements): void
    {
        $this->symfonyStyle->listing($elements);
    }

    /**
     * @param array<int, string>|string $message
     */
    public function comment(array|string $message): void
    {
        $this->symfonyStyle->comment($message);
    }

    /**
     * @param array<int, string>|string $message
     */
    public function caution(array|string $message): void
    {
        $this->symfonyStyle->caution($message);
    }

    public function newline(int $count = 1): void
    {
        $this->symfonyStyle->newLine($count);
    }

    /**
     * @param iterable<int, string>|string $messages
     * @param int<0, 511> $type
     */
    public function write(iterable|string $messages, bool $newline = false, int $type = OutputInterface::OUTPUT_NORMAL): void
    {
        $this->symfonyStyle->write($messages, $newline, $type);
    }

    /**
     * @param iterable<int, string>|string $messages
     * @param int<0, 511> $type
     */
    public function writeln(iterable|string $messages, int $type = OutputInterface::OUTPUT_NORMAL): void
    {
        $this->symfonyStyle->writeln($messages, $type);
    }

    /**
     * @param array<int, string>|string|TableSeparator ...$list
     */
    public function definitionList(array|string|TableSeparator ...$list): void
    {
        $this->symfonyStyle->definitionList(...$list);
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, array<int, string>> $rows
     */
    public function table(array $headers = [], array $rows = []): void
    {
        /** @phpstan-ignore-next-line */
        new LaravelTable(headers: $headers, rows: $rows)->display();
    }

    /**
     * @param null|(callable(string): ?string) $validator
     */
    public function ask(string $question, ?string $default = null, ?callable $validator = null): string
    {
        $validate = $validator ? fn (string $value) => $validator($value) : null;

        $result = new TextPrompt(
            label: $question,
            default: $default ?? '',
            validate: $validate,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    /**
     * @param null|(callable(string): ?string) $validator
     */
    public function askHidden(string $question, ?callable $validator = null): string
    {
        $validate = $validator ? fn (string $value) => $validator($value) : null;

        $result = new PasswordPrompt(
            label: $question,
            validate: $validate,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    public function confirm(string $question, bool $default = true, string $yes = 'Yes', string $no = 'No'): bool
    {
        return (bool) new ConfirmPrompt(
            label: $question,
            default: $default,
            yes: $yes,
            no: $no,
        )->prompt();
    }

    /**
     * @param array<int, string>|array<string, string> $choices
     * @param null|array<int, int|string>|int|string $default
     */
    public function choice(string $question, array $choices, array|int|string|null $default = null, bool $multiSelect = false): mixed
    {
        if ($multiSelect) {
            return new MultiSelectPrompt(
                label: $question,
                options: $choices,
                default: is_array($default) ? $default : [],
            )->prompt();
        }

        return new SelectPrompt(
            label: $question,
            options: $choices,
            default: is_array($default) ? null : $default,
        )->prompt();
    }

    public function title(string $title): void
    {
        new Title(title: $title)->display();
    }

    /**
     * @param array<int, string>|string $message
     */
    public function error(array|string $message): void
    {
        $msg = is_array($message) ? implode("\n", $message) : $message;
        new Note(message: $msg, type: 'error')->display();
    }

    /**
     * @param array<int, string>|string $message
     */
    public function warning(array|string $message): void
    {
        $msg = is_array($message) ? implode("\n", $message) : $message;
        new Note(message: $msg, type: 'warning')->display();
    }

    /**
     * @param array<int, string>|string $message
     */
    public function note(array|string $message): void
    {
        $msg = is_array($message) ? implode("\n", $message) : $message;
        new Note(message: $msg, type: 'warning')->display();
    }

    /**
     * @param array<int, string>|string $message
     */
    public function info(array|string $message): void
    {
        $msg = is_array($message) ? implode("\n", $message) : $message;
        new Note(message: $msg, type: 'info')->display();
    }

    /**
     * @param array<int, string>|string $message
     */
    public function success(array|string $message): void
    {
        $msg = is_array($message) ? implode("\n", $message) : $message;
        new Note(message: $msg, type: 'info')->display();
    }

    /**
     * @param null|(callable(string): ?string) $validate
     * @param null|(\Closure(string): string) $transform
     */
    public function text(string $label, string $placeholder = '', string $default = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?\Closure $transform = null): string
    {
        $result = new TextPrompt(
            label: $label,
            placeholder: $placeholder,
            default: $default,
            required: $required,
            validate: $validate,
            hint: $hint,
            transform: $transform,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    /**
     * @param array<int, string>|(\Closure(string): array<int, string>) $options
     * @param null|(callable(string): ?string) $validate
     * @param null|(\Closure(string): string) $transform
     */
    public function autocomplete(string $label, array|\Closure $options = [], string $placeholder = '', string $default = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?\Closure $transform = null): string
    {
        $result = new AutoCompletePrompt(
            label: $label,
            options: $options,
            placeholder: $placeholder,
            default: $default,
            required: $required,
            validate: $validate,
            hint: $hint,
            transform: $transform,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    /**
     * @param null|(callable(string): ?string) $validate
     */
    public function number(
        string $label,
        string $placeholder = '',
        string $default = '',
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        ?int $min = null,
        ?int $max = null,
        ?int $step = null,
    ): int|string {
        $result = new NumberPrompt(
            label: $label,
            placeholder: $placeholder,
            default: $default,
            required: $required,
            validate: $validate,
            hint: $hint,
            min: $min,
            max: $max,
            step: $step,
        )->prompt();

        return is_int($result) ? $result : (is_string($result) ? $result : '');
    }

    /**
     * @param null|(callable(string): ?string) $validate
     * @param null|(\Closure(string): string) $transform
     */
    public function textarea(string $label, string $placeholder = '', string $default = '', bool|string $required = false, mixed $validate = null, string $hint = '', int $rows = 5, ?\Closure $transform = null): string
    {
        $result = new TextareaPrompt(
            label: $label,
            placeholder: $placeholder,
            default: $default,
            required: $required,
            validate: $validate,
            hint: $hint,
            rows: $rows,
            transform: $transform,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    /**
     * @param null|(callable(string): ?string) $validate
     * @param null|(\Closure(string): string) $transform
     */
    public function password(string $label, string $placeholder = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?\Closure $transform = null): string
    {
        $result = new PasswordPrompt(
            label: $label,
            placeholder: $placeholder,
            required: $required,
            validate: $validate,
            hint: $hint,
            transform: $transform,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    /**
     * @param array<int|string, string> $options
     * @param null|(callable(int|string): ?string) $validate
     * @param null|(\Closure(int|string): (int|string)) $transform
     * @param (\Closure(int|string): string)|string $info
     */
    public function select(string $label, array $options, int|string|null $default = null, int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true, ?\Closure $transform = null, \Closure|string $info = ''): int|string
    {
        $result = new SelectPrompt(
            label: $label,
            options: $options,
            default: $default,
            scroll: $scroll,
            validate: $validate,
            hint: $hint,
            required: $required,
            transform: $transform,
            info: $info,
        )->prompt();

        return is_int($result) ? $result : (is_string($result) ? $result : '');
    }

    /**
     * @param array<int|string, string> $options
     * @param array<int, int|string> $default
     * @param null|(callable(array<int, int|string>): ?string) $validate
     * @param null|(\Closure(array<int, int|string>): array<int, int|string>) $transform
     * @param (\Closure(int|string): string)|string $info
     *
     * @return array<int, int|string>
     */
    public function multiselect(string $label, array $options, array $default = [], int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = 'Use the space bar to select options.', ?\Closure $transform = null, \Closure|string $info = ''): array
    {
        $result = new MultiSelectPrompt(
            label: $label,
            options: $options,
            default: $default,
            scroll: $scroll,
            required: $required,
            validate: $validate,
            hint: $hint,
            transform: $transform,
            info: $info,
        )->prompt();

        /** @var array<int, int|string> $result */
        return $result;
    }

    /**
     * @param array<int, string>|(\Closure(string): array<int, string>) $options
     * @param null|(callable(string): ?string) $validate
     * @param null|(\Closure(string): string) $transform
     * @param (\Closure(string): string)|string $info
     */
    public function suggest(string $label, array|\Closure $options, string $placeholder = '', string $default = '', int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = '', ?\Closure $transform = null, \Closure|string $info = ''): string
    {
        $result = new SuggestPrompt(
            label: $label,
            options: $options,
            placeholder: $placeholder,
            default: $default,
            scroll: $scroll,
            required: $required,
            validate: $validate,
            hint: $hint,
            transform: $transform,
            info: $info,
        )->prompt();

        return is_string($result) ? $result : '';
    }

    /**
     * @param \Closure(string): array<int|string, string> $options
     * @param null|(callable(int|string): ?string) $validate
     * @param null|(\Closure(int|string): (int|string)) $transform
     * @param (\Closure(int|string): string)|string $info
     */
    public function search(string $label, \Closure $options, string $placeholder = '', int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true, ?\Closure $transform = null, \Closure|string $info = ''): int|string
    {
        $result = new SearchPrompt(
            label: $label,
            options: $options,
            placeholder: $placeholder,
            scroll: $scroll,
            validate: $validate,
            hint: $hint,
            required: $required,
            transform: $transform,
            info: $info,
        )->prompt();

        return is_int($result) ? $result : (is_string($result) ? $result : '');
    }

    /**
     * @param \Closure(string): array<int|string, string> $options
     * @param null|(callable(array<int, int|string>): ?string) $validate
     * @param null|(\Closure(array<int, int|string>): array<int, int|string>) $transform
     * @param (\Closure(int|string): string)|string $info
     *
     * @return array<int, int|string>
     */
    public function multisearch(string $label, \Closure $options, string $placeholder = '', int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = 'Use the space bar to select options.', ?\Closure $transform = null, \Closure|string $info = ''): array
    {
        $result = new MultiSearchPrompt(
            label: $label,
            options: $options,
            placeholder: $placeholder,
            scroll: $scroll,
            required: $required,
            validate: $validate,
            hint: $hint,
            transform: $transform,
            info: $info,
        )->prompt();

        /** @var array<int, int|string> $result */
        return $result;
    }

    /**
     * @template T
     *
     * @param \Closure(): T $callback
     *
     * @return T
     */
    public function spin(\Closure $callback, string $message = ''): mixed
    {
        return new Spinner(message: $message)->spin(callback: $callback);
    }

    /**
     * @return Progress<int>
     */
    /**
     * @template TSteps
     * @template TReturn
     *
     * @param int|iterable<TSteps> $steps
     * @param null|(\Closure(mixed, Progress<int|iterable<TSteps>>): TReturn) $callback
     *
     * @return ($callback is \Closure ? array<int|string, TReturn> : Progress<int|iterable<TSteps>>)
     */
    public function progress(string $label, int|iterable $steps, ?\Closure $callback = null, string $hint = ''): array|Progress
    {
        /** @var Progress<int|iterable<TSteps>> $progress */
        $progress = new Progress(label: $label, steps: $steps, hint: $hint);

        if ($callback instanceof \Closure) {
            /** @var \Closure(mixed, Progress<int|iterable<TSteps>>): TReturn $mapCallback */
            $mapCallback = $callback;

            /** @var array<int|string, TReturn> $result */
            $result = $progress->map($mapCallback);

            return $result;
        }

        return $progress;
    }

    /**
     * @template T
     *
     * @param \Closure(): T $callback
     *
     * @return T
     */
    public function task(string $label, \Closure $callback, ?int $limit = null, bool $keepSummary = false, ?string $subLabel = null): mixed
    {
        return new Task(
            label: $label,
            limit: $limit ?? 10,
            keepSummary: $keepSummary,
            subLabel: $subLabel,
        )->run(callback: $callback);
    }

    public function form(): FormBuilder
    {
        return new FormBuilder();
    }

    /**
     * @param list<list<string>|string> $headers
     * @param null|array<int|string, array<int, string>> $rows
     * @param null|(callable(array<int, array<string, string>>): array<int, array<string, string>>) $validate
     * @param null|(\Closure(array<int, array<string, string>>): array<int, array<string, string>>) $transform
     * @param null|(\Closure(array<int, array<string, string>>): array<int, array<string, string>>) $filter
     */
    public function datatable(array $headers = [], ?array $rows = null, int $scroll = 10, string $label = '', string $hint = '', bool|string $required = false, mixed $validate = null, ?\Closure $transform = null, ?\Closure $filter = null): mixed
    {
        return new DataTablePrompt(
            headers: $headers,
            rows: $rows ?? [],
            scroll: $scroll,
            label: $label,
            hint: $hint,
            required: $required,
            validate: $validate,
            transform: $transform,
            filter: $filter,
        )->prompt();
    }

    /**
     * @param array<int, string> $items
     */
    public function grid(array $items = [], ?int $maxWidth = null): void
    {
        new Grid(items: $items, maxWidth: $maxWidth)->display();
    }

    public function pause(string $message = 'Press enter to continue...'): mixed
    {
        return new PausePrompt(message: $message)->prompt();
    }

    public function clear(): void
    {
        new Clear()->display();
    }

    public function stream(): Stream
    {
        return new Stream();
    }

    public function notify(string $title, string $body = '', string $subtitle = '', string $sound = '', string $icon = ''): void
    {
        new NotifyPrompt(
            title: $title,
            body: $body,
            subtitle: $subtitle,
            sound: $sound,
            icon: $icon,
        )->display();
    }
}
