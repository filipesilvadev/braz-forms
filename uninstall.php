<?php
/**
 * Arquivo executado quando o plugin é desinstalado
 * 
 * Este arquivo limpa todos os dados relacionados ao plugin do banco de dados
 */

// Se o arquivo não for chamado pelo WordPress, aborte
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove a tabela do banco de dados
global $wpdb;
$table_name = $wpdb->prefix . 'brazdigital_forms';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Remove as opções do banco de dados
delete_option('brazdigital_forms_version');