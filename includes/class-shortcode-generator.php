<?php

/**
 * Shortcode Generator Class
 *
 * @link       https://brazdigital.net
 * @since      1.0.0
 *
 * @package    BrazDigital_Forms
 * @subpackage BrazDigital_Forms/includes
 */

/**
 * Shortcode Generator Class
 *
 * @since      1.0.0
 * @package    BrazDigital_Forms
 * @subpackage BrazDigital_Forms/includes
 * @author     Braz Digital <contato@brazdigital.net>
 */
class BrazDigital_Shortcode_Generator {

    public function __construct() {
        // Construtor vazio, os shortcodes são registrados via método register_shortcodes()
    }

    /**
     * Registra os shortcodes do plugin
     */
    public function register_shortcodes() {
        add_shortcode('brazdigital_form', array($this, 'render_form_shortcode'));
    }

    /**
     * Renderiza o formulário via shortcode
     * 
     * @param array $atts Atributos do shortcode
     * @return string HTML do formulário
     */
    public function render_form_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts,
            'brazdigital_form'
        );

        if (empty($atts['id'])) {
            return '<p>É necessário informar o ID do formulário.</p>';
        }

        // Buscar o formulário no banco de dados
        global $wpdb;
        $table_name = $wpdb->prefix . 'brazdigital_forms';
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $atts['id']));
        
        if (!$form) {
            return '<p>Formulário não encontrado.</p>';
        }
        
        // Incluir o template do formulário
        ob_start();
        include BRAZDIGITAL_FORMS_PLUGIN_DIR . 'templates/form-template.php';
        return ob_get_clean();
    }

/**
 * Gera o CSS personalizado com base nas configurações do formulário
 * 
 * @param object $form Objeto do formulário com as configurações
 * @return string CSS personalizado
 */
public function generate_custom_css($form) {
  $form_id = $form->id;
  $primary_color = $form->primary_color;
  $secondary_color = $form->secondary_color;
  $background_color = $form->background_color;
  $glass_effect = $form->glass_effect;
  
  $css = "
  /* CSS personalizado para o formulário #{$form_id} */
  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
  h2.step-title {
      display: flex;
      flex-direction: column;
      font-size: 21px;
  }
  h2.step-title::after {
      display: inline-block;
      width: 100px;
      height: 1px;
      content: \"\";
      background: {$primary_color};
      margin: 0 auto 5px;
  }
  .step-form {
      max-width: 600px;
      margin: 0 auto;
      font-family: Arial, sans-serif;
      padding: 20px;
      background: {$background_color}26;
      border-radius: 8px;
      margin-bottom: 20px;
      backdrop-filter: blur(15px);
      border: 1px solid #676766;
  }
  .step {
      display: none;
  }
  .step.active {
      display: block;
  }
  .progress-container {
      position: relative;
      width: 98%;
      margin: 0 auto 20px;
      display: none;
  }
  .progress-bar {
      width: 100%;
      height: 3px;
      background: #e0e0e0;
      margin: 0 auto;
      border-radius: 4px;
  }
  .progress {
      height: 100%;
      background: {$primary_color};
      border-radius: 4px;
      transition: width 0.3s ease;
  }
  .progress-steps {
      display: flex;
      justify-content: space-between;
      position: absolute;
      width: 100%;
      top: -10px;
  }
  .step-label {
      background: #fff;
      padding: 0 10px;
      color: #666;
      font-size: 10px;
      border-radius: 20px;
      text-transform: uppercase;
  }
  .step-label.active {
      background: {$primary_color};
      font-weight: 500;
      color: #fff;
  }
  .step-title {
      font-size: 24px;
      margin-bottom: 20px;
      color: #333;
      text-align: center;
  }
  .form-group {
      margin-bottom: 10px;
  }
  input[type=\"text\"],
  input[type=\"email\"],
  input[type=\"tel\"],
  select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
  }
  .service-options {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
  }
  .service-option {
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      line-height: 1.25em;
      font-size: 15px;
  }
  .service-option:hover {
      border-color: {$primary_color};
  }
  .service-option.selected {
      background: {$primary_color};
      color: white;
      border-color: {$primary_color};
  }
  .service-option i {
      font-size: 24px;
      margin-bottom: 8px;
  }
  .buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
  }
  .single-field-step {
      display: flex;
      gap: 10px;
  }
  .single-field-step .form-group {
      flex: 1;
      margin-bottom: 0;
  }
  button {
      padding: 12px 24px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
  }
  button.prev {
      background: #666;
      color: white;
  }
  button.next, button[onclick=\"submitForm()\"] {
      background: {$primary_color};
      color: #ffffff;
  }
  button:hover {
      opacity: 0.9;
  }
  textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
      min-height: 100px;
      resize: vertical;
  }";
  
  return $css;
}
    
    /**
     * Converte cor hexadecimal para RGBA
     * 
     * @param string $hex Cor em formato hexadecimal
     * @param float $alpha Valor alpha (0-1)
     * @return string Cor em formato RGBA
     */
    private function hex_to_rgba($hex, $alpha = 1) {
        $hex = str_replace('#', '', $hex);
        
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}