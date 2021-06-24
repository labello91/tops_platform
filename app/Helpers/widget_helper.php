<?php

use App\Controllers\Security_Controller;
use App\Libraries\Template;

/**
 * activity logs widget for projects
 * @param array $params
 * @return html
 */
if (!function_exists('activity_logs_widget')) {

    function activity_logs_widget($params = array()) {
        $ci = new Security_Controller(false);

        $limit = get_array_value($params, "limit");
        $limit = $limit ? $limit : "20";
        $offset = get_array_value($params, "offset");
        $offset = $offset ? $offset : "0";

        $params["user_id"] = $ci->login_user->id;
        $params["is_admin"] = $ci->login_user->is_admin;
        $params["user_type"] = $ci->login_user->user_type;
        $params["bu_id"] = $ci->login_user->bu_id;

        //check if user has restriction to view only assigned tasks
        $params["show_assigned_tasks_only"] = get_array_value($ci->login_user->permissions, "show_assigned_tasks_only");

        $logs = $ci->Activity_logs_model->get_details($params);

        $view_data["activity_logs"] = $logs->result;
        $view_data["result_remaining"] = $logs->found_rows - $limit - $offset;
        $view_data["next_page_offset"] = $offset + $limit;

        $view_data["log_for"] = get_array_value($params, "log_for");
        $view_data["log_for_id"] = get_array_value($params, "log_for_id");
        $view_data["log_type"] = get_array_value($params, "log_type");
        $view_data["log_type_id"] = get_array_value($params, "log_type_id");

        echo $view_data["result_remaining"] = view("activity_logs/activity_logs_widget", $view_data);
    }

}

/**
 * get timeline widget
 * @param array $params
 * @return html
 */
if (!function_exists('timeline_widget')) {

    function timeline_widget($params = array()) {
        $limit = get_array_value($params, "limit");
        $limit = $limit ? $limit : "20";
        $offset = get_array_value($params, "offset");
        $offset = $offset ? $offset : "0";

        $is_first_load = get_array_value($params, "is_first_load");
        if ($is_first_load) {
            $view_data["is_first_load"] = true;
        } else {
            $view_data["is_first_load"] = false;
        }

        $Posts_model = model("App\Models\Posts_model");
        $logs = $Posts_model->get_details($params);
        $view_data["posts"] = $logs->result;
        $view_data['single_post'] = '';
        $view_data["result_remaining"] = $logs->found_rows - $limit - $offset;
        $view_data["next_page_offset"] = $offset + $limit;

        $user_id = get_array_value($params, "user_id");
        if ($user_id && !count($logs->result)) {
            //show a no post found message to user's wall for empty post list
            $template = new Template();
            return $template->view("timeline/no_post_message");
        } else {
            $template = new Template();
            return $template->view("timeline/post_list", $view_data);
        }
    }

}


/**
 * get announcement notice

 * @return html
 */
if (!function_exists('announcements_alert_widget')) {

    function announcements_alert_widget() {
        $ci = new Security_Controller(false);
        $announcements = $ci->Announcements_model->get_unread_announcements($ci->login_user->id, $ci->login_user->user_type)->getResult();
        $view_data["announcements"] = $announcements;
        $template = new Template();
        return $template->view("announcements/alert", $view_data);
    }

}


/**
 * get tasks widget of loged in user
 * 
 * @return html
 */
if (!function_exists('my_open_tasks_widget')) {

    function my_open_tasks_widget() {
        $ci = new Security_Controller(false);
        $view_data["total"] = $ci->Tasks_model->count_my_open_tasks($ci->login_user->id);
        $template = new Template();
        return $template->view("projects/tasks/open_tasks_widget", $view_data);
    }

}


/**
 * get tasks status widteg of loged in user
 * 
 * @return html
 */
if (!function_exists('my_task_stataus_widget')) {

    function my_task_stataus_widget($custom_class = "") {
        $ci = new Security_Controller(false);
        $view_data["task_statuses"] = $ci->Tasks_model->get_task_statistics(array("user_id" => $ci->login_user->id));
        $view_data["custom_class"] = $custom_class;

        $template = new Template();
        return $template->view("projects/tasks/my_task_status_widget", $view_data);
    }

}


