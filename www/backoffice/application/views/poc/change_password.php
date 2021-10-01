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
<div class="container reset-container">
    <div class="row center-block login-container">
        <h2>Reset password</h2>
        <form  role="form">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password">
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm password</label>
                <input type="password" class="form-control" name="confirm">
                <div class="help-block with-errors"></div>
            </div>
        </form>
        <div class="pull-right">
            <button class="btn btn-primary" type="submit">Reset</button>
        </div>

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

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
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

        function submit(){
            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('commitments/do_change_password/'.$key) ?>',
                    dataType: 'json',
                    data: form.serializeObject(),
                    success: function (data) {
                        if (data.success){
                            window.location = data.url;
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
