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

$proposal_id = 0;
$site_id = 0;
$attachCounter = -1;
if (isset($commitment)) {
    $proposal_id = $commitment->proposal_id;
    $site_id = $commitment->sites[0]->id;
}
?>
<div class="container create-commitment-container">
    <div class="row center-block">
        <div class="">
            <h2><?php echo $title ?></h2>
            <form role="form" class="create-commitment-form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="proposal_title">Title</label>
                            <input type="text" class="form-control"
                                   name="proposal_title" <?php echo isset($commitment) ? 'value="' . $commitment->proposal_title . '"' : '' ?> />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group combo">
                            <label for="proposal_theme">Theme</label>
                            <select id="theme-combo" class="form-control" name="proposal_theme"></select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group with-addon">
                            <label for="proposal_budget">Budget</label>
                            <div class="input-group">
                                <input type="number" class="form-control"
                                       name="proposal_budget" <?php echo isset($commitment) ? 'value="' . (int)$commitment->proposal_budget . '"' : '' ?> />
                                <span class="input-group-addon dollar">$</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group with-addon">
                            <label for="proposal_deadline">Deadline</label>
                            <div class='input-group date' id='deadline'>
                                <input type='text' class="form-control" name="proposal_deadline"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group combo">
                            <label for="proposal_completion">Progress</label>
                            <select id="completion-combo" class="form-control" name="proposal_completion">
                                <?php foreach (array_keys($completions) as $completion) {
                                    echo '<option value="' . $completion . '">' . $completions[$completion] . '</option>';
                                } ?>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proposal_announcer">Announcer</label>
                            <input type="text" class="form-control"
                                   name="proposal_announcer" <?php echo isset($commitment) ? 'value="' . $commitment->proposal_announcer . '"' : '' ?> />
                            <div class="help-block with-errors"></div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proposal_language">Language</label>
                            <input type="text" class="form-control"
                                   name="proposal_language" <?php echo isset($commitment) ? 'value="' . $commitment->proposal_language . '"' : 'English' ?> />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="proposal_abstract">Description</label>
                                <textarea class="form-control" rows="4" name="proposal_abstract"><?php
                                    if (isset($commitment)) {
                                        echo $commitment->proposal_abstract;
                                    }
                                    ?></textarea>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="proposal_impact">Impact</label>
                            <textarea class="form-control" rows="4" name="proposal_impact"><?php
                                if (isset($commitment)) {
                                    echo $commitment->proposal_impact;
                                }
                                ?></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="proposal_website">Website</label>
                            <input type="text" class="form-control"
                                   name="proposal_website" <?php echo isset($commitment) ? 'value="' . $commitment->proposal_website . '"' : '' ?> />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div><label>External attachments</label></div>
                        <div class="attachments">
                            <?php
                                if (isset($commitment) && $commitment->attachments){
                                    foreach ($commitment->attachments as $attachment){
                                        $attachCounter += 1;
                                        echo '<div id="attach-'.$attachCounter.'" class="row">';
                                        echo '<input type="hidden" name="attach_db_id" value="'.$attachment->link_id.'" />';
                                        echo '<div class="col-md-4 form-group">';
                                        echo '<input type="text" class="form-control inline-input-desc" name="attach_desc" placeholder="Description" value="'.$attachment->link_description.'" />';
                                        echo '<div class="help-block with-errors"></div>';
                                        echo '</div>';
                                        echo '<div class="col-md-7 form-group inline-input">';
                                        echo '<input type="text" class="form-control inline-input-url" name="attach_url" placeholder="URL" value="'.$attachment->link_url.'" />';
                                        echo '<div class="help-block with-errors"></div>';
                                        echo '</div>';
                                        echo '<div class="col-md-1 del-btn-container"><button class="btn btn-danger del-attachment" onclick="deleteAttachment('.$attachCounter.')"><i class="fas fa-minus-circle"></i></button></div>';
                                        echo '</div>';
                                    }
                                }
                            ?>
                        </div>
                        <button class="btn btn-primary add-attachment">Add link</button>
                        </span>
                    </div>
                </div>
                <div class="row row-mt-20">
                    <div class="col-md-6 to-bottom">
                        <div class="form-group">
                            <label for="proposal_area_active">Area actively managed</label>
                            <div class="material-switch pull-right">
                                <input id="area_active" name="proposal_area_active"
                                       type="checkbox" <?php echo isset($commitment) && $commitment->proposal_area_active == 1 ? 'checked' : '' ?> />
                                <label for="area_active" class="label-primary"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group with-addon-km">
                            <label for="proposal_area">Protected area surface</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="proposal_area"
                                       min="0" <?php echo isset($commitment) && $commitment->proposal_area != 0 ? 'value="' . $commitment->proposal_area . '"' : '' ?> />
                                <span class="input-group-addon">km<sup>2</sup></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proposal_lon">Longitude</label>
                            <input type="number" step="0.0001" class="form-control" name="proposal_lon"/>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proposal_lat">Latitude</label>
                            <input type="number" step="0.0001" class="form-control" name="proposal_lat"/>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="map"></div>
                    </div>
                </div>
                <input type="hidden" class="form-control" name="proposal_year" value="<?php echo date("Y") ?>">
                <input type="hidden" name="proposal_status"
                       value="<?php echo isset($commitment) ? $commitment->proposal_status : 'draft' ?>"/>
                <div class="row btn-section">
                    <div class="col-md-12 text-center">
                        <?php
                        if (isset($commitment)) {
                            echo '<a href="' . site_url('commitments/view/' . $commitment->proposal_id) . '" class="btn btn-default" role="button">Cancel</a>';
                        } else {
                            echo '<a href="' . site_url('commitments') . '" class="btn btn-default" role="button">Cancel</a>';
                        }
                        ?>
                        <button class="btn btn-primary save-btn"><?php echo isset($commitment) ? 'Update' : 'Create' ?></button>
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
                if ($this->auth->get_role() == 'host') {
                    echo site_url('hosts/policy');
                } else {
                    echo site_url('commitments/policy');
                }
                ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix') . $this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    function deleteAttachment(id){
        var validator = $('form').data('bootstrapValidator');
        validator.removeField($('#attach-' + id).find('input[name="attach_desc"]'))
        validator.removeField($('#attach-' + id).find('input[name="attach_url"]'))
        $('#attach-' + id).remove();
    }

    jQuery(document).ready(function () {
        var form = $('form');
        var alertError = $('.alert-danger').parent();
        var map = new L.Map('map');
        var mapData = {
            lat: 0,
            lon: 0,
            zoom: 1,
            marker: {},
            isDragRegistered: false
        }
        var attachmentIdx = <?php echo $attachCounter ?>;

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                proposal_title: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a title for your proposal"
                        }
                    }
                },
                proposal_theme: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a theme for your proposal"
                        }
                    }
                },
                proposal_budget: {
                    validators: {
                        /*notEmpty: {
                            message: "Please provide a budget for your proposal"
                        },*/
                        callback: {
                            message: 'Please provide a budget greater or equal to 0',
                            callback: function (value, validator) {
                                var isValid = false;
                                if (value === '' || (!isNaN(parseInt(value)) && value >= 0)) {
                                    isValid = true;
                                }

                                return isValid;
                            }
                        }
                    }
                },
                proposal_deadline: {
                    validators: {
                        /*notEmpty: {
                            message: "Please provide a deadline for your proposal"
                        },*/
                        date: {
                            message: 'Please provide a valid date',
                            format: 'DD/MM/YYYY'
                        },
                        callback: {
                            message: 'Please provide a deadline date in the future',
                            callback: function (value, validator) {
                                if (value !== '') {
                                    var m = new moment(value, 'DD/MM/YYYY', true);
                                    if (!m.isValid()) {
                                        return false;
                                    }
                                    return m.isAfter(moment());
                                } else {
                                    return true;
                                }
                            }
                        }
                    }
                },
                proposal_completion: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a progress status for your proposal"
                        }
                    }
                },
                proposal_website: {
                    validators: {
                        uri: {
                            message: 'Please provide a valid website address (e.g. http://www.domain.com)'
                        }
                    }
                },
                proposal_lon: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a valid longitude"
                        },
                        callback: {
                            message: 'Longitude values must be between -180 and 180',
                            callback: function (value, validator) {
                                var isValid = false;
                                if (value >= -180 && value <= 180) {
                                    isValid = true;
                                }
                                return isValid;
                            }
                        }
                    }
                },
                proposal_lat: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a valid latitude"
                        },
                        callback: {
                            message: 'Latitude values must be between -90 and 90',
                            callback: function (value, validator) {
                                var isValid = false;
                                if (value >= -90 && value <= 90) {
                                    isValid = true;
                                }
                                return isValid;
                            }
                        }
                    }
                },
                proposal_area: {
                    validators: {
                        callback: {
                            message: 'Please provide a surface area bigger than 0',
                            callback: function (value) {
                                var isValid = true;
                                if (value !== '' && value <= 0) {
                                    isValid = false;
                                }
                                return isValid;
                            }
                        }
                    }
                },
                attach_desc: {
                    validators: {
                        notEmpty : {
                            message: "Please provide a description for your attachment"
                        }
                    }
                },
                attach_url: {
                    validators: {
                        notEmpty : {
                            message: "Please provide a URL for your attachment"
                        },
                        uri: {
                            message: 'Please provide a valid address (e.g. http://www.domain.com)'
                        }
                    }
                }
            }
        });

        var validator = form.data('bootstrapValidator');
        validator.enableFieldValidators('proposal_budget', false);
        validator.enableFieldValidators('proposal_deadline', false);
        validator.enableFieldValidators('proposal_area', false);
        validator.enableFieldValidators('proposal_website', false);

        function processThemes(data) {
            var items = [];
            for (var i = 0; i < data.length; i++) {
                items.push({
                    id: data[i].id,
                    text: data[i].name
                });
            }

            return items;
        }

        var themeCombo = $('#theme-combo').select2({
            theme: 'bootstrap',
            placeholder: '',
            data: processThemes(<?php echo json_encode($themes) ?>)
        });

        var completionCombo = $('#completion-combo').select2({
            theme: 'bootstrap',
            placeholder: ''
        });

        var deadline = $('#deadline').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on('dp.change', function (e) {
            validator.enableFieldValidators('proposal_deadline', true);
            validator.revalidateField('proposal_deadline');
        });

        $('[name="proposal_budget"').change(function () {
            validator.enableFieldValidators('proposal_budget', true);
            validator.revalidateField('proposal_budget');
        });

        $('[name="proposal_lon"').change(function () {
            var value = $(this).val();
            if (value !== '') {
                mapData.lon = value;
                if ($('[name="proposal_lat"').val() !== '') {
                    if (Object.keys(mapData.marker).length === 0) {
                        addMarker(mapData.lat, mapData.lon, false);
                    } else {
                        updateMarkerPosition()
                    }
                }
            }
        });

        $('[name="proposal_lat"').change(function () {
            var value = $(this).val()
            if (value !== '') {
                mapData.lat = value;
                if ($('[name="proposal_lon"').val() !== '') {
                    if (Object.keys(mapData.marker).length === 0) {
                        addMarker(mapData.lat, mapData.lon, false);
                    } else {
                        updateMarkerPosition()
                    }
                }
            }

        });

        // restrict input: do not allow letters, non integers and minus
        $('[name="proposal_area"').keydown(function (evt) {
            var key = evt.keyCode || evt.which;
            var status = true;
            if ((key >= 65 && key <= 90) || key === 188 || key === 109 || key === 110 || key === 190) {
                status = false;
            }
            return status;
        });

        $('[name="proposal_area"').change(function () {
            var value = $(this).val();
            if (value !== '') {
                validator.enableFieldValidators('proposal_area', true);
                validator.revalidateField('proposal_area');
            } else {
                validator.enableFieldValidators('proposal_area', false);
            }

        });

        $('[name="proposal_website"').change(function () {
            validator.enableFieldValidators('proposal_website', true);
            validator.revalidateField('proposal_website');
        });


        //do not allow spaces
        $('[name="proposal_website"').on({
            keydown: function (e) {
                if (e.which === 32)
                    return false;
            },
            change: function () {
                this.value = this.value.replace(/\s/g, "");
            }
        });


        //ATTACHMENTS

        $('.add-attachment').click(function(evt){
            evt.preventDefault();

            attachmentIdx += 1;
            var row = '<div id="attach-'+ attachmentIdx + '" class="row">';
            row += '<input type="hidden" name="attach_db_id" value="0" />';
            row += '<div class="col-md-4 form-group">';
            row += '<input type="text" class="form-control" name="attach_desc" placeholder="Description" />';
            row += '<div class="help-block with-errors"></div>';
            row += '</div>';
            row += '<div class="col-md-7 form-group inline-input">';
            row += '<input type="text" class="form-control" name="attach_url" placeholder="URL" />';
            row += '<div class="help-block with-errors"></div>';
            row += '</div>';
            row += '<div class="col-md-1 form-group del-btn-container"><button class="btn btn-danger del-attachment" onclick="deleteAttachment(' + attachmentIdx + ')"><i class="fas fa-minus-circle"></i></button></span></div>';
            row += '</div>';

            $('.attachments').append(row);
            validator.addField($('#attach-' + attachmentIdx).find('input[name="attach_desc"]'));
            validator.addField($('#attach-' + attachmentIdx).find('input[name="attach_url"]'));
        });

        //MAP
        var osm = OOC.getOsmLayer();
        map.addLayer(osm);
        map.setView({lat: mapData.lat, lng: mapData.lon}, mapData.zoom);
        map.on('click', onMapClick);

        $('[data-toggle="popover"]').popover();
        $('[data-toggle="popover"]').click(function (event) {
            event.preventDefault();
        });


        <?php if (isset($commitment)) {
        echo 'setValidationForCombos(false);';
        echo 'themeCombo.val("' . $commitment->proposal_theme->id . '").trigger("change");';
        echo 'completionCombo.val("' . $commitment->proposal_completion . '").trigger("change");';
        echo 'var date = new moment("' . $commitment->proposal_deadline . '", "YYYY-MM-DD", true);';
        echo '$("#deadline").data("DateTimePicker").date(date.format("DD/MM/YYYY"));';

        if (sizeof($commitment->sites) > 0) {
            echo '$(\'[name="proposal_lon"\').val(' . $commitment->sites[0]->lng . ');';
            echo '$(\'[name="proposal_lat"\').val(' . $commitment->sites[0]->lat . ');';
            echo 'addMarker(' . $commitment->sites[0]->lat . ', ' . $commitment->sites[0]->lng . ' , false);';
            echo 'zoomToMarker();';
        }

        echo 'setValidationForCombos(true);';
    } ?>

        function setValidationForCombos(active) {
            validator.enableFieldValidators('proposal_theme', active);
            validator.enableFieldValidators('proposal_completion', active);
            //validator.enableFieldValidators('proposal_deadline', active);
        }

        function zoomToMarker() {
            map.fitBounds(L.latLngBounds([mapData.marker.getLatLng()]), {
                maxZoom: 10
            });
        }

        function updateMarkerPosition() {
            var newLatLng = new L.LatLng(mapData.lat, mapData.lon);
            mapData.marker.setLatLng(newLatLng);
        }

        function setLatLon(lat, lon) {
            $('[name="proposal_lon"]').val(Math.round(lon * 10000) / 10000);
            $('[name="proposal_lat"]').val(Math.round(lat * 10000) / 10000);
        }

        function addMarker(lat, lon, updateFields) {
            var coord = {
                lat: lat,
                lon: lon
            };
            if (updateFields) {
                mapData.lat = lat;
                mapData.lon = lon;
                setLatLon(lat, lon);
                reValidateLatLng();
            }

            mapData.marker = L.marker(coord, {draggable: true}).addTo(map);

            if (!mapData.isDragRegistered) {
                mapData.marker.on('dragend', function (evt) {
                    setLatLon(evt.target.getLatLng().lat, evt.target.getLatLng().lng);
                    reValidateLatLng();
                });
                mapData.isDragRegistered = true;
            }
        }

        function reValidateLatLng() {
            validator.revalidateField('proposal_lon');
            validator.revalidateField('proposal_lat');
        }

        function onMapClick(evt) {
            map.off('click', onMapClick);
            if (Object.keys(mapData.marker).length === 0) {
                addMarker(evt.latlng.lat, evt.latlng.lng, true);
            }
        }

        function serializeFormData() {
            var data = form.serializeObject();

            data.proposal_area_active = $('[name="proposal_area_active"]:checked').length;
            var date = new moment(data.proposal_deadline, 'DD/MM/YYYY', true);
            if (date.isValid()) {
                data.proposal_deadline = date.format('YYYY-MM-DD');
            }

            data.proposal_language = OOC.capitalizeAddress(data.proposal_language);

            <?php
            if ($site_id != 0) {
                echo 'data.proposal_site_id = ' . $site_id . ';';
            }
            ?>

            return data;
        }

        function submit() {
            form.bootstrapValidator('validate');
            console.log(form.data('bootstrapValidator').isValid());
            if (form.data('bootstrapValidator').isValid() == true) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('commitments/set_commitment/' . $proposal_id) ?>',
                    dataType: 'json',
                    data: serializeFormData(),
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
        $('.save-btn').click(function () {
            submit();
        });
    });
</script>
