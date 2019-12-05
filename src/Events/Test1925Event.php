<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class Test1925Event  extends Event
{
    public const NAME = 'Test1925';

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
