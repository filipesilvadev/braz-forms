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
                                            <textarea name="service_icon[]" placeholder="SVG do ícone" class="large-text code" rows="3" required></textarea>
                                            <button type="button" class="button button-secondary remove-service" style="display:none;">Remover</button>
                                        </p>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($services as $service) : ?>
                                        <div class="service-row">
                                            <p>
                                                <input type="text" name="service_name[]" value="<?php echo esc_attr($service['name']); ?>" placeholder="Nome do Serviço" class="regular-text" required>
                                                <textarea name="service_icon[]" placeholder="SVG do ícone" class="large-text code" rows="3" required><?php echo esc_textarea($service['icon']); ?></textarea>
                                                <button type="button" class="button button-secondary remove-service">Remover</button>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <p>
                                <button type="button" class="button button-secondary" id="add-service">Adicionar Serviço</button>
                            </p>
                            
                            <div class="default-icons" style="margin-top: 20px;">
                                <h3>Ícones Predefinidos</h3>
                                <p>Clique em um ícone para usá-lo em um serviço:</p>
                                <div class="icons-grid">
                                    <?php foreach ($default_icons as $icon_name => $icon_svg) : ?>
                                        <div class="icon-item" data-icon="<?php echo esc_attr($icon_svg); ?>" title="<?php echo esc_attr($icon_name); ?>">
                                            <?php echo $icon_svg; ?>
                                            <span><?php echo esc_html($icon_name); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
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
                                        <input type="password" id="smtp_pass" name="smtp_pass" value="" class="regular-text" autocomplete="new-password">
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
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Inicializa os color pickers
        $('.color-picker').wpColorPicker();
        
        // Adicionar serviço
        $('#add-service').on('click', function() {
            var serviceRow = `
                <div class="service-row">
                    <p>
                        <input type="text" name="service_name[]" placeholder="Nome do Serviço" class="regular-text" required>
                        <textarea name="service_icon[]" placeholder="SVG do ícone" class="large-text code" rows="3" required></textarea>
                        <button type="button" class="button button-secondary remove-service">Remover</button>
                    </p>
                </div>
            `;
            $('#services-container').append(serviceRow);
        });
        
        // Remover serviço
        $(document).on('click', '.remove-service', function() {
            $(this).closest('.service-row').remove();
        });
        
        // Usar ícone predefinido
        $('.icon-item').on('click', function() {
            var iconSvg = $(this).data('icon');
            var activeTextarea = $('#services-container textarea:focus');
            
            if (activeTextarea.length === 0) {
                // Se nenhuma textarea estiver em foco, use a última
                activeTextarea = $('#services-container textarea').last();
            }
            
            activeTextarea.val(iconSvg);
        });
        
        // Copiar shortcode
        $('.copy-shortcode').on('click', function() {
            var shortcode = $(this).data('shortcode');
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(shortcode).select();
            document.execCommand('copy');
            tempInput.remove();
            
            var originalText = $(this).text();
            $(this).text('Copiado!');
            
            setTimeout(function() {
                $('.copy-shortcode').text(originalText);
            }, 2000);
        });
        
        // Testar configurações SMTP
        $('#test-smtp').on('click', function() {
            var button = $(this);
            var resultSpan = $('#smtp-test-result');
            
            button.prop('disabled', true).text('Testando...');
            resultSpan.html('<span style="color: #666;">Enviando e-mail de teste...</span>');
            
            $.post(brazdigital_forms.ajax_url, {
                action: 'test_smtp_connection',
                nonce: brazdigital_forms.nonce,
                host: $('#smtp_host').val(),
                port: $('#smtp_port').val(),
                user: $('#smtp_user').val(),
                pass: $('#smtp_pass').val(),
                secure: $('#smtp_secure').val()
            }, function(response) {
                button.prop('disabled', false).text('Testar Configurações SMTP');
                
                if (response.success) {
                    resultSpan.html('<span style="color: green;">' + response.data.message + '</span>');
                } else {
                    resultSpan.html('<span style="color: red;">' + response.data.message + '</span>');
                }
            }).fail(function() {
                button.prop('disabled', false).text('Testar Configurações SMTP');
                resultSpan.html('<span style="color: red;">Erro na requisição. Tente novamente.</span>');
            });
        });
        
        // Enviar formulário via AJAX
        $('#brazdigital-form-settings').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitButton = $('#save-form');
            
            submitButton.prop('disabled', true).val('Salvando...');
            
            $.post(brazdigital_forms.ajax_url, form.serialize(), function(response) {
                submitButton.prop('disabled', false).val('Salvar Formulário');
                
                if (response.success) {
                    // Se for um novo formulário, redirecione para a edição com o ID
                    if (form.find('input[name="form_id"]').val() === '0') {
                        window.location.href = 'admin.php?page=brazdigital-forms-new&id=' + response.data.form_id + '&saved=1';
                    } else {
                        alert(response.data.message);
                    }
                } else {
                    alert(response.data);
                }
            }).fail(function() {
                submitButton.prop('disabled', false).val('Salvar Formulário');
                alert('Ocorreu um erro ao salvar o formulário. Por favor, tente novamente.');
            });
        });
    });
</script>