# 12 — Frontend


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Publicación como página

El componente expondrá un Menu Item Type:

`Nicode EasyForms → Formulario`

Parámetro principal:

- `Formulario`: selector dinámico de formularios utilizables.

El Item de menú almacena el identificador del formulario, no una copia de sus campos.

## 2. Publicación como módulo

`mod_nicode_easy_forms`

Parámetro principal:

- Formulario.

Parámetros secundarios, exclusivamente de contexto/presentación:

- mostrar título;
- mostrar descripción;
- class suffix/controlado;
- layout;
- comportamiento si no está disponible.

El módulo NO puede redefinir reglas, destinatarios, validadores o estructura.

## 3. Form no disponible

Si un Menu Item o module referencia un Form:

- unpublished;
- fuera de fechas;
- sin permiso;
- inexistente;

el runtime aplica una política definida:

- 404;
- mensaje de indisponibilidad;
- módulo no renderizado.

El POST se rechaza independientemente de lo que hubiera mostrado una caché antigua.

## 4. Múltiples instancias

Cada render obtiene `instance_id`.

El DOM ID se deriva de:

- form identity;
- field identity;
- instance identity.

Dos instancias del mismo Form pueden coexistir sin:

- IDs duplicados;
- rules cruzadas;
- CAPTCHA namespace conflictivo;
- mensajes cruzados.

## 5. Assets

Se cargarán mediante Web Asset Manager.

Solo se activarán cuando exista una instancia que los necesite.

Dependencias declaradas en `joomla.asset.json`.

## 6. Presentación

Por defecto se hereda el template Joomla.

EasyForms aporta estilos mínimos estructurales.

Opciones:

- labels top/side;
- required marker;
- help position;
- spacing;
- columns;
- progress;
- validation summary;
- success/error container.

No se impondrá un framework CSS externo.

## 7. Submit

Soportar:

- traditional POST;
- AJAX.

Ambos contra controller/ruta Joomla y mismo Submission Engine.

## 8. Estados UX

Como mínimo:

- initial;
- validating;
- submitting;
- success;
- validation error;
- anti-spam/captcha error;
- transient processing error;
- unavailable.

## 9. JS

JavaScript se limitará a:

- client validation UX;
- Rule Engine cliente;
- dependent options;
- AJAX;
- progressive enhancement;
- accessibility behavior.

La seguridad y la verdad del estado permanecen en servidor.

## 10. Sin JS

Se definirá qué funcionalidades soportan fallback sin JavaScript.

Los formularios simples deberían poder enviarse; funcionalidades altamente dinámicas podrán requerir JS si la especificación lo declara, pero el sistema nunca confiará en JS para validar.
