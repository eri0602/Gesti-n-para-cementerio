# PROMPT DE CONSTRUCCIÓN — Sistema de Gestión de Expedientes de Fallecidos (SIGS)

> Este documento es un prompt de especificación técnica completo, extraído del documento de avance del proyecto de curso "Análisis y Diseño de Sistemas de Información" (Sección 18172, UTP). Está redactado para que una IA o un desarrollador pueda construir la aplicación **tal cual**, en **PHP nativo + MySQL**, corriendo sobre **XAMPP**, sin dejar vacíos lógicos. Contiene: contexto de negocio, requerimientos funcionales y no funcionales, casos de uso completos con flujos alternativos, arquitectura MVC+DAO, modelo de datos físico (con tipos y restricciones), paquetes de análisis, reglas de validación y la estructura de carpetas/archivos que debe generarse.

---

## 1. CONTEXTO DEL NEGOCIO

**Nombre del sistema:** SIGS — Sistema de Gestión de Expedientes de Fallecidos (también referido como "Sistema de Información del Cementerio" / SIC).

**Entidad:** Área de Cementerio de una municipalidad. Unidad orgánica responsable de planificar, ejecutar y controlar los servicios de sepultura (inhumaciones, exhumaciones y trámites funerarios), bajo la dirección de un Jefe de Área.

**Problema actual:** el registro de expedientes se lleva en Excel manual, sin protección de contraseña, sin validación automática, con alto riesgo de duplicados (mismo fallecido registrado dos veces), sin trazabilidad de quién modifica qué, y con estadísticas calculadas a mano (fila por fila / COUNTIF), generando errores. Las búsquedas de años anteriores requieren revisar archivadores físicos y tardan entre 20 minutos y 1 hora.

**Objetivo general del sistema:** desarrollar un sistema informático que permita registrar, validar y gestionar de forma segura los expedientes de fallecidos, garantizando integridad de la información y facilitando estadísticas trimestrales y anuales.

**Objetivos específicos (deben quedar reflejados como funcionalidades reales):**
1. Módulo de autenticación para controlar el acceso al sistema.
2. Prevención de duplicidades verificando el DNI del fallecido antes de registrar.
3. Módulo de consultas con búsqueda por múltiples criterios (N° de expediente, DNI, nombres, apellidos, fecha, edad, sexo).
4. Generación automática de estadísticas de fallecidos por sexo y grupo etario.
5. Exportación de reportes en PDF.

**Actores del sistema:**
- **Deudo / Usuario externo:** persona que solicita el servicio de sepultura y entrega los datos del fallecido. No tiene acceso al sistema (interactúa presencialmente con el Auxiliar Administrativo).
- **Auxiliar Administrativo:** recibe al deudo, recolecta y verifica documentación, deriva al Personal Administrativo.
- **Personal Administrativo:** usuario del sistema con rol operativo. Inicia sesión, registra expedientes, realiza consultas.
- **Jefe de Área:** usuario del sistema con rol de supervisión. Inicia sesión, genera y valida estadísticas, exporta reportes en PDF, valida consultas escaladas.
- **Técnico Informático:** resuelve incidencias de duplicidad (rol técnico/soporte, puede modelarse como un tercer rol con permisos administrativos si se desea, o simplemente como soporte fuera del sistema).
- **Sistema:** actor automático que valida datos, verifica duplicidad, genera comprobantes y procesa estadísticas.

**Roles a implementar en el sistema (tabla `usuario.rol`):**
- `admin` → Jefe de Área (acceso total: estadísticas, reportes PDF, consulta con validación, gestión de usuarios).
- `operador` → Personal Administrativo (registro de expedientes, consultas, generación de comprobantes).

---

## 2. REQUERIMIENTOS FUNCIONALES (deben mapearse 1 a 1 a funcionalidades del código)

