#!/bin/bash

# Note that this does not use pipefail
# because if the grep later doesn't match any deleted files,
# which is likely to be the case the majority of the time,
# it does not exit with 0, as we are interested in the final exit.
# set -eo

generate_zip(){ 
    echo "Generating zip file..."
    SLUG=${GITHUB_REPOSITORY#*/}
    zip -r "${GITHUB_WORKSPACE}/${SLUG}.zip" dist/
    echo "zip-path=${GITHUB_WORKSPACE}/${SLUG}.zip" >> "${GITHUB_OUTPUT}"
    echo "${GITHUB_WORKSPACE}/${SLUG}.zip"
    echo "✓ Zip file generated!"
}


rsync -avr --exclude-from=".distignore" . dist --delete-after
rsync -avr  --include="**build***" --include="*/" --exclude="*" wp-blocks/ dist/src/Blocks/

generate_zip



