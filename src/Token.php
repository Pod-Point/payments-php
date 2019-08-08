<?php

namespace PodPoint\Payments;

class Token
{
    /**
     * The token.
     *
     * @var string
     */
    public $value;

    /**
     * The type of token.
     *
     * @var string
     */
    public $type;


    public $customer;

    /**
     * @param string $value
     * @param string $type
     * @param string $customer
     */
    public function __construct(string $value, string $type, string $customer)
    {
        $this->value = $value;
        $this->type = $type;
        $this->customer = $customer;
    }
}