| Código | Requerimiento | Dónde se implementa |
|---|---|---|
| RF01 | Login con usuario y contraseña | `login.php` |
| RF02 | Verificar validez de credenciales antes de acceder | `AuthController` / `UsuarioDAO::verificarCredenciales()` |
| RF03 | Mostrar error y permitir reintento si las credenciales son incorrectas | `login.php` (mensaje de error) |
| RF04 | Registrar datos del fallecido y datos de la solicitud de sepultura | Formulario `expediente_form.php` |
| RF05 | Validar campos obligatorios y formatos antes de guardar | Validación server-side en `ExpedienteController` |
| RF06 | Verificar que DNI y N° de expediente no existan previamente | `FallecidoDAO::existeDNI()`, `ExpedienteDAO::existeNumeroExpediente()` |
| RF07 | Notificar al usuario si el expediente está repetido | Mensaje de alerta en la vista |
| RF08 | Guardar la información validada en la BD institucional | `ExpedienteDAO::registrar()` (transacción) |
| RF09 | Generar confirmación automática tras registro exitoso | Mensaje de éxito + redirección |
| RF10 | Generar y entregar comprobante de registro al deudo | `ComprobanteController` → PDF descargable |
| RF11 | Permitir búsquedas de expedientes por distintos criterios | `consulta.php` |
| RF12 | Buscar por N° expediente, DNI, nombres, apellidos, edad, sexo, fecha de registro | Query dinámica en `ExpedienteDAO::buscar($filtros)` |
| RF13 | Procesar automáticamente estadísticas por sexo y grupo etario | `ReporteDAO::generarEstadisticas()` |
| RF14 | Generar reportes estadísticos trimestrales y anuales | Filtro de rango de fechas en el módulo de reportes |
| RF15 | Exportar reportes y estadísticas en PDF | Librería `tecnickcom/tcpdf` o `dompdf/dompdf` |
| RF16 | Mantener historial de expedientes para consultas futuras | Nunca borrar físicamente; usar tabla `expediente` como histórico permanente |

## 3. REQUERIMIENTOS NO FUNCIONALES (deben quedar reflejados en decisiones técnicas)

**Seguridad:**
- RNF01: acceso restringido a usuarios autorizados → `session_start()` + verificación de sesión en cada página protegida (`auth_check.php` incluido al inicio de cada script).
- RNF02: proteger información sensible (DNI, datos personales) → contraseñas con `password_hash()` (bcrypt), conexión mediante PDO con prepared statements (evitar SQL injection), nunca exponer DNI en URLs (usar POST/ID interno).
- RNF03: niveles de acceso según el rol → control de rol (`admin` / `operador`) en cada controlador; ocultar/bloquear menús según rol.

**Rendimiento:**
- RNF04: tiempo de respuesta en consultas ≤ 3 segundos → índices en `dni`, `numero_expediente`, `fecha_registro`.
- RNF05: procesar registros sin afectar el rendimiento → uso de transacciones (`BEGIN`/`COMMIT`) al registrar expediente completo (fallecido + solicitud + expediente + comprobante).

**Usabilidad:**
- RNF06: interfaz clara, intuitiva y fácil de usar → Bootstrap 5 para formularios y tablas.
- RNF07: mensajes comprensibles de error, validación y confirmación → clases Bootstrap `alert-success` / `alert-danger` reutilizables.

**Disponibilidad / Integridad:**
- RNF08: disponible durante el horario de atención → no aplica restricción técnica especial (servidor local XAMPP siempre activo).
- RNF09: integridad y consistencia de la información → claves foráneas con `ON DELETE RESTRICT`, constraints `UNIQUE` en `dni` y `numero_expediente`, transacciones.
- RNF10: operativa durante horario laboral → igual que RNF08, documentar como nota, sin lógica de bloqueo horario salvo que el usuario lo pida explícitamente.

---

## 4. CASOS DE USO COMPLETOS (10 ECU) — Deben implementarse todos los flujos, incluidos los alternativos

### ECU-01 — Solicitar servicio de sepultura
- **Actor:** Deudo/Usuario (no autenticado, interacción presencial registrada por el Auxiliar).
- **Precondición:** el deudo cuenta con documentación del fallecido.
- **Flujo básico:** el deudo solicita el servicio → el Auxiliar Administrativo lo atiende → verifica documentación → si está completa, deriva al Personal Administrativo → este registra los datos iniciales de la solicitud → el sistema entrega un número de solicitud al deudo.
- **Flujo alternativo (FA1):** documentación incompleta → el Auxiliar informa los documentos faltantes → el deudo los entrega → se reanuda el flujo.
- **Implementación:** formulario `solicitud_form.php` que registra en la tabla `solicitud_sepultura` (nombre_deudo, dni_deudo, parentesco, fecha_solicitud) y devuelve un `id_solicitud` autogenerado que se muestra en pantalla como "número de solicitud".

