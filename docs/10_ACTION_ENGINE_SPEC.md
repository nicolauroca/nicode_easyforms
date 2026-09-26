# 10 — Action Engine


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Concepto

Después de una Submission válida pueden ejecutarse cero, una o múltiples Actions.

Cada Action tendrá:

- UUID;
- type;
- enabled;
- order;
- condition;
- config;
- failure policy;
- retry policy cuando sea compatible.

## 2. Actions iniciales

- persist submission;
- email notification;
- email autoresponse/acknowledgement;
- redirect/post-submit navigation;
- webhook HTTP.

La persistencia puede modelarse como fase del pipeline y exponerse conceptualmente como Action/configuración; la implementación final deberá mantener atomicidad y semántica claras.

## 3. Actions futuras

El Registry permitirá:

- CRM;
- ERP;
- newsletter;
- Slack/Teams;
- user creation;
- content creation;
- ticketing;
- external API;
- custom providers.

## 4. Conditions

Una Action puede ejecutarse solo si se cumplen condiciones.

Ejemplo:

- si `tipo = comercial`, email a ventas;
- si `tipo = soporte`, email a soporte;
- si país = ES, webhook A;
- si país = PT, webhook B.

Las condiciones reutilizarán semántica compatible con Rule Engine sin permitir lógica arbitraria.

## 5. Failure policy

### Blocking

El fallo afecta al resultado global según transacción/política.

### Non-blocking

La Submission puede considerarse recibida; el fallo queda registrado y reintentable.

Cada tipo de Action debe declarar qué modos admite.

## 6. ActionRun

Cada ejecución registra:

- action;
- submission;
- attempt;
- started_at;
- finished_at;
- status;
- result code;
- mensaje técnico saneado;
- retry eligibility.

## 7. Reintentos

Debe permitirse reintentar una Action fallida compatible sin repetir las que ya finalizaron correctamente.

La administración ofrecerá:

- retry individual;
- retry de fallidas de una Submission;
- acciones masivas controladas futuras.

## 8. Email Action

Configurable:

- to;
- cc;
- bcc;
- reply-to;
- subject;
- HTML;
- plain text;
- template;
- campos incluidos;
- attachments permitidos;
- conditions.

El remitente debe proceder de una configuración segura, no de una dirección arbitraria del visitante.

Un email de usuario validado podrá usarse como Reply-To.

## 9. Tokens

Las plantillas usarán tokens declarativos:

- form;
- submission;
- date;
- user;
- field value;
- selected option label;
- response summary.

No PHP ejecutable.

## 10. Webhook

Debe contemplar:

- URL permitida/configurada;
- method;
- headers seguros;
- payload mapping;
- timeout;
- firma opcional;
- retry;
- bloqueo SSRF;
- logging sin secretos.

## 11. Orden

Las Actions se ejecutan en orden definido y la política decidirá si un fallo blocking detiene el resto.

La semántica debe ser visible en Administrator.
