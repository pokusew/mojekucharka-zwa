# simple shortcuts

TARGET:=wa.toad.cz
SERVER:=localhost:8007
MODE:=development

# ASCII color sequences
# credits: https://stackoverflow.com/questions/5947742/how-to-change-the-output-color-of-echo-in-linux
# see also: https://unix.stackexchange.com/questions/269077/tput-setaf-color-table-how-to-determine-color-codes
# to get all 256 colors:
#   for c in {0..255}; do tput setaf $c; tput setaf $c | cat -v; echo =$c; done
red = $(shell tput setaf 1)
green = $(shell tput setaf 2)
yellow = $(shell tput setaf 3)
cyan = $(shell tput setaf 6)
gray = $(shell tput setaf 8)
bl = $(shell tput bold)
rs = $(shell tput sgr0)

.PHONY: rebuild-deploy
rebuild-deploy: vendor
	@echo "$(bl)$(yellow)Rebuilding frontend assets ...$(rs)"
	yarn build
	@echo "Deploying the app to $(TARGET)$(yellow) ..."
	./deploy/$(TARGET)/deploy.sh

.PHONY: deploy
deploy: build/assets.production.json build/production vendor
	@echo "$(bl)$(yellow)Deploying the app to $(cyan)$(TARGET)$(yellow) ...$(rs)"
	./deploy/$(TARGET)/deploy.sh

.PHONY: destroy
destroy:
	@echo "$(bl)$(yellow)Destroying deployment of the app on $(cyan)$(TARGET)$(yellow) ...$(rs)"
	./deploy/$(TARGET)/destroy.sh

build/assets.production.json:
	@echo "$(bl)$(yellow)Building $(cyan)$(@)$(yellow) ...$(rs)"
	yarn build

build/production:
	@echo "$(bl)$(yellow)Building $(cyan)$(@)$(yellow) ...$(rs)"
	yarn build

vendor:
	@echo "$(bl)$(yellow)Creating $(cyan)vendor$(yellow) directory using Composer ...$(rs)"
	composer install

log:
	@echo "$(bl)$(yellow)Creating $(cyan)log$(yellow) directory ...$(rs)"
	mkdir -p log

.PHONY: phpstan
phpstan:
	@echo "$(bl)$(yellow)Running PHPStan ...$(rs)"
	@vendor/bin/phpstan

.PHONY: lint
lint: phpstan

.PHONY: run
run: vendor log
	@echo "$(bl)$(yellow)Running the app in $(cyan)$(MODE)$(yellow) mode locally using the built-in PHP web server ...$(rs)"
	MODE=$(MODE) php --server "$(SERVER)" --docroot "build/$(MODE)" app/index.php

.PHONY: docs
docs:
	@echo "$(bl)$(yellow)Generating docs ...$(rs)"
	rm -rf build/docs
	mkdir -p build/docs
	phpdoc -d app -t build/docs

.PHONY: docs-deploy
docs-deploy: docs
	@echo "$(bl)$(yellow)Deploying docs ...$(rs)"
	./deploy/docs/deploy.sh

.PHONY: docs-deploy-destroy
docs-deploy-destroy:
	@echo "$(bl)$(yellow)Destroying docs deployment ...$(rs)"
	./deploy/docs/destroy.sh

.PHONY: clean
clean:
	rm -rf build/* backend/assets.*.json
