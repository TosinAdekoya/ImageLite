#!/bin/bash

# --stderr allows tests using header() to continue without halting the script
phpunit ^
--verbose ^
--coverage-html coverage-report ^
--stderr ^
../tests > tests.log 2>&1
