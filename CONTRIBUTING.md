# Contributing

Read `docs/README.md`, the relevant topic specifications and accepted ADRs before
changing runtime behavior. Keep FormSpec data declarative. Never add per-form PHP,
per-field SQL columns, arbitrary executable configuration or client-only security.

Run the commands in `docs/TESTING_GUIDE.md`. Add shared PHP/JS fixtures when changing
rule semantics. Include negative tests for security boundaries and diagnostics for
invalid authoring data. Record architectural changes in an ADR. Regenerate the
documentation aggregate with `php tools/docs.php` after specification changes.

Never mark an end-to-end requirement complete solely because an isolated class or
unit test exists. Keep `docs/IMPLEMENTATION_STATUS.md` accurate.
