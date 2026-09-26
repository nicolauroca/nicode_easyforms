# 15 — ACL


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Base

EasyForms utilizará ACL Joomla.

Permisos de componente iniciales:

- `core.admin`;
- `core.options`;
- `core.manage`;
- `core.create`;
- `core.edit`;
- `core.edit.state`;
- `core.delete`.

Permisos específicos propuestos:

- `easyforms.forms.manage`;
- `easyforms.forms.publish`;
- `easyforms.submissions.view`;
- `easyforms.submissions.manage`;
- `easyforms.submissions.export`;
- `easyforms.submissions.delete`;
- `easyforms.submissions.view_sensitive`;
- `easyforms.resources.manage`;
- `easyforms.logs.view`;
- `easyforms.jobs.manage`.

Los nombres definitivos se fijarán antes de código.

## 2. ACL por Form

Los Forms podrán actuar como assets hijos del componente.

Casos:

- equipo A administra Form A;
- equipo B administra Form B;
- compliance puede ver respuestas pero no editar Forms;
- marketing puede exportar solo ciertos Forms.

## 3. Frontend access

Cada Form tendrá Joomla access level y reglas adicionales cuando proceda.

Comprobar en:

- render;
- Data Source requests;
- submit;
- file download;
- submission view futura de frontend.

## 4. Administrator

Cada Controller comprueba autorización.

La View puede ocultar acciones no permitidas, pero eso es UX, no seguridad.

## 5. Datos sensibles

`view_sensitive` puede ser un permiso adicional al simple `submissions.view`.

La UI deberá enmascarar/ocultar campos sensibles a usuarios sin permiso.

## 6. Export

Exportar es una capacidad distinta de visualizar y debe tener permiso independiente.

## 7. Auditoría

Las operaciones privilegiadas deberán quedar registradas:

- export;
- delete;
- anonymize;
- retry Action;
- reveal sensitive;
- cambios de configuración.
