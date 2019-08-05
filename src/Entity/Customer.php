<?php

namespace PodPoint\Payments\Entity;

class Customer
{
    /** @var string */
    private $uid;

    /** @var string */
    private $email;

    /**
     * @param string $uid
     * @param string $email
     */
    public function __construct(string $uid, string $email)
    {
        $this->uid = $uid;
        $this->email = $email;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
