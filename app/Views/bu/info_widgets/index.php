<div class="row clearfix">
    <?php if ($show_forecast_info) { ?>
        <?php if (!in_array("projects", $hidden_menu)) { ?>
            <div class="col-md-3 col-sm-6 widget-container">
                <?php echo view("bu/info_widgets/tab", array("tab" => "projects")); ?>
            </div>
        <?php } ?>

        <?php if (!in_array("forecast", $hidden_menu)) { ?>
            <div class="col-md-3 col-sm-6  widget-container">
                <?php echo view("bu/info_widgets/tab", array("tab" => "forecast_value")); ?>
            </div>
        <?php } ?>

        <?php if (!in_array("actuals", $hidden_menu) && !in_array("forecast", $hidden_menu)) { ?>
            <div class="col-md-3 col-sm-6  widget-container">
                <?php echo view("bu/info_widgets/tab", array("tab" => "actuals")); ?>
            </div>
            <div class="col-md-3 col-sm-6  widget-container">
                <?php echo view("bu/info_widgets/tab", array("tab" => "due")); ?>
            </div>
        <?php } ?>

        <?php if ((in_array("projects", $hidden_menu)) && (in_array("forecast", $hidden_menu))) { ?>
            <div class="col-sm-12 col-md-12" style="margin-top: 10%">
                <div class="text-center box">
                    <div class="box-content" style="vertical-align: middle; height: 100%">
                        <span data-feather="meh" height="20rem" width="20rem" style="color:#CBCED0;"></span>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>