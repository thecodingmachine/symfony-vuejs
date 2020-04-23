#!/bin/bash

set +x

cp Vagrantfile.template Vagrantfile

sed -i '' -e "s%#PROJECT_NAME#%$1%g" Vagrantfile
sed -i '' -e "s%#DOCKER_COMPOSE_VERSION#%$2%g" Vagrantfile
sed -i '' -e "s%#MAC_USER#%$(whoami)%g" Vagrantfile
sed -i '' -e "s%#HOST_PATH#%$(pwd)%g" Vagrantfile