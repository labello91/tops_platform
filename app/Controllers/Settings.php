<?php

namespace App\Controllers;

use App\Libraries\Imap;

class Settings extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
}

function index() {
    app_redirect('settings/general');
}

function general() {
    $tzlist = \DateTimeZone::litIdentifiers();
    $view_data['timezone_dropdown'] = array();
    foreach ($tzlist as $zone) {
        $view_data['timezone_dropdown'] [$zone] = $zone;
    }

    $view_data['language_dropdown'] = get_language_list();

    $view_data["currency_dropdown"] = get_international_currency_code_dropdown();
    return $this->template->rander("settings/general", $view_data);
}

function save_general_settings() {
    $settings = array("site_logo", "favicon", "show_background_image_in_signin_page", "show_logo_in_signin_page", "app_title", "language", "timezone", "date_format", "time_format", "first_day_of_week", "weekends", "default_currency", "currency_symbol", "currency_position", "decimal_separator", "no_of_decimals", "accepted_file_formats", "rows_per_page", "scrollbar", "enable_rich_text_editor", "rtl", "show_theme_color_changer", "default_theme_color");

    foreach ($settings as $setting) {
        $value = $this->request->getPost($setting);
        if ($value || $value === "0") {
            if ($setting === "site_logo") {
                $value = str_replace("~", ":", $value);
                $value = serialize(move_temp_file("site-logo.png", get_setting("system_file_path"), "", $value));

                //delete old file
                delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("site_logo"));
            } else if ($setting === "favicon") {
                $value = str_replace("~", ":", $value);
                $value = serialize(move_temp_file("favicon.png", get_setting("system_file_path"), "", $value));

                //delete old file
                if (get_setting("favicon")) {
                    delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("favicon"));
                }
            }

            $this->Settings_model->save_settings($setting, $value);
        }
        
        //save empty value too for weekends
        if ($setting == "weekends") {
            $this->Settings_model->save_settings($settings, $value);
        }
    }

    $reload_page = false;

    //save signin page background

    $files_data = move_files_from_temp_dir_to_permanent_dir(get_setting("system_file_path"), "system");
        $unserialize_files_data = unserialize($files_data);
        $sigin_page_background = get_array_value($unserialize_files_data, 0);
        if ($sigin_page_background) {
            delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("signin_page_background"));
            $this->Settings_model->save_setting("signin_page_background", serialize($sigin_page_background));
            $reload_page = true;
        }

        if ($_FILES) {
            $files = array("site_logo_file", "favicon_file");

            foreach ($files as $file) {
                $file_data = get_array_value($_FILES, $file);

                if (!($file_data && is_array($file_data) && count($file_data))) {
                    continue;
                }

                $file_name = get_array_value($file_data, "tmp_name");
                if (!$file_name) {
                    continue;
                }

                $new_file_name = "site-logo.png";
                $setting_name = "site_logo";
                if ($file === "favicon_file") {
                    $new_file_name = "favicon.png";
                    $setting_name = "favicon";
                }

                $new_file_data = serialize(move_temp_file($new_file_name, get_setting("system_file_path"), "", $file_name));
                //delete old file
                delete_app_files(get_setting("system_file_path"), get_system_files_setting_value($setting_name));
                $this->Settings_model->save_setting($setting_name, $new_file_data);
            }

            $reload_page = true;
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated'), 'reload_page' => $reload_page));
    }

    function bu() {
        return $this->template->rander("settings/bu");
    }

    function save_bu_settings() {
        $settings = array("bu_name", "bu_vp", "bu_poc", "bu_email");

        foreach ($settings as $setting) {
            $this->Settings_model->save_setting($setting, $this->request->getPost($setting));
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function email() {
        return $this->template->rander("settings/email");
    }

    function save_email_settings() {
        $settings = array("email_sent_from_address", "email_sent_from_name", "email_protocol", "email_smtp_host", "email_smtp_port", "email_smtp_user", "email_smtp_pass", "email_smtp_security_type");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (!$value) {
                $value = "";
            }

            if ($setting == "email_smtp_pass") {
                if ($value === "******") {
                    $value = get_setting('email_smtp_pass');
                } else {
                    $value = encode_id($value, "email_smtp_pass");
                }
            }


            $this->Settings_model->save_setting($setting, $value);
        }

        $test_email_to = $this->request->getPost("send_test_mail_to");
        if ($test_email_to) {
            $email_config = Array(
                'charset' => 'utf-8',
                'mailType' => 'html'
            );
            if ($this->request->getPost("email_protocol") === "smtp") {
                $email_config["protocol"] = "smtp";
                $email_config["SMTPHost"] = $this->request->getPost("email_smtp_host");
                $email_config["SMTPPort"] = $this->request->getPost("email_smtp_port");
                $email_config["SMTPUser"] = $this->request->getPost("email_smtp_user");

                $email_smtp_pass = $this->request->getPost("email_smtp_pass");
                if ($email_smtp_pass === "******") {
                    $email_smtp_pass = decode_password(get_setting('email_smtp_pass'), "email_smtp_pass");
                }
                $email_config["SMTPPass"] = $email_smtp_pass;
                $email_config["SMTPCrypto"] = $this->request->getPost("email_smtp_security_type");
                if ($email_config["SMTPCrypto"] === "none") {
                    $email_config["SMTPCrypto"] = "";
                }
            }

            $email = \CodeIgniter\Config\Services::email();
            $email->initialize($email_config);

            $email->setNewline("\r\n");
            $email->setCRLF("\r\n");
            $email->setFrom($this->request->getPost("email_sent_from_address"), $this->request->getPost("email_sent_from_name"));

            $email->setTo($test_email_to);
            $email->setSubject("Test message");
            $email->setMessage("This is a test message to check mail configuration.");

            if ($email->send()) {
                echo json_encode(array("success" => true, 'message' => app_lang('test_mail_sent')));
                return false;
            } else {
                log_message('error', $email->printDebugger());
                echo json_encode(array("success" => false, 'message' => app_lang('test_mail_send_failed')));
                return false;
            }
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function ip_restriction() {
        return $this->template->rander("settings/ip_restriction");
    }

    function save_ip_settings() {
        $this->Settings_model->save_setting("allowed_ip_addresses", $this->request->getPost("allowed_ip_addresses"));

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function db_backup() {
        return $this->template->rander("settings/db_backup");
    }

    function bu_permissions() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "team_member"))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $hidden_menus = array(
            "announcements",
            "events",
            "forecast",
            "knowledge_base",
            "projects",
            "actuals",
            "tickets"
        );

        $hidden_menu_dropdown = array();
        foreach ($hidden_menus as $hidden_menu) {
            $hidden_menu_dropdown[] = array("id" => $hidden_menu, "text" => app_lang($hidden_menu));
        }

        $view_data['hidden_menu_dropdown'] = json_encode($hidden_menu_dropdown);
        $view_data['members_dropdown'] = json_encode($members_dropdown);
        return $this->template->rander("settings/bu_permissions", $view_data);
    }
}