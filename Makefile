# Configuration
override SHELL:=/bin/bash
override SHELLOPTS:=errexit:pipefail
export SHELLOPTS

# Programs
TEST=test
PHP=php
CD=cd
GIT=git
TAR=tar
XZ=xz
MKDIR=mkdir

# Default targets
.PHONY: check
check:
	@$(TEST) -f bootstrap.php -a -d classes

# Run tests independently instead of a main test file running all the test.
# This allows for testing each components separately without any side effects
# of having all other previous components loaded.
.PHONY: tests
tests: check
	@$(CD) tests && $(PHP) classes/classloader.php
	@$(CD) tests && $(PHP) classes/config.php
	@$(CD) tests && $(PHP) classes/paths.php
	@$(CD) tests && $(PHP) classes/cache.php
	@$(CD) tests && $(PHP) classes/database.php

# Build an archive of the current branch/tag
.PHONY: archive
archive: NAME:=sitehelper-$(shell $(GIT) describe --always)
archive: check
	@$(MKDIR) -p output
	@$(GIT) archive --format=tar --prefix=$(NAME)/ HEAD | $(XZ) > output/$(NAME).tar.xz


