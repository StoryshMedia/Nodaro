<?php

namespace Smug\Core\Service\Email;

class EmailData
{
    protected string $template;

    protected string $subject;

    protected array $sender;

    protected array $recipients = [];

    protected array $data;

    public function __get($name): mixed
    {
        return $this->$name;
    }

    public function __set($name, $value): void
    {
        $this->$name = $value;
    }

    public function __add($name, $value): void
    {
        $this->$name[] = $value;
    }
}