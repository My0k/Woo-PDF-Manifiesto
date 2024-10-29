<?php
if (!class_exists('WC_Settings_My0k', false)) {
    class WC_Settings_My0k extends WC_Settings_Page {

        public function __construct() {
            $this->id    = 'my0k';
            $this->label = __('PDF Manifiesto', 'woocommerce');

            // Agregar pestaña en WooCommerce
            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }

        // Definir configuraciones para la pestaña
        public function get_settings() {
            $settings = array(
                'section_title' => array(
                    'name'     => __('Configuración de PDF Manifiesto', 'woocommerce'),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'my0k_section_title'
                ),
                'custom_text' => array(
                    'name' => __('Texto Personalizado', 'woocommerce'),
                    'type' => 'textarea',
                    'desc' => __('Texto que aparece en la página de impresión para indicar dónde encontrar la pestaña de configuración.', 'woocommerce'),
                    'id'   => 'my0k_custom_text',
                    'default' => 'Donde encontrar la pestaña para modificar el texto'
                ),
                'confirmation_text' => array(
                    'name' => __('Texto de Confirmación de Entrega', 'woocommerce'),
                    'type' => 'textarea',
                    'desc' => __('Texto de confirmación de entrega que aparece en la página de impresión.', 'woocommerce'),
                    'id'   => 'my0k_confirmation_text',
                    'default' => 'Confirmación de entrega conforme. Declaro que los datos indicados corresponden a la persona que recibió el pedido.'
                ),
                'logo_url' => array(
                    'name' => __('URL del Logo', 'woocommerce'),
                    'type' => 'text',
                    'desc' => __('Ingrese la URL del logo para mostrar en la página de impresión.', 'woocommerce'),
                    'id'   => 'my0k_logo_url'
                ),
                'section_end' => array(
                    'type' => 'sectionend',
                    'id'   => 'my0k_section_end'
                )
            );
            return apply_filters('wc_settings_my0k_settings', $settings);
        }

        // Generar campos de configuración en WooCommerce
        public function output() {
            $settings = $this->get_settings();
            WC_Admin_Settings::output_fields($settings);
        }

        // Guardar los campos configurados
        public function save() {
            $settings = $this->get_settings();
            WC_Admin_Settings::save_fields($settings);
        }
    }
}

