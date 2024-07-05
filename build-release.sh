#!/bin/bash

# Note that this does not use pipefail
# because if the grep later doesn't match any deleted files,
# which is likely to be the case the majority of the time,
# it does not exit with 0, as we are interested in the final exit.
# set -eo

SLUG=${GITHUB_REPOSITORY#*/}
VERSION="${GITHUB_REF#refs/tags/}"
VERSION="${VERSION#v}"

generate_zip(){ 
    echo "Generating zip file..."
    
    zip -r "${GITHUB_WORKSPACE}/${SLUG}-${VERSION}.zip" "${SLUG}/"
    echo "zip-path=${GITHUB_WORKSPACE}/${SLUG}-${VERSION}.zip" >> "${GITHUB_OUTPUT}"
    echo "✓ Zip file generated!"
}


rsync -avr --exclude-from=".distignore" . "${SLUG}" --delete-after
rsync -avr  --include="**build***" --include="*/" --exclude="*" wp-blocks/ "${SLUG}/src/Blocks/"

generate_zip



