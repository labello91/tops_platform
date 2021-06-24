<?php

namespace App\Models;

class Users_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'users';
        parent::__construct($this->table);
    }

    function authenticate($email, $password) {

        $email = $this->db->escapeString($email);

        $this->db_builder->select("id,user_type,bu_id,password");
        $result = $this->db_builder->getWhere(array('email' => $email, 'status' => 'active', 'deleted' => 0, 'disable_login' => 0));

        if (count($result->getResult()) !== 1) {
            return false;
        }

        $user_info = $result->getRow();

        //there has two password encryption method for legacy (md5) compatibility
        //check if anyone of them is correct
        if ((strlen($user_info->password) === 60 && password_verify($password, $user_info->password)) || $user_info->password === md5($password)) {

            if ($this->_bu_can_login($user_info) !== false) {
                $session = \Config\Services::session();
                $session->set('user_id', $user_info->id);
                return true;
            }
        }
    }

    private function _bu_can_login($user_info) {
        //check bu login settings
        if ($user_info->user_type === "bu" && get_setting("disable_bu_login")) {
            return false;
        } else if ($user_info->user_type === "bu") {
            //user can't be loged in if bu has deleted
            $bu_table = $this->db->prefixTable('bu');

            $sql = "SELECT $bu_table.id
                    FROM $bu_table
                    WHERE $bu_table.id = $user_info->bu_id AND $bu_table.deleted=0";
            $bu_result = $this->db->query($sql);

            if ($bu_result->resultID->num_rows !== 1) {
                return false;
            }
        }
    }

    function login_user_id() {
        $session = \Config\Services::session();
        return $session->has("user_id") ? $session->get("user_id") : "";
    }

    function sign_out() {
        $session = \Config\Services::session();
        $session->destroy();
        app_redirect('signin');
    }

    function get_details($options = array()) {
        $users_table = $this->db->prefixTable('users');
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        $id = get_array_value($options, "id");
        $status = get_array_value($options, "status");
        $user_type = get_array_value($options, "user_type");
        $bu_id = get_array_value($options, "bu_id");
        $exclude_user_id = get_array_value($options, "exclude_user_id");

        if ($id) {
            $where .= " AND $users_table.id=$id";
        }
        if ($status === "active") {
            $where .= " AND $users_table.status='active'";
        } else if ($status === "inactive") {
            $where .= " AND $users_table.status='inactive'";
        }

        if ($user_type) {
            $where .= " AND $users_table.user_type='$user_type'";
        }

        if ($user_type == 'bu') {
            $where .= " AND $bu_table.deleted=0";
        }


        if ($bu_id) {
            $where .= " AND $users_table.bu_id=$bu_id";
        }

        if ($exclude_user_id) {
            $where .= " AND $users_table.id!=$exclude_user_id";
        }

        $show_own_bu_only_user_id = get_array_value($options, "show_own_bu_only_user_id");
        if ($user_type == "bu" && $show_own_bu_only_user_id) {
            $where .= " AND $users_table.bu_id IN(SELECT $bu_table.id FROM $bu_table WHERE $bu_table.deleted=0 AND $bu_table.created_by=$show_own_bu_only_user_id)";
        }


        $custom_field_type = "team_members";
        if ($user_type === "bu") {
            $custom_field_type = "bu_contacts";
        } else if ($user_type === "lead") {
            $custom_field_type = "lead_contacts";
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $users_table);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");


        //prepare full query string
        $sql = "SELECT $users_table.*,
            $team_member_job_info_table.date_of_hire, $team_member_job_info_table.salary, $team_member_job_info_table.salary_term $select_custom_fieds
        FROM $users_table
        LEFT JOIN $team_member_job_info_table ON $team_member_job_info_table.user_id=$users_table.id
        LEFT JOIN $bu_table ON $bu_table.id=$users_table.bu_id
        $join_custom_fieds    
        WHERE $users_table.deleted=0 $where
        ORDER BY $users_table.first_name";
        return $this->db->query($sql);
    }

    function is_email_exists($email, $id = 0) {
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT $users_table.* FROM $users_table   
        WHERE $users_table.deleted=0 AND $users_table.email='$email' AND $users_table.user_type!='lead'";

        $result = $this->db->query($sql);

        if ($result->resultID->num_rows && $result->getRow()->id != $id) {
            return $result->getRow();
        } else {
            return false;
        }
    }

    function get_job_info($user_id) {
        parent::use_table("team_member_job_info");
        return parent::get_one_where(array("user_id" => $user_id));
    }

    function save_job_info($data) {
        parent::use_table("team_member_job_info");

        //check if job info already exists
        $where = array("user_id" => get_array_value($data, "user_id"));
        $exists = parent::get_one_where($where);
        if ($exists->user_id) {
            //job info found. update the record
            return parent::update_where($data, $where);
        } else {
            //insert new one
            return parent::ci_save($data);
        }
    }

    function get_team_members($member_ids = "") {
        $users_table = $this->db->prefixTable('users');
        $sql = "SELECT $users_table.*
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.user_type='team_member' AND FIND_IN_SET($users_table.id, '$member_ids')
        ORDER BY $users_table.first_name";
        return $this->db->query($sql);
    }

    function get_access_info($user_id = 0) {
        $users_table = $this->db->prefixTable('users');
        $roles_table = $this->db->prefixTable('roles');
        $team_table = $this->db->prefixTable('team');

        if (!$user_id) {
            $user_id = 0;
        }

        $sql = "SELECT $users_table.id, $users_table.user_type, $users_table.is_admin, $users_table.role_id, $users_table.email,
            $users_table.first_name, $users_table.last_name, $users_table.image, $users_table.message_checked_at, $users_table.notification_checked_at, $users_table.bu_id, $users_table.enable_web_notification,
            $users_table.is_primary_contact, $users_table.sticky_note,
            $roles_table.title as role_title, $roles_table.permissions,
            (SELECT GROUP_CONCAT(id) team_ids FROM $team_table WHERE FIND_IN_SET('$user_id', `members`)) as team_ids
        FROM $users_table
        LEFT JOIN $roles_table ON $roles_table.id = $users_table.role_id AND $roles_table.deleted = 0
        WHERE $users_table.deleted=0 AND $users_table.id=$user_id";
        return $this->db->query($sql)->getRow();
    }

    function get_team_members_and_bu($user_type = "", $user_ids = "", $exlclude_user = 0) {

        $users_table = $this->db->prefixTable('users');
        $bu_table = $this->db->prefixTable('bu');


        $where = "";
        if ($user_type) {
            $where .= " AND $users_table.user_type='$user_type'";
        } else {
            $where .= " AND $users_table.user_type!='lead'";
        }

        if ($user_ids) {
            $where .= "  AND FIND_IN_SET($users_table.id, '$user_ids')";
        }

        if ($exlclude_user) {
            $where .= " AND $users_table.id !=$exlclude_user";
        }

        $sql = "SELECT $users_table.id,$users_table.bu_id, $users_table.user_type, $users_table.first_name, $users_table.last_name, $bu_table.company_name,
            $users_table.image,  $users_table.job_title, $users_table.last_online
        FROM $users_table
        LEFT JOIN $bu_table ON $bu_table.id = $users_table.bu_id AND $bu_table.deleted=0
        WHERE $users_table.deleted=0 AND $users_table.status='active' $where
        ORDER BY $users_table.user_type, $users_table.first_name ASC";
        return $this->db->query($sql);
    }

    /* return comma separated list of user names */

    function user_group_names($user_ids = "") {
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT GROUP_CONCAT(' ', $users_table.first_name, ' ', $users_table.last_name) AS user_group_name
        FROM $users_table
        WHERE FIND_IN_SET($users_table.id, '$user_ids')";
        return $this->db->query($sql)->getRow();
    }

    /* return list of ids of the online users */

    function get_online_user_ids() {
        $users_table = $this->db->prefixTable('users');
        $now = get_current_utc_time();

        $sql = "SELECT $users_table.id 
        FROM $users_table
        WHERE TIMESTAMPDIFF(MINUTE, users.last_online, '$now')<=0";
        return $this->db->query($sql)->getResult();
    }

    function get_active_members_and_bu($options = array()) {
        $users_table = $this->db->prefixTable('users');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";

        $user_type = get_array_value($options, "user_type");
        if ($user_type) {
            $where .= " AND $users_table.user_type='$user_type'";
        }

        $exclude_user_id = get_array_value($options, "exclude_user_id");
        if ($exclude_user_id) {
            $where .= " AND $users_table.id!=$exclude_user_id";
        }

        $show_own_bu_only_user_id = get_array_value($options, "show_own_bu_only_user_id");
        if ($user_type == "bu" && $show_own_bu_only_user_id) {
            $where .= " AND $users_table.bu_id IN(SELECT $bu_table.id FROM $bu_table WHERE $bu_table.deleted=0 AND $bu_table.created_by=$show_own_bu_only_user_id)";
        }

        $sql = "SELECT CONCAT($users_table.first_name, ' ',$users_table.last_name) AS member_name, $users_table.last_online, $users_table.id, $users_table.image, $users_table.job_title, $users_table.user_type, $bu_table.company_name
        FROM $users_table
        LEFT JOIN $bu_table ON $bu_table.id = $users_table.bu_id AND $bu_table.deleted=0
        WHERE $users_table.deleted=0 $where
        ORDER BY $users_table.last_online DESC";
        return $this->db->query($sql);
    }

    function count_total_contacts($show_own_bu_only_user_id = "") {
        $users_table = $this->db->prefixTable('users');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        if ($show_own_bu_only_user_id) {
            $where .= " AND $users_table.bu_id IN(SELECT $bu_table.id FROM $bu_table WHERE $bu_table.deleted=0 AND $bu_table.created_by=$show_own_bu_only_user_id)";
        }

        $sql = "SELECT COUNT($users_table.id) AS total
        FROM $users_table 
        WHERE $users_table.deleted=0 AND $users_table.user_type='bu' $where";
        return $this->db->query($sql)->getRow()->total;
    }

}
