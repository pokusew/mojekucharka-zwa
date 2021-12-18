#!/usr/bin/env bash

# NOTE: This script should be run from the project root.

set -e

ssh endlemar@wa.toad.cz "rm -rf www app config log"
