# Plan General del Proyecto: Futpedia

**Repositorio:** [jmfr65/futpedia](https://github.com/jmfr65/futpedia)
**Fecha de Última Actualización:** *(Actualizar esta fecha al modificar el plan)*

## Visión General
Futpedia será una aplicación web para gestionar y mostrar información relacionada con el fútbol, incluyendo noticias, equipos, jugadores, resultados, y más. Contará con un sistema de usuarios con diferentes roles y un panel de administración para la gestión de contenidos.

## Fases del Proyecto y Tareas

### Fase 0: Organización y Base de la Web

**0.0. Preparativos Iniciales**

-   [ ] Configurar el entorno de desarrollo local (servidor web como Apache/Nginx, PHP, MySQL/MariaDB).
-   [ ] Inicializar un repositorio Git para el control de versiones del proyecto.
-   [ ] Definir y documentar las convenciones de codificación (PHP, JS, CSS, SQL).
-   [ ] Crear un archivo README.md inicial con la descripción del proyecto y enlaces a la documentación (este plan).

**0.1. Estructura de Carpetas del Proyecto**

-   [ ] Crear la estructura de carpetas base según lo definido:
    -   `/public_html` (o raíz del dominio, ej. `htdocs`, `www`, o `public` en nuestro caso)
        -   [ ] `index.php` (Punto de entrada principal)
        -   [ ] `.htaccess` (Si se usa Apache, para reescritura de URLs amigables y seguridad)
        -   `/admin` (Panel de administración)
            -   [ ] `index.php` (Punto de entrada del panel de admin)
    -   `/includes` (Funciones, conexión a BD, helpers, lógica de negocio)
        -   [ ] `config.php` (Configuraciones generales, rutas, etc. NO credenciales de BD)
        -   [ ] `db_config.php` (Credenciales de BD - COLOCAR FUERA DEL DOCUMENT ROOT SI ES POSIBLE)
        -   [ ] `database.php` (Clase/funciones para la conexión PDO y operaciones comunes de BD)
        -   [ ] `functions.php` (Funciones globales de utilidad)
        -   [ ] `session.php` (Manejo de sesiones)
        -   [ ] `auth.php` (Lógica de autenticación y roles)
        -   [ ] `localization.php` (Funciones para manejo de idiomas y carga de textos)
        -   [ ] `senior_admin_logic.php` (Contendrá la lógica y reglas del Asistente Virtual SeniorAdmin)
    -   `/assets` (Recursos estáticos: `/css`, `/js`, `/images`, `/fonts`)
    -   `/templates` (Plantillas HTML comunes: `header.php`, `footer.php`, `header_admin.php`, `footer_admin.php`, `email_templates/`, etc.)
    -   `/api` (Futuro: Endpoints para la app móvil, ej. `/v1/`)
    -   `/logs` (Para logs de errores de PHP, y logs de SeniorAdmin IA, fuera del document root si es posible)
    -   `/uploads` (Para archivos subidos por usuarios, ej. logos de equipos, fuera del document root o con acceso restringido)
    -   `/languages` (Archivos de idioma: `es.php`, `en.php`, `fr.php`, `pt.php`)
    -   `/cron_jobs` (Scripts para tareas programadas, ej. `procesar_contribuciones_ia.php`)

**0.2. Conexión Segura a la Base de Datos y Definición de Tablas**

-   [ ] Crear `includes/db_config.php` (idealmente fuera de `public_html`) con credenciales de BD.
-   [ ] Desarrollar `includes/database.php` con conexión PDO.
-   [ ] Creación/Adaptación de Tablas en la Base de Datos (SQL DDL):
    -   [ ] Tabla `usuarios` (id_usuario, nombre_usuario, email, password_hash, rol ('superadmin', 'senior_admin_ia', 'editor', 'registrado'), puntos, idioma_interfaz_preferido, fecha_registro, ultimo_login, activo, token_verificacion_email, email_verificado, token_reset_password, fecha_expiracion_token_reset, fecha_ultima_actividad). (Nota: 'senior_admin_ia' podría ser un usuario placeholder si la IA necesita un 'created_by' o simplemente se usa el ID del SuperAdmin humano para acciones de la IA).
    -   [ ] Tabla `confederaciones` (id, nombres multilingües (es,en,fr,pt), siglas, created_by, updated_by, created_at, updated_at. FKs a usuarios).
    -   [ ] Tabla `paises` (id, nombres multilingües, codigos_iso, id_confederacion_fk, created_by, updated_by, created_at, updated_at. FKs a usuarios, confederaciones).
    -   [ ] Tabla `regiones` (id, id_pais_fk, nombres multilingües, codigo_region, created_by, updated_by, created_at, updated_at. FKs a usuarios, paises).
    -   [ ] Tabla `competiciones` (antes `rsssf_competiciones_fuente`):
        Campos: `id_competicion`, `id_competicion_padre_fk`, `nombre_competicion_es/en/fr/pt`, `nombre_competicion_ingresado` (etiqueta interna), `id_pais_fk`, `id_confederacion_fk`, `id_region_fk`, `tipo_clasificacion`, `temporada_inicio_anio`, `temporada_fin_anio`, `nivel_liga`, `tipo_entrada` (RAIZ, FASE, etc.), `url_fuente_datos`, `datos_fuente_texto_original`, `fuente_principal_origen` (ej. 'RSSSF', 'Usuario'), todos los campos `pos_...` para clasificaciones, `notas_formato_competicion`, `tabla_clasificacion_texto_original`, `created_by`, `updated_by`, `created_at`, `updated_at`. FKs a usuarios, paises, confederaciones, regiones, y a sí misma.
    -   [ ] Tabla `equipos` (maestra canónica):
        Campos: `id_equipo`, nombres oficiales y cortos multilingües, siglas, `id_pais_fk`, `id_region_fk`, `fundacion_anio`, `desaparicion_anio`, `logo_url`, `colores_principales_json`, `id_estadio_principal_fk` (futuro), `estado_equipo` ('activo', 'desaparecido', etc.), `notas_adicionales_json`, `created_by`, `updated_by`, `created_at`, `updated_at`, `id_equipo_fusionado_con_fk`, `ids_equipos_anteriores_json`. FKs a usuarios, paises, regiones.
    -   [ ] Tabla `partidos` (antes `rsssf_partidos`):
        Campos: `id_partido`, `id_competicion_fk`, `ronda_texto`, `fecha_partido`, `equipo_local_texto_original`, `goles_local`, `equipo_visitante_texto_original`, `goles_visitante`, `resultado_especial_flag`, `detalles_partido_texto`, `id_equipo_local_maestro_fk` (a `equipos.id_equipo`), `id_equipo_visitante_maestro_fk` (a `equipos.id_equipo`), `estado_mapeo_equipos`, `created_by`, `updated_by`, `created_at`, `updated_at`. FKs a usuarios, competiciones.
    -   [ ] Tabla `contribuciones` (cola de moderación):
        Campos: `id_contribucion`, `id_usuario_fk`, `email_visitante`, `tipo_entidad_principal`, `id_entidad_principal_fk`, `accion_solicitada`, `datos_propuestos_json`, `datos_actuales_json`, `id_partido_original_fk` (si la contribución es sobre un partido existente), `equipo_texto_original_para_mapear`, `id_equipo_sugerido_fk`, `comentario_usuario`, `estado_moderacion` ('pendiente_ia', 'aprobada_ia_directo', 'rechazada_ia', 'requiere_revision_humana', 'aprobada_humano', 'rechazada_humano'), `id_moderador_ia_fk` (puede ser el ID del usuario 'senior_admin_ia' o NULL), `id_moderador_humano_fk`, `fecha_contribucion`, `fecha_moderacion_ia`, `fecha_moderacion_humana`, `comentario_moderador` (puede ser llenado por la IA o humano), `puntos_otorgados`. FKs a usuarios, y dinámicamente a otras tablas según `tipo_entidad_principal`.
    -   [ ] Tabla `editor_permisos_seccion` (`id_usuario_editor_fk`, `tipo_entidad_editable` ('CONFEDERACION', 'PAIS', 'COMPETICION_RAIZ'), `id_entidad_fk`, `puede_crear_competiciones`, `puede_crear_equipos`, `puede_moderar_contribuciones`. FKs a usuarios y otras tablas).
    -   [ ] Tabla `log_actividad_admin` (`id_log`, `id_usuario_fk` (incluye acciones de la IA referenciando a 'senior_admin_ia' o al SuperAdmin que la configuró), `accion_realizada`, `detalles_json`, `fecha_accion`. FK a usuarios).
    -   [ ] (Opcional) Tabla `log_puntos` (`id`, `user_id`, `puntos_ganados`, `motivo`, `fecha`).
-   [ ] Documentar el esquema final de la BD (diagrama ERD y descripción de tablas/campos).
-   [ ] Cargar los datos existentes de las tablas `rsssf_...` en las nuevas tablas renombradas (competiciones, partidos), realizando las adaptaciones de datos necesarias.

**0.3. Página de “En Construcción”**

-   [ ] Crear `en_construccion.html` multilingüe en `public_html`.
-   [ ] Configurar `index.php` o `.htaccess` para mostrarla si es necesario.

**0.4. Diseño y Experiencia de Usuario (Consideraciones Iniciales de Monetización)**

-   [ ] (M) Durante el diseño inicial de wireframes/mockups, identificar visualmente las ubicaciones para los 2 anuncios nativos (AdSense) permitidos por página (header, footer, sidebars, in-content), asegurando que no comprometan la usabilidad ni el diseño "rompedor".
-   [ ] (M) Definir cómo se adaptarán estas ubicaciones de anuncios en la versión móvil.

---
### Fase 1: Sistema de Usuarios y Autenticación

**1.0. Preparación del Sistema de Idiomas y Sesiones**

-   [ ] Desarrollar `includes/localization.php` (detectar/establecer idioma, función `__()`, `getCurrentLanguage()`).
-   [ ] Crear archivos base de idioma (`languages/es.php`, `en.php`, `fr.php`, `pt.php`).
-   [ ] Desarrollar `includes/session.php` (inicio seguro, manejo de variables, CSRF tokens).

**1.1. Registro, Login y Logout de Usuarios**

-   [ ] Registro: Formulario (`registro.php` con CAPTCHA), script de procesamiento (`procesar_registro.php` con validación, hash de contraseña, inserción en BD con rol 'registrado', envío de email de verificación).
-   [ ] Login: Formulario (`login.php`), script de procesamiento (`procesar_login.php` con validación, `password_verify()`, creación de sesión, actualización `ultimo_login`).
-   [ ] Logout: Script (`logout.php`) para destruir sesión.
-   [ ] Verificación de Email: Script (`verificar_email.php`) para validar token y activar cuenta.
-   [ ] Recuperación de Contraseña: Formularios y scripts para solicitar y procesar reseteo.

**1.2. Gestión de Roles y Permisos (Lógica Base)**

-   [ ] Desarrollar `includes/auth.php` (funciones `is_logged_in()`, `get_user_role()`, `has_permission()`, `check_editor_permission()`).
-   [ ] Implementar lógica de redirección basada en permisos.
-   [ ] Crear el usuario SuperAdmin (jmfr65) manualmente en BD con rol 'superadmin'.
-   [ ] (Opcional) Crear un usuario placeholder `senior_admin_ia` en BD con rol 'senior_admin_ia' si se necesita para trazar acciones de la IA con un ID de usuario específico. Alternativamente, las acciones de la IA pueden ser logueadas bajo el ID del SuperAdmin (jmfr65) o un ID de sistema especial.

**1.3. Sistema de Puntos/Logros Básico**

-   [ ] Crear función `add_points($user_id, $points, $reason)` (actualizar `usuarios.puntos` y opcional `log_puntos`).
-   [ ] Integrar `add_points()` para registro y verificación de email.
-   [ ] (Diseño) Definir dónde mostrar los puntos del usuario.
-   [ ] (Planificación) Listar todas las acciones futuras que otorgarán puntos (según enfoque fútbol discutido).

---
### Fase 2: Panel de Administración y Gestión de Datos

**2.0. Estructura Base del Panel de Administración (`/admin`)**

-   [ ] Crear `admin/index.php` y usar plantillas (`header_admin.php`, `footer_admin.php`, `sidebar_admin.php`).
-   [ ] Menú del sidebar dinámico según rol. Proteger acceso a páginas.

**2.1. CRUD Completo para Países, Confederaciones, Regiones, Competiciones, Equipos y Partidos**

Para cada entidad: Listar (con paginación/filtros), Crear/Editar (formulario), Procesar (validación, INSERT/UPDATE, `created_by`/`updated_by`, `log_actividad_admin`), Eliminar (con confirmación, DELETE o inactivo, `log_actividad_admin`).
-   [ ] CRUD para confederaciones.
-   [ ] CRUD para paises.
-   [ ] CRUD para regiones.
-   [ ] CRUD para competiciones (manejo de jerarquía, campos multilingües, etc.). El SuperAdmin (jmfr65) tiene control total.
-   [ ] CRUD para equipos (maestra). El SuperAdmin (jmfr65) tiene control total.
-   [ ] CRUD para partidos (mapeo a equipos, resultados, etc.). El SuperAdmin (jmfr65) tiene control total.

**2.2. Moderación de Envíos (Contribuciones) y Lógica del SeniorAdmin (Asistente Virtual/IA)**

SeniorAdmin (IA) - Lógica y Procesamiento:
-   [ ] Diseñar el conjunto inicial de reglas de decisión para el SeniorAdmin (IA) (en `includes/senior_admin_logic.php` o documento de especificación) para cada `tipo_entidad_principal` y `accion_solicitada`, cubriendo:
    -   Validación de datos (formato, rangos, spam básico).
    -   Reglas de confianza para aprobación/rechazo automático (basadas en historial del usuario, consistencia de datos, etc.).
    -   Criterios para escalamiento a `requiere_revision_humana`.
    -   Lógica de mapeo automático de equipos con niveles de confianza.
-   [ ] Implementar el script de procesamiento de contribuciones por IA (`cron_jobs/procesar_contribuciones_ia.php`) que:
    -   Cargue contribuciones con `estado_moderacion = 'pendiente_ia'`.
    -   Aplique las reglas definidas en `includes/senior_admin_logic.php`.
    -   Actualice `contribuciones.estado_moderacion` (a 'aprobada_ia_directo', 'rechazada_ia', o 'requiere_revision_humana').
    -   Actualice `contribuciones.id_moderador_ia_fk`, `contribuciones.fecha_moderacion_ia`, y `contribuciones.comentario_moderador` (con justificación de la IA).
    -   Si 'aprobada_ia_directo', aplique el cambio a la BD y asigne puntos.
    -   Registre la acción en `log_actividad_admin`.
-   [ ] Configurar el Cron Job para ejecutar `cron_jobs/procesar_contribuciones_ia.php` periódicamente.
-   [ ] Implementar el logging detallado de las decisiones de la IA (en `logs/senior_admin.log` o similar).

Interfaz de Moderación para SuperAdmin (jmfr65):
-   [ ] Interfaz en `/admin/moderacion/` para listar contribuciones con estado `requiere_revision_humana`.
-   [ ] Vista detallada de contribución (comparar `datos_propuestos_json` vs `datos_actuales_json`, ver comentario de la IA).
-   [ ] Lógica para SuperAdmin (jmfr65) para revisión humana: Aprobar/Rechazar, aplicar cambios a BD, actualizar contribuciones (estado_moderacion a 'aprobada_humano'/'rechazada_humano', `id_moderador_humano_fk`, `fecha_moderacion_humana`, `comentario_moderador`), asignar puntos, loguear.
-   [ ] El SuperAdmin (jmfr65) puede ver y moderar todas las contribuciones, y anular decisiones previas.

Interfaz de Moderación para Editores:
-   [ ] Interfaz para Editores para moderar contribuciones (`requiere_revision_humana` o directamente `pendiente_ia` si se configura así) SOLO de sus secciones asignadas.

**2.3. Gestión de Usuarios (Capacidades Extendidas para SuperAdmin jmfr65)**

-   [ ] Interfaz en `/admin/gestion_usuarios/` con funcionalidades completas para el SuperAdmin (jmfr65):
    -   Listar, Crear (cualquier rol, incluyendo otros SuperAdmins con extrema precaución), Modificar rol, Activar/Desactivar/Eliminar, Ajustar puntos, Enviar email reseteo, Ver historial.
    -   Asignar/Revocar permisos de sección a los Editores (gestionar `editor_permisos_seccion`).
    -   Editores pueden crear nuevas competiciones/países/equipos dentro de su ámbito asignado.
-   [ ] Sección de "Configuración del Sitio" (Solo SuperAdmin jmfr65):
    -   Parámetros globales, emails contacto, gestión de API de terceros, (Futuro) herramientas mantenimiento BD.
    -   (Opcional) Parámetros configurables para el SeniorAdmin (IA) (ej. umbrales de confianza, activar/desactivar ciertas reglas).

**2.4. Visualización de Logs y Auditoría (Solo SuperAdmin jmfr65)**

-   [ ] Interfaz para visualizar `log_actividad_admin` (filtros por usuario, acción, fecha).
-   [ ] Interfaz para visualizar logs de errores PHP y logs del SeniorAdmin (IA).

---
### Fase 3: Frontend Público

**3.0. Estructura Base del Frontend Público**

-   [ ] `public_html/index.php` como controlador/enrutador. Plantillas `header.php`, `footer.php`.
-   [ ] Selector de idioma visible y funcional. Todas las cadenas estáticas localizadas.
-   [ ] (M) Preparar plantillas para inserción de bloques de anuncios AdSense.

**3.1. Listados y Buscador de Países, Competiciones, Partidos, Equipos**

-   [ ] Listados: Páginas para Confederaciones, Países (filtro confederación), Competiciones (filtros país, temporada, nombres multilingües), Partidos (filtros competición, fecha, equipo).
-   [ ] Buscador Básico: Formulario en header, script `buscar.php` (países, competiciones, equipos - multilingüe).
-   [ ] SEO para Listados: URLs amigables, `<title>`/meta descripciones dinámicas y multilingües, `hreflang`, paginación SEO.
-   [ ] (M) Integrar bloques de anuncios nativos AdSense en plantillas de listados (respetando límite de 2/página).

**3.2. Página de Detalle para Cada Entidad**

-   [ ] Páginas de detalle para Confederación, País, Región, Competición (con clasificación/partidos), Equipo (con partidos/historial), Partido (con resultado/detalles).
-   [ ] SEO para Detalles: URLs amigables, `<title>`/meta descripciones, `hreflang`, Schema.org.
-   [ ] (M) Integrar bloques de anuncios nativos AdSense en plantillas de detalle (respetando límite de 2/página).

**3.3. Multilenguaje (es, en, fr, pt) en Todas las Páginas Públicas**

-   [ ] Asegurar funcionamiento del selector, persistencia, traducción de cadenas y datos dinámicos.
-   [ ] Adaptar formatos de fecha/número.

**3.4. Sistema Simple para que Usuarios Registrados (y Visitantes) Envíen Nuevos Datos/Resultados**

-   [ ] Sección "Contribuir" (accesible logueado, y para visitantes con CAPTCHA y campo email).
-   [ ] Formularios para proponer: nuevo partido/resultado, nuevo equipo, sugerir mapeo de equipo.
-   [ ] Al enviar, crear entrada en `contribuciones` (estado `pendiente_ia`).
-   [ ] Mensaje "Gracias, enviado para moderación". Perfil de usuario con estado de contribuciones.

**3.5. Configuración Inicial de Publicidad (AdSense)**

-   [ ] (M) Crear cuenta AdSense, verificar sitio (cuando tenga contenido y cumpla políticas).
-   [ ] (M) Crear unidades de anuncios nativos en AdSense.
-   [ ] (M) Obtener e implementar códigos de anuncio AdSense en ubicaciones definidas.
-   [ ] (M) Crear `ads.txt`.
-   [ ] (M) (Legal) Actualizar Política de Privacidad (cookies, publicidad AdSense).
-   [ ] (M) (Legal) Implementar banner/mecanismo de consentimiento de cookies (CMP) compatible con AdSense.

---
### Fase 4: Estadísticas y Foro Interno

**4.1. Dashboards para Admins**

-   [ ] Página principal `/admin/index.php` con estadísticas: Nº usuarios (por rol), nuevas altas, contribuciones (estados), nº entidades BD, actividad moderación. El SuperAdmin (jmfr65) ve todo.
-   [ ] (Opcional) Gráficos JS (ej. Chart.js). Consultas SQL optimizadas.

**4.2. Reportes Exportables (PDF, CSV) (Admin)**

-   [ ] Identificar datos para exportar. Implementar generación CSV y PDF (con librería).
-   [ ] Botones "Exportar" en listas relevantes del admin.

**4.3. Pequeño Foro o Sistema de Mensajes Interno para Coordinación (Admin)**

-   [ ] Opción 1 (Muro de Anuncios): Tabla `anuncios_internos`, interfaz para publicar/ver anuncios en admin. SuperAdmin (jmfr65) modera.

---
### Fase 5: API Básica para Futura App Móvil

**5.0. Diseño y Seguridad de la API**

-   [ ] Definir endpoints iniciales (GET para competiciones, partidos, equipos). Formato JSON.
-   [ ] Autenticación: API Keys (tabla `api_keys`, lógica de validación).
-   [ ] Implementar Rate Limiting. Versionado (`/v1/`).

**5.1. Endpoints Públicos Protegidos por Clave/API Token**

-   [ ] Endpoints para listar/detallar Competiciones, Partidos, Equipos (con filtros).
-   [ ] Cada endpoint: validar API Key, recibir params, consultar BD, formatear JSON, manejar errores.

**5.2. Documentación de la API**

-   [ ] Documentación (Markdown/HTML) para cada endpoint: URL, método, params, ejemplo respuesta, errores, uso de API Key.
-   [ ] El SuperAdmin (jmfr65) gestiona las API Keys desde el panel.

---
### Fase 6: Mantenimiento y Evolución (Post-Lanzamiento Continuo)

-   [ ] Analizar periódicamente el rendimiento del SeniorAdmin (IA) y refinar/expandir su conjunto de reglas basado en el feedback de las revisiones humanas del SuperAdmin (jmfr65).
-   [ ] Monitoreo de seguridad, rendimiento y SEO.
-   [ ] Actualizaciones de software y librerías.
-   [ ] Implementación de nuevas funcionalidades basadas en feedback y prioridades.
-   [ ] Gestión de la comunidad de contribuidores.

---
### Consideraciones Adicionales (Continuas durante todo el proyecto):

**Seguridad:**
-   [ ] Validar TODA entrada. Usar sentencias preparadas PDO. Escapar salida HTML (XSS). Protección CSRF. Headers de seguridad HTTP. Mantener software actualizado. Estrategias anti-scraping.
-   [ ] Especial atención a la seguridad de la cuenta SuperAdmin (jmfr65).

**SEO:**
-   [ ] Generar Sitemaps XML (por idioma o índice). `robots.txt`. URLs canónicas. Optimización velocidad. Diseño responsive.

**Rendimiento:**
-   [ ] Optimizar consultas SQL (índices). Caching (OpCache, datos). Minimizar CSS/JS. Optimizar imágenes.