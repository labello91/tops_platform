<?php

namespace App\Controllers;

class Intake extends Security_Controller {
    
    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("intake");
    }

    /* load intake list view */

    function index() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_intake");

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("intake", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['intake_statuses'] = $this->Intake_status_model->get_details()->getResult();
        $view_data['intake_sources'] = $this->Intake_source_model->get_details()->getResult();
        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");

        return $this->template->rander("intake/index", $view_data);
    }

    /* load intake add/edit modal */

    function modal_form() {
        $intake_id = $this->request->getPost('id');
        $this->can_access_this_intake($intake_id);
        $view_data = $this->make_intake_modal_form_data($intake_id);
        return $this->template->view('intake/modal_form', $view_data);
    }

    private function make_intake_modal_form_data($intake_id = 0) {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loding from the intake's details view
        $view_data['model_info'] = $this->BU_model->get_one($intake_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
        $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

        $view_data['statuses'] = $this->Intake_status_model->get_details()->getResult();
        $view_data['sources'] = $this->Intake_source_model->get_details()->getResult();

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("intake", $intake_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $view_data;
    }

    //get owners dropdown
    //owner will be team member
    private function _get_owners_dropdown($view_type = "") {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "team_member", "deleted" => 0, "status" => "active"))->getResult();
        $team_members_dropdown = array();

        if ($view_type == "filter") {
            $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("owner") . " -"));
        }

        foreach ($team_members as $member) {
            $team_members_dropdown[] = array("id" => $member->id, "text" => $member->first_name . " " . $member->last_name);
        }

        return $team_members_dropdown;
    }

    /* insert or update an intake */

    function save() {
        $bu_id = $this->request->getPost('id');
        $this->can_access_this_intake($bu_id);

        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "bu_name" => "required"
        ));

        $data = array(
            "bu_name" => $this->request->getPost('bu_name'),
            "name_of_requestor" => $this->request->getPost('name_of_requestor'),
            "project_type" => $this->request->getPost('project_type'),
            "description" => $this->request->getPost('description'),
            "problematic" => $this->request->getPost('problematic'),
            "targeted_audience" => $this->request->getPost('targeted_audience'),
            "success_look" => $this->request->getPost('success_look'),
            "end_state" => $this->request->getPost('end_state'),
            "importance_rate" => $this->request->getPost('importance_rate'),
            "urgency_rate" => $this->request->getPost('urgency_rate'),
            "timeline" => $this->request->getPost('timeline'),
            "is_intake" => 1,
            "intake_status_id" => $this->request->getPost('intake_status_id'),
            "intake_source_id" => $this->request->getPost('intake_source_id'),
            "owner_id" => $this->request->getPost('owner_id') ? $this->request->getPost('owner_id') : $this->login_user->id
        );

        if (!$bu_id) {
            $data["created_date"] = get_current_utc_time();
        }

        $data = clean_data($data);

        $save_id = $this->BU_model->ci_save($data, $bu_id);
        if ($save_id) {
            save_custom_fields("intake", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            if (!$bu_id) {
                log_notification("intake_created", array("intake_id" => $save_id), $this->login_user->id);
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->request->getPost('view'), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_ocurred')));
        }
    }

    /*delete or undo an intake */

    function delete() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $this->can_access_this_intake($id);

        if ($this->BU_model->delete_bu_and_sub_items($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    private function show_own_intake_only_user_id() {
        if ($this->login_user->user_type == "team_member") {
            return get_array_values($this->login_user->permissions, "intake") == "own" ? $this->login_user->id : false;
        }
    }

    private function can_access_this_intake($intake_id = 0) {
        $intake_info = $this->BU_model->get_one($intake_id);

        if ($intake_info->id && get_array_value($this->login_user->permissions, "intake") == "own" && $intake_info->owner_id !== $this->login_user->id) {
            app_redirect("forbidden");
        }
    }

    /* list of intake, prepared for datatable */

    function list_data() {
        $this->access_only_allowed_members();
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("intake", $this->login_user->is_admin, $this->login_user->user_type);

        $show_own_intake_only_user_id = $this->show_own_intake-only_user_id();

        $options = array(
            "custom_fields" => $custom_fields,
            "intake_only" => true,
            "status" => $this->request->getPost('status'),
            "source" => $this->request->getPost('source'),
            "owner_id" => $show_own_intake_only_user_id ? $show_own_intake_only_user_id : $this->request->getPost('owner_id')
        );

        $list_data = $this->BU_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of intake list table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("intake", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields,
            "intake_only" => true
        );
        $data = $this->BU_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of intake list table */

    private function _make_row($data, $custom_fields) {
        //primary contact 
        $image_url = get_avatar($data->contact_avatar);
        $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        $primary_contact = get_intake_contact_profile_link($data->primary_contact_id, $contact);

        //intake owner
        $owner = "-";
        if ($data->owner_id) {
            $owner_image_url = get_avatar($data->owner_avatar);
            $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->owner_name";
            $owner = get_team_member_profile_link($data->owner_id, $owner_user);
        }

        $row_data = array(
            anchor(get_uri("intake/view/" . $data->id), $data->bu_name),
            $data->primary_contact ? $primary_contact : "",
            $owner
        );

        $row_data[] = js_anchor($data->intake_status_title, array("style" => "background-color: $data->intake_status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->intake_status_id, "data-act" => "update-intake-status"));

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = modal_anchor(get_uri("intake/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_intake'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_intake'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("intake/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load intake details view */

    function view($bu_id = 0, $tab = "") {
        $this->check_module_availability("module_intake");
        $this->access_only_allowed_members();

        if ($bu_id) {
            $options = array("id" => $bu_id);
            $intake_info = $this->BU_model->get_details($options)->getRow();
            $this->can_access_this_intake($bu_id);

            if ($intake_info && $intake_info->is_intake) {

                $access_info = $this->get_access_info("forecast");
                $view_data["show_forecast_info"] = (get_setting("module_forecast") && $access_info->access_type == "all") ? true : false;

                /*
                  $access_info = $this->get_access_info("ticket");
                  $view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_info->access_type == "all") ? true : false;
                 */

                $view_data["show_ticket_info"] = false; //don't show tickets for now.

                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $view_data['intake_info'] = $intake_info;

                $view_data["tab"] = $tab;

                return $this->template->rander("intake/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /* load forecast tab  */

    function forecast($bu_id) {
        $this->access_only_allowed_members();

        if ($bu_id) {
            $this->can_access_this_bu($bu_id);
            $view_data["intake_info"] = $this->BU_model->get_one($bu_id);
            $view_data['bu_id'] = $bu_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("forecast", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("intake/forecast/forecast", $view_data);
        }
    }

    /* load notes tab  */

    function notes($bu_id) {
        $this->access_only_allowed_members();

        if ($bu_id) {
            $this->can_access_this_intake($bu_id);
            $view_data['bu_id'] = $bu_id;
            return $this->template->view("intake/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($bu_id) {
        $this->access_only_allowed_members();

        if ($bu_id) {
            $this->can_access_this_intake($bu_id);
            $view_data['bu_id'] = $bu_id;
            $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown("intake");
            $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, app_lang("event") . " " . strtolower(app_lang("label"))));
            return $this->template->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($bu_id) {

        $this->access_only_allowed_members();
        $this->can_access_this_intake($bu_id);

        $options = array("bu_id" => $bu_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->getResult();
        $view_data['bu_id'] = $bu_id;
        return $this->template->view("intake/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $bu_id = $this->request->getPost('bu_id') ? $this->request->getPost('bu_id') : $view_data['model_info']->bu_id;

        $this->access_only_allowed_members();
        $this->can_access_this_intake($bu_id);

        $view_data['bu_id'] = $bu_id;
        return $this->template->view('intake/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {


        $this->validate_submitted_data(array(
            "id" => "numeric",
            "bu_id" => "required|numeric"
        ));

        $bu_id = $this->request->getPost('bu_id');
        $this->access_only_allowed_members();
        $this->can_access_this_intake($bu_id);


        $files = $this->request->getPost("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("bu", $bu_id);

        //process the files which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->request->getPost('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "bu_id" => $bu_id,
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->ci_save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($bu_id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_intake($bu_id);

        $options = array("bu_id" => $bu_id);
        $list_data = $this->General_files_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='float-start'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("intake/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("intake/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("intake/delete_file"), "data-action" => "delete-confirmation"));


        return array($data->id,
            "<div data-feather='$file_icon' class='mr10 float-start'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();

        if ($file_info) {
            $this->access_only_allowed_members();

            if (!$file_info->bu_id) {
                app_redirect("forbidden");
            }

            $this->can_access_this_intake($file_info->bu_id);

            $view_data['can_comment_on_files'] = false;

            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("bu", $file_info->bu_id));

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            return $this->template->view("intake/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->bu_id) {
            app_redirect("forbidden");
        }

        $this->can_access_this_intake($file_info->bu_id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        return $this->download_app_files(get_general_file_path("bu", $file_info->bu_id), $file_data);
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for intake */

    function validate_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->bu_id) {
            app_redirect("forbidden");
        }

        $this->can_access_this_intake($info->bu_id);

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("bu", $info->bu_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->check_module_availability("module_intake");
        $this->access_only_allowed_members();

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->can_access_this_intake($view_data['user_info']->bu_id);

        $view_data['intake_info'] = $this->BU_model->get_one($view_data['user_info']->bu_id);
        $view_data['tab'] = $tab;
        if ($view_data['user_info']->user_type === "intake") {

            $view_data['show_contact_info'] = true;
            return $this->template->rander("intake/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    /* load contacts tab  */

    function contacts($bu_id) {
        $this->access_only_allowed_members();

        if ($bu_id) {
            $this->can_access_this_intake($bu_id);
            $view_data['bu_id'] = $bu_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("intake_contacts", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("intake/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->bu_id = $this->request->getPost('bu_id');
        $this->can_access_this_intake($view_data['model_info']->bu_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("intake_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('intake/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members();

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $this->can_access_this_intake($view_data['model_info']->bu_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("intake_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            return $this->template->view('intake/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's bu info tab view */

    function bu_info_tab($bu_id = 0) {
        if ($bu_id) {
            $this->access_only_allowed_members();
            $this->can_access_this_intake($bu_id);

            $view_data['model_info'] = $this->BU_model->get_one($bu_id);
            $view_data['statuses'] = $this->Intake_status_model->get_details()->getResult();
            $view_data['sources'] = $this->Intake_source_model->get_details()->getResult();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("intake", $bu_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";

            $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

            return $this->template->view('intake/contacts/bu_info_tab', $view_data);
        }
    }


    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->request->getPost('contact_id');
        $bu_id = $this->request->getPost('bu_id');

        $this->access_only_allowed_members();
        $this->can_access_this_intake($bu_id);

        $user_data = array(
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "slack_username" => $this->request->getPost('username'),
            "job_title" => $this->request->getPost('job_title'),
            "gender" => $this->request->getPost('gender'),
            "note" => $this->request->getPost('note'),
            "user_type" => "intake"
        );

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "bu_id" => "required|numeric",
            "email" => "required|valid_email"
        ));

        $user_data["email"] = trim($this->request->getPost('email'));

        if (!$contact_id) {
            //inserting new contact. bu_id is required
            //we'll save following fields only when creating a new contact from this form
            $user_data["bu_id"] = $bu_id;
            $user_data["created_at"] = get_current_utc_time();
        }

        //by default, the first contact of a intake is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->BU_model->get_primary_contact($bu_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->request->getPost('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }

        $user_data = clean_data($user_data);

        $save_id = $this->Users_model->ci_save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("intake_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->ci_save($user_data, $primary_contact);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        $this->access_only_allowed_members();
        $intake_info = $this->Users_model->get_one($user_id);
        $this->can_access_this_intake($intake_info->bu_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->request->getPost("profile_image"));

        if ($profile_image) {
            $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image));

            //delete old file
            delete_app_files(get_setting("profile_image_path"), array(@unserialize($intake_info->image)));

            $image_data = array("image" => $profile_image);
            $this->Users_model->ci_save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                if (!$this->check_profile_image_dimension($image_file_name)) {
                    echo json_encode(array("success" => false, 'message' => app_lang('profile_image_error_message')));
                    exit();
                }

                $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name));

                //delete old file
                delete_app_files(get_setting("profile_image_path"), array(@unserialize($intake_info->image)));

                $image_data = array("image" => $profile_image);
                $this->Users_model->ci_save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed'), "reload_page" => true));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->request->getPost('id');

        $intake_info = $this->Users_model->get_one($id);
        $this->can_access_this_intake($intake_info->bu_id);

        if ($this->request->getPost('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($bu_id = 0) {

        $this->access_only_allowed_members();
        $this->can_access_this_intake($bu_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("intake_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("user_type" => "intake", "bu_id" => $bu_id, "custom_fields" => $custom_fields);
        $list_data = $this->Users_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("intake_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "intake",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->getRow();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1") {
            $primary_contact = "<span class='bg-info badge text-white'>" . app_lang('primary_contact') . "</span>";
        }

        $contact_link = anchor(get_uri("intake/contact_profile/" . $data->id), $full_name . $primary_contact);
        if ($this->login_user->user_type === "intake") {
            $contact_link = $full_name; //don't show clickable link to intake
        }


        $row_data = array(
            $user_avatar,
            $contact_link,
            $data->job_title,
            $data->email,
            $data->username ? $data->username : "-",
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("intake/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* upadate a intake status */

    function save_intake_status($id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_intake($id);

        $data = array(
            "intake_status_id" => $this->request->getPost('value')
        );

        $save_id = $this->BU_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    function all_intake_kanban() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_intake");

        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");
        $view_data['intake_sources'] = $this->Intake_source_model->get_details()->getResult();

        return $this->template->rander("intake/kanban/all_intake", $view_data);
    }

    function all_intake_kanban_data() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_intake");
        $show_own_intake_only_user_id = $this->show_own_intake_only_user_id();

        $options = array(
            "status" => $this->request->getPost('status'),
            "owner_id" => $show_own_intake_only_user_id ? $show_own_intake_only_user_id : $this->request->getPost('owner_id'),
            "source" => $this->request->getPost('source'),
            "search" => $this->request->getPost('search'),
        );

        $view_data["intake"] = $this->BU_model->get_intake_kanban_details($options)->getResult();

        $statuses = $this->Intake_status_model->get_details();
        $view_data["total_columns"] = $statuses->resultID->num_rows;
        $view_data["columns"] = $statuses->getResult();

        return $this->template->view('intake/kanban/kanban_view', $view_data);
    }

    function save_intake_sort_and_status() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_intake");

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $this->can_access_this_intake($id);

        $intake_status_id = $this->request->getPost('intake_status_id');
        $data = array(
            "sort" => $this->request->getPost('sort')
        );

        if ($intake_status_id) {
            $data["intake_status_id"] = $intake_status_id;
        }

        $this->BU_model->ci_save($data, $id);
    }

    function make_BU_modal_form($intake_id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_intake($intake_id);

        //prepare bu details
        $view_data["intake_info"] = $this->make_intake_modal_form_data($intake_id);
        $view_data["intake_info"]["to_custom_field_type"] = "bu";

        //prepare contacts info
        $final_contacts = array();
        $contacts = $this->Users_model->get_all_where(array("user_type" => "intake", "deleted" => 0, "status" => "active", "bu_id" => $intake_id))->getResult();

        //add custom fields for contacts
        foreach ($contacts as $contact) {
            $contact->custom_fields = $this->Custom_fields_model->get_combined_details("intake_contacts", $contact->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $final_contacts[] = $contact;
        }

        $view_data["contacts"] = $final_contacts;

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        return $this->template->view('intake/migration/modal_form', $view_data);
    }

    function save_as_bu() {
        $this->access_only_allowed_members();

        $bu_id = $this->request->getPost('main_bu_id');
        $this->can_access_this_intake($bu_id);

        if ($bu_id) {
            //save bu info
            $this->validate_submitted_data(array(
                "main_bu_id" => "numeric",
                "bu_name" => "required"
            ));

            $bu_name = $this->request->getPost('bu_name');

            $bu_info = $this->BU_model->get_details(array("id" => $bu_id))->getRow();

            $data = array(
                "bu_name" => $bu_name,
                "name_of_requestor" => $this->request->getPost('name_of_requestor'),
                "project_type" => $this->request->getPost('project_type'),
                "description" => $this->request->getPost('description'),
                "problematic" => $this->request->getPost('problematic'),
                "targeted_audience" => $this->request->getPost('targeted_audience'),
                "success_look" => $this->request->getPost('success_look'),
                "end_state" => $this->request->getPost('end_state'),
                "importance_rate" => $this->request->getPost('importance_rate'),
                "group_ids" => $this->request->getPost('group_ids') ? $this->request->getPost('group_ids') : "",
                "urgency_rate" => $this->request->getPost('urgency_rate'),
                "timeline" => $this->request->getPost('timeline'),
                "is_intake" => 0,
                "bu_migration_date" => get_current_utc_time(),
                "last_intake_status" => $bu_info->intake_status_title,
                "created_by" => $this->request->getPost('created_by') ? $this->request->getPost('created_by') : $bu_info->owner_id
            );

            if ($this->login_user->is_admin) {
                $data["currency_symbol"] = $this->request->getPost('currency_symbol') ? $this->request->getPost('currency_symbol') : "";
                $data["currency"] = $this->request->getPost('currency') ? $this->request->getPost('currency') : "";
            }

            $data = clean_data($data);

            //save contacts
            if ($save_bu_id) {
                log_notification("bu_created_from_intake", array("bu_id" => $save_bu_id), $this->login_user->id);

                //save custom field for bu
                if ($this->request->getPost("merge_custom_fields-$bu_id")) {
                    save_custom_fields("intake", $save_bu_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "bu");
                }

                $contacts = $this->Users_model->get_all_where(array("user_type" => "intake", "deleted" => 0, "status" => "active", "bu_id" => $bu_id))->getResult();
                $found_primary_contact = false;

                foreach ($contacts as $contact) {
                    $this->validate_submitted_data(array(
                        'first_name-' . $contact->id => "required",
                        'last_name-' . $contact->id => "required",
                        'email-' . $contact->id => "required|valid_email"
                    ));

                    $user_data = array(
                        "first_name" => $this->request->getPost('first_name-' . $contact->id),
                        "last_name" => $this->request->getPost('last_name-' . $contact->id),
                        "slack_username" => $this->request->getPost('slack_username-' . $contact->id),
                        "job_title" => $this->request->getPost('job_title-' . $contact->id),
                        "gender" => $this->request->getPost('gender-' . $contact->id),
                        "email" => trim($this->request->getPost('email-' . $contact->id)),
                        "password" => md5($this->request->getPost('login_password-' . $contact->id)),
                        "user_type" => "bu"
                    );

                    if ($this->request->getPost('is_primary_contact_value-' . $contact->id) && !$found_primary_contact) {
                        $user_data["is_primary_contact"] = 1;
                        $found_primary_contact = true; //flag that, a primary contact found
                    } else {
                        $user_data["is_primary_contact"] = 0;
                    }

                    if ($this->Users_model->is_email_exists($user_data["email"], $contact->id)) {
                        echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
                        exit();
                    }

                    $user_data = clean_data($user_data);

                    $save_contact_id = $this->Users_model->ci_save($user_data, $contact->id);

                    if ($save_contact_id) {
                        //save custom fields for bu contacts
                        if ($this->request->getPost("merge_custom_fields-$contact->id")) {
                            save_custom_fields("intake_contacts", $save_contact_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "bu_contacts", $contact->id);
                        }

                        if ($this->request->getPost('email_login_details-' . $contact->id)) {
                            $email_template = $this->Email_templates_model->get_final_template("login_info");

                            $parser_data["SIGNATURE"] = $email_template->signature;
                            $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                            $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                            $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                            $parser_data["USER_LOGIN_PASSWORD"] = $this->request->getPost('login_password-' . $contact->id);
                            $parser_data["DASHBOARD_URL"] = base_url();
                            $parser_data["LOGO_URL"] = get_logo_url();

                            $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                            send_app_mail($this->request->getPost('email-' . $contact->id), $email_template->subject, $message);
                        }
                    }
                }

                echo json_encode(array("success" => true, 'redirect_to' => get_uri("bu/view/$save_bu_id"), "message" => app_lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        }
    }

    function upload_excel_file() {
        upload_file_to_temp(true);
    }

    function import_intake_modal_form() {
        $this->access_only_allowed_members();
        return $this->template->view("intake/import_intake_modal_form");
    }

    private function _prepare_intake_data($data_row, $allowed_headers) {
        //prepare intake data
        $intake_data = array("is_intake" => 1);
        $intake_contact_data = array("user_type" => "intake", "is_primary_contact" => 1);
        $custom_field_values_array = array();

        foreach ($data_row as $row_data_key => $row_data_value) {
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if (strpos($header_key_value, 'cf') !== false) { //custom field
                $explode_header_key_value = explode("-", $header_key_value);
                $custom_field_id = get_array_value($explode_header_key_value, 1);
                $custom_field_values_array[$custom_field_id] = $row_data_value;
            } else if ($header_key_value == "contact_first_name") {
                $intake_contact_data["first_name"] = $row_data_value;
            } else if ($header_key_value == "contact_last_name") {
                $intake_contact_data["last_name"] = $row_data_value;
            } else if ($header_key_value == "contact_email") {
                $intake_contact_data["email"] = $row_data_value;
            } else if ($header_key_value == "status") {
                //get existing status, if not create new one and add the id
                $existing_status = $this->Intake_status_model->get_one_where(array("title" => $row_data_value, "deleted" => 0));
                if ($existing_status->id) {
                    $intake_data["intake_status_id"] = $existing_status->id;
                } else {
                    $max_sort_value = $this->Intake->get_max_sort_value();
                    $status_data = array("title" => $row_data_value, "color" => "#f1c40f", "sort" => ($max_sort_value * 1 + 1));
                    $intake_data["intake_status_id"] = $this->Intake_status_model->ci_save($status_data);
                }
            } else if ($header_key_value == "source") {
                //get existing source, if not create new one and add the id
                $existing_source = $this->Intake_source_model->get_one_where(array("title" => $row_data_value, "deleted" => 0));
                if ($existing_source->id) {
                    $intake_data["intake_source_id"] = $existing_source->id;
                } else {
                    $max_sort_value = $this->Intake_source_model->get_max_sort_value();
                    $source_data = array("title" => $row_data_value, "sort" => ($max_sort_value * 1 + 1));
                    $intake_data["intake_source_id"] = $this->Intake_source_model->ci_save($source_data);
                }
            } else {
                $Intake_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "intake_data" => $intake_data,
            "intake_contact_data" => $intake_contact_data,
            "custom_field_values_array" => $custom_field_values_array
        );
    }

    private function _get_existing_custom_field_id($title = "") {
        if (!$title) {
            return false;
        }

        $custom_field_data = array(
            "title" => $title,
            "related_to" => "intake"
        );

        $existing = $this->Custom_fields_model->get_one_where(array_merge($custom_field_data, array("deleted" => 0)));
        if ($existing->id) {
            return $existing->id;
        }
    }

    private function _prepare_headers_for_submit($headers_row, $headers) {
        foreach ($headers_row as $key => $header) {
            if (!((count($headers) - 1) < $key)) { //skip default headers
                continue;
            }

            //so, it's a custom field
            //check if there is any custom field existing with the title
            //add id like cf-3
            $existing_id = $this->_get_existing_custom_field_id($header);
            if ($existing_id) {
                array_push($headers, "cf-$existing_id");
            }
        }

        return $headers;
    }

    function save_intake_from_excel_file() {
        if (!$this->validate_import_intake_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }

        $file_name = $this->request->getPost('file_name');
        require_once(APPPATH . "ThirdParty/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new \SpreadsheetReader($temp_file_path . $file_name);
        $allowed_headers = $this->_get_allowed_headers();
        $now = get_current_utc_time();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, modify this for custom fields and continue for the next loop
                $allowed_headers = $this->_prepare_headers_for_submit($value, $allowed_headers);
                continue;
            }

            $intake_data_array = $this->_prepare_intake_data($value, $allowed_headers);
            $intake_data = get_array_value($intake_data_array, "intake_data");
            $intake_contact_data = get_array_value($intake_data_array, "intake_contact_data");
            $custom_field_values_array = get_array_value($intake_data_array, "custom_field_values_array");

            //couldn't prepare valid data
            if (!($intake_data && count($intake_data) > 1)) {
                continue;
            }

            //found information about intake, add some additional info
            $intake_data["created_date"] = $now;
            $intake_data["owner_id"] = $this->login_user->id;
            $intake_contact_data["created_at"] = $now;

            //save intake data
            $intake_save_id = $this->BU_model->ci_save($intake_data);
            if (!$intake_save_id) {
                continue;
            }

            //save custom fields
            $this->_save_custom_fields_of_intake($intake_save_id, $custom_field_values_array);

            //add intake id to contact data
            $intake_contact_data["BU_id"] = $intake_save_id;
            $this->Users_model->ci_save($intake_contact_data);
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _save_custom_fields_of_intake($intake_id, $custom_field_values_array) {
        if (!$custom_field_values_array) {
            return false;
        }

        foreach ($custom_field_values_array as $key => $custom_field_value) {
            $field_value_data = array(
                "related_to_type" => "intake",
                "related_to_id" => $intake_id,
                "custom_field_id" => $key,
                "value" => $custom_field_value
            );

            $field_value_data = clean_data($field_value_data);

            $this->Custom_field_values_model->ci_save($field_value_data);
        }
    }

    private function _get_allowed_headers() {
        return array(
            "bu_name",
            "name_of_requestor",
            "project_type",
            "description",
            "problematic",
            "targeted_audience",
            "success_look",
            "end_state",
            "importance_rate",
            "urgency_rate",
            "timeline",
            "is_intake",
            "intake_status_id",
            "intake_source_id",
            "owner_id"
        );
    }

    private function _store_headers_position($headers_row = array()) {
        $allowed_headers = $this->_get_allowed_headers();

        //check if all headers are correct and on the right position
        $final_headers = array();
        foreach ($headers_row as $key => $header) {
            $key_value = str_replace(' ', '_', strtolower(trim($header, " ")));
            $header_on_this_position = get_array_value($allowed_headers, $key);
            $header_array = array("key_value" => $header_on_this_position, "value" => $header);

            if ($header_on_this_position == $key_value) {
                //allowed headers
                //the required headers should be on the correct positions
                //the rest headers will be treated as custom fields
                //pushed header at last of this loop
            } else if (((count($allowed_headers) - 1) < $key) && $key_value) {
                //custom fields headers
                //check if there is any existing custom field with this title
                if (!$this->_get_existing_custom_field_id(trim($header, " "))) {
                    $header_array["has_error"] = true;
                    $header_array["custom_field"] = true;
                }
            } else { //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            if ($key_value) {
                array_push($final_headers, $header_array);
            }
        }

        return $final_headers;
    }

    function validate_import_intake_file() {
        $file_name = $this->request->getPost("file_name");
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if ($file_ext == "xlsx") {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_a_excel_file') . " (.xlsx)"));
        }
    }

    function validate_import_intake_file_data($check_on_submit = false) {
        $table_data = "";
        $error_message = "";
        $headers = array();
        $got_error_header = false; //we've to check the valid headers first, and a single header at a time
        $got_error_table_data = false;

        $file_name = $this->request->getPost("file_name");

        require_once(APPPATH . "ThirdParty/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new \SpreadsheetReader($temp_file_path . $file_name);

        $table_data .= '<table class="table table-responsive table-bordered table-hover" style="width: 100%; color: #444;">';

        $table_data_header_array = array();
        $table_data_body_array = array();

        foreach ($excel_file as $row_key => $value) {
            if ($row_key == 0) { //validate headers
                $headers = $this->_store_headers_position($value);

                foreach ($headers as $row_data) {
                    $has_error_class = false;
                    if (get_array_value($row_data, "has_error") && !$got_error_header) {
                        $has_error_class = true;
                        $got_error_header = true;

                        if (get_array_value($row_data, "custom_field")) {
                            $error_message = app_lang("no_such_custom_field_found");
                        } else {
                            $error_message = sprintf(app_lang("import_bu_error_header"), app_lang(get_array_value($row_data, "key_value")));
                        }
                    }

                    array_push($table_data_header_array, array("has_error_class" => $has_error_class, "value" => get_array_value($row_data, "value")));
                }
            } else { //validate data
                if (!array_filter($value)) {
                    continue;
                }

                $error_message_on_this_row = "<ol class='pl15'>";
                $has_contact_first_name = get_array_value($value, 1) ? true : false;

                foreach ($value as $key => $row_data) {
                    $has_error_class = false;

                    if (!$got_error_header) {
                        $row_data_validation = $this->_row_data_validation_and_get_error_message($key, $row_data, $has_contact_first_name);
                        if ($row_data_validation) {
                            $has_error_class = true;
                            $error_message_on_this_row .= "<li>" . $row_data_validation . "</li>";
                            $got_error_table_data = true;
                        }
                    }

                    if ($row_data === "0" || $row_data === 0 || $row_data || $has_error_class) {
                        $table_data_body_array[$row_key][] = array("has_error_class" => $has_error_class, "value" => $row_data);
                    }
                }

                $error_message_on_this_row .= "</ol>";

                //error messages for this row
                if ($got_error_table_data) {
                    $table_data_body_array[$row_key][] = array("has_error_text" => true, "value" => $error_message_on_this_row);
                }
            }
        }

        //return false if any error found on submitting file
        if ($check_on_submit) {
            return ($got_error_header || $got_error_table_data) ? false : true;
        }

        //add error header if there is any error in table body
        if ($got_error_table_data) {
            array_push($table_data_header_array, array("has_error_text" => true, "value" => app_lang("error")));
        }

        //add headers to table
        $table_data .= "<tr>";
        foreach ($table_data_header_array as $table_data_header) {
            $error_class = get_array_value($table_data_header, "has_error_class") ? "error" : "";
            $error_text = get_array_value($table_data_header, "has_error_text") ? "text-danger" : "";
            $value = get_array_value($table_data_header, "value");
            $table_data .= "<th class='$error_class $error_text'>" . $value . "</th>";
        }
        $table_data .= "<tr>";

        //add body data to table
        foreach ($table_data_body_array as $table_data_body_row) {
            $table_data .= "<tr>";

            foreach ($table_data_body_row as $table_data_body_row_data) {
                $error_class = get_array_value($table_data_body_row_data, "has_error_class") ? "error" : "";
                $error_text = get_array_value($table_data_body_row_data, "has_error_text") ? "text-danger" : "";
                $value = get_array_value($table_data_body_row_data, "value");
                $table_data .= "<td class='$error_class $error_text'>" . $value . "</td>";
            }

            $table_data .= "<tr>";
        }

        //add error message for header
        if ($error_message) {
            $total_columns = count($table_data_header_array);
            $table_data .= "<tr><td class='text-danger' colspan='$total_columns'><i data-feather='alert-triangle' class='icon-16'></i> " . $error_message . "</td></tr>";
        }

        $table_data .= "</table>";

        echo json_encode(array("success" => true, 'table_data' => $table_data, 'got_error' => ($got_error_header || $got_error_table_data) ? true : false));
    }

    private function _row_data_validation_and_get_error_message($key, $data, $has_contact_first_name) {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //bu name field is required
        if ($header_value == "bu_name" && !$data) {
            return app_lang("import_bu_error_bu_name_field_required");
        }

        //if there is contact first name then the contact last name and email is required
        //the email should be unique then
        if ($has_contact_first_name) {
            if ($header_value == "contact_last_name" && !$data) {
                return app_lang("import_intake_error_contact_name");
            }
        }
    }

    function download_sample_excel_file() {
        $this->access_only_allowed_members();
        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-intake-sample.xlsx"))));
    }

}

/* End of file intake.php */
/* Location: ./app/controllers/intake.php */
