<?php

namespace PodPoint\Payments;

class Refund
{
    /**@var string */
    public $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
