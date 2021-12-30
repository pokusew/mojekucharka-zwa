#!/usr/bin/env bash

# NOTE: This script must be run from the project root.

set -e

project_dir="$PWD"
src_deployment_dir="$project_dir/deploy/wa.toad.cz"
build_dir="$project_dir/build"
build_production_dir="$build_dir/production"
build_deployment_dir="$build_dir/wa.toad.cz"

if [[ ! -f "$src_deployment_dir/config.local.php" ]]; then
	echo "Deploy error: $src_deployment_dir/config.local.php does not exist!"
	echo \
		"Copy $src_deployment_dir/config.template.php to $src_deployment_dir/config.local.php " \
		"and fill the required values before deployment."
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

# delete and recreate $build_deployment_dir
rm -rf "$build_deployment_dir"
mkdir -p "$build_deployment_dir" \
	"$build_deployment_dir/app" \
	"$build_deployment_dir/www" \
	"$build_deployment_dir/config" \
	"$build_deployment_dir/log"
chmod o+w "$build_deployment_dir/log"

# prepare the deployment in the $build_deployment_dir
cp "$src_deployment_dir/SERVER-README.txt" "$build_deployment_dir/www/README.txt"
cp "$src_deployment_dir/.htaccess" "$src_deployment_dir/index.php" "$build_deployment_dir/www/"
cp "$src_deployment_dir/config.local.php" "$build_deployment_dir/config/"
cp "$build_dir/assets.production.json" "$build_deployment_dir/config/assets.wa.toad.cz.json"
cp -r "$build_production_dir/" "$build_deployment_dir/www/"
cp -r "$project_dir/app/" "$build_deployment_dir/app/"
cp "$project_dir/composer.json" "$project_dir/composer.lock" "$build_deployment_dir/"

# fix paths in PWA manifest.*.json (prefix with basePath=/~endlemar/)
sed -i '' -E -e 's|"/|"/~endlemar/|g' "$build_deployment_dir"/www/manifest.*.json
# recalculate integrity
old_integrity=$(compute_integrity "$build_production_dir"/manifest.*.json)
new_integrity=$(compute_integrity "$build_deployment_dir"/www/manifest.*.json)
# TODO: this does not handle edge-case when multiple files have same integrity
sed -i '' -E -e "s|$old_integrity|$new_integrity|" "$build_deployment_dir/config/assets.wa.toad.cz.json"

# generate production-ready Composer autoloader
cd "$build_deployment_dir"
# 1st option: do clean install with --no-dev and --classmap-authoritative
composer install --no-dev --classmap-authoritative
# 2nd option: copy already installed vendor dir and just regenerate the autoloader with --classmap-authoritative
#             and remove (somehow) the dev dependencies
# cp -r "$project_dir/vendor/" "$build_deployment_dir/vendor/"
# composer dump-autoload --classmap-authoritative
cd "$project_dir"

# copy to server using rsync
# --exclude='/.*' ensures that no home directory dot-files and dot-dirs are deleted
rsync -a --delete -v -e ssh \
	--exclude='/.*' \
	--exclude='/composer.*' \
	"$build_deployment_dir/" \
	endlemar@wa.toad.cz:~/
