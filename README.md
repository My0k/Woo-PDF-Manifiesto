# My0k WooCommerce Order Print Page

Este plugin añade una funcionalidad de impresión personalizada en WooCommerce para generar una página HTML imprimible con detalles de cada pedido, incluyendo un logo y texto personalizado.

## Instalación

1. **Descargar el Plugin**: Descarga el archivo ZIP del plugin.
2. **Subir el Plugin a WordPress**:
   - Ve al panel de administración de WordPress.
   - Dirígete a **Plugins > Añadir nuevo** y selecciona **Subir plugin**.
   - Sube el archivo ZIP descargado y haz clic en **Instalar ahora**.
3. **Activar el Plugin**: Una vez instalado, activa el plugin en **Plugins > Plugins instalados**.

## Configuración Inicial

Para asegurarte de que el botón "Generar Manifiesto" se muestre junto a cada pedido en WooCommerce:

1. Dirígete a **WooCommerce > Pedidos** en el dashboard.
2. En la esquina superior derecha, haz clic en **Opciones de pantalla**.
3. Activa la opción **Acciones** para mostrar el botón de "Generar Manifiesto" junto a cada pedido.

## Cómo Usar el Plugin

### Generar el Manifiesto de un Pedido
1. En **WooCommerce > Pedidos**, verás el botón de "Generar Manifiesto" junto a cada pedido.
2. Haz clic en el botón para abrir la página de impresión del pedido en una nueva pestaña.

### Configuración de Opciones Personalizadas
Para personalizar los elementos que aparecerán en la página de impresión:

1. Ve a **WooCommerce > PDF Manifiesto** en el menú del dashboard.
2. Configura los siguientes campos según tus necesidades:
   - **Texto Personalizado**: Este texto se mostrará en la parte superior de la página de impresión.
   - **Texto de Confirmación de Entrega**: Mensaje que aparecerá al final de la página, antes de la firma.
   - **URL del Logo**: Ingresa la URL de la imagen que deseas mostrar como logo en la página de impresión.

## Edición del Plugin

Si deseas hacer modificaciones en el código, sigue estos pasos:

1. **Archivo Principal**: El archivo principal del plugin es `my0k-order-print.php`. Aquí puedes modificar la estructura de la página de impresión en la función `print_order`.
2. **Nuevos Campos de Configuración**: Para agregar más opciones de configuración, ajusta la función `my0k_register_settings` en el archivo principal.

Para asistencia adicional, visita:
- [Facebook del Autor](https://web.facebook.com/diego.robok/)
- [GitHub del Proyecto](https://github.com/My0k) 

