# ADR-0004 — Distribuir Component + Module + Library en Package

Status: Accepted

## Context

Página y módulo necesitan compartir runtime sin duplicar código.

## Decision

Distribuir `com_nicode_easy_forms`, `mod_nicode_easy_forms` y `lib_nicode_easy_forms` mediante `pkg_nicode_easy_forms`.

## Consequences

- instalación única;
- runtime común;
- dependencias explícitas;
- futuros plugins pueden añadirse al package.
