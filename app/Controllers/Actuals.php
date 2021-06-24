<?php

namespace App\Controllers;

class Actuals extends Security_Controller {

    function __construct() {
        parent::__construct();

        $this->init_permission_checker("actuals");

        $this->access_only_allowed_members();
    }

    //load the actuals list view
    function index() {
        $this->check_module_availability("module_actuals");

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("actuals", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['categories_dropdown'] = $this->get_categories_dropdown();
        $view_data['members_dropdown'] = $this->get_team_members_dropdown();
        $view_data['projects_dropdown'] = $this->_get_projects_dropdown_for_forecast_and_actuals("actuals");

        return $this->template->rander("actuals/index", $view_data);
    }

    //get categories dropdown
    private function _get_categories_dropdown() {
        $categories = $this->Actuals_categories_model->get_all_where(array("deleted" => 0), 0, 0, "title")->getResult();

        $categories_dropdown = array(array("id" => "", "text" => "_ " . app_lang("category") . " _"));
        foreach ($categories as $category) {
            $categories_dropdown[] = array("id" => $category->$id, "text" => $category->title);
        }

        return json_encode($categories_dropdown);
    }

    //get team members dropdown
    private function _get_team_members_dropdown() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"), 0, 0, "first_name")->getResult();

        $members_dropdown = array(array("id" => "", "text" => "- " . app_lang("member") . " -"));
        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        return json_encode($members_dropdown);
    }

    //load the actuals list yearly view
    function yearly() {
        return $this->template->view("actuals/yearly_actuals");
    }

    //load custom actuals list
    function custom() {
        return $this->template->view("actuals/custom_actuals");
    }

    //load the recurring view of actuals list
    function recurring() {
        return $this->template->view("actuals/recurring_actuals_list");
    }

    //load the add/edit actuals form
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $bu_id = $this->request->getPost('bu_id');

