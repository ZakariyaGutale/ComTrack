<?php
/* Copyright 2021 European Commission
    
Licensed under the EUPL, Version 1.2 only (the "Licence");
You may not use this work except in compliance with the Licence.

You may obtain a copy of the Licence at:
	https://joinup.ec.europa.eu/software/page/eupl5

Unless required by applicable law or agreed to in writing, software 
distributed under the Licence is distributed on an "AS IS" basis, 
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
express or implied.

See the Licence for the specific language governing permissions 
and limitations under the Licence. */
?>
<div class="container reset-container-1">
    <div class="row center-block login-container reset-form-container">
        <h2>Password recovery</h2>
        <form  role="form">
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
                <div class="help-block with-errors"></div>
            </div>
            <div class="row">
                <div class="g-recaptcha"
                     data-size="invisible"
                     data-sitekey="<?php echo $this->config->item('recaptcha_site_key') ?>"></div>
            </div>
        </form>
        <div class="pull-right">
            <button class="btn btn-primary" type="submit">Reset</button>
        </div>
    </div>
    <div class="row center-block register-container success-msg hidden">
        <div class="alert alert-success table-layout" role="alert">
            <div class="table-row">
                <div class="table-cell table-cell-middle">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="table-cell">
                    <span>A recovery email has been sent. Follow the link included in the email to update your password.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row center-block login-container error-msg hidden">
        <div class="col-md-12 alert alert-danger table-layout" role="alert">
            <div class="table-row">
                <div class="table-cell table-cell-middle alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="table-cell">
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback' async defer></script>
<script>
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                "background": "#252e39"
            },
            "button": {
                "background": "#14a7d0"
            }
        },
        "theme": "classic",
        "content": {
            "message": "By using our site, you acknowledge that you have read and agreed our Cookies and Data Policies.",
            "dismiss": '<i class="fas fa-times"></i>',
            "link": "Learn more",
            "href": "<?php echo site_url('hosts/policy') ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix').$this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    var onloadCallback = function(){
        grecaptcha.execute();
    };


    jQuery(document).ready(function(){
        var form = $('form');
        var alertSuccess = $('.alert-success').parent();
        var alertError = $('.alert-danger').parent();

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide your email address'
                        },
                        emailAddress: {
                            message: 'Please provide a valid email address'
                        }
                    }
                }
            }
        });

        function getFormData(){
            var data = form.serializeObject();

            if (data['g-recaptcha-response'] == ''){
                data['g-recaptcha-response'] = grecaptcha.getResponse();
            }

            return data;
        }


        function submit(){
            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true){
                alertError.addClass('hidden');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('hosts/do_recovery') ?>',
                    dataType: 'json',
                    data: getFormData(),
                    success: function (data) {
                        if (data.success){
                            alertSuccess.removeClass('hidden');
                            $('.reset-form-container').addClass('hidden');
                        } else {
                            alertError.removeClass('hidden');
                            var cell = $('.table-cell').last()
                            var span = cell.children('span');
                            span.html(data.message);
                        }
                    }
                });
            }
        }

        OOC.registerEnterKey(form, submit);

        $('button').click(function(){
            submit();
        });
    });

</script>