### ECU-02 — Entregar datos del fallecido
- **Actor:** Deudo/Usuario.
- **Incluido por (`include`):** ECU-01.
- **Flujo básico:** el Auxiliar informa al deudo los datos y documentos requeridos (nombres, DNI, fecha de nacimiento, fecha de fallecimiento, sexo, edad, datos del solicitante) → el deudo entrega la documentación → el Auxiliar verifica que esté completa.
- **FA1:** información incompleta → se solicita completarla.
- **Implementación:** los campos capturados aquí son precisamente los campos de la tabla `fallecido`; en la práctica se modela como parte del mismo formulario de registro de expediente (paso previo a guardar).

### ECU-03 — Iniciar sesión
- **Actores:** Personal Administrativo, Jefe de Área.
- **Precondición:** el usuario tiene usuario/contraseña asignados.
- **Flujo básico:** ingresa usuario y contraseña → el sistema valida credenciales → si son válidas, otorga acceso según el rol.
- **FA1:** credenciales inválidas → mensaje de error, permite reintentar.
- **Requerimientos relacionados:** RF01, RF02, RF03, RNF01, RNF03.
- **Implementación:** `login.php` (formulario) + `procesar_login.php` (valida contra `usuario` con `password_verify()`, guarda `$_SESSION['id_usuario']`, `$_SESSION['usuario']`, `$_SESSION['rol']`).

### ECU-04 — Registrar expediente de fallecido (CASO DE USO CENTRAL)
- **Actor:** Personal Administrativo.
- **Relaciones:** `include` → Validar datos ingresados (ECU-05); `include` → Verificar duplicidad (ECU-06); `extend` ← Generar comprobante de registro (ECU-07).
- **Precondición:** el usuario inició sesión; la documentación del fallecido fue entregada y revisada.
- **Flujo básico:**
  1. El Personal Administrativo ingresa los datos del fallecido (nombres, DNI, fecha de defunción, sexo, grupo etario, etc.) y de la solicitud.
  2. El sistema valida los datos ingresados (ECU-05).
  3. El sistema verifica que el DNI y el N° de expediente no existan previamente (ECU-06).
  4. El sistema guarda el expediente validado en la base de datos.
  5. El sistema genera una confirmación automática del registro.
  6. (Extensión) El sistema genera el comprobante de registro para entrega al deudo (ECU-07).
- **Flujos alternativos:**
  - **FA1 — Datos inválidos:** en el paso 2, si los datos no son válidos, el sistema muestra un mensaje de error; el Personal Administrativo corrige y reintenta.
  - **FA2 — Expediente duplicado:** en el paso 3, si existe duplicidad, el sistema muestra una alerta; el Técnico Informático revisa y resuelve la incidencia antes de continuar (en la práctica: el flujo se detiene y no se permite guardar hasta que se resuelva/corrija el dato).
- **Postcondición:** el expediente queda almacenado y se genera una confirmación de registro.
- **Requerimientos relacionados:** RF04, RF05, RF06, RF07, RF08, RF09; RNF09.
- **Implementación:** `expediente_form.php` (form único con campos de fallecido + solicitud) → `ExpedienteController::registrar()`:
  1. Sanitiza y valida (ver ECU-05).
  2. Verifica duplicidad (ECU-06).
  3. Si todo OK, abre transacción PDO: INSERT en `fallecido` → INSERT en `solicitud_sepultura` → INSERT en `expediente` (con FKs) → INSERT en `comprobante_registro` → COMMIT.
  4. Si algo falla, ROLLBACK y mensaje de error.
  5. Redirige a `comprobante_ver.php?id=X` mostrando el comprobante generado, con botón "Descargar PDF" y "Volver al listado".

