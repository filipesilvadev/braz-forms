<?php
// Impede o acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Gera CSS personalizado
$shortcode_generator = new BrazDigital_Shortcode_Generator();
$custom_css = $shortcode_generator->generate_custom_css($form);

// Decodifica os serviços
$services = json_decode($form->services, true);
if (!is_array($services)) {
    $services = array();
}

// ID único para o formulário
$form_id = 'brazdigital-form-' . $form->id;
?>

<style>
    <?php echo $custom_css; ?>
</style>

<div class="step-form" id="<?php echo esc_attr($form_id); ?>">
    <div class="progress-container">
        <div class="progress-steps">
            <span class="step-label active">1</span>
            <span class="step-label">2</span>
            <span class="step-label">3</span>
        </div>
        <div class="progress-bar">
            <div class="progress" style="width: 33.33%"></div>
        </div>
    </div>
    
    <!-- Step 1 -->
    <div class="step active" id="step1">
        <h2 class="step-title">Select a Service</h2>
        <div class="service-options">
          <?php foreach ($services as $index => $service) : ?>
              <div class="service-option" onclick="selectService(this, '<?php echo esc_attr($service['name']); ?>')">
                  <i class="<?php echo esc_attr($service['icon']); ?>"></i>
                  <span><?php echo esc_html($service['name']); ?></span>
              </div>
          <?php endforeach; ?>
        </div>
        <input type="hidden" id="selected-service" name="service">
        <div class="buttons">
            <button class="next" onclick="nextStep(1)">Next</button>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="step" id="step2">
        <h2 class="step-title">Location</h2>
        <div class="form-group">
            <input type="text" id="location" name="location" required placeholder="Address">
        </div>
        <div class="buttons">
            <button class="prev" onclick="prevStep(2)">Previous</button>
            <button class="next" onclick="nextStep(2)">Next</button>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="step" id="step3">
        <h2 class="step-title">Contact Information</h2>
        <div class="form-group">
            <input type="text" id="name" name="name" required placeholder="Name">
        </div>
        <div class="form-group">
            <input type="email" id="email" name="email" required placeholder=" E-mail">
        </div>
        <div class="form-group">
            <input type="tel" id="phone" name="phone" required placeholder="Phone">
        </div>
        <div class="form-group">
            <textarea id="project" name="project" required placeholder="Tell us about your project" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; min-height: 100px;"></textarea>
        </div>
        <div class="buttons">
            <button class="prev" onclick="prevStep(3)">Previous</button>
            <button class="next" onclick="submitForm()">Submit</button>
        </div>
    </div>
</div>

<script>
(function() {
    // Variáveis globais para este formulário específico
    var formId = '<?php echo esc_js($form->id); ?>';
    var companyName = '<?php echo esc_js($form->company_name); ?>';
    var currentStep = 1;
    
    // Adiciona as funções ao escopo global
    window.selectService = function(element, service) {
        var formElement = document.getElementById('<?php echo esc_js($form_id); ?>');
        var options = formElement.querySelectorAll('.service-option');
        options.forEach(function(opt) {
            opt.classList.remove('selected');
        });
        element.classList.add('selected');
        formElement.querySelector('#selected-service').value = service;
    };
    
    window.nextStep = function(step) {
        if (!validateStep(step)) return;
        var formElement = document.getElementById('<?php echo esc_js($form_id); ?>');
        formElement.querySelector('#step' + step).classList.remove('active');
        formElement.querySelector('#step' + (step + 1)).classList.add('active');
        currentStep = step + 1;
        updateProgressBar();
    };
    
    window.prevStep = function(step) {
        var formElement = document.getElementById('<?php echo esc_js($form_id); ?>');
        formElement.querySelector('#step' + step).classList.remove('active');
        formElement.querySelector('#step' + (step - 1)).classList.add('active');
        currentStep = step - 1;
        updateProgressBar();
    };
    
    function updateProgressBar() {
        var formElement = document.getElementById('<?php echo esc_js($form_id); ?>');
        var progress = formElement.querySelector('.progress');
        var labels = formElement.querySelectorAll('.step-label');
        
        progress.style.width = ((currentStep / 3) * 100) + '%';
        
        labels.forEach(function(label, index) {
            label.classList.toggle('active', index + 1 === currentStep);
        });
    }
    
    function validateStep(step) {
        var formElement = document.getElementById('<?php echo esc_js($form_id); ?>');
        if (step === 1) {
            return formElement.querySelector('#selected-service').value !== '';
        } else if (step === 2) {
            return formElement.querySelector('#location').value !== '';
        }
        return formElement.querySelector('#name').value !== '' && 
               formElement.querySelector('#email').value !== '' && 
               formElement.querySelector('#phone').value !== '' &&
               formElement.querySelector('#project').value !== '';
    }
    
    window.submitForm = function() {
        if (!validateStep(3)) return;
        
        var formElement = document.getElementById('<?php echo esc_js($form_id); ?>');
        
        // Cria um objeto com os dados do formulário
        var formData = {
            action: 'submit_service_form',
            form_id: formId,
            service: formElement.querySelector('#selected-service').value,
            location: formElement.querySelector('#location').value,
            name: formElement.querySelector('#name').value,
            email: formElement.querySelector('#email').value,
            phone: formElement.querySelector('#phone').value,
            project: formElement.querySelector('#project').value
        };
        
        // Desabilita o botão de envio para evitar múltiplos envios
        var submitButton = formElement.querySelector('button[onclick="submitForm()"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando...';
        
        // Envia os dados via Ajax
        jQuery.post(brazdigital_forms.ajax_url, formData)
            .done(function(response) {
                if (response.success) {
                    formElement.querySelector('#step3').innerHTML = `
                        <h2 class="step-title">Thanks!</h2>
                        <div style="text-align: center; padding: 40px 20px;">
                            <p style="font-size: 18px; margin-bottom: 20px;">Thank you for your interest in our services!</p>
                            <p style="font-size: 16px;">The ${companyName} team will contact you soon.</p>
                        </div>
                    `;
                } else {
                    alert(response.data || 'Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Submit';
                }
            })
            .fail(function() {
                alert('Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
                submitButton.disabled = false;
                submitButton.textContent = 'Submit';
            });
    };
})();
</script>