/**
 * get todays event widget
 * 
 * @return html
 */
if (!function_exists('events_today_widget')) {

    function events_today_widget() {
        $ci = new Security_Controller(false);

        $options = array(
            "user_id" => $ci->login_user->id,
            "team_ids" => $ci->login_user->team_ids
        );

        if ($ci->login_user->user_type == "bu") {
            $options["is_bu"] = true;
        }

        $view_data["total"] = $ci->Events_model->count_events_today($options);
        $template = new Template();
        return $template->view("events/events_today", $view_data);
    }

}


/**
 * get new posts widget
 * 
 * @return html
 */
if (!function_exists('new_posts_widget')) {

    function new_posts_widget() {
        $Posts_model = model("App\Models\Posts_model");
        $view_data["total"] = $Posts_model->count_new_posts();
        $template = new Template();
        return $template->view("timeline/new_posts_widget", $view_data);
    }

}


/**
 * get event list widget
 * 
 * @return html
 */
if (!function_exists('events_widget')) {

    function events_widget() {
        $ci = new Security_Controller(false);

        $options = array("user_id" => $ci->login_user->id, "limit" => 10, "team_ids" => $ci->login_user->team_ids);

        if ($ci->login_user->user_type == "bu") {
            $options["is_bu"] = true;
        }

        $view_data["events"] = $ci->Events_model->get_upcomming_events($options);

        $template = new Template();
        return $template->view("events/events_widget", $view_data);
    }

}


/**
 * get event icons based on event sharing 
 * 
 * @return html
 */
if (!function_exists('get_event_icon')) {

    function get_event_icon($share_with = "") {
        $icon = "";
        if (!$share_with) {
            $icon = "lock";
        } else if ($share_with == "all") {
            $icon = "globe";
        } else {
            $icon = "at-sign";
        }
        return $icon;
    }

}



/**
 * get forecast actual widget
 * 
 * @return html
 */
if (!function_exists('forecast_vs_actual_widget')) {

    function forecast_vs_actuals_widget($custom_class = "") {
        $Actuals_model = model("App\Models\Actuals_model");
        $info = $Actuals_model->get_forecast_actuals_info();
        $view_data["forecast"] = $info->forecast ? $info->forecast : 0;
        $view_data["actuals"] = $info->expneses ? $info->expneses : 0;
        $view_data["custom_class"] = $custom_class;
        $template = new Template();
        return $template->view("actuals/forecast_actuals_widget", $view_data);
    }

}


/**
 * get ticket status widget
 * 
 * @return html
 */
if (!function_exists('ticket_status_widget')) {

    function ticket_status_widget() {
        $Tickets_model = model("App\Models\Tickets_model");
        $statuses = $Tickets_model->get_ticket_status_info()->getResult();

        $view_data["new"] = 0;
        $view_data["open"] = 0;
        $view_data["closed"] = 0;
        foreach ($statuses as $status) {
            if ($status->status === "new") {
                $view_data["new"] = $status->total;
            } else if ($status->status === "closed") {
                $view_data["closed"] = $status->total;
            } else {
                $view_data["open"] += $status->total;
            }
        }

        $template = new Template();
        return $template->view("tickets/ticket_status_widget", $view_data);
    }

}


/**
 * get forecast statistics widget
 * 
 * @return html
 */
