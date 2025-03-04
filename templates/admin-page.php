<?php
// Impede o acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>BrazDigital Forms</h1>
    
    <div class="notice notice-info">
        <p>Crie e gerencie seus formulários de serviços personalizados. Use o shortcode <code>[brazdigital_form id=X]</code> para exibir o formulário em qualquer página, substituindo X pelo ID do formulário.</p>
    </div>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=brazdigital-forms" class="nav-tab nav-tab-active">Todos os Formulários</a>
        <a href="?page=brazdigital-forms-new" class="nav-tab">Adicionar Novo</a>
    </h2>
    
    <div class="tablenav top">
        <div class="alignleft actions">
            <a href="?page=brazdigital-forms-new" class="button button-primary">Adicionar Novo Formulário</a>
        </div>
        <br class="clear">
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-id">ID</th>
                <th scope="col" class="manage-column column-name">Nome</th>
                <th scope="col" class="manage-column column-shortcode">Shortcode</th>
                <th scope="col" class="manage-column column-date">Data de Criação</th>
                <th scope="col" class="manage-column column-actions">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($forms)) : ?>
                <tr>
                    <td colspan="5">Nenhum formulário encontrado. <a href="?page=brazdigital-forms-new">Criar um novo formulário</a>.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($forms as $form) : ?>
                    <tr>
                        <td><?php echo esc_html($form->id); ?></td>
                        <td><?php echo esc_html($form->name); ?></td>
                        <td><code>[brazdigital_form id=<?php echo esc_html($form->id); ?>]</code> <button type="button" class="copy-shortcode button button-small" data-shortcode="[brazdigital_form id=<?php echo esc_html($form->id); ?>]">Copiar</button></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($form->created_at))); ?></td>
                        <td>
                            <a href="?page=brazdigital-forms-new&id=<?php echo esc_attr($form->id); ?>" class="button button-small">Editar</a>
                            <button type="button" class="button button-small delete-form" data-id="<?php echo esc_attr($form->id); ?>" data-name="<?php echo esc_attr($form->name); ?>">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
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
        
        // Confirmar exclusão
        $('.delete-form').on('click', function() {
            var formId = $(this).data('id');
            var formName = $(this).data('name');
            
            if (confirm('Tem certeza que deseja excluir o formulário "' + formName + '"? Esta ação não pode ser desfeita.')) {
                $.post(brazdigital_forms.ajax_url, {
                    action: 'delete_brazdigital_form',
                    form_id: formId,
                    nonce: brazdigital_forms.nonce
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data);
                    }
                });
            }
        });
    });
</script>