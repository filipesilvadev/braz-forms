<?php
/**
 * Classe para enviar e-mails com suporte a SMTP
 */
class BrazDigital_Mail_Sender {
    
    /**
     * Envia um e-mail usando as configurações SMTP
     */
    public function send_smtp_email($form, $to, $subject, $message) {
        // Adicionar filtro para configurar o PHPMailer para usar SMTP
        add_action('phpmailer_init', function($phpmailer) use ($form) {
            $phpmailer->isSMTP();
            $phpmailer->Host = $form->smtp_host;
            $phpmailer->SMTPAuth = true;
            $phpmailer->Port = $form->smtp_port;
            $phpmailer->Username = $form->smtp_user;
            $phpmailer->Password = $form->smtp_pass;
            $phpmailer->SMTPSecure = $form->smtp_secure;
            $phpmailer->From = $form->smtp_user;
            $phpmailer->FromName = $form->company_name;
        });
        
        // Envia o e-mail
        $headers = array('Content-Type: text/html; charset=UTF-8');
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Testa as configurações SMTP
     */
    public function test_smtp($host, $port, $user, $pass, $secure) {
        // Validar os parâmetros
        if (empty($host) || empty($port) || empty($user) || empty($pass)) {
            return array(
                'success' => false,
                'message' => 'Todas as configurações SMTP são obrigatórias para o teste.'
            );
        }
        
        // Salvar configurações originais do wp_mail
        $original_phpmailer = $GLOBALS['phpmailer'];
        
        // Configurar um novo PHPMailer para teste
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $pass;
            $mail->SMTPSecure = $secure;
            $mail->Port = $port;
            
            // Configurar remetente e destinatário para teste
            $mail->setFrom($user, 'Teste BrazDigital Forms');
            $mail->addAddress(get_option('admin_email'));
            
            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Teste de Configuração SMTP - BrazDigital Forms';
            $mail->Body = 'Este é um e-mail de teste enviado pelo plugin BrazDigital Forms para verificar as configurações SMTP.';
            
            // Enviar e-mail
            $mail->send();
            
            // Restaurar phpmailer original
            $GLOBALS['phpmailer'] = $original_phpmailer;
            
            return array(
                'success' => true,
                'message' => 'Conexão SMTP estabelecida com sucesso! Um e-mail de teste foi enviado para ' . get_option('admin_email')
            );
        } catch (Exception $e) {
            // Restaurar phpmailer original
            $GLOBALS['phpmailer'] = $original_phpmailer;
            
            return array(
                'success' => false,
                'message' => 'Erro ao conectar-se ao servidor SMTP: ' . $mail->ErrorInfo
            );
        }
    }
}