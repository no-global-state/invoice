<?php

namespace Site\Collection;

use Krystal\Stdlib\ArrayCollection;

final class StatusCollection extends ArrayCollection
{
    const PARAM_STATUS_TEMPORARY = -1;
    const PARAM_STATUS_COMPLETE = 1;
    
    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::PARAM_STATUS_TEMPORARY => 'Temporary',
        self::PARAM_STATUS_COMPLETE => 'Complete'
    ];
}
