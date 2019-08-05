<?php

namespace PodPoint\Payments\Entity;

class Refund
{
    /** @var string */
    private $uid;

    /**
     * @param string $uid
     */
    public function __construct(string $uid)
    {
        $this->uid = $uid;
    }

    public function getUid(): string
    {
        return $this->uid;
    }
}
