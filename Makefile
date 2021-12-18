# simple shortcuts

TARGET:=wa.toad.cz
SERVER:=localhost:8007
MODE:=development

.PHONY: rebuild-deploy
rebuild-deploy: vendor
	@echo "rebuilding frontend assets ..."
	yarn build
	@echo "deploying app to $(TARGET) ..."
	./deploy/$(TARGET)/deploy.sh

.PHONY: deploy
deploy: build/assets.production.json build/production vendor
	@echo "deploying app to $(TARGET) ..."
	./deploy/$(TARGET)/deploy.sh

.PHONY: destroy
destroy:
	@echo "destroying app on $(TARGET) ..."
	./deploy/$(TARGET)/destroy.sh

build/assets.production.json:
	@echo "building $(@) ..."
	yarn build

build/production:
	@echo "building $(@) ..."
	yarn build

vendor:
	@echo "creating vendor directory using Composer ..."
	composer install

log:
	@echo "creating log directory ..."
	mkdir -p log

.PHONY: phpstan
phpstan:
	@echo "running PHPStan ..."
	@vendor/bin/phpstan

.PHONY: lint
lint: phpstan

.PHONY: run
run: vendor log
	@echo "running the app in $(MODE) mode locally using the built-in PHP web server ..."
	MODE=$(MODE) php --server "$(SERVER)" --docroot "build/$(MODE)" app/index.php

.PHONY: clean
clean:
	rm -rf build/* backend/assets.*.json
