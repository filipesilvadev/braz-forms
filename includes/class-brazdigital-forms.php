<?php
/**
 * Classe principal do plugin BrazDigital Forms
 */
class BrazDigital_Forms {
    
    /**
     * Instância do manipulador de formulários
     */
    protected $form_handler;
    
    /**
     * Instância do gerador de shortcodes
     */
    protected $shortcode_generator;
    
    /**
     * Instância do enviador de emails
     */
    protected $mail_sender;
    
    /**
     * Inicializa o plugin, definindo as classes e hooks
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    /**
     * Carrega as dependências necessárias
     */
    private function load_dependencies() {
      // Inclui os arquivos das classes necessárias
      require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-form-handler.php';
      require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-shortcode-generator.php';
      require_once BRAZDIGITAL_FORMS_PLUGIN_DIR . 'includes/class-mail-sender.php';
      
      // Instancia as classes
      $this->form_handler = new BrazDigital_Form_Handler();
      $this->shortcode_generator = new BrazDigital_Shortcode_Generator();
      $this->mail_sender = new BrazDigital_Mail_Sender();
    }
    
    /**
     * Define os hooks da área administrativa
     */
    private function define_admin_hooks() {
        // Adiciona o menu no painel administrativo
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registra os assets (CSS e JS) do admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Registra o Ajax para criar/editar formulários
        add_action('wp_ajax_save_brazdigital_form', array($this->form_handler, 'save_form'));

        // Registra o Ajax para testar as configurações SMTP
        add_action('wp_ajax_test_smtp_connection', array($this, 'test_smtp_connection'));
        
        // Registra o Ajax para excluir formulários
        add_action('wp_ajax_delete_brazdigital_form', array($this->form_handler, 'delete_form'));
    }
    
    /**
     * Define os hooks públicos (front-end)
     */
    private function define_public_hooks() {
        // Registra os assets (CSS e JS) do front-end
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        
        // Registra o shortcode para exibir o formulário
        add_action('init', array($this->shortcode_generator, 'register_shortcodes'));
        
        // Registra o Ajax para submissão do formulário
        add_action('wp_ajax_submit_service_form', array($this->form_handler, 'handle_submission'));
        add_action('wp_ajax_nopriv_submit_service_form', array($this->form_handler, 'handle_submission'));
    }
    
    /**
     * Executa o plugin
     */
    public function run() {
        // Plugin inicializado
    }
    
    /**
     * Adiciona o menu no painel administrativo
     */
    public function add_admin_menu() {
        add_menu_page(
            'BrazDigital Forms',
            'BrazDigital Forms',
            'manage_options',
            'brazdigital-forms',
            array($this, 'display_admin_page'),
            'dashicons-feedback',
            20
        );
        
        add_submenu_page(
            'brazdigital-forms',
            'Todos os Formulários',
            'Todos os Formulários',
            'manage_options',
            'brazdigital-forms',
            array($this, 'display_admin_page')
        );
        
        add_submenu_page(
            'brazdigital-forms',
            'Adicionar Novo',
            'Adicionar Novo',
            'manage_options',
            'brazdigital-forms-new',
            array($this, 'display_form_editor')
        );
    }
    
    /**
     * Carrega os assets (CSS e JS) da área administrativa
     */
    public function enqueue_admin_assets($hook) {
        // Verifica se estamos na página do nosso plugin
        if (strpos($hook, 'brazdigital-forms') === false) {
            return;
        }
        
        // CSS
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('brazdigital-forms-admin', BRAZDIGITAL_FORMS_PLUGIN_URL . 'assets/css/admin.css', array(), BRAZDIGITAL_FORMS_VERSION);
        
        // JavaScript
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('brazdigital-forms-admin', BRAZDIGITAL_FORMS_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), BRAZDIGITAL_FORMS_VERSION, true);
        
        // Localize script para passar variáveis para o JavaScript
        wp_localize_script('brazdigital-forms-admin', 'brazdigital_forms', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('brazdigital_forms_nonce')
        ));
    }
    
    /**
     * Carrega os assets (CSS e JS) do front-end
     */
    public function enqueue_public_assets() {
        // O CSS será injetado diretamente pelo shortcode com os estilos personalizados
        wp_enqueue_script('jquery');
        wp_enqueue_script('brazdigital-forms-public', BRAZDIGITAL_FORMS_PLUGIN_URL . 'assets/js/form.js', array('jquery'), BRAZDIGITAL_FORMS_VERSION, true);
        
        // Localize script para passar variáveis para o JavaScript
        wp_localize_script('brazdigital-forms-public', 'brazdigital_forms', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('brazdigital_forms_public_nonce')
        ));
    }
    
    /**
     * Exibe a página administrativa principal
     */
    public function display_admin_page() {
        // Busca todos os formulários do banco de dados
        global $wpdb;
        $table_name = $wpdb->prefix . 'brazdigital_forms';
        $forms = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
        
        // Inclui o template da página administrativa
        include BRAZDIGITAL_FORMS_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    /**
     * Exibe o editor de formulários
     */
    public function display_form_editor() {
        // Verifica se estamos editando um formulário existente
        $form_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $form = null;
        
        if ($form_id > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'brazdigital_forms';
            $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $form_id));
        }
        
        // Inclui o arquivo com os ícones padrão
        $default_icons = include BRAZDIGITAL_FORMS_PLUGIN_DIR . 'assets/icons/default-icons.php';
        
        // Inclui o template do editor de formulário
        include BRAZDIGITAL_FORMS_PLUGIN_DIR . 'templates/form-settings.php';
    }

    /**
     * Testa as configurações SMTP
     */
    public function test_smtp_connection() {
      // Verifica o nonce de segurança
      if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'brazdigital_forms_nonce')) {
          wp_send_json_error('Erro de segurança. Recarregue a página e tente novamente.');
      }
      
      // Verifica permissões
      if (!current_user_can('manage_options')) {
          wp_send_json_error('Você não tem permissão para realizar esta ação.');
      }
      
      // Obtém os dados do formulário
      $host = isset($_POST['host']) ? sanitize_text_field($_POST['host']) : '';
      $port = isset($_POST['port']) ? intval($_POST['port']) : 0;
      $user = isset($_POST['user']) ? sanitize_text_field($_POST['user']) : '';
      $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
      $secure = isset($_POST['secure']) ? sanitize_text_field($_POST['secure']) : 'tls';
      
      // Testa as configurações SMTP
      $result = $this->mail_sender->test_smtp($host, $port, $user, $pass, $secure);
      
      if ($result['success']) {
          wp_send_json_success(array('message' => $result['message']));
      } else {
          wp_send_json_error(array('message' => $result['message']));
      }
    }
}