#!/bin/bash
set -e

REDY=$(docker-compose ps template-project)

if [[ ! ${REDY} =~ template-project.*Up ]]; then
    echo "start template-project"
    docker-compose up -d
else
    echo "starting template-project"
fi
