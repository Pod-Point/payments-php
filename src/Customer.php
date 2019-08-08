<?php

namespace PodPoint\Payments;


class Customer {

    /** @var string */
    public $uid;
    /** @var string */
    public $email;

    /**
     * @param string $uid
     * @param string $email
     */
    public function __construct(string $uid, string $email)
    {
        $this->uid = $uid;
        $this->email = $email;
    }
}
