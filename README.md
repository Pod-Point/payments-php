# Payments

[![Build Status](https://travis-ci.com/Pod-Point/payments-php.svg?branch=master)](https://travis-ci.com/Pod-Point/payments-php)
[![codecov](https://codecov.io/gh/Pod-Point/payments-php/branch/master/graph/badge.svg)](https://codecov.io/gh/Pod-Point/payments-php)

A payment service for PHP applications.


## Installation

To install this package, run the following command:
```bash
composer require pod-point/payments-php
```


## Usage

When performing actions using a payment service, a provider-specific exception will be thrown if it's requirements to perform that action are not met. This exception will contain the data received from the provider which can be used by the consuming application to resolve any issues. 

For example, when trying to make a payment with the Stripe payment service, if authorisation is required, an exception will be thrown with a "payment intent" object which can be used by a clients Stripe SDK to carry out the authorisation.

### Laravel

For Laravel applications, bind the payment service interface to an instance of a provider payment service within an application service provider.


## Development

### Testing

This project uses PHPUnit, run the following command to run the tests:

```bash
vendor/bin/phpunit
```

A test secret key for Stripe will be required to run the tests.
