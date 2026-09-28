# ADR 0016 — Retención de auditoría e intentos de Actions

Estado: aceptado.

SPEC 27 exige retención independiente para auditoría, registro técnico e historial
de Actions. Se añaden dos políticas de componente: `audit_log_days` y
`action_history_days`, de 0 a 3650 días. Cero conserva indefinidamente y es el
valor inicial explícito de ambas; el operador decide su plazo. La política de
respuestas y su anonimización/borrado siguen aplicándose por separado.

El scheduler encola un job interno por política finita como máximo cada hora.
Los jobs fijan su fecha de corte UTC al comenzar y recorren `(created_at,id)` por
cursor, hasta 500 candidatos por lote. Mutaciones y checkpoint se confirman en
la misma transacción. Los intentos de acción en ejecución nunca se eliminan.

El último ActionRun de cada `(submission_id,action_uuid)` es también el marcador
de idempotencia y el estado de reintento. Se conserva hasta que la respuesta sea
borrada o anonimizada. Solo se eliminan intentos anteriores, terminales y vencidos;
suprimir el último permitiría repetir una entrega ya completada. Este marcador
no contiene payload, destinatarios, errores libres ni credenciales. La política
se presenta como retención del historial de intentos anteriores, sin prometer
eliminar el estado operativo necesario para conservar la corrección del motor.

La consulta pagina candidatos antes de descartar los últimos intentos para evitar
que un LIMIT aparente escanee millones de marcadores no eliminables. Los índices
por fecha/ID y por identidad/attempt hacen acotado cada lote. El contador de trabajo
registra filas examinadas y el cursor conserva también las filas eliminadas.
Los jobs de auditoría retiran únicamente registros vencidos; no leen respuestas.
Cambiar una política a cero o ampliarla debe impedir que un job antiguo elimine
filas bajo un plazo más corto: cada lote compara su política capturada con la
configuración vigente antes de borrar y termina sin cambios si ya no coincide.
