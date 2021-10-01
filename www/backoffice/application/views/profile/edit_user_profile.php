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
<div class="container edit-form-container">
    <div class="row center-block">
        <div class="">
            <h2><?php echo $title ?></h2>
            <form role="form" class="edit-profile-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input type="text" class="form-control" name="firstname" value="<?php echo $user->user_firstname ?>">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Last name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $user->user_name ?>">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <?php
                    if ($this->auth->get_role() == 'host' && $this->auth->get_id() != $user->user_id){ ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group combo">
                                    <label for="organisation">Organisation</label>
                                    <select id="org-combo" class="form-control" name="organisation"></select>
                                    <!--<div class="help-block with-errors"></div>-->
                                </div>
                            </div>
                        </div>

                <?php } ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="office">Office/Unit</label>
                            <input type="text" class="form-control" name="office" value="<?php echo $user->user_office != '' && $user->user_office != '0' ? $user->user_office: '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="function">Function</label>
                            <input type="text" class="form-control" name="function" value="<?php echo $user->user_function != '' && $user->user_function != '0' ? $user->user_function : '' ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <div class="input-group phone-group">
                                <span class="input-group-addon">
                                    <select id="phone-combo" class="form-control" name="country_code"></select>
                                </span>
                                <input type="text" class="form-control" name="phone" value="">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    if ($this->auth->get_id() == $user->user_id){
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="checkbox" id="user_accept_pub" name="user_accept_pub" value="<?php echo $user->user_accept_pub ?>" <?php if ($user->user_accept_pub == 1) {echo 'checked';} ?>>
                            <label class="check-label" for="user_accept_pub">I accept the publishing of my personal information which will be visible on the public OOC website.</label>
                        </div>
                    </div>
                </div>
                <?php } elseif ($this->auth->get_role() == 'host'){
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="checkbox" id="user_is_host" name="user_is_host" value="<?php echo $user->user_role ?>" <?php if ($user->user_role == 'host') {echo 'checked';} ?>>
                            <label class="check-label" for="user_is_host">Has host privileges</label>
                        </div>
                    </div>
                </div>
                <?php
                    }
                ?>
                <input name="user_id" value="<?php echo $user->user_id ?>" hidden>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row center-block error-msg hidden">
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
            "href": "<?php
                if ($this->auth->get_role() == 'host' ){
                    echo site_url('hosts/policy');
                } else {
                    echo site_url('commitments/policy');
                }
            ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix').$this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    jQuery(document).ready(function(){
        var combo;
        var form = $('form');
        var alertError = $('.alert-danger').parent();
        <?php
            if ($this->auth->get_role() == 'poc' ){
                $url = site_url('commitments/set_profile/'.$user->user_id);
            } else {
                $url = site_url('hosts/set_profile/'.$user->user_id);
            }
            echo 'var url = \''.$url.'\';'
        ?>

        $.ajax({
            type: 'GET',
            url: '<?php echo site_url('assets/json/country_phone_codes.json') ?>',
            dataType: 'json',
            success: function(data){
                processJson(data);
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
                phone: {
                    validators: {
                        callback: {
                            message: 'Please provide a valid phone number',
                            callback: validatePhoneNumber
                        }
                    }
                }
            }
        });

        var validator = form.data('bootstrapValidator');

        function processJson(data){
            var items = [];
            for (var i = 0; i < data.length; i++){
                items.push({
                    id: data[i].phoneCode,
                    text: '[' + data[i].code + '] ' + data[i].countryName
                });
            }

            setupCountryCombo(items);
        }

        function formatCodes(item){
            if (!item.element) {
                return null;
            }
            var tpl = $('<span>' + item.text + '<span class="phone-code"> +' + item.element.value + '</span><span>');
            return tpl;
        }
        
        function searchCodes(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            if (typeof data.text === 'undefined') {
                return null;
            }

            var text = data.text.toLowerCase();
            var id = '+' + data.id;
            if (text.indexOf(params.term.toLowerCase()) > -1 || id.indexOf(params.term) > -1) {
                return data;
            }

            return null;
        }

        function setPhoneFields(number){
            try {
                var phoneNr = new libphonenumber.parsePhoneNumber(number);
                $('[name="phone"]').val(phoneNr.nationalNumber);
                combo.val(phoneNr.countryCallingCode).trigger('change');
            } catch (e) {
                console.log(e.message);
            }

        }

        function setupCountryCombo(data) {
            combo = $('#phone-combo').select2({
                theme:"bootstrap",
                placeholder: '',
                data: data,
                templateSelection: formatCodes,
                templateResult: formatCodes,
                matcher: searchCodes
            });

            setPhoneFields('<?php echo $user->user_phone ?>');

            combo.on('select2:select', function (evt) {
                if ($('[name="phone"').val() !== ''){
                    validator.revalidateField('phone');
                }
            });

        }

        function validatePhoneNumber(value, validator, $field){
            var isValid = false;
            if (value !== ''){
                try {
                    var tmpNr = '+' + $('#phone-combo').val() + value;
                    var phoneNr = new libphonenumber.parsePhoneNumber(tmpNr);
                    if (phoneNr.isValid()){
                        isValid = true;
                    } else {
                        isValid = false;
                    }
                } catch (e) {
                    isValid = false;
                }
            }

            return isValid;
        }

        <?php
            if ($this->auth->get_role() == 'host' && $this->auth->get_id() != $user->user_id){ ?>
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


        <?php
            if ($organisation != null) {
                echo "var newOption = new Option('".$organisation->applicant_legal_name."', ".$organisation->applicant_id.", true, true);";
                echo "$('#org-combo').append(newOption).trigger('change');";
            }
        } ?>

        function submit(){
            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true){
                var data = form.serializeObject();
                if ($('[name="phone"]').val() !== ''){
                    data.phone = '+' + data.country_code + data.phone;
                    data.phone = data.phone.replace(/\s/g, "");
                }
                delete data.country_code;

                <?php
                    if ($this->auth->get_id() == $user->user_id){
                ?>
                    data.user_accept_pub = $('[name="user_accept_pub"]:checked').length;
                <?php
                    }
                    if ($this->auth->get_id() != $user->user_id && $this->auth->get_role() == 'host'){
                ?>
                    data.user_is_host = $('[name="user_is_host"]:checked').length;
                <?php
                    }
                ?>

                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        if (data.success) {
                            window.location = data.url;
                        }
                    },
                    error: function (data) {
                        alertError.removeClass('hidden');
                        var cell = $('.table-cell').last()
                        var span = cell.children('span');
                        span.html(data.responseJSON.message);
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
