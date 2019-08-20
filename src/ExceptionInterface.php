<?php

namespace PodPoint\Payments;

use PodPoint\Payments\Token;

interface ExceptionInterface
{
    /**
     * Returns the response.
     *
     * @return Token
     */
    public function getResponse(): Token;
}