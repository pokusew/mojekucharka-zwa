#!/usr/bin/env bash

# a script to export everything as zip
# includes the complete sources (everything what Git tracks) but also some of the build outputs

set -e

export_dir="$PWD/build/export"

# - exports the whole project sources as ZIP
# - in order to include only the project sources (i.e. the versioned files),
#   we want to respect the .gitignore
# - let the git to print the filenames of version all files using
#     git ls-tree --full-tree -r --name-only HEAD (only committed)
#     git ls-files (all)
#     (see https://stackoverflow.com/questions/8533202/list-files-in-local-git-repo)
#   and then supply these names as input files for zip
mkdir -p "$export_dir"
git ls-tree --full-tree -r --name-only HEAD | xargs zip "$export_dir"/export.zip
# - adds also the build outputs (built docs, wa.toad.cz deployment and production assets)
find build/docs build/wa.toad.cz build/production build/assets.production.json -print0 | xargs -0 zip "$export_dir"/export.zip
