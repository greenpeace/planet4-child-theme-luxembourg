#!/bin/sh
# <?php exit; ?>
cd plugins
rm -rf gravityforms-gpf
git clone https://bitbucket.org/greenpeacefrance/gravityforms-gpf.git
rm -rf gravityforms-gpf/.git
cd ..