### ECU-05 — Validar datos ingresados
- **Actor:** Sistema (disparado por Personal Administrativo).
- **Incluido desde:** ECU-04.
- **Flujo básico:** el sistema comprueba campos obligatorios y formatos → si todo correcto, los marca como válidos y permite continuar.
- **FA1:** datos incorrectos → mensaje de error indicando la corrección requerida.
- **Requerimientos:** RF05; RNF07.
- **Reglas de validación exactas a implementar (PHP server-side, además de HTML5 `required`):**
  - `nombres`, `apellidos`: no vacíos, solo letras y espacios (regex `^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$`), máx. 100 caracteres.
  - `dni` (fallecido) y `dni_deudo`: exactamente 8 dígitos numéricos (regex `^\d{8}$`).
  - `fecha_nacimiento`, `fecha_fallecimiento`: formato fecha válido (`DATE`); `fecha_fallecimiento >= fecha_nacimiento`; ninguna fecha puede ser futura.
  - `edad`: entero positivo, calculado automáticamente en el servidor a partir de `fecha_nacimiento` y `fecha_fallecimiento` (no confiar en un campo editable por el usuario, aunque el modelo físico lo declare como columna almacenada).
  - `sexo`: debe ser uno de los valores permitidos (`Masculino`, `Femenino`) — implementar como `<select>`, no texto libre.
  - `parentesco`: no vacío, texto libre máx. 50 caracteres.
  - `numero_expediente`: no vacío, formato sugerido `EXP-AAAA-#####` (año + correlativo), único.
  - Si cualquier regla falla → acumular errores en un array y mostrarlos todos juntos en un único `alert-danger` (no detener en el primer error), conservando los valores ya ingresados en el formulario (repoblado de campos).

### ECU-06 — Verificar duplicidad (DNI y N° de expediente)
- **Actor:** Sistema (disparado por Personal Administrativo).
- **Incluido desde:** ECU-04.
- **Flujo básico:** el sistema busca el DNI y el N° de expediente en la base de datos → si no existe coincidencia, permite continuar.
- **FA1:** duplicado encontrado → alerta de duplicado, notifica al usuario.
- **Requerimientos:** RF06, RF07; RNF09.
- **Implementación exacta:** antes del INSERT, ejecutar:
  ```sql
  SELECT id_fallecido FROM fallecido WHERE dni = :dni;
  SELECT id_expediente FROM expediente WHERE numero_expediente = :numero_expediente;
  ```
  Si cualquiera devuelve una fila, cancelar el registro y mostrar: *"Ya existe un expediente registrado con este DNI/número de expediente. Verifique con el Técnico Informático si considera que se trata de un error."*

### ECU-07 — Generar comprobante de registro
- **Actor:** Sistema / Personal Administrativo.
- **Relación:** `extend` de ECU-04.
- **Flujo básico:** el sistema genera el comprobante con los datos del registro → el Personal Administrativo imprime y entrega el comprobante al deudo.
- **Requerimientos:** RF09, RF10.
- **Implementación:** al completar el registro exitosamente, se inserta una fila en `comprobante_registro` (id_expediente, fecha_emision = hoy) y se muestra/permite descargar un PDF con: datos del fallecido, N° de expediente, nombre del deudo/solicitante, fecha de emisión, y un mensaje de conformidad. Usar TCPDF o DomPDF.

### ECU-08 — Consultar expedientes (criterios múltiples)
- **Actores:** Personal Administrativo; Jefe de Área (validación).
- **Precondición:** el usuario inició sesión.
- **Flujo básico:**
  1. El usuario selecciona la opción de consulta.
  2. Ingresa los criterios de búsqueda (N° de expediente, DNI, nombres, apellidos, edad, sexo o fecha de registro) — todos opcionales y combinables.
  3. El sistema filtra los expedientes según los criterios.
  4. El sistema muestra la lista de resultados.
  5. El usuario revisa los expedientes encontrados.
- **Flujos alternativos:**
  - **FA1 — Sin resultados:** mensaje "No se encontraron expedientes"; el usuario reingresa nuevos criterios.
  - **FA2 — Requiere validación:** si el expediente requiere corrección, el usuario (Personal Administrativo) eleva la consulta al Jefe de Área, quien revisa, valida o corrige la información y el sistema actualiza el expediente.
- **Requerimientos:** RF11, RF12, RF16; RNF04.
- **Implementación:** `consulta.php` con formulario GET de filtros opcionales; `ExpedienteDAO::buscar($filtros)` construye dinámicamente el `WHERE` solo con los campos que vengan no vacíos (usar prepared statements con placeholders condicionales). Resultado en tabla paginada (Bootstrap). Cada fila con botón "Ver" (detalle) y, si el rol es `admin`, botón "Editar" (implementa FA2: edición/corrección del expediente).

