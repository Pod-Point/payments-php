<?php

namespace PodPoint\Payments;

class Customer
{
    /**
     * @var string
     */
    public $uid;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $description;

    /**
     * @param string $uid
     * @param string $email
     * @param string $description
     */
    public function __construct(string $uid, string $email, string $description = null)
    {
        $this->uid = $uid;
        $this->email = $email;
        $this->description = $description;
    }
}