if (!function_exists('forecast_statistics_widget')) {

    function forecast_statistics_widget($options = array()) {
        $ci = new Security_Controller(false);

        $currency_symbol = get_array_value($options, "currency");

        if ($ci->login_user->user_type == "bu") {
            $options["bu_id"] = $ci->login_user->bu_id;
            $bu_info = $ci->BU_model->get_one($ci->login_user->bu_id);
            $currency_symbol = $bu_info->currency_symbol;
        }

        $currency_symbol = $currency_symbol ? $currency_symbol : get_setting("default_currency");

        $options["currency_symbol"] = $currency_symbol;
        $info = $ci->Forecast_model->forecast_statistics($options);

        $actuals = array();
        $actuals_array = array();

        $forecast = array();
        $forecast_array = array();

        for ($i = 1; $i <= 12; $i++) {
            $actuals[$i] = 0;
            $forecast[$i] = 0;
        }

        foreach ($info->actuals as $actuals) {
            $actuals[$actuals->month] = $actuals->total;
        }
        foreach ($info->forecast as $forecast) {
            $forecast[$forecast->month] = $forecast->total;
        }

        foreach ($actuals as $key => $actuals) {
            $actuals_array[] = $actuals;
        }

        foreach ($forecast as $key => $forecast) {
            $forecast_array[] = $forecast;
        }

        $view_data["actuals"] = json_encode($actuals_array);
        $view_data["forecast"] = json_encode($forecast_array);
        $view_data["currencies"] = $info->currencies;
        $view_data["currency_symbol"] = $currency_symbol;

        $template = new Template();
        return $template->view("forecast/forecast_statistics_widget/index", $view_data);
    }

}

/**
 * get project count status widteg
 * @param integer $user_id
 * 
 * @return html
 */
if (!function_exists('count_project_status_widget')) {

    function count_project_status_widget($user_id = 0) {
        $ci = new Security_Controller(false);
        $options = array(
            "user_id" => $user_id ? $user_id : $ci->login_user->id
        );
        $info = $ci->Projects_model->count_project_status($options);
        $view_data["project_open"] = $info->open;
        $view_data["project_completed"] = $info->completed;
        $template = new Template();
        return $template->view("projects/widgets/project_status_widget", $view_data);
    }

}


/**
 * count total time widget
 * @param integer $user_id
 * 
 * @return html
 */
if (!function_exists('count_total_time_widget')) {

    function count_total_time_widget($user_id = 0) {
        $ci = new Security_Controller(false);
        $options = array("user_id" => $user_id ? $user_id : $ci->login_user->id);

        $permissions = $ci->login_user->permissions;

        $view_data["show_projects_count"] = false;
        if ($ci->login_user->is_admin || get_array_value($permissions, "can_manage_all_projects") == "1") {
            $view_data["show_projects_count"] = true;
        }
    }

}


/**
 * count unread messages
 * @return number
 */
if (!function_exists('count_unread_message')) {

    function count_unread_message() {
        $ci = new Security_Controller(false);
        return $ci->Messages_model->count_unread_message($ci->login_user->id);
    }

}


/**
 * count new tickets
 * @param string $ticket_types
 * @return number
 */
if (!function_exists('count_new_tickets')) {

    function count_new_tickets($ticket_types = "", $show_assigned_tickets_only_user_id = 0) {
        $Tickets_model = model("App\Models\Tickets_model");
        return $Tickets_model->count_new_tickets($ticket_types, $show_assigned_tickets_only_user_id);
    }

}


/**
 * get all tasks kanban widget
 * 
 * @return html
 */
if (!function_exists('all_tasks_kanban_widget')) {

    function all_tasks_kanban_widget() {
        $ci = new Security_Controller(false);

        $projects = $ci->Tasks_model->get_my_projects_dropdown_list($ci->login_user->id)->getResult();
        $projects_dropdown = array(array("id" => "", "text" => "- " . app_lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("team_member") . " -"));
        $assigned_to_list = $ci->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "team_member"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $ci->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value, "isSelected" => true);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            }
        }

        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data['projects_dropdown'] = json_encode($projects_dropdown);

        $view_data['task_statuses'] = $ci->Task_status_model->get_details()->getResult();

        $template = new Template();
        return $template->view("projects/tasks/kanban/all_tasks_kanban_widget", $view_data);
    }

}


/**
 * get todo lists widget
 * 
 * @return html
 */
if (!function_exists('todo_list_widget')) {

    function todo_list_widget() {
        $template = new Template();
        return $template->view("todo/todo_lists_widget");
    }

}


/**
 * get invalid access widget
 * 
 * @return html
 */
if (!function_exists('invalid_access_widget')) {

    function invalid_access_widget() {
        $template = new Template();
        return $template->view("dashboards/custom_dashboards/invalid_access_widget");
    }

}


/**
 * get open projects widget
 * @param integer $user_id
 * 
 * @return html
 */
