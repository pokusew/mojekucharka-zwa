#!/usr/bin/env bash

set -e

if [[ ! -f config.local.php ]]; then
	echo "Deploy error: $PWD/config.local.php does not exist"
	exit 1
fi

base64_sha_sum() {
	local alg="$1"
	local file="$2"
	shasum -a "$alg" -b "$file" | cut -f1 -d ' ' | xxd -r -p | base64
}

compute_integrity() {
	local file="$1"
	echo "sha256-$(base64_sha_sum 256 "$file") sha384-$(base64_sha_sum 384 "$file")"
}

# src_dir="$PWD"
build_production_dir="../../build/production/"
build_deployment_dir="$PWD/../../build/wa.toad.cz"

rm -rf "$build_deployment_dir"
mkdir -p "$build_deployment_dir"

cp SERVER-README.txt "$build_deployment_dir/README.txt"
cp .htaccess index.php "$build_deployment_dir/"
cp ../../build/assets.production.json ../../build/assets.wa.toad.cz.json
cp -r "$build_production_dir" "$build_deployment_dir/"
# fix paths in manifest.*.json (prefix with basePath=/~endlemar/)
sed -i '' -E -e 's|"/|"/~endlemar/|g' "$build_deployment_dir"/manifest.*.json
# recalculate integrity
old_integrity=$(compute_integrity "$build_production_dir"/manifest.*.json)
new_integrity=$(compute_integrity "$build_deployment_dir"/manifest.*.json)
# TODO: this does not handle edge-case when multiple files have same integrity
sed -i '' -E -e "s|$old_integrity|$new_integrity|" ../../build/assets.wa.toad.cz.json

# copy to server using rsync
rsync -a --delete -v -e ssh "$build_deployment_dir/" endlemar@wa.toad.cz:~/www/
rsync -a --delete -v -e ssh ../../backend/ endlemar@wa.toad.cz:~/app/
rsync -a --delete -v -e ssh ../../vendor/ endlemar@wa.toad.cz:~/vendor/
rsync -a --delete -v -e ssh config.local.php ../../build/assets.wa.toad.cz.json endlemar@wa.toad.cz:~/config/
