<table id="forecast-item-table" class="table display dataTable text-right strong table-responsive">
    <tr>
        <td><?php echo app_lang("sub_total"); ?></td>
        <td style="width: 120px;"><?php echo to_currency($forecast_total_summary->forecast_subtotal, $forecast_total_summary->currency_symbol); ?></td>
        <?php if ($can_edit_forecast) { ?>
            <td style="width: 100px;"> </td>
        <?php } ?>
    </tr>

    <?php if ($forecast_total_summary->total_achieved) { ?>
        <tr>
            <td><?php echo app_lang("achieved"); ?></td>
            <td><?php echo to_currency($forecast_total_summary->total_achieved, $forecast_total_summary->currency_symbol); ?></td>
            <?php if ($can_edit_forecast) { ?>
                <td></td>
            <?php } ?>
        </tr>
    <?php } ?>
    <tr>
        <td><?php echo app_lang("balance_due"); ?></td>
        <td><?php echo to_currency($forecast_total_summary->balance_due, $forecast_total_summary->currency_symbol); ?></td>
        <?php if ($can_edit_forecast) { ?>
            <td></td>
        <?php } ?>
    </tr>
</table>