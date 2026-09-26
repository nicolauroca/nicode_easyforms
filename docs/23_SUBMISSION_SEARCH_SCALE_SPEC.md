# 23 — Submissions, búsqueda y escala


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Requisito principal

EasyForms debe seguir siendo operable cuando existan:

- cientos de Forms;
- millones de Submissions;
- millones o decenas de millones de valores indexados;
- años de histórico.

"Operable" significa poder encontrar información sin descargarla completa ni recorrer manualmente páginas infinitas.

## 2. Separación obligatoria

### Canonical Submission Store
Preserva íntegramente la respuesta.

### Search Projection
Optimizada para consultar.

### Search Provider
Contrato que ejecuta búsqueda.

El índice es regenerable; el canonical payload no se deriva del índice.

## 3. Campos indexables

Cada field podrá declarar, según capacidades:

- searchable;
- filterable;
- sortable;
- facetable/aggregatable futuro;
- not indexed.

Defaults conservadores:

- IDs, email, estados y campos cortos pueden ser buenos candidatos;
- textareas largos no deberían crear automáticamente índices costosos sin necesidad;
- fields sensibles no se indexan por defecto.

## 4. Tipos indexados

No almacenar todo como string.

Tipos:

- keyword;
- text;
- integer;
- decimal;
- boolean;
- date;
- datetime;
- multi-value.

Esto permite comparaciones correctas.

## 5. Búsqueda administrativa

### Nivel global

Buscar por:

- Submission UUID/ID;
- Form;
- fechas;
- state;
- user;
- action status;
- texto global cuando provider lo soporte.

### Nivel Form

Cuando se selecciona un Form:

- aparecen sus fields indexados;
- operadores coherentes con Field Type;
- columnas configurables;
- filtros combinables.

Ejemplos:

- email contiene dominio;
- importe > 1000;
- fecha entre A y B;
- provincia = Madrid;
- consentimiento = sí.

## 6. Filtros combinados

Debe permitirse:

- AND;
- múltiples condiciones;
- presets/filtros guardados.

OR avanzado podrá planificarse si complica la primera release, pero el modelo de query no debe impedirlo.

## 7. Saved Views

El Administrator debería permitir guardar una vista:

- Form;
- filtros;
- columnas;
- order;
- nombre.

Puede ser privada por usuario o compartida si ACL lo permite.

## 8. Paginación a escala

Para tablas masivas se evitará depender únicamente de `OFFSET N` a profundidades enormes.

El SearchProvider debe poder implementar keyset/cursor pagination usando orden estable, por ejemplo:

`received_at DESC, id DESC`.

La UI puede seguir presentando navegación cómoda sin obligar a contar/offsetear millones constantemente.

## 9. Total counts

Un `COUNT(*)` exacto sobre filtros complejos puede ser costoso.

La interfaz debe distinguir:

- exact count cuando sea razonable;
- estimate/unknown cuando el provider lo determine;
- conteos preagregados futuros.

No bloquear una pantalla únicamente para obtener un total exacto decorativo.

## 10. Ordenación

Solo campos declarados sortable.

Nunca construir `ORDER BY` directamente desde input no validado.

## 11. Global search

El contrato SearchProvider permite:

### SQL Core Provider
Filtros estructurados e indexación relacional.

### Full-text DB provider opcional
Si se decide implementar por motor.

### External Search Provider futuro
Por ejemplo un motor dedicado.

El dominio no se acoplará a una marca concreta.

## 12. Reindexado

Debe poder:

- reindexar un Form;
- reindexar periodo;
- reindexar Submission;
- reconstruir índice completo.

Reindexar no modifica canonical payload.

Para grandes volúmenes se ejecuta como Job resumible.

## 13. Cambios de configuración de indexación

Si se marca un field antiguo como searchable:

- nuevas submissions se indexan inmediatamente;
- histórico queda `index pending`;
- un job backfill reconstruye el índice.

Administrator debe mostrar progreso/estado.

## 14. Exportaciones

Exportar el resultado de un filtro:

- no carga todo en memoria;
- procesa por chunks/cursor;
- genera file temporal/protegido;
- registra Job;
- permite descargar cuando finaliza;
- expira según política.

Formatos iniciales:

- CSV;
- JSON.

Futuros:

- XLSX;
- NDJSON;
- otros providers.

## 15. Millones de filas y acciones masivas

Una operación sobre "todo lo que coincide con este filtro" no enviará todos los IDs desde el browser.

Se persistirá:

- query/filtro inmutable;
- snapshot temporal o strategy;
- Job;
- progreso;
- errores.

## 16. Retención e índices

Eliminar/anonimizar canonical data debe actualizar/eliminar su search projection.

No pueden quedar PII buscable después de su eliminación lógica/física según política.

## 17. Vista de Submission

La pantalla de detalle puede reconstruir el layout desde FormVersion, pero no debe ejecutar Data Sources remotas históricas para interpretar un valor.

Labels/opciones necesarias deben estar snapshotizadas/resolubles históricamente.

## 18. Observabilidad

Métricas/health:

- total submissions por Form;
- index lag;
- failed index operations;
- export jobs;
- average search latency opcional;
- storage growth.

## 19. Índices de DB

Todo índice físico se justificará por patrones de query.

No se crearán índices indiscriminados en cada columna.

Se documentarán con query plans en pruebas de escala.

## 20. Degradación

Si SearchProvider externo no está disponible:

- no perder canonical submissions;
- registrar el fallo;
- marcar index pending;
- según arquitectura, permitir búsquedas core limitadas o informar indisponibilidad;
- reindexar al recuperar servicio.
