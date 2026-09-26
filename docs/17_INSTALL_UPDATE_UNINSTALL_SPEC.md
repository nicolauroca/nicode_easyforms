# 17 — Instalación, actualización y desinstalación


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Package

Artefacto distribuible:

`pkg_nicode_easy_forms.zip`

Constituyentes iniciales:

- `com_nicode_easy_forms`;
- `mod_nicode_easy_forms`;
- `lib_nicode_easy_forms`.

Las dependencias internas deberán quedar declaradas.

## 2. Instalación

Proceso:

1. comprobar versión Joomla;
2. comprobar PHP;
3. comprobar DB soportada por nuestra matriz;
4. instalar library;
5. instalar component;
6. instalar module;
7. crear schema;
8. registrar schema version;
9. instalar configuración por defecto;
10. registrar assets/resources;
11. limpiar/inicializar caches necesarias;
12. health check;
13. mostrar resultado.

## 3. No depender de Compatibility Plugin

La compatibilidad Joomla 6 debe basarse en APIs actuales.

## 4. SQL

Habrá:

- install schema;
- update/migration scripts versionados;
- uninstall strategy.

Cambios de schema no se harán ad-hoc desde un Controller normal.

## 5. Actualizaciones

Una instalación puede saltar varias versiones.

Las migraciones deben aplicarse en orden.

Cada release puede incluir:

- DB migration;
- FormSpec migration;
- config migration;
- index rebuild requirement;
- background post-update job cuando un cambio sea demasiado grande para una petición de actualización.

## 6. Datos masivos durante updates

Nunca se asumirá que millones de submissions pueden reescribirse síncronamente durante la instalación.

Si una versión requiere:

- reindexar;
- recalcular;
- transformar payloads masivos;

se hará mediante proceso versionado, resumible e idempotente fuera del paso crítico de instalación.

## 7. Desinstalación

Configurable:

### Purge data
Eliminar:

- schema;
- cache;
- archivos propiedad inequívoca de EasyForms según política;
- temporales.

### Preserve data
Conservar:

- forms;
- versions;
- submissions;
- files según política;
- schema version.

Una reinstalación debe detectar datos conservados.

## 8. Almacenamiento externo

No borrar automáticamente objetos externos que no sean inequívocamente propiedad del package.

## 9. Integridad del package

Cuando sea soportado por el manifest/package, impedir desinstalar una pieza hija necesaria dejando el resto roto.

## 10. Uninstall warnings

Antes de un purge destructivo, Joomla debe mostrar información suficiente sobre:

- formularios;
- submissions;
- files;
- tamaño aproximado;
- irreversibilidad.

La confirmación final se implementará conforme a capacidades Joomla.
