# simple shortcuts

TARGET:=wa.toad.cz
SERVER:=localhost:8007
MODE:=development

.PHONY: deploy
deploy: build/assets.production.json build/production
	@echo "deploying app to $(TARGET) ..."
	@cd "deploy/$(TARGET)" && ./deploy.sh

.PHONY: destroy
destroy:
	@echo "destroying app on $(TARGET) ..."
	@cd "deploy/$(TARGET)" && ./destroy.sh

build/assets.production.json:
	@echo "building $(@) ..."
	yarn build

build/production:
	@echo "building $(@) ..."
	yarn build

.PHONY: run
run:
	@echo "running the app in $(MODE) mode locally using the built-in PHP web server ..."
	MODE=$(MODE) php --server "$(SERVER)" --docroot "build/$(MODE)" backend/index.php

.PHONY: clean
clean:
	rm -rf build/* backend/assets.*.json
