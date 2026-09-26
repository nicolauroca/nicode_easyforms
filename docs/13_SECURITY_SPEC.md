# 13 — Seguridad


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Principios

- deny by default;
- server authoritative;
- least privilege;
- contextual escaping;
- typed validation;
- secrets out of FormSpec portable;
- no arbitrary executable code.

## 2. Amenazas a cubrir

- CSRF;
- XSS almacenado/reflejado;
- SQL injection;
- parameter tampering;
- option tampering;
- rule bypass;
- ACL bypass;
- direct POST a Form despublicado;
- malicious uploads;
- path traversal;
- email header injection;
- SSRF en webhooks/Data Sources;
- replay/double submit;
- brute spam;
- over-posting;
- mass assignment;
- information leakage;
- insecure direct object references;
- export abuse;
- formula injection en CSV;
- log injection;
- secret leakage.

## 3. Input

No se sobrescribirá `$_POST`.

Se extraerán exclusivamente keys esperadas según FormSpec.

Campos desconocidos serán ignorados o rechazados según política.

## 4. SQL

Consultas parametrizadas y APIs DatabaseInterface.

Los identificadores dinámicos deben proceder de allowlists internas; los valores se bindearán.

## 5. Output

Escaping contextual:

- HTML text;
- attribute;
- URL;
- JSON;
- email;
- CSV/export.

Safe HTML será una capacidad explícita y restringida de contenido administrativo, nunca una excusa para imprimir input del visitante sin escapar.

## 6. CSRF

Se utilizarán los mecanismos de token/sesión de Joomla donde el flujo de sesión lo permita.

CSRF y CAPTCHA son controles distintos.

## 7. ACL

Toda acción administrativa comprueba permiso en servidor.

Ocultar botones no sustituye autorización.

## 8. Uploads

- allowlist extension;
- MIME inspection;
- size;
- count;
- generated storage names;
- storage no público/predecible;
- no ejecución;
- download controller con ACL;
- optional malware scanner provider futuro.

## 9. Email

- sender configurado;
- Reply-To validado;
- no concatenar headers con input arbitrario;
- recipient sources controladas;
- tokens escapados según contexto.

## 10. Webhooks y HTTP Data Sources

Mitigación SSRF:

- esquemas permitidos;
- bloqueo de loopback/link-local/private cuando corresponda;
- redirects controlados;
- timeouts;
- límites;
- DNS/rebinding considerations en implementación;
- secretos no logados.

## 11. Exports

CSV debe mitigar formula injection para valores que comienzan con caracteres peligrosos según política de exportación.

Las exportaciones respetan ACL y datos sensibles.

## 12. CAPTCHA

EasyForms NO implementará un algoritmo CAPTCHA propio.

Se integrará con el sistema/provider de CAPTCHA Joomla según `25_JOOMLA_CAPTCHA_ANTISPAM_SPEC.md`.

## 13. Rate limiting

Si Joomla no ofrece un mecanismo genérico aplicable al caso, EasyForms podrá implementar un limiter propio como control anti-abuso, desacoplado mediante servicio/provider. No debe confundirse con CAPTCHA.

## 14. Logs

No registrar por defecto:

- password;
- tokens;
- secrets;
- payload completo;
- datos sensibles de campos.

## 15. Security headers

EasyForms no debe romper CSP u otras políticas del sitio mediante inline JS innecesario. Assets y scripts deben diseñarse para integrarse con la política del sitio.
