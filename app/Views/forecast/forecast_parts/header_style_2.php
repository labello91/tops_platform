<table class="header-style">
    <tr class="forecast-preview-header-row">
        <td class="forecast-info-container forecast-header-style-two" style="width: 40%; vertical-align: top;"><?php
            $data = array(
                "bu_info" => $bu_info,
                "color" => $color,
                "forecast_info" => $forecast_info
            );
            echo view('forecast/forecast_parts/forecast_info', $data);
            ?>
        </td>
        <td class="hidden-forecast-preview-row" style="width: 20%;"></td>
        <td style="width: 40%; vertical-align: top;">
            <?php echo view('forecast/forecast_parts/company_logo'); ?>
        </td>
    </tr>
    <tr>
        <td style="padding: 5px;"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><?php
            echo view('forecast/forecast_parts/forecast_to', $data);
            ?>
        </td>
        <td></td>
        <td><?php
            echo view('forecast/forecast_parts/forecast_from', $data);
            ?>
        </td>

    </tr>
</table>