<?php
/**
 * Classe para manipulação dos formulários
 */
class BrazDigital_Form_Handler {
    
    /**
     * Salva um formulário (criar ou atualizar)
     */
    public function save_form() {
        // Verifica o nonce de segurança
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'brazdigital_forms_nonce')) {
            wp_send_json_error('Erro de segurança. Recarregue a página e tente novamente.');
        }
        
        // Verifica permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Você não tem permissão para realizar esta ação.');
        }
        
        // Obtém os dados do formulário
        $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
        $form_name = sanitize_text_field($_POST['form_name']);
        $primary_color = sanitize_text_field($_POST['primary_color']);
        $secondary_color = sanitize_text_field($_POST['secondary_color']);
        $background_color = sanitize_text_field($_POST['background_color']);
        $glass_effect = isset($_POST['glass_effect']) ? 1 : 0;
        $company_name = sanitize_text_field($_POST['company_name']);
        
        // Validar e sanitizar os serviços
        $services = array();
        if (isset($_POST['service_name']) && is_array($_POST['service_name'])) {
            for ($i = 0; $i < count($_POST['service_name']); $i++) {
                if (!empty($_POST['service_name'][$i])) {
                    $services[] = array(
                        'name' => sanitize_text_field($_POST['service_name'][$i]),
                        'icon' => sanitize_text_field($_POST['service_icon'][$i])
                    );
                }
            }
        }
        
        // Sanitizar e validar os destinatários de e-mail
        $recipients = sanitize_textarea_field($_POST['recipients']);
        $recipients_array = array_map('trim', explode(',', $recipients));
        $valid_recipients = array();
        
        foreach ($recipients_array as $email) {
            if (is_email($email)) {
                $valid_recipients[] = sanitize_email($email);
            }
        }
        
        $recipients = implode(',', $valid_recipients);
        
        // Sanitizar configurações SMTP
        $smtp_host = sanitize_text_field($_POST['smtp_host']);
        $smtp_port = intval($_POST['smtp_port']);
        $smtp_user = sanitize_text_field($_POST['smtp_user']);
        $smtp_pass = $_POST['smtp_pass']; // Não sanitizamos senhas para não corromper caracteres especiais
        $smtp_secure = sanitize_text_field($_POST['smtp_secure']);

        $background_opacity = intval($_POST['background_opacity']);
        if ($background_opacity < 0) $background_opacity = 0;
        if ($background_opacity > 100) $background_opacity = 100;
        
        // Preparar os dados para o banco de dados
        $data = array(
            'name' => $form_name,
            'primary_color' => $primary_color,
            'secondary_color' => $secondary_color,
            'background_color' => $background_color,
            'glass_effect' => $glass_effect,
            'company_name' => $company_name,
            'services' => json_encode($services),
            'recipients' => $recipients,
            'smtp_host' => $smtp_host,
            'smtp_port' => $smtp_port,
            'smtp_user' => $smtp_user,
            'smtp_secure' => $smtp_secure,
            'background_opacity' => $background_opacity
        );
        
        // Apenas atualiza a senha se uma nova senha for fornecida
        if (!empty($smtp_pass)) {
            $data['smtp_pass'] = $smtp_pass;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'brazdigital_forms';
        
        // Atualizar ou inserir no banco de dados
        if ($form_id > 0) {
            // Atualizar formulário existente
            $wpdb->update(
                $table_name,
                $data,
                array('id' => $form_id)
            );
            
            wp_send_json_success(array(
                'message' => 'Formulário atualizado com sucesso!',
                'form_id' => $form_id
            ));
        } else {
            // Criar novo formulário
            $wpdb->insert($table_name, $data);
            $new_form_id = $wpdb->insert_id;
            
            wp_send_json_success(array(
                'message' => 'Formulário criado com sucesso!',
                'form_id' => $new_form_id
            ));
        }
    }
    
    /**
     * Exclui um formulário
     */
    public function delete_form() {
        // Verifica o nonce de segurança
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'brazdigital_forms_nonce')) {
            wp_send_json_error('Erro de segurança. Recarregue a página e tente novamente.');
        }
        
        // Verifica permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Você não tem permissão para realizar esta ação.');
        }
        
        // Obtém o ID do formulário
        $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
        
        if ($form_id <= 0) {
            wp_send_json_error('ID de formulário inválido.');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'brazdigital_forms';
        
        // Exclui o formulário
        $result = $wpdb->delete(
            $table_name,
            array('id' => $form_id),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error('Não foi possível excluir o formulário.');
        }
        
        wp_send_json_success(array(
            'message' => 'Formulário excluído com sucesso!'
        ));
    }
    
