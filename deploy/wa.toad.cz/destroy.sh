#!/usr/bin/env bash

if [[ ! -f config.local.php ]]; then
	echo "Deploy error: $PWD/config.local.php does not exist"
	exit 1
fi

ssh endlemar@wa.toad.cz "rm -rf www app config"