if (!function_exists('open_projects_widget')) {

    function open_projects_widget($user_id = 0) {
        $ci = new Security_Controller(false);
        $options = array(
            "user_id" => $user_id ? $user_id : $ci->login_user->id
        );
        $view_data["project_open"] = $ci->Projects_model->count_project_status($options)->open;
        $template = new Template();
        return $template->view("projects/widgets/open_projects_widget", $view_data);
    }

}


/**
 * get completed projects widget
 * @param integer $user_id
 * 
 * @return html
 */
if (!function_exists('completed_projects_widget')) {

    function completed_projects_widget($user_id = 0) {
        $ci = new Security_Controller(false);
        $options = array(
            "user_id" => $user_id ? $user_id : $ci->login_user->id
        );
        $view_data["project_completed"] = $ci->Projects_model->count_project_status($options)->completed;
        $template = new Template();
        return $template->view("projects/widgets/completed_projects_widget", $view_data);
    }

}


/**
 * get count of clocked in users widget
 * 
 * @return html
 */
if (!function_exists('count_clock_in_widget')) {

    function count_clock_in_widget() {
        $Attendance_model = model("App\Models\Attendance_model");
        $info = $Attendance_model->count_clock_status()->members_clocked_in;
        $view_data["members_clocked_in"] = $info ? $info : 0;
        $template = new Template();
        return $template->view("attendance/count_clock_in_widget", $view_data);
    }

}


/**
 * get count of clocked out users widget
 * 
 * @return html
 */
if (!function_exists('count_clock_out_widget')) {

    function count_clock_out_widget() {
        $Attendance_model = model("App\Models\Attendance_model");
        $info = $Attendance_model->count_clock_status()->members_clocked_out;
        $view_data["members_clocked_out"] = $info ? $info : 0;
        $template = new Template();
        return $template->view("attendance/count_clock_out_widget", $view_data);
    }

}


/**
 * get user's open project list widget
 * 
 * @return html
 */
if (!function_exists('my_open_projects_widget')) {

    function my_open_projects_widget($bu_id = 0) {
        $ci = new Security_Controller(false);

        $options = array(
            "statuses" => "open",
            "user_id" => $ci->login_user->id
        );

        if ($ci->login_user->user_type == "bu") {
            $options["bu_id"] = $bu_id;
        }

        $view_data["projects"] = $ci->Projects_model->get_details($options)->getResult();
        $template = new Template();
        return $template->view("projects/widgets/my_open_projects_widget", $view_data);
    }

}


/**
 * get user's starred project list widget
 * @param integer $user_id
 * 
 * @return html
 */
if (!function_exists('my_starred_projects_widget')) {

    function my_starred_projects_widget($user_id = 0) {
        $ci = new Security_Controller(false);

        $options = array(
            "user_id" => $user_id ? $user_id : $ci->login_user->id,
            "starred_projects" => true
        );

        $view_data["projects"] = $ci->Projects_model->get_details($options)->getResult();
        $template = new Template();
        return $template->view("projects/widgets/my_starred_projects_widget", $view_data);
    }

}


/**
 * get sticky note widget for logged in user
 * @param string $custom_class
 * 
 * @return html
 */
if (!function_exists('sticky_note_widget')) {

    function sticky_note_widget($custom_class = "") {
        $template = new Template();
        return $template->view("dashboards/sticky_note_widget", array("custom_class" => $custom_class));
    }

}


/**
 * get ticket status small widget for current logged in user
 * @param integer $user_id
 * @param string $type ($type should be new/open/closed)
 * 
 * @return html
 */
if (!function_exists('ticket_status_widget_small')) {

    function ticket_status_widget_small($data = array()) {
        $ci = new Security_Controller(false);
        $allowed_ticket_types = get_array_value($data, "allowed_ticket_types");
        $status = get_array_value($data, "status");

        $options = array("status" => $status);
        if ($ci->login_user->user_type == "team_member") {
            $options["allowed_ticket_types"] = $allowed_ticket_types;
            $options["show_assigned_tickets_only_user_id"] = get_array_value($data, "show_assigned_tickets_only_user_id");
        } else {
            $options["bu_id"] = $ci->login_user->bu_id;
        }

        $view_data["total_tickets"] = $ci->Tickets_model->count_tickets($options);
        $view_data["status"] = $status;

        $template = new Template();
        return $template->view("tickets/ticket_status_widget_small", $view_data);
    }

}


