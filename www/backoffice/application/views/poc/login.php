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
<div class="container">
    <div class="row center-block login-container">
        <h2>Sign in</h2>
        <form  role="form">
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
                <div class="help-block with-errors"></div>
            </div>
        </form>
        <div class="pull-left">
            <a href="<?php echo site_url('commitments/reset_password') ?>" title="reset my password">Forgot your password?</a>
            <p><a href="<?php echo site_url('commitments/register') ?>" title="request an account">Register an account</a></p>
        </div>
        <div class="pull-right">
            <button class="btn btn-primary" type="submit">Sign in</button>
        </div>

    </div>
    <div class="row center-block login-container alert-msg hidden">
        <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle"></i><span>Could not sign you in! Check your credentials.</span></div>
    </div>
</div>
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
            "href": "<?php echo site_url('commitments/policy') ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix').$this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });



    jQuery(document).ready(function() {
        var form = $('form');
        var alert = $('.alert-msg');

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
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide your password'
                        }
                    }
                }
            }
        });

        function submit(){
            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true){
                alert.addClass('hidden');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('commitments/do_login') ?>',
                    dataType: 'json',
                    data: form.serializeObject(),
                    success: function (data) {
                        if (data.success) {
                            window.location = '<?php echo $target ?>';
                        } else {
                            alert.removeClass('hidden');
                        }
                    }
                });
            }
        }

        OOC.registerEnterKey(form, submit);
        OOC.removeFromLocalStorage('ooc-dashboard');
        OOC.removeDataTablesStorage('all');

        $('button').click(function(){
            submit();
        });
    });
</script>
