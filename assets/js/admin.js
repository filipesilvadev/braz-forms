/**
 * Script para gerenciamento do formulário na área administrativa
 */
(function($) {
  'use strict';
  
  // Quando o DOM estiver pronto
  $(document).ready(function() {
      // Inicializar seletores de cor
      if ($.fn.wpColorPicker) {
          $('.color-picker').wpColorPicker();
      }
      
      // Adicionar um novo serviço
      $('#add-service').on('click', function() {
          var serviceRow = `
              <div class="service-row">
                  <p>
                      <input type="text" name="service_name[]" placeholder="Nome do Serviço" class="regular-text" required>
                      <input type="hidden" name="service_icon[]" class="icon-input" value="fa-solid fa-house">
                      <span class="icon-preview"><i class="fa-solid fa-house"></i></span>
                      <button type="button" class="button button-secondary icon-selector-button" onclick="openIconSelector(this)">Selecionar Ícone</button>
                      <button type="button" class="button button-secondary remove-service">Remover</button>
                  </p>
              </div>
          `;
          $('#services-container').append(serviceRow);
      });
      
      // Remover um serviço
      $(document).on('click', '.remove-service', function() {
          $(this).closest('.service-row').remove();
      });

      // Atualizar valor do slider de opacidade em tempo real
      $('#background_opacity').on('input', function() {
        $('#opacity-value').text($(this).val() + '%');
      });
      
      // Copiar shortcode para a área de transferência
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
          
          // Verificar campos obrigatórios
          var host = $('#smtp_host').val();
          var port = $('#smtp_port').val();
          var user = $('#smtp_user').val();
          var pass = $('#smtp_pass').val();
          
          if (!host || !port || !user) {
              resultSpan.html('<span style="color: red;">Preencha o host, porta e usuário para testar.</span>');
              return;
          }
          
          // Se for para manter a senha, verificamos se já existe uma senha no servidor
          if (!pass && $('#smtp_pass').data('has-previous')) {
              // Tem senha anterior, pode continuar
          } else if (!pass) {
              resultSpan.html('<span style="color: red;">A senha SMTP é obrigatória para testar.</span>');
              return;
          }
          
          // Desabilitar botão durante o teste
          button.prop('disabled', true).text('Testando...');
          resultSpan.html('<span style="color: #666;">Enviando e-mail de teste...</span>');
          
          // Enviar solicitação Ajax para testar SMTP
          $.ajax({
              url: brazdigital_forms.ajax_url,
              type: 'POST',
              data: {
                  action: 'test_smtp_connection',
                  nonce: brazdigital_forms.nonce,
                  host: host,
                  port: port,
                  user: user,
                  pass: pass,
                  secure: $('#smtp_secure').val()
              },
              success: function(response) {
                  button.prop('disabled', false).text('Testar Configurações SMTP');
                  
                  if (response.success) {
                      resultSpan.html('<span style="color: green;">' + response.data.message + '</span>');
                  } else {
                      resultSpan.html('<span style="color: red;">' + response.data + '</span>');
                  }
              },
              error: function() {
                  button.prop('disabled', false).text('Testar Configurações SMTP');
                  resultSpan.html('<span style="color: red;">Erro na requisição. Tente novamente.</span>');
              }
          });
      });
      
      // Enviar formulário de configurações via Ajax
      $('#brazdigital-form-settings').on('submit', function(e) {
          e.preventDefault();
          
          var form = $(this);
          var submitButton = $('#save-form');
          
          // Verificar campos obrigatórios
          var formName = $('#form_name').val();
          var companyName = $('#company_name').val();
          
          if (!formName || !companyName) {
              alert('Por favor, preencha o nome do formulário e o nome da empresa.');
              return;
          }
          
          // Verificar se há pelo menos um serviço
          if ($('.service-row').length === 0) {
              alert('Adicione pelo menos um serviço ao formulário.');
              return;
          }
          
          // Verificar recipientes de e-mail
          var recipients = $('#recipients').val();
          if (!recipients) {
              alert('Por favor, adicione pelo menos um e-mail de destinatário.');
              return;
          }
          
          // Desabilitar botão durante o envio
          submitButton.prop('disabled', true).val('Salvando...');
          
          // Enviar formulário via Ajax
          $.ajax({
              url: brazdigital_forms.ajax_url,
              type: 'POST',
              data: form.serialize(),
              success: function(response) {
                  submitButton.prop('disabled', false).val('Salvar Formulário');
                  
                  if (response.success) {
                      // Se for um novo formulário, redirecione para a página de edição
                      if ($('input[name="form_id"]').val() === '0') {
                          window.location.href = 'admin.php?page=brazdigital-forms-new&id=' + response.data.form_id + '&saved=1';
                      } else {
                          // Mostrar mensagem de sucesso
                          alert(response.data.message);
                      }
                  } else {
                      alert(response.data);
                  }
              },
              error: function() {
                  submitButton.prop('disabled', false).val('Salvar Formulário');
                  alert('Ocorreu um erro ao salvar o formulário. Por favor, tente novamente.');
              }
          });
      });
      
      // Confirmar exclusão de formulário
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
      
      // Mostrar mensagem caso tenha salvado o formulário
      if (window.location.search.indexOf('saved=1') > -1) {
          alert('Formulário salvo com sucesso!');
      }
    });
  
})(jQuery);