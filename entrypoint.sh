#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

php composer.phar install

php ./vendor/bin/phinx && php ./vendor/bin/phinx -nq migrate && php ./vendor/bin/phinx -nq seed:run

php-fpm