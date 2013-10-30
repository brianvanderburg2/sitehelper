# Configuration
override SHELL:=/bin/bash
override SHELLOPTS:=errexit:pipefail
export SHELLOPTS

# Test settings, keep in sync with test/helper/helpers.inc
TESTHOST = 0.0.0.0
TESTPORT = 8023

# Programs
TEST=test
PHP=php
PHPTEST = php -S $(TESTHOST):$(TESTPORT)
CD=cd
GIT=git
TAR=tar
XZ=xz
MKDIR=mkdir
RM=rm
DOXYGEN=doxygen

# Default targets
.PHONY: check
check:
	@$(TEST) -f bootstrap.php -a -d helper

# Run tests independently instead of a main test file running all the test.
# This allows for testing each components separately without any side effects
# of having all other previous components loaded.
.PHONY: tests
tests: offline-tests online-tests

.PHONY: offline-tests
offline-tests: check
	@$(CD) tests && $(PHP) helper/classloader.php
	@$(CD) tests && $(PHP) helper/config.php
	@$(CD) tests && $(PHP) helper/path.php
	@$(CD) tests && $(PHP) helper/event.php
	@$(CD) tests && $(PHP) helper/cache.php
	@$(CD) tests && $(PHP) helper/database.php
	@$(CD) tests && $(PHP) helper/session.php

.PHONY: online-tests
online-tests: check
	@$(CD) tests && $(PHP) helper/action.php

.PHONY: webserver
webserver: check
	@$(CD) tests && $(PHPTEST)


# Build an archive of the current branch/tag
.PHONY: tarball
tarball: NAME:=sitehelper-$(shell $(GIT) describe --always)
tarball: check
	@$(MKDIR) -p output
	@$(GIT) archive --format=tar --prefix=$(NAME)/ HEAD | $(XZ) > output/$(NAME).tar.xz

# Build documentation
.PHONY: doc
doc: check
	@$(TEST) ! -d output/html || $(RM) -r output/html
	@$(DOXYGEN) doc/doxygen/Doxyfile

# Cleanup
.phony: clean
clean: check
	@$(RM) -r output

