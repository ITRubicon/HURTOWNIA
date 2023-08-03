#!/bin//bash

bin/console make:migration && bin/console doctrine:migrations:migrate --no-interaction && bin/console doctrine:schema:update --em=warehouse --force --complete