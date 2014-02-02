# Configuration
override SHELL:=/bin/bash
override SHELLOPTS:=errexit:pipefail
export SHELLOPTS

# Test settings, keep in sync with test/helper/helpers.inc
TESTHOST = 0.0.0.0
TESTPORT = 8023

# Default targets
.PHONY: check
check:
	@test -f bootstrap.php -a -d helper

# Run tests independently instead of a main test file running all the test.
# This allows for testing each components separately without any side effects
# of having all other previous components loaded.
.PHONY: tests
tests: offline-tests online-tests

.PHONY: offline-tests
offline-tests: check
	@cd tests && php helper/classloader.php
	@cd tests && php helper/config.php
	@cd tests && php helper/event.php
	@cd tests && php helper/cache.php
	@cd tests && php helper/database.php
	@cd tests && php helper/session.php
	@cd tests && php helper/template.php

.PHONY: online-tests
online-tests: check

.PHONY: webserver
webserver: check
	@cd tests && php -S $(TESTHOST):$(TESTPORT)


# Build an archive of the current branch/tag
.PHONY: tarball
tarball: NAME:=sitehelper-$(shell date +%Y%m%d)-$(shell git describe --always)
tarball: check
	@mkdir -p output
	@git archive --format=tar --prefix=$(NAME)/ HEAD | xz > output/$(NAME).tar.xz

# Build documentation
.PHONY: doc
doc: check
	@test ! -d output/html || rm -r output/html
	@doxygen doc/doxygen/Doxyfile

# Cleanup
.phony: clean
clean: check
	@rm -r output

