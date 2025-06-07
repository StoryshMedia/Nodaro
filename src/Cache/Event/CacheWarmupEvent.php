<?php

namespace Smug\Core\Cache\Event;

final class CacheWarmupEvent
{
    private array $errors = [];

    public function __construct(private readonly array $groups) {}

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function hasGroup(string $group): bool
    {
        return in_array($group, $this->groups, true);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}
