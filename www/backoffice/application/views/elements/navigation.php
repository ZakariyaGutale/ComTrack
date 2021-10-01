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
<div class="container-fluid header-separator">
    <?php if($this->auth->get_role() != '' && isset($navigation) && sizeof($navigation) > 0){ ?>
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-xs-11 col-md-11">
                        <ol class="breadcrumb">
                            <?php foreach ($navigation as $item){
                                if (isset($item->active) && $item->active == true){
                                    echo '<li class="active">'.$item->name.'</li>';
                                } else {
                                    echo '<li><a href="'.$item->ref.'">'.$item->name.'</a></li>';
                                }
                            } ?>
                        </ol>
                    </div>
                    <div class="col-xs-1 col-md-1">
                        <ol class="breadcrumb text-right">
                            <li class="download" title="Download documentation"><a href="<?php echo base_url() ?>assets/docs/<?php echo  $this->auth->get_role() == 'poc' ? 'ooc_doc_poc.pdf' : 'ooc_doc_hosts.pdf' ?>" download><i class="fas fa-info-circle"></i></a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