### ECU-09 — Generar estadísticas (sexo y grupo etario)
- **Actores:** Jefe de Área; Personal Administrativo (verificación).
- **Relación:** `extend` ← Exportar reportes en PDF (ECU-10).
- **Flujo básico:**
  1. El Jefe de Área selecciona la opción de estadísticas.
  2. Define filtros (rango de fechas, sexo, grupo etario).
  3. El Personal Administrativo verifica que los expedientes del periodo estén completos.
  4. El sistema procesa los datos filtrados y genera las estadísticas.
  5. El Jefe de Área revisa los resultados.
  6. (Extensión) el sistema exporta el reporte en PDF.
- **FA1:** expedientes incompletos → se completan o corrigen antes de continuar.
- **Requerimientos:** RF13, RF14; RNF05.
- **Implementación:** `reportes.php` con selector de rango de fechas (o periodo trimestral/anual predefinido). Consultas SQL:
  ```sql
  -- Por sexo
  SELECT sexo, COUNT(*) AS total FROM fallecido
  WHERE fecha_fallecimiento BETWEEN :desde AND :hasta
  GROUP BY sexo;

  -- Por grupo etario (definir rangos: 0-17, 18-29, 30-59, 60+)
  SELECT
    CASE
      WHEN edad BETWEEN 0 AND 17 THEN 'Menor de edad'
      WHEN edad BETWEEN 18 AND 29 THEN 'Joven adulto'
      WHEN edad BETWEEN 30 AND 59 THEN 'Adulto'
      ELSE 'Adulto mayor'
    END AS grupo_etario,
    COUNT(*) AS total
  FROM fallecido
  WHERE fecha_fallecimiento BETWEEN :desde AND :hasta
  GROUP BY grupo_etario;
  ```
  Mostrar resultados en tablas y en un gráfico de barras (Chart.js vía CDN, opcional pero recomendado dado que el curso pide "cuadros estadísticos"). Registrar la generación en `reporte_estadistico` (tipo_reporte, fecha_generacion, generado_por = id_usuario del Jefe de Área).

### ECU-10 — Exportar reportes en PDF
- **Actor:** Jefe de Área.
- **Relación:** `extend` de ECU-09.
- **Flujo básico:** el sistema genera el archivo PDF del reporte → el Jefe de Área lo descarga y guarda.
- **Requerimientos:** RF15.
- **Implementación:** botón "Exportar PDF" en `reportes.php` que envía los mismos filtros a `reporte_pdf.php`, el cual reconstruye las mismas consultas y genera el PDF con TCPDF/DomPDF (tabla de resultados + totales + fecha de generación + nombre del Jefe de Área que lo generó), con `Content-Disposition: attachment`.

---

## 5. ARQUITECTURA — MVC + DAO (obligatoria, tal como está definida en el documento)

Patrón exacto a replicar: el Usuario interactúa con la **Vista** (formularios de login, registro, búsqueda, reportes) → la Vista solo muestra información y envía solicitudes al **Controlador** → el Controlador procesa la lógica de negocio y solicita información al **Modelo** → el Modelo, mediante el patrón **DAO**, accede a MySQL para registrar, consultar, actualizar o eliminar → los resultados regresan al Controlador → este los envía a la Vista.

**Paquetes de análisis (deben reflejarse como agrupación de carpetas/módulos):**

| Paquete | Da soporte a | Clases de interfaz | Clases de control | Clases de entidad |
|---|---|---|---|---|
| Seguridad y Acceso | Iniciar sesión | FormInicioSesion | ControlAutenticacion | PersonalAdministrativo, JefeDeArea |
| Atención y Solicitudes | Solicitar servicio, Entregar datos del fallecido | FormularioSolicitud | ControlSolicitud | Deudo, SolicitudServicio |
| Gestión de Expedientes | Registrar, validar, verificar duplicidad, comprobante, consulta | FormRegistroExpediente, FormConsultaExpediente | ControlExpediente, ControlConsulta | Expediente, Fallecido, ComprobanteRegistro |
| Reportes y Estadísticas | Generar estadísticas, exportar PDF | FormEstadisticas | ControlEstadisticas | Reporte |

