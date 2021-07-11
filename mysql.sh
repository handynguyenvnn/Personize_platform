#!/bin/bash
set -e

REDY=$(docker-compose ps template-project-db)

if [[ ! ${REDY} =~ template-project-db.*Up ]]; then
    docker-compose up -d template-project-db
fi

docker-compose exec template-project-db bash -c 'mysql -uroot -p${MYSQL_PASSWORD} ${MYSQL_DATABASE}'
