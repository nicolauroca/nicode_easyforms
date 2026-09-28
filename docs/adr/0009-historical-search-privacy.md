# ADR-0009 — Privacidad histórica en filtros de respuestas

Status: Accepted

## Context

Un mismo UUID de campo puede ser sensible en una versión y público en otra.
Autorizar un filtro solamente con el snapshot seleccionado permite inferir un
valor sensible antiguo, incluso mediante `not_equals` o valores sin índice.

## Decision

Mantener `version_field_policy`, proyección regenerable de cada snapshot con
identidad de formulario/versión/campo, sensibilidad e indexación. La publicación
la escribe en la misma transacción que activa la versión. Reconstruirla exige
comprobar la propiedad y el hash del snapshot; no modifica datos canónicos.

Cada filtro exige una política histórica explícita e indexada. Sin permiso
`view_sensitive` exige además `sensitive = 0`. Si faltan registros, la búsqueda
no concede acceso por defecto. La condición se aplica fuera de la negación del
valor, por lo que `not_equals` tampoco incluye versiones sensibles ocultas.

## Consequences

- Los snapshots conservan su significado y no es necesario prohibir cambios de
  sensibilidad de una versión a otra.
- El filtro añade una consulta escalar por condición sobre una clave única de
  versión/campo; no interpreta JSON ni recorre payloads por respuesta. Una fila
  inexistente devuelve NULL y no satisface el predicado. Evita que el optimizador
  convierta la política en la tabla conductora y recorra todas las respuestas
  de una versión antes de aplicar filtros selectivos por valor.
- Las migraciones deben reconstruir esta proyección antes de declarar que el
  índice está listo. Falta de proyección produce exclusión segura, no resultados
  autorizados por la versión actual.
- Repetir benchmarks después de introducir la política es obligatorio; las
  mediciones anteriores no acreditan el coste de la nueva consulta.
- El job de reindexado reconstruye primero políticas en lotes con cursor y límite
  superior de versiones. Después reconstruye valores sin cambiar el payload.
