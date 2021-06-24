<?php

namespace App\Controllers;

class Forecast extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("forecast");
    }

    /* load forecast list view */

    function index() {
        if ($this->login_user->user_type == "team_member") {
            $view_data["currencies_dropdown"] = $this->_get_currencies_dropdown();
            $view_data["projects_dropdown"] = $this->_get_projects_dropdown_for_forecast_and_actuals("forecast");
            return $this->template->rander("invoices/payment_received", $view_data);
        } else {
            $view_data["bu_info"] = $this->BU_model->get_one($this->login_user->bu_id);
            $view_data['bu_id'] = $this->login_user->bu_id;
            $view_data['page_type'] = "full";
            return $this->template->rander("bu/actuals/index", $view_data);
        }
    } 
        
}
