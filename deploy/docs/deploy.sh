#!/usr/bin/env bash

# NOTE: This script must be run from the project root.

set -e

project_dir="$PWD"
src_deployment_dir="$project_dir/deploy/docs"
build_dir="$project_dir/build"
build_deployment_dir="$build_dir/docs"

if [[ ! -d $build_deployment_dir ]]; then
	echo "Deploy error: $build_deployment_dir dir does not exist!"
	echo "Please run 'make docs' first to generate it."
	exit 1
fi

cp \
	"$src_deployment_dir/_headers" \
	"$src_deployment_dir/_redirects" \
	"$src_deployment_dir/robots.txt" \
	"$build_deployment_dir/"
cd "$src_deployment_dir"
netlify deploy --dir "$build_deployment_dir" --prod
