#!/usr/bin/env bash

if [[ ! -f config.local.php ]]; then
	echo "Deploy error: $PWD/config.local.php does not exist"
	exit 1
fi

cp SERVER-README.txt README.txt
rsync -a --delete -v -e ssh ../../build/production/ .htaccess index.php README.txt endlemar@wa.toad.cz:~/www/
rsync -a --delete -v -e ssh ../../backend/ endlemar@wa.toad.cz:~/app/
rsync -a --delete -v -e ssh ../../vendor/ endlemar@wa.toad.cz:~/vendor/
rsync -a --delete -v -e ssh config.local.php ../../build/assets.production.json endlemar@wa.toad.cz:~/config/
