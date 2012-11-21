# Configuration
override SHELL:=/bin/bash
override SHELLOPTS:=errexit:pipefail
export SHELLOPTS

# Programs
TEST=test
PHP=php
CD=cd

# Default targets
.PHONY: check
check:
	@$(TEST) -f bootstrap.php -s -d classes

# Run tests independently instead of a main test file running all the test.
# This allows for testing each components separately without any side effects
# of having all other previous components loaded.
.PHONY: tests
tests:
	@$(CD) tests && $(PHP) classes/classloader.php
	@$(CD) tests && $(PHP) classes/config.php
	@$(CD) tests && $(PHP) classes/cache.php


