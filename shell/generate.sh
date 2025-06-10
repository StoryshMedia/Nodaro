#!/bin/sh
php -d memory_limit=-1 bin/console smug:schema:generate
php -d memory_limit=-1 bin/console smug:database:update