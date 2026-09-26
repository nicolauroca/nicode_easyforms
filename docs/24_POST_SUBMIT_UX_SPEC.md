# 24 — Post-submit, mensajes y experiencia posterior


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Principio

Configurar un Form incluye configurar qué ocurre **después** de pulsar Enviar.

No debe requerir editar templates o PHP.

## 2. Categorías de resultado

- success;
- validation_error;
- captcha_error;
- anti_spam_rejected;
- rate_limited;
- upload_error;
- persistence_error;
- action_partial_failure;
- action_blocking_failure;
- form_unavailable;
- permission_error;
- session/csrf error;
- unexpected_error.

## 3. Mensajes configurables

Cada Form podrá personalizar, con fallback global:

- encabezado de éxito;
- cuerpo de éxito;
- mensaje de validación;
- mensaje CAPTCHA;
- mensaje de rate limit;
- mensaje upload;
- mensaje temporal/retry;
- mensaje indisponible;
- error genérico.

No mostrar detalles técnicos al visitante.

## 4. Comportamiento de éxito

Opciones:

- mantener Form y mostrar mensaje;
- ocultar Form y mostrar mensaje;
- resetear Form;
- conservar determinados valores;
- mostrar un summary de respuestas autorizado;
- redirigir a Menu Item;
- redirigir a URL autorizada;
- ir a página/estado de confirmación;
- entregar identificador/reference code.

## 5. Redirect

Configurable:

- Menu Item;
- internal route;
- approved URL.

Evitar open redirect: no redirigir a una URL enviada libremente por el visitante.

## 6. Mensaje condicional

El mensaje de success podrá seleccionarse condicionalmente.

Ejemplo:

- tipo soporte → mensaje A;
- tipo comercial → mensaje B.

Debe usar lógica declarativa.

## 7. Emails de notificación

Cero o varios.

Cada email:

- condition;
- to;
- cc;
- bcc;
- reply-to;
- subject;
- template/body;
- attachment rules;
- field inclusion;
- failure policy.

## 8. Autorespuesta

Un email al visitante puede configurarse como Action separada.

Debe:

- obtener destinatario de un Field Type email validado;
- permitir condition;
- tener subject/body;
- admitir tokens seguros;
- registrar ActionRun.

## 9. Respuesta al administrador

No confundir:

- notificación interna;
- autorespuesta al visitante.

Son Actions independientes.

## 10. Attachments

Se podrá decidir:

- adjuntar uploads;
- no adjuntar y proporcionar referencia segura;
- límites.

Por seguridad y tamaño, el default debería evitar adjuntar indiscriminadamente archivos grandes.

## 11. Partial Action Failure

Si Submission se guarda pero una Action non-blocking falla:

- al visitante se puede confirmar recepción;
- se registra el fallo;
- Administrator lo muestra;
- retry disponible.

El mensaje al usuario no debe afirmar que un email externo fue enviado si no lo fue, salvo que ese detalle sea irrelevante para la promesa comunicada.

## 12. Blocking Failure

Se debe especificar si:

- rollback es posible;
- Submission queda en error;
- se conserva para diagnóstico;
- el usuario puede retry sin duplicar.

La transacción exacta dependerá del tipo de Action; no se prometerá atomicidad distribuida imposible con servicios externos.

## 13. Form reset

Configurar:

- reset all;
- preserve selected fields;
- no reset.

En redirect no aplica salvo persistencia cliente explícita.

## 14. Reference code

Puede mostrarse un identificador seguro y no predecible de Submission para soporte.

No debe exponer un ID secuencial si eso crea riesgo.

## 15. Eventos post-submit

El motor expondrá eventos/hooks para integraciones, además de Actions declarativas.

## 16. UX AJAX

Durante submit:

- evitar doble click;
- indicar progreso;
- mantener accesibilidad;
- restaurar botón ante error;
- mover foco a resultado;
- tratar timeout sin asumir automáticamente que servidor no recibió el POST.

## 17. Confirmación antes de envío

Opción futura/compatible:

- página de revisión previa;
- checkbox de confirmación;
- summary.

El modelo multipaso deberá permitirlo.

## 18. Mensajes globales y overrides

Jerarquía:

1. Form override;
2. template/resource si aplica;
3. component global default;
4. language default.

## 19. Traducción

Todos los mensajes son traducibles.

## 20. Logging

La configuración de mensajes nunca debe provocar que el log guarde contenido personal innecesario.