/**
 * get all team members widget
 * 
 * @return html
 */
if (!function_exists('all_team_members_widget')) {

    function all_team_members_widget() {
        $Users_model = model("App\Models\Users_model");
        $options = array("status" => "active", "user_type" => "team_member");
        $view_data["members"] = $Users_model->get_details($options)->getResult();
        $template = new Template();
        return $template->view("team_members/team_members_widget", $view_data);
    }

}


/**
 * get active members widget
 * 
 * @return html
 */
if (!function_exists('active_members_and_bu_widget')) {

    function active_members_and_bu_widget($user_type = "", $show_own_bu_only_user_id = "") {
        $ci = new Security_Controller(false);

        $options = array("user_type" => $user_type, "exclude_user_id" => $ci->login_user->id, "show_own_bu_only_user_id" => $show_own_bu_only_user_id);

        $view_data["users"] = $ci->Users_model->get_active_members_and_bu($options)->getResult();
        $view_data["user_type"] = $user_type;
        $template = new Template();
        return $template->view("team_members/active_members_and_bu_widget", $view_data);
    }

}


/**
 * get total forecast/actuals/due value widget
 * @param string $type
 * 
 * @return html
 */
if (!function_exists('get_forecast_value_widget')) {

    function get_forecast_value_widget($type = "") {
        $Forecast_model = model("App\Models\Forecast_model");
        $view_data["forecast_info"] = $Forecast_model->get_forecast_total_and_paymnts();
        $view_data["type"] = $type;
        $template = new Template();
        return $template->view("forecast/total_forecast_value_widget", $view_data);
    }

}


/**
 * get my tasks list widget
 * 
 * @return html
 */
if (!function_exists('my_tasks_list_widget')) {

    function my_tasks_list_widget() {
        $Task_status_model = model("App\Models\Task_status_model");
        $view_data['task_statuses'] = $Task_status_model->get_details()->getResult();
        $template = new Template();
        return $template->view("projects/tasks/my_tasks_list_widget", $view_data);
    }

}

/**
 * get draft forecast
 * 
 * @return html
 */
if (!function_exists('draft_forecast_widget')) {

    function draft_forecast_widget() {
        $Forecast_model = model("App\Models\Forecast_model");
        $view_data["draft_forecast"] = $Forecast_model->count_draft_forecast();
        $template = new Template();
        return $template->view("forecast/draft_forecast_widget", $view_data);
    }

}

/**
 * get total bu
 * 
 * @return html
 */
if (!function_exists('total_bu_widget')) {

    function total_bu_widget($show_own_bu_only_user_id = "") {
        $BU_model = model("App\Models\BU_model");
        $view_data["total"] = $BU_model->count_total_bu($show_own_bu_only_user_id);
        $template = new Template();
        return $template->view("bu/total_bu_widget", $view_data);
    }

}

/**
 * get total bu contacts
 * 
 * @return html
 */
if (!function_exists('total_contacts_widget')) {

    function total_contacts_widget($show_own_bu_only_user_id = "") {
        $Users_model = model("App\Models\Users_model");
        $view_data["total"] = $Users_model->count_total_contacts($show_own_bu_only_user_id);
        $template = new Template();
        return $template->view("bu/total_contacts_widget", $view_data);
    }

}

/**
 * get open tickets list widget
 * 
 * @return html
 */
if (!function_exists('open_tickets_list_widget')) {

    function open_tickets_list_widget() {
        $ci = new Security_Controller(false);

        if ($ci->login_user->user_type == "bu") {
            $view_data["bu_id"] = $ci->login_user->bu_id;
            $template = new Template();
            return $template->view("bu/tickets/open_tickets_list_widget", $view_data);
        } else {
            $template = new Template();
            return $template->view("tickets/open_tickets_list_widget");
        }
    }

}