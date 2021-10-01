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
            <h2>Edit Organisation Profile</h2>
            <form role="form" class="edit-organisation-form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="applicant_legal_name">Organisation name</label>
                            <input type="text" class="form-control" name="applicant_legal_name" value="<?php echo $organisation->applicant_legal_name ?>">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group combo">
                            <label for="applicant_type">Entity</label>
                            <select id="type-combo" class="form-control" name="applicant_type"></select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="applicant_web_page">Website</label>
                            <input type="text" class="form-control" name="applicant_web_page" value="<?php echo $organisation->applicant_web_page ?>">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row center-block error-msg hidden">
                    <div class="col-md-12 alert alert-warning table-layout" role="alert">
                        <div class="table-row">
                            <div class="table-cell table-cell-middle alert-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="table-cell">
                                <span>It was not possible to retrieve the details of your address. Please check your configurations.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="applicant_street">Address</label>
                                    <input type="text" class="form-control" name="applicant_street" placeholder="house number, address" value="<?php echo $organisation->applicant_street ?>">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="applicant_post_code">Postal Code</label>
                                    <input type="text" class="form-control" name="applicant_post_code" value="<?php echo $organisation->applicant_post_code ?>">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="applicant_city">City</label>
                                    <input type="text" class="form-control" name="applicant_city" value="<?php echo $organisation->applicant_city ?>">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group combo">
                                    <label for="applicant_country_code">Country</label>
                                    <select id="country-combo" class="form-control" name="applicant_country_code">
                                        <?php
                                        foreach ($this->config->item('countries') as $code => $label){
                                            if ($code == $organisation->applicant_country_code){
                                                echo '<option value="'.$code.'" selected="selected">'.$label.'</option>';
                                            } else {
                                                echo '<option value="'.$code.'">'.$label.'</option>';
                                            }
                                        } ?>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" class="form-control" name="applicant_osm_lng" value="<?php echo $organisation->applicant_osm_lng ?>">
                    <input type="hidden" class="form-control" name="applicant_osm_lat" value="<?php echo $organisation->applicant_osm_lat ?>">
                </div>
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
        var form = $('form');
        var alertWarning = $('.alert-warning').parent();
        var alertError = $('.alert-danger').parent();
        <?php
            if ($this->auth->get_role() == 'poc' ){
                $url = site_url('commitments/set_organisation/'.$organisation->applicant_id);
            } else {
                $url = site_url('hosts/set_organisation/'.$organisation->applicant_id);
            }
            echo 'var url = \''.$url.'\';'
        ?>

        var map = new L.Map('map');
        var nominatim = {
            marker: undefined,
            params: {},
            isDragRegistered: false,
            view: {
                lat: 0,
                lon: 0,
                zoom: 1
            },
            searchResult: {}
        }

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                applicant_legal_name: {
                    validators: {
                        notEmpty: {
                            message: "Please provide your organisation's name"
                        }
                    }
                },
                applicant_type: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide a valid entity type'
                        }
                    }
                },
                applicant_web_page: {
                    validators: {
                        uri: {
                            message: 'Please provide a valid website address (e.g. http://www.domain.com)'
                        }
                    }
                },
                applicant_street: {
                    validators: {
                        notEmpty: {
                            message: "Please provide your organization's address"
                        }
                    }
                },
                applicant_post_code: {
                    validators: {
                        notEmpty: {
                            message: "Please provide your organization's postal code"
                        }
                    }
                },
                applicant_city: {
                    validators: {
                        notEmpty: {
                            message: "Please provide your organization's city"
                        }
                    }
                },
                applicant_country_code: {
                    validators: {
                        notEmpty: {
                            message: "Please provide your organization's country"
                        }
                    }
                }
            }
        });

        var validator = form.data('bootstrapValidator');
        validator.enableFieldValidators('applicant_type', false);
        validator.enableFieldValidators('applicant_country_code', false);
        validator.enableFieldValidators('applicant_web_page', false);

        function processTypes(data){
            var items = [];
            for (var i = 0; i < data.length; i++){
                items.push({
                    id: data[i].id,
                    text: data[i].name
                });
            }

            return items;
        }

        var typeCombo = $('#type-combo').select2({
            theme: 'bootstrap',
            placeholder: '',
            data: processTypes(<?php echo json_encode($types) ?>)
        });

        typeCombo.val('<?php echo $organisation->applicant_type ?>').trigger('change');

        var countryCombo = $('#country-combo').select2({
            theme:"bootstrap",
            minimumInputLength: 1,
            tags: false
        });

        countryCombo.val('<?php echo $organisation->applicant_country_code ?>').trigger('change');

        countryCombo.on('select2:select', function (evt) {
            var value = evt.params.data.text;
            if (value !== ''){
                nominatim.params.country = value;
            } else {
                delete nominatim.params.country;
            }
            getNominatim();
        });

        $('[name="applicant_web_page"').change(function(){
            validator.enableFieldValidators('applicant_web_page', true);
            validator.revalidateField('applicant_web_page');
        });

        //do not allow spaces
        $('[name="applicant_web_page"').on({
            keydown: function(e) {
                if (e.which === 32)
                    return false;
            },
            change: function() {
                this.value = this.value.replace(/\s/g, "");
            }
        });

        $('[name="applicant_city"').change(function(){
            var value = $( this ).val()
            if (value !== ''){
                nominatim.params.city = value;
            } else {
                delete nominatim.params.city;
            }
            getNominatim();
        });

        $('[name="applicant_street"').change(function(){
            var value = $( this ).val();
            if (value !== ''){
                nominatim.params.street = value;
            } else {
                delete nominatim.params.street;
            }
            getNominatim();
        });

        function buildNominatimParms(){
            var query = {
                format: 'json',
                addressdetails: 1,
                'accept-language': 'en-us',
                limit: 1
            };

            Object.assign(query, nominatim.params);

            return query;
        }

        function registerDragEvent() {
            nominatim.marker.on('drag', function(event) {
                $('[name="applicant_osm_lng"]').val(Math.round(nominatim.marker.getLatLng().lng * 10000) / 10000);
                $('[name="applicant_osm_lat"]').val(Math.round(nominatim.marker.getLatLng().lat * 10000) / 10000);
            });
            nominatim.isDragEventRegistered = true;
        }

        function updateMap() {
            var coord = {
                lat: nominatim.searchResult.lat,
                lon: nominatim.searchResult.lon
            }

            if (typeof nominatim.marker !== 'undefined'){
                map.removeLayer(nominatim.marker);
            }

            nominatim.marker = L.marker(coord,{draggable:'true'}).addTo(map);

            if (!nominatim.isDragEventRegistered){
                registerDragEvent();
            }

            map.flyToBounds([
                [nominatim.searchResult.boundingbox[0], nominatim.searchResult.boundingbox[2]],
                [nominatim.searchResult.boundingbox[1], nominatim.searchResult.boundingbox[3]]
            ]);

            $('[name="applicant_osm_lng"]').val(Math.round(coord.lon * 10000) / 10000);
            $('[name="applicant_osm_lat"]').val(Math.round(coord.lat * 10000) / 10000);
        }

        function checkCountryCodeAndName(countryCode, countryName){
            var comboOptions = $('#country-combo').find("option");
            for (let i = 0; i < comboOptions.length ; i++) {
                var option = comboOptions[i];
                if (countryCode.toUpperCase() == option.value || countryName.toUpperCase() == option.text.toUpperCase()){
                    return option.value;
                }
            }

            return null;
        }

        function updateFields(){
            if (nominatim.searchResult.address.hasOwnProperty('postcode') && $('[name="applicant_post_code"]').val() !== nominatim.searchResult.address.postcode){
                $('[name="applicant_post_code"]').val(nominatim.searchResult.address.postcode);
                validator.revalidateField('applicant_post_code');
            }

            if (nominatim.searchResult.address.hasOwnProperty('country_code') && countryCombo.val() !== nominatim.searchResult.address.country_code.toUpperCase()){
                var code = checkCountryCodeAndName(nominatim.searchResult.address.country_code, nominatim.searchResult.address.country);
                if (code !== null){
                    countryCombo.val(code).trigger('change');
                    validator.enableFieldValidators('applicant_country_code', true);
                    validator.revalidateField('applicant_country_code');
                    nominatim.params.country = nominatim.searchResult.address.country_code;
                }
            }

            if (nominatim.searchResult.address.hasOwnProperty('city_district') &&  $('[name="applicant_city"]').val() !== nominatim.searchResult.address.city_district){
                $('[name="applicant_city"]').val(nominatim.searchResult.address.city_district);
                validator.revalidateField('applicant_city');
                nominatim.params.city = nominatim.searchResult.address.city_district;
            }
        }

        function checkResults(data){
            if (data.length > 0){
                var osmData = data[0];

                if (Object.keys(nominatim.searchResult).length === 0 || nominatim.searchResult.lat !== osmData.lat || nominatim.searchResult.lon !== osmData.lon){
                    nominatim.searchResult = osmData;
                }

                updateMap();
                updateFields()
            } else {
                alertWarning.removeClass('hidden');
            }
        }

        function getNominatim(){
            alertWarning.addClass('hidden');
            $.ajax({
                type: 'GET',
                url: 'https://nominatim.openstreetmap.org/search',
                dataType: 'json',
                data: buildNominatimParms(),
                success: function(data){
                    checkResults(data);
                }
            });
        }

        var osm = OOC.getOsmLayer();
        map.addLayer(osm);

        function setInitialMarker(){
            if ($('[name="applicant_osm_lng"]').val() !== '0.0000' && $('[name="applicant_osm_lat"]').val() !== '0.0000'){
                var coord = {
                    lat: $('[name="applicant_osm_lat"]').val(),
                    lon: $('[name="applicant_osm_lng"]').val()
                }
                nominatim.marker = L.marker(coord,{draggable:'true'}).addTo(map);
                registerDragEvent();
                nominatim.view = {
                    lat: coord.lat,
                    lon: coord.lon,
                    zoom: 18
                }
            }

        }

        setInitialMarker();

        map.setView({lat: nominatim.view.lat, lng: nominatim.view.lon}, nominatim.view.zoom);

        function submit(){
            validator.enableFieldValidators('applicant_type', true);
            validator.enableFieldValidators('applicant_country_code', true);
            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true){
                var data = form.serializeObject();
                data.applicant_street = OOC.capitalizeAddress(data.applicant_street);
                data.applicant_city = OOC.capitalizeAddress(data.applicant_city);
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
