# 22 — Release y Definition of Done


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Versionado

Semantic Versioning:

`MAJOR.MINOR.PATCH`.

## 2. Tres versiones relevantes

- package/software version;
- DB schema version;
- FormSpec schema version.

No deben confundirse.

## 3. Definition of Done de feature

Una feature no está terminada solo porque funcione manualmente.

Debe tener:

- requirement ID;
- spec actualizada;
- acceptance criteria;
- tests adecuados;
- implementación;
- ACL revisado;
- security review proporcional;
- i18n;
- accessibility cuando aplique;
- migration si aplica;
- docs;
- changelog.

## 4. Release checklist

- unit/integration/system tests;
- PHP/Joomla compatibility matrix;
- DB matrix declarada;
- package installation clean;
- upgrade from supported previous versions;
- uninstall modes;
- schema checks;
- FormSpec migration;
- assets build;
- language completeness;
- no deprecated APIs bloqueantes;
- security checks;
- performance smoke tests;
- changelog.

## 5. Breaking changes

Requieren:

- ADR;
- migration path;
- documentación;
- version bump acorde;
- compatibility analysis.

## 6. Soporte

`Information System` debe permitir recopilar diagnóstico sin exponer secretos ni payloads personales.

## 7. Release reproducible

El repositorio deberá definir cómo construir el package final a partir de source, assets y manifests.
