# 11 — Administrator UX


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Menú principal

`Nicode EasyForms`

- Panel de control
- Formularios
- Envíos
- Recursos
- Registros
- Configuración
- Información del sistema

## 2. Dashboard

Debe mostrar información operativa, no solo accesos:

- formularios publicados;
- formularios despublicados;
- borradores;
- submissions recientes;
- submissions 7/30 días;
- spam;
- errores;
- Actions fallidas;
- emails fallidos;
- webhooks fallidos;
- formularios inválidos;
- últimas modificaciones;
- volumen de almacenamiento;
- jobs/exportaciones en curso o pendientes cuando exista subsystem de jobs.

Alertas:

- Action incompleta;
- Data Source desactivada;
- CAPTCHA seleccionado no disponible;
- versión con warning;
- ficheros huérfanos;
- errores repetidos;
- índice de búsqueda pendiente.

## 3. Formularios — listado

Columnas mínimas:

- selección;
- state;
- name;
- alias;
- ID;
- published version;
- field count;
- submission count;
- modified;
- author;
- language;
- access.

Filtros:

- search;
- state;
- language;
- access;
- author;
- tag/categoría administrativa futura.

Acciones:

- new;
- edit;
- publish;
- unpublish;
- duplicate;
- archive;
- trash;
- export;
- import.

## 4. Editor de formulario

Pestañas/áreas:

- General
- Constructor
- Lógica
- Acciones
- Datos y privacidad
- Presentación
- Publicación
- Permisos
- Versiones
- Vista previa
- Validación/diagnóstico

## 5. Constructor

Tres áreas principales:

### Paleta
Tipos disponibles.

### Canvas
Estructura.

### Inspector
Propiedades del elemento seleccionado.

Debe haber vista árbol para formularios complejos.

## 6. Publicación

`Publish` ejecuta `Compile & Publish`.

Los errores de compilación se presentan con:

- código;
- descripción;
- elemento afectado;
- enlace/navegación al elemento cuando sea posible.

## 7. Versiones

Listado:

- revision;
- date;
- author;
- comment;
- state;
- submission count;
- compare;
- preview;
- restore.

`Restore` crea nuevo draft.

## 8. Envíos — diseño de alto volumen

La vista de submissions debe estar pensada para cientos o millones de filas.

Debe permitir:

- selector de Form;
- date range;
- state;
- processing/action state;
- user;
- channel;
- free search cuando el SearchProvider lo soporte;
- filtros por campos indexados del formulario;
- filtros guardados;
- columnas configurables;
- orden por columnas autorizadas;
- navegación eficiente;
- selección de filas;
- detalle en panel/página;
- exportación según filtro;
- acciones masivas seguras.

No debe intentar cargar todas las respuestas ni construir una tabla con todos los campos de todos los formularios simultáneamente.

## 9. Vista contextual por formulario

Al seleccionar un formulario:

- la tabla puede mostrar columnas relevantes de ese Form;
- aparecen sus campos indexables como filtros;
- se usan labels de la versión/contexto actual con indicación histórica cuando haga falta;
- se puede abrir la Submission conservando la interpretación de su FormVersion.

## 10. Detalle de Submission

Áreas:

- Overview
- Respuestas
- Archivos
- Actions
- Historial
- Notas
- Datos técnicos autorizados

Debe mostrar:

- Submission ID/UUID;
- Form;
- FormVersion;
- received_at;
- estado;
- usuario si procede;
- canal;
- respuestas agrupadas según layout histórico;
- ActionRuns;
- errores;
- auditoría.

## 11. Acciones masivas

Como mínimo:

- cambiar estado;
- archive;
- export;
- anonymize cuando proceda;
- delete conforme ACL/política.

Para volúmenes grandes, una acción masiva deberá poder ejecutarse como job por criterio/filtro, no enviando millones de IDs en el navegador.

## 12. Recursos

Subsecciones:

- Option Sets;
- Data Sources;
- Email Templates;
- Form Templates.

## 13. Registros

Separar:

- technical logs;
- audit log;
- Action failures;
- job history.

## 14. Configuración

Secciones:

- General
- Submissions
- Search/index
- Security
- CAPTCHA/anti-spam defaults
- Email
- Files
- Retention
- Logs
- Performance
- Development/diagnostics

## 15. Información del sistema

Mostrar:

- EasyForms package/component/module/library version;
- DB schema version;
- supported FormSpec versions;
- Joomla version;
- PHP version;
- DB driver;
- mail availability;
- upload storage;
- cache;
- CAPTCHA providers detectados;
- filesystem permissions;
- cron/scheduler/CLI capabilities si se usan;
- health checks.
