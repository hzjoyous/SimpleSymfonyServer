<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class TestEvent  extends Event
{
    public const NAME = 'Test';

    protected $content;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function getOrder()
    {
        return $this->content;
    }
}
