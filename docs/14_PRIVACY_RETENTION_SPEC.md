# 14 — Privacidad, retención y datos sensibles


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Configuración por Form

- store submissions;
- store authenticated user relation;
- IP storage policy;
- User-Agent policy;
- retention;
- automatic deletion;
- automatic anonymization;
- sensitive fields;
- export visibility;
- email inclusion.

## 2. Minimización

La recogida de IP y User-Agent estará desactivada por defecto salvo decisión explícita de producto/configuración.

No se debe almacenar información técnica "por si acaso".

## 3. Sensitive field

Un Field marcado como sensible:

- no aparece en technical logs;
- puede excluirse de emails;
- puede excluirse de exports;
- puede requerir permiso específico de visualización;
- puede tener retención distinta;
- puede quedar fuera del índice de búsqueda.

## 4. Consent Field

Debe registrar de manera interpretable:

- accepted/not accepted;
- consent text/version reference;
- timestamp de submission;
- FormVersion.

Así puede conocerse qué texto se mostró cuando se obtuvo el consentimiento.

## 5. Retention

Políticas:

- indefinite explícito;
- N días/meses/años;
- delete;
- anonymize.

Los jobs de retención deben ser idempotentes y auditables.

## 6. Derecho operativo de eliminación/anonimización

La UI debe poder:

- localizar submissions;
- anonimizar;
- eliminar conforme a permisos;
- registrar la acción administrativa.

## 7. Archivos

La retención de Submission y de sus files debe ser coherente.

Eliminar/anonymize debe tener reglas explícitas sobre:

- fichero;
- metadata;
- checksum;
- ActionRun logs.

## 8. Backups

La documentación deberá aclarar que eliminar de la base activa no equivale necesariamente a eliminar copias históricas del backup del sitio; las políticas de backup pertenecen también al operador del sitio.

## 9. FormSpec histórico

Preservar una FormVersion no obliga a preservar datos personales de submissions. Son dominios de retención separados.
