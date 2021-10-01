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
<div class="container register-container">
    <div class="row center-block login-container">
        <div class="register-form-container">
            <h2>Register</h2>
            <p><small>Already have an account? Go to <a href="<?php echo site_url() ?>">sign in</a>.</small></p>
            <form role="form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input type="text" class="form-control" name="firstname">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Last name</label>
                            <input type="text" class="form-control" name="name">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group combo">
                            <label for="organisation">Organisation</label>
                            <?php if ($organisation) {?>
                                <input type="text" class="form-control" name="organisation" value="<?php echo $organisation->applicant_legal_name ?>" disabled="true">
                            <?php } else { ?>
                                <select id="org-combo" class="form-control" name="organisation"></select>
                                <div class="help-block with-errors"></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Email</label>
                            <input type="email" class="form-control" name="email">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="confirm">Confirm password</label>
                            <input type="password" class="form-control" name="confirm">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="g-recaptcha"
                         data-size="invisible"
                         data-sitekey="<?php echo $this->config->item('recaptcha_site_key') ?>"></div>
                    <input type="hidden" id="captcha-response" name="captcha-response">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row center-block register-container success-msg hidden">
        <div class="alert alert-success table-layout" role="alert">
            <div class="table-row">
                <div class="table-cell table-cell-middle">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="table-cell">
                    <span>Your registration has been successfully submitted and will be reviewed in the next 48 hours.<br>
                        Once your registration is accepted by the host, you will receive an activation email.<br>
                        In some cases this email might land on your spam folder, so please be sure to check it also.<br>
                        If you are facing any technical issues please contact <a href = "mailto: ouroceanconference@gmail.com">ouroceanconference@gmail.com</a>
                    </span>
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
            "href": "<?php echo site_url('commitments/policy') ?>"
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

        $('#org-combo').select2({
            theme:"bootstrap",
            minimumInputLength: 1,
            tags: true,
            delay: 250,
            templateResult: function(data){
                var text = data.text;
                if (isNaN(parseInt(data.id))){
                    text = "Create New Organisation: " + data.text;
                }
                return text;

            },
            ajax: {
                type: 'POST',
                url: '<?php echo site_url('rest/organisation') ?>',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    }

                    return query;
                },
                processResults: function (data, params) {
                    var page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: data.pagination
                    }
                },
                cache: true
            }
        });

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                firstname: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide your first name'
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide your last name'
                        }
                    }
                },
                organisation: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide your organisation name'
                        }
                    }
                },
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
                        stringLength: {
                            min: 6,
                            max: 16,
                            message: "Password must be between 6 and 16 characters long"
                        },
                        notEmpty: {
                            message: 'Please provide your password'
                        }
                    }
                },
                confirm: {
                    validators: {
                        identical: {
                            field: 'password',
                            message: "The password and the confirmation don't match"
                        },
                        notEmpty: {
                            message: 'Please provide your password'
                        }
                    }
                }
            }
        });

        function getFormData(){
            <?php
                if (isset($organisation)) {
                    echo 'var isInvitation = true;';
                    echo 'var id = '.$organisation->applicant_id.';';
                } else {
                    echo 'var isInvitation = false;';
                }
            ?>

            var data = form.serializeObject();
            if (isInvitation){
                data.organisation = id;
            }

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
                    url: '<?php echo site_url('commitments/do_register') ?>',
                    dataType: 'json',
                    data: getFormData(),
                    success: function (data) {
                        if(data.registered) {
                            alertSuccess.removeClass('hidden');
                            $('.register-form-container').addClass('hidden');
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