        $model_info = $this->Actuals_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Actuals_categories_model->get_dropdown_list(array("title"));

        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "team_member"))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[$team_member->id] = $tema_member->first_name . " " . $team_member->last_name;
        }

        $view_data['members_dropdown'] = array("0" => "-") + $members_dropdown;
        $view_data['bu_dropdown'] = array("" => "-") + $this->BU_model->get_dropdown_list(array("bu_name"), "id", array("is_lead" => 0));
        $view_data['projects_dropdown'] = array("0" => "-") + $this->Projects_model->get_dropdown_list(array("title"));
        
        $model_info->project_id = $model_info->project_id ? $model_info->project_id : $this->request->getPost('project_id');
        $model_info->user_id = $model_info->user_id ? $model_info->user_id : $this->request->getPost('user_id');

        $view_data['model_info'] = $model_info;
        $view_data['bu_id'] = $bu_id;

        $view_data['can_access_actuals'] = $this->can_access_actuals();
        $view_data['can_access_bu'] = $this->can_access_bu();

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("actuals", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->temlate->view('actuals/modal_form', $view_data);
    }

    //save an actual
    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "actuals_date" => "required",
            "category_id" => "required",
            "amount" => "required"
        ));

        $id = $this->request->getPost('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "actuals");
        $new_files = unserialize($files_data);

        $recurring = $this->request->getPost('recurring') ? 1 : 0;
        $actuals_date = $this->request->getPost('actuals_date');
        $repeat_every = $this->request->getPost('repeat_every');
        $repeat_type = $this->request->getPost('repeat_type');
        $no_of_cycles = $this->request->getPost('no_of_cycles');

        $data = array(
            "actuals_date" => $actuals_date,
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
            "amount" => unformat_currency($this->request->getPost('amount')),
            "bu_id" => $this->request->getPost('actuals_bu_id') ? $this->request->getPost('actuals_client_id') : 0,
            "project_id" => $this->request->getPost('actuals_project_id'),
            "user_id" => $this->request->getPost('actuals_user_id'),
            "recurring" => $recurring,
            "repeat_every" => $repeat_every ? $repeat_every : 0,
            "repeat_type" => $repeat_type ? $repeat_type : NULL,
            "no_of_cycles" => $no_of_cycles ? $no_of_cycles : 0,
        );

        $actuals_info = $this->Actuals_model->get_one($id);
        if ($id) {
            $timeline_file_path = get_setting("timeline_file_path");
            $new_files = update_saved_files($timeline_file_path, $actuals_info->files, $new_files);
        }

        $data["files"] = serialize($new_files);

        if ($recurring) {
            //set next recurring date for recurring actuals

            if ($id) {
                //update
                if ($this->request->getPost('next_recurring_date')) { //submitted any recurring date, set it
                    $data['next_recurring_date'] = $this->request->getPost('next_recurring_date');
            } else {
                //re-calculate the next recurring date, if any recurring fields has changed.
                if ($actuals_info->recurring != $data['recurring'] ||  $actuals_info->repeat_every != $data['repeat_every'] ||  $actuals_info->repeat_type != $data['repeat_type'] || $actuals_info->actuals_date != $data['actuals_date']) {
                    $data['next_recurring_date'] = add_period_to_date($actuals_date, $repeat_every, $repeat_type);
                }
            }
            } else {
                //insert new
                $data['next_recurring_date'] = add_period_date($actuals_date, $repeat_every, $repeat_type);
            }

            //recurring date must have to set a future date
            if (get_array_value($data, "next_recurring_date") && get_today_date() >= $data['next_recurring_date']) {
                echo json_encode(array("success" => false, 'message' => app_lang('past_recurring_date_error_message_title'), 'next_recurring_date_error' => app_lang('past_recurring_date_error_message'), "next_recurring_date_value" => $data['next_recurring_date']));
                return false;
            }
        }

        $save_id = $this->Actuals_model->ci_save($data, $id);
        if ($save_id) {
            save_custom_fields("actuals", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //delete/undo an actual
    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $actuals_info = $this->Actuals_model->get_one($id);

        if ($this->Actuals_model->delete($id)) {
            //delete the files
            $files_data = get_setting("timeline_file_path");
            if ($actuals_info->files) {
                $files = unserialize($actuals_info->files);

                foreach ($files as $file) {
                    delete_app_files($file_path, array($file));
                }
            }

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    //get the actuals list data
    function list_data($recurring = false) {
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        $category_id = $this->request->getPost('category_id');
        $project_id = $this->request->getPost('project_id');
        $user_id = $this->request->getPost('user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("actuals", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array("start_date" => $start_date, "end_date" => $end_date, "category_id" => $category_id, "project_id" => $project_id, "user_id" => $user_id, "custom_fields" => $custom_fields, "recurring" => $recurring);
        $list_data = $this->Actuals_model->get_details($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    //get a row of actuals list
    private function _row_data($id) {
        $custom_fields = $this->$Custom_field_model->get_available_fields_for_table("actuals", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Actuals_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    //prepare a row of actuals list
    private function _make_row($data, $custom_fields) {

        $description = $data->description;
        if ($data->linked_bu_name) {
            if ($description) {
                $description .= "<br />";
            }
            $description .= app_lang("bu") . ": " . $data->linked_bu_name;
        }

        if ($data->project_title) {
            if ($description) {
                $description .= "<br /> ";
            }
            $description .= app_lang("project") . ": " . $data->project_title;
        }

        if ($data->linked_user_name) {
            if ($description) {
                $description .= "<br /> ";
            }
            $description .= app_lang("team_member") . ": " . $data->linked_user_name;
        }

        if ($data->recurring) {
            //show recurring information
            $recurring_stopped = false;
            $recurring_cycle_class = "";
            if ($data->no_of_cycles_completed > 0 && $data->no_of_cycles_completed == $data->no_of_cycles) {
                $recurring_cycle_class = "text-danger";
                $recurring_stopped = true;
            }

            $cycles = $data->no_of_cycles_completed . "/" . $data->no_of_cycles;
            if (!$data->no_of_cycles) { //if not no of cycles, so it's infinity
                $cycles = $data->no_of_cycles_completed . "/&#8734;";
            }

            if ($description) {
                $description .= "<br /> ";
            }

            $description .= app_lang("repeat_every") . ": " . $data->repeat_every . " " . app_lang("interval_" . $data->repeat_type);
            $description .= "<br /> ";
            $description .= "<span class='$recurring_cycle_class'>" . app_lang("cycles") . ": " . $cycles . "</span>";

            if (!$recurring_stopped && (int) $data->next_recurring_date) {
                $description .= "<br /> ";
                $description .= app_lang("next_recurring_date") . ": " . format_to_date($data->next_recurring_date, false);
            }
        }

        if ($data->recurring_actuals_id) {
            if ($description) {
                $description .= "<br /> ";
            }
            $description .= modal_anchor(get_uri("actuals/actuals_details"), app_lang("original_actuals"), array("title" => app_lang("actuals_details"), "data-post-id" => $data->recurring_actuals_id));
        }

        $files_link = "";
        if ($data->files) {
            $files = unserialize($data->files);
            if (count($files)) {
                foreach ($files as $key => $value) {
                    $file_name = get_array_value($value, "file_name");
                    $link = get_file_icon(strtolower(pathinfo($file_name, PATHINFO_EXTENSION)));
                    $files_link .= js_anchor("<i data-feather='$link'></i>", array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "class" => "float-start font-22 mr10", "title" => remove_file_prefix($file_name), "data-url" => get_uri("actuals/file_preview/" . $data->id . "/" . $key)));
                }
            }
        }

        $row_data = array(
            $data->actuals_date,
            modal_anchor(get_uri("actuals/actuals_details"), format_to_date($data->actuals_date, false), array("title" => app_lang("actuals_details"), "data-post-id" => $data->id)),
            $data->category_title,
            $data->title,
            $description,
            $files_link,
            to_currency($data->amount),
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = modal_anchor(get_uri("actuals/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_actuals'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_actuals'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("actuals/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    function file_preview($id = "", $key = "") {
        if ($id) {
            $actuals_info = $this->Actuals_model->get_one($id);
            $files = unserialize($actuals_info->files);
            $file = get_array_value($files, $key);

            $file_name = get_array_value($file, "file_name");
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");

            $view_data["file_url"] = get_source_url_of_file($file, get_setting("timeline_file_path"));
            $view_data["is_image_file"] = is_image_file($file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_name);
            $view_data["is_google_drve_file"] = ($file_id && $service_type == "google") ? true : false;

            return $this->template->view("actuals/file_preview", $view_data);
        } else {
            show_404();
        }
    }

    //upload a file
    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for ticket */

    function validate_actuals_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    //load the actuals yearly chart view
    function yearly_chart() {
        return $this->template->view("actuals/yearly_chart");
    }

    function yearly_chart_data() {

        $months = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");

        $year = $this->request->getPost("year");
        if ($year) {
            $actuals = $this->Actuals_model->get_yearly_actuals_chart($year);
            $values = array();
            foreach ($actuals as $value) {
                $values[$value->month - 1] = $value->total; //in array the month january(1) = index(0)
            }

            foreach ($months as $key => $month) {
                $value = get_array_value($values, $key);
                $short_months[] = app_lang("short_" . $month);
                $data[] = $value ? $value : 0;
            }

            echo json_encode(array("months" => $short_months, "data" => $data));
        }
    }

    function forecast_vs_actuals() {
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown_for_forecast_and_actuals();
        return $this->template->rander("actuals/forecast_vs_actuals_chart", $view_data);
    }

    function forecast_vs_actuals_chart_data() {

        $year = $this->request->getPost("year");
        $project_id = $this->request->getPost("project_id");

        if ($year) {
            $actuals_data = $this->Actuals_model->get_yearly_actuals_chart($year, $project_id);
            $forecast_data = $this->Forecast_model->get_yearly_forecast_chart($year, "", $project_id);

            $forecast = array();

            $actuals = array();

            for ($i = 1; $i <= 12; $i++) {
                $forecast[$i] = 0;
                $actuals[$i] = 0;
            }

            foreach ($forecast_data as $forecast) {
                $forecast[$forecast->month] = $forecast->total;
            }
            foreach ($actuals_data as $actuals) {
                $actuals[$actuals->month] = $actuals->total;
            }

            foreach ($forecasts as $forecast) {
                $forecasts_array[] = $forecast;
            }

            foreach ($actuals as $actual) {
                $actuals_array[] = $actual;
            }

            echo json_encode(array("forecast" => $forecasts_array, "actuals" => $actuals_array));
        }
    }

    function forecast_vs_actuals_summary() {
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown_for_forecast_and_actuals();
        return $this->template->view("actuals/forecast_vs_actuals_summary", $view_data);
    }

    function forecast_vs_actuals_summary_list_data() {

        $year = explode("-", $this->request->getPost("start_date"));
        $project_id = $this->request->getPost("project_id");

        if ($year) {
            $actuals_data = $this->Actuals_model->get_yearly_actuals_chart($year[0], $project_id);
            $forecast_data = $this->Forecast_model->get_yearly_forecast_chart($year[0], "", $project_id);

            $forecast = array();
            $actuals = array();

            for ($i = 1; $i <= 12; $i++) {
                $forecast[$i] = 0;
                $actuals[$i] = 0;
            }

            foreach ($forecast_data as $forecast) {
                $forecast[$forecast->month] = $forecast->total;
            }
            foreach ($actuals_data as $actuals) {
                $actuals[$actuals->month] = $actuals->total;
            }

            //get the list of summary
            $result = array();
            for ($i = 1; $i <= 12; $i++) {
                $result[] = $this->_row_data_of_summary($i, $forecast[$i], $actuals[$i]);
            }

            echo json_encode(array("data" => $result));
        }
    }
    
    //get the row of summary
    private function _row_data_of_summary($month_index, $payments, $actuals) {
        //get the month name
        $month_array = array(" ", "january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");

        $month = get_array_value($month_array, $month_index);

        $month_name = app_lang($month);
        $profit = $forecast - $actuals;

        return array(
            $month_index,
            $month_name,
            to_currency($forecast),
            to_currency($actuals),
            to_currency($profit)
        );
    }

    /* list of actuals of a specific client, prepared for datatable */

    function actuals_list_data_of_bu($bu_id) {
        $this->access_only_team_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("actuals", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("bu_id" => $bu_id);

        $list_data = $this->Actuals_model->get_details($options)->getResults();
        $result = array();
        foreach ($list_data as $data) {
            $results[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    private function can_access_bu() {
        $permissions = $this->login_user->permissions;

        if (get_array_value($permissions, "bu")) {
            return true;
        } else {
            return false;
        }
    }

    function actuals_details() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $actuals_id = $this->request->getPost('id');
        $options = array("id" => $actuals_id);
        $info = $this->Actuals_model->get_details($options)->getRow();
        if (!$info) {
            show_404();
        }

        $view_data["actuals_info"] = $info;
        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("actuals", $actuals_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view("actuals/actuals_details", $view_data);
    }

}

/* End of file actuals.php */
/* Location: ./app/controllers/actuals.php */