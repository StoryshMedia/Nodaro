#!/bin/sh
php bin/console lexik:jwt:generate-keypair
php -d memory_limit=-1 bin/console nodaro:install:database