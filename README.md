# Payments

A payment service for PHP applications.


## Installation

To install this package, run the following command:
```bash
composer require pod-point/payments-php
```


## Usage

### Laravel

For Laravel applications, bind the payment service interface to an instance of a provider payment service within an application service provider.


## Development

### Testing

This project uses PHPUnit, run the following command to run the tests:

```bash
vendor/bin/phpunit
```

A test secret key for Stripe will be required to run the tests.
