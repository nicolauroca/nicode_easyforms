# ADR 0018 — Identidades de opción sin equivalencias de collation

Estado: aceptado.

Las opciones usan identidades literales. La collation `utf8mb4_bin` distingue
mayúsculas y acentos, pero su comparación PAD SPACE equipara `a` y `a `.
Esto afectaba a la unicidad de opciones y a búsquedas por igualdad.

En MySQL/MariaDB, `field_options.option_value`,
`option_set_items.option_value` y `submission_index.value_keyword` usan
`VARBINARY(1020)`: capacidad para los 255 puntos de código UTF-8 permitidos por
la aplicación. No hay padding ni normalización del valor. Se conservan los
índices y sus columnas. PostgreSQL conserva VARCHAR y su igualdad exacta
verificada en los casos compartidos. La igualdad de índices de texto largo
en MySQL/MariaDB usa CAST AS BINARY; LIKE conserva sus patrones escapados.

El instalador migra las instalaciones de desarrollo previas, inspeccionando
cada tipo antes de ALTER TABLE. Los cambios DDL de MySQL no forman una
transacción conjunta: una interrupción se retoma inspeccionando las columnas
restantes. Un fallo impide marcar finalizada la instalación. El DDL limpio
ya crea los tipos correctos. La conversión puede bloquear escrituras y
reconstruir índices durante la actualización controlada del paquete.

`database-selection-identity.php` guarda opciones que difieren en espacios,
mayúsculas, acentos y composición Unicode y verifica igualdad y negación en
los tres motores. Los ciclos nativos comprueban actualización y conservación
del historial.

En el fixture local MariaDB con tres millones de filas de índice, la primera
migración tardó 34,57659 segundos y conservó las cardinalidades. La siguiente
inspección tardó 19,9194 ms sin repetir ALTER. Son mediciones del entorno
sintético, no una estimación del tiempo de actualización de otros despliegues.
La búsqueda global mostró variabilidad en el optimizador que se registra,
separada del tiempo de lectura, en [SCALE_BENCHMARK.md](../SCALE_BENCHMARK.md).

Referencias: [MySQL BINARY/VARBINARY](https://dev.mysql.com/doc/refman/8.0/en/binary-varbinary.html)
y [MariaDB VARBINARY](https://mariadb.com/docs/server/reference/data-types/string-data-types/varbinary).
