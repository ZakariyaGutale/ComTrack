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
	
	$workflow = array_keys($this->config->item('workflow'));

?>
<div class="container main-screen poc-dashboard">
    <?php
        if ($viewer['app_host'] != ''){
    ?>
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active"><a href="#my-commitments" data-toggle="tab">My commitments</a></li>
                    <li role="presentation" ><a href="#all-commitments" data-toggle="tab">All published commitments</a></li>
                </ul>
            </div>
        </div>
    <?php } ?>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="my-commitments">
            <div class="row">
                <div class="col-md-3">
                    <form>
                        <div class="form-group side-menu-adjust">
                            <div class="input-group">
                                <input type="text" class="form-control" id="free-text-search" name="q" placeholder="Search..."/>
                                <span class="input-group-addon side-menu-search"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Area of action</label>
                            <?php foreach ($themes as $theme) {
                                echo '<div class="checkbox">';
                                echo '<input type="checkbox" id="check-theme'.$theme->id.'" name="area" value="'.$theme->id.'" checked>';
                                echo '<label class="check-label" for="check-theme'.$theme->id.'"><span class="action-theme"><img src="/assets/images/ooc/'.$theme->id.'.png"></span>'.$theme->name.'</label>';
                                echo '</div>';
                            } ?>
                        </div>
                        <div class="from-group combo-group">
                            <label for="year">Year</label>
                            <select id="year-combo" class="form-control" name="year">
                                <option value="all">All</option>
                                <?php foreach ($years as $year){
                                    echo '<option value="'.$year->year.'">'.$year->year.'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="from-group combo-group">
                            <label for="completion">Progress</label>
                            <select id="completion-combo" class="form-control" name="completion">
                                <option value="all">All</option>
                                <?php foreach (array_keys($completions) as $completion){
                                    echo '<option value="'.$completion.'">'.$completions[$completion].'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <?php foreach ($status as $item) {
                                echo '<div class="checkbox">';
                                echo '<input type="checkbox" id="check-commit'.$item.'" name="status" value="'.$item.'" checked>';
                                echo '<label class="check-label" for="check-commit'.$item.'">'. (strpos($item, '_') ? ucfirst(str_replace('_', ' ', $item)) : ucfirst($item)).'</label>';
                                echo '</div>';
                            } ?>
                        </div>
                    </form>
                </div>
                <!--DATA TABLE-->
                <div class="col-md-9">
                    <table id="commits-table" class="table table-striped table-hover wrap" style="width: 100%;">
                        <thead class="table-header">
                        <tr>
                            <th>Area</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <?php
            if ($viewer['app_host'] != ''){
        ?>
            <div role="tabpanel" class="tab-pane fade in" id="all-commitments">
                <div class="embed-responsive embed-responsive-4by3">
                    <iframe
                            src="<?php echo $viewer['app_host'].'?mode='.$viewer['app_mode'].'&countries='.$countries.'&cutoffs='.$viewer['years'] ?>">
                    </iframe>
                </div>
            </div>
        <?php } ?>
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

    jQuery(document).ready(function(){
        var isLoading = true;
        var form = $('form');
        <?php
            if ($this->auth->get_role() == 'poc' ){
                $url = site_url('commitments/view');
            } else {
                $url = site_url('hosts/view');
            }
            echo 'var url = \''.$url.'\';'
        ?>

        var table = $('#commits-table').DataTable({
            ordering: false,
            searching: false,
            lengthChange: false,
            info: false,
            responsive: {
                details: false
            },
            serverSide: true,
            pageLength: 25,
            pagingType: 'simple_numbers',
            stateSave: true,
            ajax: {
                url: '<?php echo site_url('rest/commitments') ?>',
                type: 'POST',
                dataSrc: 'items',
                data: function(d){
                    Object.assign(d, serializeForm());
                    return d;
                }
            },
            preDrawCallback: function (settings) {
                var api = new $.fn.dataTable.Api(settings);
                var pagination = $(this)
                    .closest('.dataTables_wrapper')
                    .find('.dataTables_paginate');
                pagination.toggle(api.page.info().pages > 1);
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            },
            language: {
                emptyTable: 'No data to display',
                paginate: {
                    next: '<i class="fas fa-angle-double-right"></i>',
                    previous: '<i class="fas fa-angle-double-left"></i>'
                }
            },
            columns: [{
                data: 'proposal_theme',
                render: function(data, type){
                    var url = '<?php echo site_url('assets/images/ooc') ?>/' + data.id + '.png';
                    return '<img src="'+ url +'" style="height: 20px; width: 20px;" title="'+ data.name +'" />';
                }
            },{
                data: 'proposal_title'
            },{
                data: 'proposal_year'
            },{
                data: 'proposal_completion',
                render: function (data, type) {
                    return data + '%';
                }
            }, {
                data: 'proposal_status',
                render: function(data, type){
                    if (data.indexOf('_') != -1){
                        data = data.replace('_', ' ');
                    }
                    return OOC.capitalizeAddress(data);
                }
            }]
        });

        $('#commits-table tbody').on( 'click', 'tr', function(){
            var data = table.row( this ).data();
            if (data.hasOwnProperty('proposal_id') && data.proposal_id !== ''){
                window.location = url + '/' + data.proposal_id;
            }
        });

        OOC.registerEnterKey($('#free-text-search'), table.ajax.reload);
        OOC.registerEscKey($('#free-text-search'), function(){
            $('#free-text-search').val('');
            table.ajax.reload();
        });
        $('.side-menu-search').click(function () {
            table.ajax.reload();
        });

        var yearCombo = $('#year-combo').select2({
            theme:"bootstrap"
        });

        yearCombo.on('select2:select', function(e){
            table.ajax.reload();
        });

        var compCombo = $('#completion-combo').select2({
            theme:"bootstrap"
        });

        compCombo.on('select2:select', function(e){
            table.ajax.reload();
        });

        var cb = $('form input[type=checkbox]');
        for (var i = 0; i < cb.length; i++){
            $(cb[i]).click(function(){
                table.ajax.data = serializeForm();
                table.ajax.reload();
            });
        }

        function getCbState(id){
            var cbs = $('[id*=' + id + ']');
            var data = [];
            for (var i = 0; i < cbs.length ; i++) {
                data.push({
                    id: '#' + cbs[i].id,
                    checked: cbs[i].checked
                });
            }

            return data;
        }

        function getComboState(id){
            var combo = $(id).select2('data')[0];
            var data = {
                id: id,
                value: combo.id,
                text: combo.text
            };

            return data;
        }

        function serializeForm(d){
            var values = form.serializeArray();
            var postValues = {
                q: '',
                area: [],
                year: '',
                completion: '',
                status: []
            };

            values.forEach(function (item) {
                if (item.name === 'area' || item.name == 'status'){
                    postValues[item.name].push(item.value);
                } else {
                    postValues[item.name] = item.value;
                }
            });

            if (!isLoading){
                var copy = Object.assign({}, postValues);
                copy.area = getCbState('check-theme');
                copy.status = getCbState('check-commit');
                copy.completion = getComboState('#completion-combo');
                copy.year = getComboState('#year-combo');

                OOC.setToLocalSorage(copy, '#commitments');
            }

            return postValues;
        }

        function setStatus(){
            var storage = OOC.getFromLocalStorage();
            if (storage !== null && typeof(storage) === 'object' && Object.keys(storage).length > 0){
                OOC.cleanDataTablesStorage(storage.tab.replace('#', ''));
                var table = $(storage.table).DataTable();
                for (var key in storage) {
                    if (key === 'cb'){
                        for (var i = 0; i < storage.cb.length; i++) {
                            $(storage.cb[i]['id']).prop('checked', storage.cb[i]['checked']);
                        }
                    } else if (key === 'combo'){
                        for (var i = 0; i < storage.combo.length; i++) {
                            if ($(storage.combo[i].id).find('option[value="' + storage.combo[i].value + '"]').length > 0){
                                $(storage.combo[i].id).val(storage.combo[i].value).trigger('change');
                            } else {
                                var newOption = new Option(storage.combo[i].text, storage.combo[i].value, true, true);
                                $(storage.combo[i].id).append(newOption).trigger('change');
                            }
                        }
                    } else if (key === 'text') {
                        $(storage[key].id).val(storage[key].value);
                    }
                }

                setTimeout(function(){table.ajax.reload(null, false)}, 10);
            }
            isLoading = false;
        }

        setStatus();
    });
</script>
