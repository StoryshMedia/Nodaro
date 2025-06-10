#!/bin/sh
php -d memory_limit=-1 bin/console frontend:alias:build
php -d memory_limit=-1 bin/console webpack:module:build
php -d memory_limit=-1 bin/console frontend:styles:build
php -d memory_limit=-1 bin/console frontend:form:fields:build
php -d memory_limit=-1 bin/console backend:fields:build
php -d memory_limit=-1 bin/console dynamic:frontend:component:build

yarn build

php -d memory_limit=-1 bin/console dynamic:frontend:component:build
