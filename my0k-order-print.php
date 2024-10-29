<?php
/**
 * Plugin Name: My0k WooCommerce Order Print Page
 * Plugin URI:  https://github.com/My0k
 * Description: Genera una página HTML imprimible para cada pedido de WooCommerce, incluyendo el método de envío, texto personalizado y logo.
 * Version:     1.2
 * Author:      my0k
 * Author URI:  https://web.facebook.com/diego.robok/
 */

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

// Añadir botón para imprimir en el listado de órdenes
add_action('woocommerce_admin_order_actions_end', 'add_print_button');
function add_print_button($order) {
    $url = add_query_arg(array(
        'action' => 'print_order',
        'order_id' => $order->get_id(),
        '_wpnonce' => wp_create_nonce('print-order')
    ), admin_url('admin-ajax.php'));
    echo '<a href="' . esc_url($url) . '" class="button tips print-button" data-tip="Imprimir Orden" target="_blank" style="display: flex; align-items: center;">
            <span class="dashicons dashicons-media-text" style="margin-right: 5px;"></span> Imprimir
          </a>';
}

// Manejar la solicitud de impresión
add_action('wp_ajax_print_order', 'handle_print_order');
function handle_print_order() {
    if (!current_user_can('manage_woocommerce') || !check_admin_referer('print-order')) {
        wp_die('No tienes permiso para imprimir este pedido.');
    }
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    if ($order_id) {
        $order = wc_get_order($order_id);
        print_order($order);
    }
    exit;
}

function print_order($order) {
    // Preparar los datos del método de envío
    $shipping_method_code = array();
    $shipping_items = $order->get_items('shipping');
    $free_shipping = false;

    if (!empty($shipping_items)) {
        foreach ($shipping_items as $item_id => $shipping_item) {
            $shipping_method_instance = $shipping_item->get_instance_id();
            $shipping_method_id = $shipping_item->get_method_id();

            if ($shipping_method_id === 'free_shipping' && $shipping_method_instance === '1') {
                $free_shipping = true;
            } else {
                $shipping_method_code[] = $shipping_method_id . ':' . $shipping_method_instance;
            }
        }
    }

    $shipping_codes = implode(', ', $shipping_method_code);

    // Obtener opciones personalizadas con valores por defecto
    $custom_text = get_option('my0k_custom_text', 'Donde encontrar la pestaña para modificar el texto');
    $confirmation_text = get_option('my0k_confirmation_text', 'Confirmación de entrega conforme. Declaro que los datos indicados corresponden a la persona que recibió el pedido.');
    $logo_url = get_option('my0k_logo_url', '');

    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Impresión de Orden</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .order-details { display: flex; justify-content: space-between; margin-bottom: 20px; }
            .signature { margin-top: 50px; line-height: 1; }
            .signature-line { border-top: 1px solid #000; width: 300px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>';
        if ($logo_url) {
            echo '<div class="custom-logo">
                <img src="' . esc_url($logo_url) . '" alt="Logo" style="max-width: 200px; margin-bottom: 20px;">
            </div>';
        }
        if ($custom_text) {
            echo '<div class="custom-text" style="margin-bottom: 20px;">
                ' . esc_html($custom_text) . '
            </div>';
        }
        echo '<h1>Envío Orden: ' . $order->get_order_number() . '</h1>
        <div class="order-details">
            <div class="order-info">
                <strong>Nombre:</strong> ' . $order->get_billing_first_name() . '<br>
                <strong>Apellido:</strong> ' . $order->get_billing_last_name() . '<br>';
                if (!empty($order->get_billing_company())) {
                    echo '<strong>Empresa:</strong> ' . $order->get_billing_company() . '<br>';
                }
                echo '<strong>Dirección:</strong> ' . $order->get_billing_address_1() . ' ' . $order->get_billing_address_2() . '<br>
                <strong>Ciudad:</strong> ' . $order->get_billing_city() . '<br>
                <strong>Región:</strong> ' . $order->get_billing_state() . '<br>
                <strong>Teléfono:</strong> ' . $order->get_billing_phone() . '<br>
                <strong>Tipo de Envío:</strong> ';
                if ($free_shipping) {
                    echo 'Envío gratis!';
                } else {
                    echo $shipping_codes;
                }
            echo '</div>
        </div>
        <h2>Productos</h2>
        <table>
            <tr>
                <th>SKU</th>
                <th>Producto</th>
                <th>Unidades</th>
            </tr>';
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                echo '<tr>
                    <td>' . $product->get_sku() . '</td>
                    <td>' . $item->get_name() . '</td>
                    <td>' . $item->get_quantity() . '</td>
                </tr>';
            }
        echo '</table>
        <div class="signature">
            <strong>' . esc_html($confirmation_text) . '</strong><br><br>
            <strong>Firma Cliente:_______________________</strong><br><br>
            <strong>RUT Cliente:_______________________</strong><br><br>
            <strong>Fecha Entrega:______/_______/________</strong>
        </div>
        <script>
            window.print();
        </script>
    </body>
    </html>';
}

// Añadir una página de configuración en el menú de WooCommerce en el dashboard
add_action('admin_menu', 'my0k_add_custom_menu');
function my0k_add_custom_menu() {
    add_submenu_page(
        'woocommerce',
        'PDF Manifiesto',
        'PDF Manifiesto',
        'manage_woocommerce',
        'my0k_settings_page',
        'my0k_settings_page_callback'
    );
}

function my0k_settings_page_callback() {
    echo '<div class="wrap">';
    echo '<h1>Configuración de PDF Manifiesto</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('my0k_settings_group');
    do_settings_sections('my0k_settings_page');
    submit_button();
    echo '</form>';
    echo '</div>';
}

// Registrar las configuraciones en el menú de WooCommerce
add_action('admin_init', 'my0k_register_settings');
function my0k_register_settings() {
    register_setting('my0k_settings_group', 'my0k_custom_text');
    register_setting('my0k_settings_group', 'my0k_confirmation_text');
    register_setting('my0k_settings_group', 'my0k_logo_url');

    add_settings_section('my0k_main_section', 'Configuración de PDF Manifiesto', null, 'my0k_settings_page');

    add_settings_field('my0k_custom_text', 'Texto Personalizado', 'my0k_custom_text_callback', 'my0k_settings_page', 'my0k_main_section');
    add_settings_field('my0k_confirmation_text', 'Texto de Confirmación de Entrega', 'my0k_confirmation_text_callback', 'my0k_settings_page', 'my0k_main_section');
    add_settings_field('my0k_logo_url', 'URL del Logo', 'my0k_logo_url_callback', 'my0k_settings_page', 'my0k_main_section');
}

function my0k_custom_text_callback() {
    $custom_text = esc_attr(get_option('my0k_custom_text', 'Donde encontrar la pestaña para modificar el texto'));
    echo '<textarea name="my0k_custom_text" rows="5" cols="50">' . $custom_text . '</textarea>';
}

function my0k_confirmation_text_callback() {
    $confirmation_text = esc_attr(get_option('my0k_confirmation_text', 'Confirmación de entrega conforme. Declaro que los datos indicados corresponden a la persona que recibió el pedido.'));
    echo '<textarea name="my0k_confirmation_text" rows="5" cols="50">' . $confirmation_text . '</textarea>';
}

function my0k_logo_url_callback() {
    $logo_url = esc_url(get_option('my0k_logo_url', ''));
    echo '<input type="text" name="my0k_logo_url" value="' . $logo_url . '" size="50">';
}

