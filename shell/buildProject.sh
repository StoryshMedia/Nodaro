#!/bin/sh
php -d memory_limit=-1 bin/console language:message:build

php -d memory_limit=-1 bin/console frontend:safelist:build
php -d memory_limit=-1 bin/console frontend:alias:build
php -d memory_limit=-1 bin/console webpack:module:build
php -d memory_limit=-1 bin/console frontend:styles:build
php -d memory_limit=-1 bin/console frontend:form:fields:build
php -d memory_limit=-1 bin/console backend:fields:build
php -d memory_limit=-1 bin/console dynamic:frontend:component:build

NODE_ENV=production yarn encore production

php -d memory_limit=-1 bin/console cache:clear
