# Those variables are only used for the Vagrant command!
PROJECT_NAME=companies-and-products
DOCKER_COMPOSE_VERSION=1.25.5

# Start the Docker Compose stack.
up:
	docker-compose up -d

# Stop the Docker Compose stack.
down:
	docker-compose down

# Run bash in the webapp service.
webapp:
	docker-compose exec webapp bash

# Run bash in the api service.
api:
	docker-compose exec api bash

# Create the Vagrantfile from the template Vagrantfile.template.
vagrant:
	./scripts/create-vagrantfile-from-template.sh $(PROJECT_NAME) $(DOCKER_COMPOSE_VERSION)