<?php
/**
 * Plugin Name: BrazDigital Forms
 * Plugin URI: https://brazdigital.com/
 * Description: Um plugin de formulários personalizáveis para serviços com opções de estilo, notificações por e-mail e shortcodes.
 * Version: 1.0.0
 * Author: Filipe Oliveira
 * Author URI: https://brazdigital.com/
 * Text Domain: brazdigital-forms
 * Domain Path: /languages
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('BRAZDIGITAL_FORMS_VERSION', '1.0.0');
define('BRAZDIGITAL_FORMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRAZDIGITAL_FORMS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BRAZDIGITAL_FORMS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Incluir arquivos principais
require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-brazdigital-forms.php';
require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-form-handler.php';
require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-mail-sender.php';
require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-shortcode-generator.php';

// Inicializar o plugin
function brazdigital_forms_init() {
    global $wpdb;
    $plugin = new BrazDigital_Forms();
    $plugin->run();
}

// Hooks de ativação e desativação
register_activation_hook(__FILE__, 'brazdigital_forms_activate');
register_deactivation_hook(__FILE__, 'brazdigital_forms_deactivate');

function brazdigital_forms_activate() {
    // Criação de tabelas necessárias no banco de dados
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'brazdigital_forms';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        primary_color varchar(20) DEFAULT '#ef3a24',
        secondary_color varchar(20) DEFAULT '#ffffff',
        background_color varchar(20) DEFAULT '#ffffff',
        glass_effect tinyint(1) DEFAULT 1,
        company_name varchar(255) DEFAULT 'Business name',
        services longtext,
        recipients text,
        smtp_host varchar(255),
        smtp_port int(5),
        smtp_user varchar(255),
        smtp_pass varchar(255),
        smtp_secure varchar(10),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Adicionar versão ao banco de dados
    add_option('brazdigital_forms_version', BRAZDIGITAL_FORMS_VERSION);
    
    // Criar formulário padrão
    if (!$wpdb->get_var("SELECT COUNT(*) FROM {$table_name}")) {
        $default_services = json_encode([
            [
                'name' => 'Custom Home',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
            ],
            [
                'name' => 'Framing',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18"></path><path d="M3 9h18"></path><path d="M3 15h18"></path><path d="M3 21h18"></path><path d="M6 3v18"></path><path d="M12 3v18"></path><path d="M18 3v18"></path></svg>'
            ],
            [
                'name' => 'Remodeling',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V8a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v13"/><path d="M10 9h4"/><path d="M10 12h4"/><path d="M10 15h4"/><path d="M13 5v4"/><path d="M19 12v.01"/><path d="M19 15v.01"/><path d="M19 18v.01"/><path d="M5 12v.01"/><path d="M5 15v.01"/><path d="M5 18v.01"/><path d="M15 3.3V5.8c0 .6-.4 1-1 1h-4c-.6 0-1-.4-1-1V3.3c0-.6.4-1 1-1h4c.6 0 1 .4 1 1z"/></svg>'
            ]
        ]);
        
        $wpdb->insert(
            $table_name,
            [
                'name' => 'Standard form',
                'primary_color' => '#ef3a24',
                'secondary_color' => '#ffffff',
                'background_color' => '#ffffff',
                'glass_effect' => 1,
                'company_name' => 'Business name',
                'services' => $default_services,
                'recipients' => get_option('admin_email')
            ]
        );
    }
}

function brazdigital_forms_deactivate() {
    // Ações na desativação do plugin
}

// Iniciar o plugin
brazdigital_forms_init();