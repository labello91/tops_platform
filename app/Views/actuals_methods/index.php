<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "actuals_methods";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">
                <div class="page-title clearfix">
                    <h4> <?php echo app_lang('actuals_methods'); ?></h4>
                    <div class="title-button-group">
                        <?php echo modal_anchor(get_uri("actuals_methods/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_actuals_method'), array("class" => "btn btn-default", "title" => app_lang('add_actuals_method'))); ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="actuals-method-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#actuals-method-table").appTable({
            source: '<?php echo_uri("actuals_methods/list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<?php echo app_lang("description"); ?>'},
                {title: '<?php echo app_lang("available_on_forecast"); ?>'},
                {title: '<?php echo app_lang("minimum_actuals_amount"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>