#!/bin/bash

phpunit ^
--verbose ^
--log-tap summary.log ^
--coverage-html ./reports ^
.
