# ADR-0002 — Almacenamiento híbrido de Submissions

Status: Accepted

## Context

Solo JSON dificulta búsquedas a gran escala. EAV completo puede multiplicar filas innecesariamente.

## Decision

Guardar payload canónico íntegro y una proyección tipada regenerable de fields indexados.

## Consequences

- fidelidad histórica;
- búsqueda eficiente;
- reindexado posible;
- mayor complejidad controlada;
- SearchProvider desacoplado.
