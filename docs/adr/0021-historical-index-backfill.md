# ADR-0021 — Política de indexación histórica y backfill

Status: Accepted

## Context

SPEC 23 exige habilitar búsqueda sobre valores antiguos sin modificar su payload
ni los snapshots inmutables. ADR-0009 exige conservar la privacidad histórica.

## Decision

La proyección combina el snapshot original con el indicador de indexación de la
versión publicada, exclusivamente para UUID y tipo coincidentes. Conserva el
layout original, los valores, la sensibilidad, la persistencia y el consentimiento
original para indexar datos sensibles. Un campo eliminado o de tipo distinto
conserva su política histórica; no se convierte ni se interpreta como el nuevo
tipo. La configuración derivada nunca se publica ni sustituye al snapshot.

Un cambio del conjunto de campos indexados marca las respuestas históricas
como pendientes y encola un reindexado en la transacción de publicación, cuando
existen respuestas. Los filtros por valores excluyen respuestas pendientes,
incluidas las comparaciones negativas. Las búsquedas por metadatos siguen
mostrándolas. El dashboard muestra el número pendiente y Jobs el progreso.

Cada lote toma el bloqueo del formulario antes de reconstruir políticas y
valores, igual que publicación y persistencia. El cursor registra la versión de
política: si cambia entre lotes, reinicia el recorrido acotado. No puede escribir
con una política obsoleta ni borrar el indicador de una publicación concurrente.
Un envío aceptado con una versión antigua que termina después de publicar
conserva sus datos y encola su propia reconstrucción si cambió la indexación.

Los trabajos mantienen la identidad del publicador y revalidan su permiso de
reindexado. Si carece del permiso, el trabajo falla sin elevar privilegios;
un administrador autorizado puede solicitar otro desde Respuestas. Los valores
históricos fuera de los límites portables del índice producen un fallo seguro:
no se truncan y la respuesta permanece pendiente.

## Consequences

No hay cambio de esquema ni migración del payload. El marcado usa metadatos SQL;
la lectura de JSON y reconstrucción se ejecutan en lotes resumibles. Repetir un
lote o un trabajo es idempotente. El contador del trabajo cuenta procesamiento,
por lo que puede incluir filas repetidas después de un cambio de publicación.

La aceptación cubre habilitación histórica, privacidad y consentimiento, filtros
negativos durante el estado pendiente, envío tardío, publicación entre lotes,
replay y conservación exacta de payload y snapshot.
