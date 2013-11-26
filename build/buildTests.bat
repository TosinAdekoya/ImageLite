:: Clean up command outputs
::del .\%~n0-tap.log /F
del .\%~n0.log /F
del /S /F /AH .\%~n0-coverage-report

:: Note: --stderr allows tests using header() to continue without halting the script
phpunit ^
--verbose ^
--coverage-html %~n0-coverage-report ^
--stderr ^
..\tests > %~n0.log 2>&1