/**
 * Processa a submissão do formulário pelo front-end
 */
public function handle_submission() {
  // Define a resposta padrão como JSON
  header('Content-Type: application/json');
  
  // Validar dados
  $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
  $service = isset($_POST['service']) ? sanitize_text_field($_POST['service']) : '';
  $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
  $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
  $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
  $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
  $project = isset($_POST['project']) ? sanitize_textarea_field($_POST['project']) : '';
  
  // Validação básica
  if (empty($service) || empty($location) || empty($name) || empty($email) || empty($phone) || empty($project)) {
      wp_send_json_error('Por favor, preencha todos os campos obrigatórios.');
      return;
  }
  
  if (!is_email($email)) {
      wp_send_json_error('Por favor, forneça um endereço de e-mail válido.');
      return;
  }
  
  // Buscar informações do formulário no banco de dados
  global $wpdb;
  $table_name = $wpdb->prefix . 'brazdigital_forms';
  $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $form_id));
  
  if (!$form) {
      wp_send_json_error('Formulário não encontrado.');
      return;
  }
  
  // Preparar e enviar o e-mail
  $to = $form->recipients;
  if (empty($to)) {
      $to = get_option('admin_email'); // Usar e-mail do administrador como fallback
  }
  
  $subject = "Novo formulário de serviço: {$service}";
  
  $message = "Um novo formulário de serviço foi preenchido:<br><br>";
  $message .= "<strong>Serviço:</strong> {$service}<br>";
  $message .= "<strong>Localização:</strong> {$location}<br>";
  $message .= "<strong>Nome:</strong> {$name}<br>";
  $message .= "<strong>E-mail:</strong> {$email}<br>";
  $message .= "<strong>Telefone:</strong> {$phone}<br>";
  $message .= "<strong>Projeto:</strong> {$project}<br>";
  
  $headers = array('Content-Type: text/html; charset=UTF-8');
  
  // Se as configurações SMTP estiverem preenchidas, use-as para enviar o e-mail
  if (!empty($form->smtp_host) && !empty($form->smtp_user) && !empty($form->smtp_pass)) {
      // Incluir a classe de e-mail se ainda não estiver incluída
      if (!class_exists('BrazDigital_Mail_Sender')) {
          require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-mail-sender.php';
      }
      
      $mail_sender = new BrazDigital_Mail_Sender();
      $sent = $mail_sender->send_smtp_email($form, $to, $subject, $message);
  } else {
      // Caso contrário, use a função wp_mail padrão
      $sent = wp_mail($to, $subject, $message, $headers);
  }
  
  if ($sent) {
      wp_send_json_success('Formulário enviado com sucesso!');
  } else {
      // Tente novamente sem formatação HTML
      $plain_message = strip_tags(str_replace('<br>', "\n", $message));
      $plain_headers = array('From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>');
      
      $sent = wp_mail($to, $subject, $plain_message, $plain_headers);
      
      if ($sent) {
          wp_send_json_success('Formulário enviado com sucesso!');
      } else {
          wp_send_json_error('Não foi possível enviar o formulário. Por favor, tente novamente mais tarde.');
      }
  }
}
}