**Dependencias `access` entre paquetes (a respetar en el orden de `require`/`include`):**
- Gestión de Expedientes → accede a → Seguridad y Acceso (requiere sesión iniciada).
- Gestión de Expedientes → accede a → Atención y Solicitudes (el expediente nace de una solicitud).
- Reportes y Estadísticas → accede a → Seguridad y Acceso (requiere sesión iniciada).
- Reportes y Estadísticas → accede a → Gestión de Expedientes (usa los datos de expedientes).

---

## 6. MODELO DE DATOS FÍSICO (MySQL) — Script SQL completo a generar

Traducir exactamente estas 6 tablas del modelo físico del documento a InnoDB/utf8mb4, respetando PK/FK/UNIQUE/NOT NULL, y agregando los índices necesarios para RNF04:

```sql
CREATE DATABASE IF NOT EXISTS sigs_cementerio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sigs_cementerio;

-- Tabla: Usuario
CREATE TABLE usuario (
    id_usuario   INT AUTO_INCREMENT PRIMARY KEY,
    usuario      VARCHAR(50) NOT NULL UNIQUE,
    contrasena   VARCHAR(255) NOT NULL,   -- almacenar con password_hash()
    rol          VARCHAR(30) NOT NULL     -- 'admin' (Jefe de Área) | 'operador' (Personal Administrativo)
) ENGINE=InnoDB;

-- Tabla: Fallecido
CREATE TABLE fallecido (
    id_fallecido         INT AUTO_INCREMENT PRIMARY KEY,
    dni                  CHAR(8) NOT NULL UNIQUE,
    nombres              VARCHAR(100) NOT NULL,
    apellidos            VARCHAR(100) NOT NULL,
    fecha_nacimiento     DATE NOT NULL,
    fecha_fallecimiento  DATE NOT NULL,
    edad                 INT NOT NULL,
    sexo                 VARCHAR(10) NOT NULL,
    INDEX idx_dni (dni)
) ENGINE=InnoDB;

-- Tabla: Solicitud_Sepultura
CREATE TABLE solicitud_sepultura (
    id_solicitud     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_deudo     VARCHAR(100) NOT NULL,
    dni_deudo        CHAR(8) NOT NULL,
    parentesco       VARCHAR(50) NOT NULL,
    fecha_solicitud  DATE NOT NULL
) ENGINE=InnoDB;

-- Tabla: Expediente
CREATE TABLE expediente (
    id_expediente      INT AUTO_INCREMENT PRIMARY KEY,
    numero_expediente  VARCHAR(20) NOT NULL UNIQUE,
    id_fallecido       INT NOT NULL,
    id_solicitud       INT NOT NULL,
    fecha_registro     DATE NOT NULL,
    FOREIGN KEY (id_fallecido) REFERENCES fallecido(id_fallecido) ON DELETE RESTRICT,
    FOREIGN KEY (id_solicitud) REFERENCES solicitud_sepultura(id_solicitud) ON DELETE RESTRICT,
    INDEX idx_numero_expediente (numero_expediente),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB;

-- Tabla: Reporte_Estadistico
CREATE TABLE reporte_estadistico (
    id_reporte        INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte      VARCHAR(30) NOT NULL,   -- 'Trimestral' | 'Anual'
    fecha_generacion  DATE NOT NULL,
    generado_por      INT NOT NULL,
    FOREIGN KEY (generado_por) REFERENCES usuario(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla: Comprobante_Registro
CREATE TABLE comprobante_registro (
    id_comprobante  INT AUTO_INCREMENT PRIMARY KEY,
    id_expediente   INT NOT NULL,
    fecha_emision   DATE NOT NULL,
    FOREIGN KEY (id_expediente) REFERENCES expediente(id_expediente) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Usuarios semilla (contraseña real generada con password_hash('123456', PASSWORD_BCRYPT) en PHP)
INSERT INTO usuario (usuario, contrasena, rol) VALUES
('jefe.cementerio', '$2y$10$REEMPLAZAR_CON_HASH_REAL', 'admin'),
('admin.personal', '$2y$10$REEMPLAZAR_CON_HASH_REAL', 'operador');
```

> **Importante:** generar los hashes reales de contraseña con un script PHP (`password_hash('123456', PASSWORD_BCRYPT)`) y reemplazar los placeholders antes de ejecutar el INSERT, no dejar el hash literal como texto en el script.

