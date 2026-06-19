<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

/**
 * @template TPayload
 */
class Context
{
    public PipeStatus $status = PipeStatus::PROCESSING;
    public private(set) Notification $notification;

    /**
     * @param TPayload $payload
     */
    public function __construct(
        public mixed $payload,
    ) {
        $this->notification = new Notification();
    }

    /**
     * @return self<TPayload>
     */
    public function cancel(): self
    {
        $this->status = PipeStatus::CANCELLED;

        return $this;
    }

    /**
     * @return self<TPayload>
     */
    public function fail(string $message, int $code = 100): self
    {
        $this->notification->addError($message, $code);
        $this->status = PipeStatus::FAILED;

        return $this;
    }
}
