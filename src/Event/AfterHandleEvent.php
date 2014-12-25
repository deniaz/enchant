<?php

namespace Enchant\Event;

use League\Event\AbstractEvent;

class AfterHandleEvent extends AbstractEvent
{
    public function getName()
    {
        return 'AfterHandleEvent';
    }
}