---

## 7. ESTRUCTURA DE CARPETAS A GENERAR (PHP nativo, sin framework, compatible con XAMPP)

```
/xampp/htdocs/sigs/
│
├── config/
│   └── database.php              -- conexión PDO (host=localhost, dbname=sigs_cementerio, charset=utf8mb4)
│
├── models/                       -- clases de entidad (paquete "Clases de Entidad")
│   ├── Usuario.php
│   ├── Fallecido.php
│   ├── SolicitudSepultura.php
│   ├── Expediente.php
│   ├── ReporteEstadistico.php
│   └── ComprobanteRegistro.php
│
├── dao/                           -- capa de Acceso a Datos (patrón DAO)
│   ├── UsuarioDAO.php             -- login(), verificarCredenciales()
│   ├── FallecidoDAO.php           -- existeDNI(), registrar(), buscar()
│   ├── SolicitudDAO.php           -- registrar()
│   ├── ExpedienteDAO.php          -- existeNumeroExpediente(), registrar(), buscar($filtros), obtenerPorId()
│   └── ReporteDAO.php             -- porSexo(), porGrupoEtario(), registrarGeneracion()
│
├── controllers/                  -- lógica de negocio (paquete "Clases de control")
│   ├── AuthController.php        -- ControlAutenticacion
│   ├── SolicitudController.php   -- ControlSolicitud
│   ├── ExpedienteController.php  -- ControlExpediente (valida + verifica duplicidad + registra + genera comprobante)
│   ├── ConsultaController.php     -- ControlConsulta
│   └── ReporteController.php      -- ControlEstadisticas
│
├── views/                         -- interfaz (paquete "Clases de interfaz")
│   ├── layout/
│   │   ├── header.php             -- navbar Bootstrap, cambia opciones según $_SESSION['rol']
│   │   └── footer.php
│   ├── login.php                  -- FormInicioSesion
│   ├── solicitud_form.php         -- FormularioSolicitud
│   ├── expediente_form.php        -- FormRegistroExpediente
│   ├── consulta.php               -- FormConsultaExpediente
│   ├── expediente_detalle.php     -- vista de detalle/edición (FA2 de ECU-08)
│   ├── comprobante_ver.php        -- vista previa del comprobante + botón descargar PDF
│   └── reportes.php               -- FormEstadisticas (filtros + tablas + gráfico Chart.js + botón exportar)
│
├── includes/
│   ├── auth_check.php             -- verifica $_SESSION activa; redirige a login.php si no existe (RNF01)
│   └── functions.php              -- helpers: calcularEdad(), sanitizar(), generarNumeroExpediente()
│
├── pdf/
│   ├── comprobante_pdf.php        -- genera PDF del comprobante (TCPDF/DomPDF)
│   └── reporte_pdf.php            -- genera PDF del reporte estadístico
│
├── assets/
│   ├── css/estilos.css
│   └── js/validaciones.js         -- validaciones client-side (complementarias, nunca sustitutas de las server-side)
│
├── vendor/                        -- si se usa Composer para TCPDF/DomPDF
│
├── logout.php
├── index.php                      -- redirige a login.php o al dashboard según sesión
└── database/
    └── sigs_cementerio.sql        -- script del punto 6, listo para importar en phpMyAdmin
```

---

## 8. FLUJO DE NAVEGACIÓN ESPERADO (para que no queden vacíos de UX)

1. `index.php` → si no hay sesión, redirige a `login.php`; si hay sesión, redirige a un `dashboard.php` simple con accesos según rol.
2. `login.php` → valida → guarda sesión → redirige a `dashboard.php`.
3. Desde el dashboard:
   - **Operador (Personal Administrativo):** ve tarjetas/enlaces a "Nueva Solicitud", "Registrar Expediente", "Consultar Expedientes".
   - **Admin (Jefe de Área):** ve además "Reportes y Estadísticas" y "Exportar PDF"; también puede acceder a Consultar/Editar.
