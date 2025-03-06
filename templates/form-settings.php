<?php
// Impede o acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define valores padrão se estiver criando um novo formulário
if (empty($form)) {
    $form = (object) array(
        'id' => 0,
        'name' => '',
        'primary_color' => '#ef3a24',
        'secondary_color' => '#ffffff',
        'background_color' => '#ffffff',
        'glass_effect' => 1,
        'company_name' => 'Business Name',
        'services' => json_encode(array()),
        'recipients' => get_option('admin_email'),
        'smtp_host' => '',
        'smtp_port' => '587',
        'smtp_user' => '',
        'smtp_pass' => '',
        'smtp_secure' => 'tls'
    );
}

// Decodifica os serviços
$services = json_decode($form->services, true);
if (!is_array($services)) {
    $services = array();
}

// Lista de categorias de ícones Font Awesome
$icon_categories = array(
  'Construction' => array(
      'fa-solid fa-house', 'fa-solid fa-wrench', 'fa-solid fa-hammer', 'fa-solid fa-paint-roller', 
      'fa-solid fa-screwdriver', 'fa-solid fa-kitchen-set', 'fa-solid fa-roof', 
      'fa-solid fa-plug', 'fa-solid fa-faucet', 'fa-solid fa-ruler', 'fa-solid fa-temperature-arrow-up',
      'fa-solid fa-window-maximize', 'fa-solid fa-couch', 'fa-solid fa-car-side', 'fa-solid fa-bath', 
      'fa-solid fa-shower', 'fa-solid fa-sink', 'fa-solid fa-solar-panel', 'fa-solid fa-toolbox', 
      'fa-solid fa-truck', 'fa-solid fa-broom', 'fa-solid fa-stairs', 'fa-solid fa-ladder',
      'fa-solid fa-trowel', 'fa-solid fa-trowel-bricks', 'fa-solid fa-helmet-safety', 
      'fa-solid fa-fence', 'fa-solid fa-house-chimney', 'fa-solid fa-paint-brush',
      'fa-solid fa-screwdriver-wrench', 'fa-solid fa-toilet', 
      'fa-solid fa-faucet-drip', 'fa-solid fa-fan', 'fa-solid fa-door-open',
      'fa-solid fa-door-closed', 'fa-solid fa-plug-circle-bolt', 'fa-solid fa-lightbulb'
  ),
  'Tree Services' => array(
      'fa-solid fa-tree', 'fa-solid fa-seedling', 'fa-solid fa-leaf', 'fa-solid fa-plant-wilt',
      'fa-solid fa-spa', 'fa-solid fa-pagelines', 'fa-solid fa-cloud-sun', 'fa-solid fa-sun',
      'fa-solid fa-mountain-sun', 'fa-solid fa-water', 'fa-solid fa-tractor', 'fa-solid fa-shovel',
      'fa-solid fa-scissors', 'fa-solid fa-bugs', 'fa-solid fa-bug', 'fa-solid fa-mosquito',
      'fa-solid fa-truck-monster', 'fa-solid fa-truck-pickup', 'fa-solid fa-axe', 
      'fa-solid fa-worm', 'fa-solid fa-location-dot', 'fa-solid fa-map-location-dot',
      'fa-solid fa-wheat-awn', 'fa-solid fa-spray-can', 'fa-solid fa-map',
      'fa-solid fa-compass', 'fa-solid fa-person-digging', 'fa-solid fa-briefcase'
  ),
  'Brands' => array(
      'fa-brands fa-facebook', 'fa-brands fa-twitter', 'fa-brands fa-instagram', 'fa-brands fa-youtube',
      'fa-brands fa-whatsapp', 'fa-brands fa-pinterest', 'fa-brands fa-linkedin', 'fa-brands fa-tiktok',
      'fa-brands fa-telegram', 'fa-brands fa-slack', 'fa-brands fa-google', 'fa-brands fa-apple'
  )
);
?>
<div class="wrap">
    <h1><?php echo ($form->id > 0) ? 'Editar Formulário' : 'Novo Formulário'; ?></h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=brazdigital-forms" class="nav-tab">Todos os Formulários</a>
        <a href="?page=brazdigital-forms-new" class="nav-tab nav-tab-active">Adicionar Novo</a>
    </h2>
    
    <form id="brazdigital-form-settings" method="post">
        <input type="hidden" name="form_id" value="<?php echo esc_attr($form->id); ?>">
        <input type="hidden" name="action" value="save_brazdigital_form">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('brazdigital_forms_nonce'); ?>">
        
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="postbox">
                        <h2 class="hndle"><span>Informações Básicas</span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="form_name">Nome do Formulário</label></th>
                                    <td>
                                        <input type="text" id="form_name" name="form_name" value="<?php echo esc_attr($form->name); ?>" class="regular-text" required>
                                        <p class="description">Nome para identificar este formulário (não será exibido para os usuários).</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="company_name">Nome da Empresa</label></th>
                                    <td>
                                        <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($form->company_name); ?>" class="regular-text" required>
                                        <p class="description">Nome da empresa que será exibido na mensagem de sucesso.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <h2 class="hndle"><span>Configurações de Estilo</span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="primary_color">Cor Primária</label></th>
                                    <td>
                                        <input type="text" id="primary_color" name="primary_color" value="<?php echo esc_attr($form->primary_color); ?>" class="color-picker" data-default-color="#ef3a24">
                                        <p class="description">Cor principal para botões, destaques e elementos selecionados.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="secondary_color">Cor Secundária</label></th>
                                    <td>
                                        <input type="text" id="secondary_color" name="secondary_color" value="<?php echo esc_attr($form->secondary_color); ?>" class="color-picker" data-default-color="#ffffff">
                                        <p class="description">Cor de texto sobre elementos coloridos com a cor primária.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="background_color">Cor de Fundo</label></th>
                                    <td>
                                        <input type="text" id="background_color" name="background_color" value="<?php echo esc_attr($form->background_color); ?>" class="color-picker" data-default-color="#ffffff">
                                        <p class="description">Cor de fundo do formulário.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="glass_effect">Efeito Glass</label></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" id="glass_effect" name="glass_effect" value="1" <?php checked($form->glass_effect, 1); ?>>
                                            Ativar efeito de glass (blur) no fundo do formulário
                                        </label>
                                        <p class="description">Adiciona um efeito de transparência com blur no fundo do formulário.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <h2 class="hndle"><span>Opções de Serviços</span></h2>
                        <div class="inside">
                            <p>Adicione os serviços que estarão disponíveis para seleção no formulário.</p>
                            
                            <div id="services-container">
                                <?php if (empty($services)) : ?>
                                    <div class="service-row">
                                        <p>
                                            <input type="text" name="service_name[]" placeholder="Nome do Serviço" class="regular-text" required>
                                            <input type="hidden" name="service_icon[]" class="icon-input" value="fa-solid fa-house">
                                            <span class="icon-preview"><i class="fa-solid fa-house"></i></span>
                                            <button type="button" class="button button-secondary icon-selector-button" onclick="openIconSelector(this)">Selecionar Ícone</button>
                                            <button type="button" class="button button-secondary remove-service" style="display:none;">Remover</button>
                                        </p>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($services as $service) : ?>
                                        <div class="service-row">
                                            <p>
                                                <input type="text" name="service_name[]" value="<?php echo esc_attr($service['name']); ?>" placeholder="Nome do Serviço" class="regular-text" required>
                                                <input type="hidden" name="service_icon[]" class="icon-input" value="<?php echo esc_attr($service['icon']); ?>">
                                                <span class="icon-preview"><i class="<?php echo esc_attr($service['icon']); ?>"></i></span>
                                                <button type="button" class="button button-secondary icon-selector-button" onclick="openIconSelector(this)">Selecionar Ícone</button>
                                                <button type="button" class="button button-secondary remove-service">Remover</button>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <p>
                                <button type="button" class="button button-secondary" id="add-service">Adicionar Serviço</button>
                            </p>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <h2 class="hndle"><span>Configurações de E-mail</span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="recipients">Destinatários</label></th>
                                    <td>
                                        <textarea id="recipients" name="recipients" class="large-text code" rows="3"><?php echo esc_textarea($form->recipients); ?></textarea>
                                        <p class="description">Endereços de e-mail que receberão as submissões do formulário (separados por vírgula).</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <h3>Configurações SMTP (opcional)</h3>
                            <p class="description">Configure o SMTP para envio de e-mails mais confiável. Deixe em branco para usar o wp_mail padrão.</p>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="smtp_host">Servidor SMTP</label></th>
                                    <td>
                                        <input type="text" id="smtp_host" name="smtp_host" value="<?php echo esc_attr($form->smtp_host); ?>" class="regular-text">
                                        <p class="description">Ex: smtp.gmail.com</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="smtp_port">Porta SMTP</label></th>
                                    <td>
                                        <input type="number" id="smtp_port" name="smtp_port" value="<?php echo esc_attr($form->smtp_port); ?>" class="small-text">
                                        <p class="description">Ex: 587 (TLS) ou 465 (SSL)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="smtp_secure">Segurança</label></th>
                                    <td>
                                        <select id="smtp_secure" name="smtp_secure">
                                            <option value="tls" <?php selected($form->smtp_secure, 'tls'); ?>>TLS</option>
                                            <option value="ssl" <?php selected($form->smtp_secure, 'ssl'); ?>>SSL</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="smtp_user">Usuário SMTP</label></th>
                                    <td>
                                        <input type="text" id="smtp_user" name="smtp_user" value="<?php echo esc_attr($form->smtp_user); ?>" class="regular-text">
                                        <p class="description">Geralmente seu endereço de e-mail completo</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="smtp_pass">Senha SMTP</label></th>
                                    <td>
                                        <input type="password" id="smtp_pass" name="smtp_pass" value="" class="regular-text" autocomplete="new-password" data-has-previous="<?php echo !empty($form->smtp_pass) ? '1' : ''; ?>">
                                        <p class="description">
                                            <?php if (!empty($form->smtp_pass)) : ?>
                                                A senha está definida. Deixe em branco para mantê-la ou insira uma nova senha.
                                            <?php else : ?>
                                                Insira a senha da sua conta de e-mail.
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td>
                                        <button type="button" id="test-smtp" class="button button-secondary">Testar Configurações SMTP</button>
                                        <span id="smtp-test-result"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle"><span>Salvar</span></h2>
                        <div class="inside">
                            <div class="submitbox">
                                <div id="major-publishing-actions">
                                    <div id="publishing-action">
                                        <input type="submit" name="save" id="save-form" class="button button-primary button-large" value="Salvar Formulário">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($form->id > 0) : ?>
                        <div class="postbox">
                            <h2 class="hndle"><span>Shortcode</span></h2>
                            <div class="inside">
                                <p>Use este shortcode para exibir o formulário em qualquer página ou post:</p>
                                <p><code>[brazdigital_form id=<?php echo esc_html($form->id); ?>]</code></p>
                                <button type="button" class="button button-secondary copy-shortcode" data-shortcode="[brazdigital_form id=<?php echo esc_html($form->id); ?>]">Copiar Shortcode</button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="postbox">
                        <h2 class="hndle"><span>Visualização</span></h2>
                        <div class="inside">
                            <p>Salve o formulário para visualizá-lo. Após salvar, você pode usar o shortcode em uma página para ver como ele ficará.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Modal do Seletor de Ícones -->
    <div id="icon-selector-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Selecione um Ícone</h2>
                <span class="close-modal">&times;</span>
            </div>
            <input type="text" id="icon-search" placeholder="Buscar ícones...">
            
            <!-- Abas de categorias -->
            <div class="icon-tabs">
                <?php $first_tab = true; ?>
                <?php foreach ($icon_categories as $category => $icons) : ?>
                    <button class="icon-tab-btn <?php echo $first_tab ? 'active' : ''; ?>" data-category="<?php echo esc_attr($category); ?>">
                        <?php echo esc_html($category); ?>
                    </button>
                    <?php $first_tab = false; ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Conteúdo das abas -->
            <?php $first_tab = true; ?>
            <?php foreach ($icon_categories as $category => $icons) : ?>
                <div class="icon-tab-content <?php echo $first_tab ? 'active' : ''; ?>" data-category="<?php echo esc_attr($category); ?>">
                    <div class="fa-icons-grid">
                        <?php foreach ($icons as $icon) : ?>
                            <div class="icon-item" data-icon="<?php echo esc_attr($icon); ?>" data-name="<?php echo esc_attr(str_replace(array('fa-', 'fa-solid ', 'fa-regular ', 'fa-brands '), '', $icon)); ?>">
                                <i class="<?php echo esc_attr($icon); ?>"></i>
                                <span><?php echo esc_html(str_replace(array('fa-', 'fa-solid ', 'fa-regular ', 'fa-brands '), '', $icon)); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php $first_tab = false; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>