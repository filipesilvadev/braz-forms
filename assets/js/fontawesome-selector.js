/**
 * Script para gerenciamento do seletor de ícones Font Awesome
 */
(function($) {
  'use strict';
  
  // Quando o DOM estiver pronto
  $(document).ready(function() {
      // Função para abrir o modal de seleção de ícones
      window.openIconSelector = function(buttonElement) {
          var targetInput = $(buttonElement).siblings('.icon-input');
          
          // Abrir o modal
          $('#icon-selector-modal').data('target-input', targetInput).show();
          
          // Atualizar o ícone selecionado
          var currentIcon = targetInput.val();
          if (currentIcon) {
              $('#icon-selector-modal .icon-item').removeClass('selected');
              $('#icon-selector-modal .icon-item[data-icon="' + currentIcon + '"]').addClass('selected');
          }
      };
      
      // Fechar o modal
      $('.close-modal').on('click', function() {
          $('#icon-selector-modal').hide();
      });
      
      // Ao clicar em qualquer lugar fora do modal, fechar o modal
      $(window).on('click', function(event) {
          if ($(event.target).is('#icon-selector-modal')) {
              $('#icon-selector-modal').hide();
          }
      });
      
      // Alternar entre as abas
      $('.icon-tab-btn').on('click', function() {
          var category = $(this).data('category');
          
          // Ativar botão da aba
          $('.icon-tab-btn').removeClass('active');
          $(this).addClass('active');
          
          // Mostrar conteúdo da aba
          $('.icon-tab-content').removeClass('active');
          $('.icon-tab-content[data-category="' + category + '"]').addClass('active');
      });
      
      // Filtrar ícones ao digitar na busca
      $('#icon-search').on('input', function() {
          var searchTerm = $(this).val().toLowerCase();
          
          $('.icon-item').each(function() {
              var iconName = $(this).data('name').toLowerCase();
              if (iconName.indexOf(searchTerm) > -1) {
                  $(this).show();
              } else {
                  $(this).hide();
              }
          });
      });
      
      // Selecionar ícone
      $(document).on('click', '.icon-item', function() {
          var iconValue = $(this).data('icon');
          var targetInput = $('#icon-selector-modal').data('target-input');
          
          // Atualizar o input com o ícone selecionado
          targetInput.val(iconValue);
          
          // Atualizar a pré-visualização
          var previewSpan = targetInput.siblings('.icon-preview');
          previewSpan.html('<i class="' + iconValue + '"></i>');
          
          // Destacar o ícone selecionado
          $('.icon-item').removeClass('selected');
          $(this).addClass('selected');
          
          // Fechar o modal
          $('#icon-selector-modal').hide();
      });
  });
  
})(jQuery);