4. `solicitud_form.php` → guarda y muestra el número de solicitud generado, con botón "Continuar a registrar expediente" que pasa el `id_solicitud` por GET a `expediente_form.php`.
5. `expediente_form.php` → precarga datos del deudo si viene con `id_solicitud`; captura datos del fallecido; al enviar, pasa por `ExpedienteController::registrar()` (valida → verifica duplicidad → transacción → comprobante) → redirige a `comprobante_ver.php`.
6. `comprobante_ver.php` → muestra resumen + botón "Descargar PDF" (`pdf/comprobante_pdf.php?id=X`) + botón "Volver al listado".
7. `consulta.php` → formulario de filtros (todos opcionales) → tabla de resultados → botón "Ver" → `expediente_detalle.php?id=X` (el admin puede editar/corregir aquí, cubriendo FA2 de ECU-08).
8. `reportes.php` (solo admin, o visible pero restringido si se implementa el rol operador con "verificación" según ECU-09) → selecciona rango/periodo → muestra tablas + gráfico → botón "Exportar PDF" → `pdf/reporte_pdf.php`.
9. `logout.php` → destruye sesión → redirige a `login.php`.

---

## 9. VALIDACIONES Y MENSAJES ESTÁNDAR (RNF07 — deben ser reutilizables, un solo componente de alerta)

- Éxito: `alert alert-success` → "Expediente registrado correctamente. N° de expediente: {numero_expediente}."
- Error de validación: `alert alert-danger` con lista `<ul>` de todos los errores encontrados.
- Duplicado: `alert alert-warning` → "Ya existe un registro con este DNI o número de expediente."
- Sin resultados en consulta: `alert alert-info` → "No se encontraron expedientes con los criterios indicados."
- Credenciales inválidas: `alert alert-danger` → "Usuario o contraseña incorrectos. Intente nuevamente."

---

## 10. STACK TÉCNICO EXACTO A USAR

- **Servidor local:** XAMPP (Apache + MySQL/MariaDB + PHP 8.x).
- **Backend:** PHP nativo (sin frameworks), PDO con prepared statements, `password_hash()`/`password_verify()`, sesiones nativas (`$_SESSION`).
- **Frontend:** HTML5 + Bootstrap 5 (vía CDN) + JS vanilla para validaciones y, opcionalmente, Chart.js (CDN) para el gráfico de estadísticas.
- **PDF:** TCPDF o DomPDF (instalado vía Composer si está disponible en el entorno; si no, usar TCPDF standalone descargado).
- **Base de datos:** MySQL/MariaDB, base `sigs_cementerio`, importar `database/sigs_cementerio.sql` desde phpMyAdmin.
- **Sin APIs externas, sin autenticación de terceros, sin JWT** — todo con sesiones PHP tradicionales, coherente con el patrón MVC+DAO descrito.

---

## 11. CHECKLIST DE ACEPTACIÓN (para verificar que no falta nada del documento original)

- [ ] Login funcional con roles `admin`/`operador` y protección de todas las páginas internas (RNF01, RNF03).
- [ ] Registro de solicitud de sepultura con número autogenerado (ECU-01).
- [ ] Registro de expediente en un único flujo transaccional: fallecido + solicitud + expediente + comprobante (ECU-04).
- [ ] Validación server-side completa con mensajes agregados y repoblado de formulario (ECU-05).
- [ ] Verificación de duplicidad por DNI y N° de expediente antes de guardar, con bloqueo si hay coincidencia (ECU-06).
- [ ] Generación y descarga de comprobante en PDF (ECU-07).
- [ ] Consulta con al menos 7 criterios combinables: N° expediente, DNI, nombres, apellidos, edad, sexo, fecha de registro (ECU-08).
- [ ] Vista de detalle/edición para corrección de expedientes (rol admin) (FA2 de ECU-08).
- [ ] Módulo de estadísticas por sexo y grupo etario, con filtro de periodo trimestral/anual (ECU-09).
- [ ] Exportación de reportes estadísticos a PDF (ECU-10).
- [ ] Script SQL completo con las 6 tablas, PK/FK/UNIQUE exactos y datos semilla de usuarios.
- [ ] Estructura de carpetas MVC+DAO tal como se detalla en la sección 7.
- [ ] Índices en `dni`, `numero_expediente` y `fecha_registro` para cumplir RNF04.
- [ ] Ninguna contraseña almacenada en texto plano.

---

*Fin del prompt de especificación. Este archivo puede pegarse íntegro como instrucción inicial para comenzar la generación de código PHP/XAMPP del sistema SIGS.*
