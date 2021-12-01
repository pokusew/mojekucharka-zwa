# simple shortcuts

TARGET:=wa.toad.cz
SERVER:=localhost:8007

.PHONY: deploy
deploy: backend/assets.json www
	@echo "deploying app to $(TARGET) ..."
	@cd "deploy/$(TARGET)" && ./deploy.sh

.PHONY: destroy
destroy:
	@echo "destroying app on $(TARGET) ..."
	@cd "deploy/$(TARGET)" && ./destroy.sh

backend/assets.json:
	@echo "building $(@) ..."
	yarn build

www:
	@echo "building $(@) ..."
	yarn build

.PHONY: run
run:
	@echo "running the app locally using the built-in PHP web server ..."
	php --server "$(SERVER)" --docroot www backend/index.php

.PHONY: clean
clean:
	rm -rf www backend/assets.json
