<?php

namespace Enchant\Event;

use League\Event\AbstractEvent;

class BeforeRouteEvent extends AbstractEvent
{
    public function getName()
    {
        return 'BeforeRouteEvent';
    }
}