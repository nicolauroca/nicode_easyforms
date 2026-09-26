# ADR-0001 — Separar Authoring Model y Runtime FormSpec

Status: Accepted

## Context

El Builder necesita entidades editables, mientras que frontend necesita consistencia, histórico y rendimiento.

## Decision

Mantener un Authoring Model relacional y compilar cada publicación a un FormSpec inmutable.

## Consequences

- editar draft no cambia producción;
- cada Submission referencia FormVersion;
- runtime puede usar cache/snapshot;
- se necesita Compiler y migraciones FormSpec.
