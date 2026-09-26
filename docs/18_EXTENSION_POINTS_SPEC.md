# 18 — Extension Points


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Objetivo

Extender EasyForms sin modificar core.

## 2. Registries

### FieldTypeRegistry
Campos.

### ValidatorRegistry
Validaciones.

### RuleOperatorRegistry
Comparadores.

### RuleEffectRegistry
Efectos.

### DataSourceRegistry
Fuentes.

### ActionRegistry
Acciones post-submit.

### StorageProviderRegistry
Files/payload storage si evoluciona.

### SearchProviderRegistry
Búsqueda de submissions.

## 3. Contratos

Cada provider debe declarar:

- ID estable;
- versión;
- config schema;
- capabilities;
- validation;
- lifecycle;
- security constraints;
- serializable metadata.

## 4. Eventos

Eventos conceptuales:

- BeforeFormRender;
- AfterFormRender;
- BeforeValidation;
- AfterValidation;
- BeforeSubmissionPersist;
- AfterSubmissionPersist;
- BeforeAction;
- AfterAction;
- ResolveDataSource;
- BeforeExport;
- AfterExport.

Los nombres y tipos finales deben ajustarse al sistema de eventos Joomla actual.

## 5. Plugins Joomla

Cuando una capacidad encaje naturalmente en el ecosistema Joomla, se preferirá un plugin/provider Joomla antes que un mecanismo paralelo.

CAPTCHA es caso obligatorio de esta estrategia.

## 6. Compatibility contracts

Un FormSpec publicado debe declarar dependencias de providers no-core.

Si falta un provider requerido:

- el Form no debe ejecutarse silenciosamente de forma incorrecta;
- Administrator debe mostrar error;
- render/submit debe fail closed según criticidad.

## 7. Versionado de provider

Cambios breaking deben versionarse.

El Compiler debe poder detectar configuración antigua incompatible.

## 8. No `if form_id`

Está prohibido ampliar el producto introduciendo lógica del tipo:

`if ($formId === 12) Ellipsis`

Las variaciones deben expresarse mediante datos o providers.
