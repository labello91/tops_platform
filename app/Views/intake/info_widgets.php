<div class="clearfix">
    <?php if ($show_forecast_info) { ?>
        <?php if (!in_array("projects", $hidden_menu)) { ?>
            <div class="col-md-3 col-sm-6 widget-container">
                <div class="card dashboard-icon-widget">
                    <div class="card-body ">
                        <div class="widget-icon bg-info">
                            <i data-feather='grid' class='icon'></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo to_decimal_format($bu_info->total_projects); ?></h1>
                            <span><?php echo app_lang("projects"); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!in_array("forecast", $hidden_menu)) { ?>
            <div class="col-md-3 col-sm-6  widget-container">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-primary">
                            <i data-feather='file-text' class='icon'></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo to_currency($bu_info->forecast_value, $bu_info->currency_symbol); ?></h1>
                            <div><?php echo app_lang("forecast_value"); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!in_array("actuals", $hidden_menu) && !in_array("forecast", $hidden_menu)) { ?>
            <div class="col-md-3 col-sm-6  widget-container">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-success">
                            <i data-feather='check-circle' class='icon'></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo to_currency($bu_info->actuals_achieved, $bu_info->currency_symbol); ?></h1>
                            <span><?php echo app_lang("actuals"); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6  widget-container">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-coral">
                            <i data-feather='dollar-sign' class='icon'></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo to_currency($bu_info->forecast_value - $bu_info->actuals_achieved, $bu_info->currency_symbol); ?></h1>
                            <span><?php echo app_lang("due"); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ((in_array("projects", $hidden_menu)) && (in_array("forecast", $hidden_menu))) { ?>
            <div class="col-sm-12 col-md-12" style="margin-top: 10%">
                <div class="text-center box">
                    <div class="box-content" style="vertical-align: middle; height: 100%">
                        <span data-feather="meh" height="15rem" width="15rem" style="color:#CBCED0;"></span>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>