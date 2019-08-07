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

    /**
     * @param string $value
     * @param string $type
     */
    public function __construct(string $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }
}
