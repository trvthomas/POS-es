# TRV Solutions Sistema POS 🎉
## Acerca del Proyecto
¡Hola! 👋 Conoce TRV Solutions POS, un proyecto que comencé en 2018 cuando solo era un curioso programador de 14 años. Es un **sistema de punto de venta completo** construido con la simplicidad en mente utilizando PHP, HTML, CSS (Bulma.io) y JavaScript.

Este sistema POS fue creado para **pequeñas empresas** que necesitan algo gratuito para comenzar, offline, confiable y sin complicaciones. ¿No tienes internet? No hay problema. ¿No tienes servidores en la nube o sitio web sofisticado? No necesitas uno. Solo una computadora, y estás listo para empezar.

For English speakers (and for you to avoid translating the whole repo), check out the [English version here](https://github.com/trvthomas/POS).

## Características 🌟
### General
- Funciona completamente offline, sin servidores ni sin tarifas de suscripción.
- Configuración fácil con XAMPP para una solución permanente y confiable.
- Interfaz simple e intuitiva diseñada para pequeñas empresas y usuarios no técnicos.
- Ligero y eficiente, diseñado para hacer el trabajo.
![Mockup 1](https://github.com/user-attachments/assets/21f1180f-61a3-4725-8df8-adfdc89b2342)[^2]

### Funcional
- Permisos de usuario (vendedor, personal de inventario, administrador)
- Control avanzado de inventario
    - Recepción de stock
    - Retiro de stock
    - Ajuste/Conteo de inventario
    - Historial de cambios
- Productos y categorías ilimitados
- Método de pago personalizado
- Impresión automática
- Estadísticas detalladas de ventas
- Panel de administrador
    - Estadísticas diarias y mensuales
        - Estadísticas de ventas por método de pago
        - Estadísticas de ventas por usuario
        - Estadísticas de productos vendidos
        - Estadísticas de usos de cupones
    - Generador de códigos de barras
    - Cupones de descuento
    - Diseño personalizado de recibos
    - Importación/edición masiva de productos
    - Informes diarios y mensuales por correo electrónico[^1]
    - Informes de stock bajo por correo electrónico[^1]
    - Tickets de regalo
    - Límites de descuento
- ¡Y mucho más!
![Mockup 2](https://github.com/user-attachments/assets/f7b7d59c-1166-4fc2-b318-c3d44cafbc3c)[^2]

## Historia 📖
Este proyecto tiene un lugar especial en mi trayectoria como programador. Comenzó como una forma de aprender y experimentar, y con los años, se convirtió en algo mucho más grande.

Desde 2018 hasta 2023, dediqué mucho tiempo y esfuerzo a este proyecto, mejorándolo constantemente hasta que decidí centrarme en nuevas aventuras. Aunque ya no lo estoy actualizando, eres más que bienvenido a usarlo, mejorarlo y mantenerlo vivo.

![Mockup 3](https://github.com/user-attachments/assets/ce089eab-4709-4c75-8842-f3bfc9c97cdf)[^2]

## Instalación y Configuración ⚙️
¡Empezar es muy sencillo! Aquí hay dos formas de ejecutar TRV Solutions POS:

### 1️⃣ Configuración Permanente con XAMPP:
1. Descarga e instala [XAMPP](https://www.apachefriends.org/download.html).
2. Coloca la carpeta **/trv** dentro del directorio **htdocs** creado por XAMPP (la ruta completa debería verse así: **/xampp/htdocs/trv/**).
3. Inicia XAMPP y accede al POS en **[http://localhost/trv](http://localhost/trv)** en tu navegador.
> [!TIP]
> Puedes configurar Apache y MySQL para que se inicien automáticamente con tu computadora. Abre el Panel de Control de XAMPP y, en la columna de Service, marca la casilla junto a cada módulo (recuerda ejecutar XAMPP como administrador).

### 2️⃣ Prueba Rápida con la Extensión PHP Server de VS Code:
1. Instala la extensión [PHP Server](https://marketplace.visualstudio.com/items?itemName=brapifra.phpserver) en VS Code.
2. Abre la carpeta **/trv** e inicia el servidor PHP.
3. Accede al sistema a través del enlace localhost proporcionado.

### Configuración
El sistema te guiará a través de los pasos de configuración la primera vez que lo abras.

### Correos Electrónicos y Zona Horaria
Para habilitar la funcionalidad de correo electrónico, necesitarás configurar los ajustes del servidor de correo. Esto se puede hacer editando el archivo **DBData.php** (ubicado dentro de la carpeta **include**) y actualizando las constants para tu servidor de correo.
Además, puedes establecer la zona horaria correcta en el mismo archivo para asegurar que las marcas de tiempo de las transacciones y los informes sean precisas.

> [!NOTE]
> Aunque TRV Solutions POS ha sido probado extensamente en sistemas Windows, puede haber errores menores. Además, no se ha probado en otros sistemas operativos, por lo que la compatibilidad puede variar.

## Contribuir 💡
Ya no estoy manteniendo activamente este proyecto, ¡pero me encantaría ver lo que otros pueden hacer con él! Siéntete libre de:
- Hacer un fork de este proyecto y hacerlo tuyo.
- Enviar pull requests con mejoras o correcciones de errores.
- Compartir tus pensamientos, ideas o casos de uso en la pestaña de Issues.

## ¿Y si pruebas esto? 🛠️
Aquí hay algunas cosas interesantes que puedes intentar agregar al Sistema TRV Solutions POS:
- Agregar un modo oscuro a la interfaz de usuario (*PD* Bulma ahora ha lanzado su versión 1.x con soporte automático para modo oscuro).
- Crear características adicionales como una base de datos de clientes o tarjetas de regalo.
- Migrarlo a un framework moderno para una actualización tecnológica.

> [!IMPORTANT]
> Inicié este proyecto con la intención de mejorar mis habilidades en programación, así que no te asustes si algunas (o quizás muchas) variables, funciones y la sintaxis en general están un poco desordenadas y no siguen completamente las coding best practices. 😅 (ya he mejorado, por cierto)

[^1]: Requiere tu propio servidor de correo electrónico
[^2]: Nota sobre las imágenes: Tal vez te estés preguntando: *"Espera, ¿no dijiste que solo lo has probado en Windows? Entonces, ¿por qué las imágenes tienen mockups de Mac?"* Bueno, la verdad es que no encontré mockups decentes de Windows... así que aquí estamos. ¡Al menos se ve genial! 😄