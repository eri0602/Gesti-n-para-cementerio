# SIGS — Sistema de Gestión de Expedientes de Fallecidos

Este es el proyecto migrado y refactorizado a **Java Spring Boot 3 + Spring Data JPA + Spring Security + Thymeleaf** del sistema SIGS para el Área de Cementerio de la Municipalidad.

## 🛠️ Requisitos previos
* **Java**: JDK 17 o superior.
* **Base de datos**: MySQL/MariaDB (vía XAMPP u otro servidor local).
* **Gestor de proyectos**: Maven (incluido por defecto en IDEs modernos como IntelliJ IDEA, Eclipse o VS Code).

---

## 🗄️ Paso 1: Configurar la Base de Datos

1. Inicia tus servicios de Apache y MySQL en tu panel de control de **XAMPP**.
2. Ingresa a `http://localhost/phpmyadmin/`.
3. Crea una base de datos con el nombre: **`sigs_cementerio`**.
4. Selecciona la base de datos `sigs_cementerio` y ve a la pestaña **Importar**.
5. Selecciona el archivo **`sigs_db.sql`** ubicado en la raíz de este proyecto y presiona **Importar**.
   *(Este archivo contiene la estructura completa de 6 tablas y todos los datos semilla de prueba para estadísticas, búsquedas y deudos).*

---

## ⚙️ Paso 2: Configurar Credenciales de Base de Datos

Si tu base de datos MySQL local tiene contraseña de acceso, abre el archivo:
📄 `src/main/resources/application.properties`

Y actualiza las credenciales:
```properties
spring.datasource.username=tu_usuario
spring.datasource.password=tu_contraseña
```
*Por defecto viene configurado para usuario `root` y sin contraseña.*

---

## 🔑 Credenciales de Acceso al Sistema

Una vez ejecutada la aplicación, ve a `http://localhost:8080/` e inicia sesión con cualquiera de los siguientes usuarios:

### 1. Rol: Administrador (Jefe de Área)
* **Usuario:** `jefe.cementerio`
* **Contraseña:** `123456`
* *Funciones:* Acceso total (Dashboard, Registrar solicitudes, Registrar expedientes, Consultas con filtros, Edición/Corrección de datos y Reportes Estadísticos con gráficos).

### 2. Rol: Operador (Personal Administrativo)
* **Usuario:** `admin.personal`
* **Contraseña:** `123456`
* *Funciones:* Registro de solicitudes (Paso 1), Registro de expedientes (Paso 2), visor de comprobante (Paso 3) y consultas básicas de búsqueda.

---

## 🚀 Cómo Ejecutar la Aplicación

### Desde tu IDE (Recomendado)
1. Importa la carpeta raíz del proyecto como un proyecto Maven.
2. Abre la clase principal en: `src/main/java/com/cementerio/sigs/SigsApplication.java`.
3. Ejecuta la clase haciendo clic derecho y seleccionando **Run SigsApplication**.

### Desde la Consola de Comandos
Abre una terminal en la raíz del proyecto y ejecuta:
```bash
mvn spring-boot:run
```
La aplicación se compilará y levantará en el puerto **`8080`**. Puedes abrir tu navegador favorito e ingresar a:
👉 `http://localhost:8080`

---

## 🎨 Características Premium Implementadas
* **Sistema de Diseño Consistente**: Interfaz moderna basada en tipografías Google Fonts `Inter`, con Sidebar lateral interactivo y adaptable a móviles.
* **Flujo Transaccional Real**: El registro se divide en 3 pasos reales (Paso 1: Solicitud del deudo -> Paso 2: Datos del fallecido vinculando la solicitud previa -> Paso 3: Emisión de Comprobante).
* **Buscador Dinámico Multicriterio**: Permite combinar hasta 7 filtros distintos para realizar búsquedas veloces en la base de datos de forma segura (prevención de Inyección SQL mediante JPA).
* **Módulo de Reportes Estadísticos**: Gráficos interactivos de barra y dona mediante `Chart.js` inyectados dinámicamente según filtros de fecha.
* **Diseño Listo para Impresión (PDF)**: Integración de hojas de estilo `@media print` en comprobantes y reportes, permitiendo al usuario descargar o imprimir hermosos documentos A4 directamente mediante el sistema nativo del navegador sin sobrecargas del servidor.
