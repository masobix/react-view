#!/bin/bash

# Note that this does not use pipefail
# because if the grep later doesn't match any deleted files,
# which is likely to be the case the majority of the time,
# it does not exit with 0, as we are interested in the final exit.
# set -eo

generate_zip(){ 
    echo "Generating zip file..."
    SLUG="fundrizer"
    zip -r "${SLUG}.zip" dist/
    echo "✓ Zip file generated!"
}

rm fundrizer.zip
rsync -avr --exclude-from=".distignore" . dist --delete-after
rsync -avr  --include="**build***" --include="*/" --exclude="*" wp-blocks/ dist/src/Blocks/

generate_zip



