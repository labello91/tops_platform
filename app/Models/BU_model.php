<?php

namespace App\Models;

class BU_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'bu';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $bu_table = $this->db->prefixTable('bu');
        $projects_table = $this->db->prefixTable('projects');
        $users_table = $this->db->prefixTable('users');
        $forecast_table = $this->db->prefixTable('forecast');
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_items_table = $this->db->prefixTable('forecast_items');
        $bu_groups_table = $this->db->prefixTable('bu_groups');
        $intake_status_table = $this->db->prefixTable('intake_status');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $bu_table.id=$id";
        }

        $custom_field_type = "bu";

        $intake_only = get_array_value($options, "intake_only");
        if ($intake_only) {
            $custom_field_type = "intake";
            $where .= " AND $bu_table.is_intake=1";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $bu_table.intake_status_id='$status'";
        }

        $source = get_array_value($options, "source");
        if ($source) {
            $where .= " AND $bu_table.intake_source_id='$source'";
        }

        $owner_id = get_array_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $bu_table.owner_id=$owner_id";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $bu_table.created_by=$created_by";
        }

        $show_own_bu_only_user_id = get_array_value($options, "show_own_bu_only_user_id");
        if ($show_own_bu_only_user_id) {
            $where .= " AND ($bu_table.created_by=$show_own_bu_only_user_id OR $bu_table.owner_id=$show_own_bu_only_user_id)";
        }

        if (!$id && !$intake_only) {
            //only bu
            $where .= " AND $bu_table.is_intake=0";
        }

        $group_id = get_array_value($options, "group_id");
        if ($group_id) {
            $where .= " AND FIND_IN_SET('$group_id', $bu_table.group_ids)";
        }

        $quick_filter = get_array_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $bu_table, $projects_table, $forecast_table, $taxes_table, $forecast_actuals_table, $forecast_items_table);
        }


        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $bu_table);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");

        $forecast_value_calculation_query = "(SUM" . $this->_get_forecast_value_calculation_query($forecast_table) . ")";

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $forecast_value_select = "IFNULL(forecast_details.forecast_value,0)";
        $actuals_value_select = "IFNULL(forecast_details.actuals_received,0)";

        $sql = "SELECT $bu_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact, $users_table.id AS primary_contact_id, $users_table.image AS contact_avatar,  project_table.total_projects, $actuals_value_select AS actuals_received $select_custom_fieds,
                IF((($forecast_value_select > $actuals_value_select) AND ($forecast_value_select - $actuals_value_select) <0.05), $actuals_value_select, $forecast_value_select) AS forecast_value,
                (SELECT GROUP_CONCAT($bu_groups_table.title) FROM $bu_groups_table WHERE FIND_IN_SET($bu_groups_table.id, $bu_table.group_ids)) AS bu_groups, $intake_status_table.title AS intake_status_title,  $intake_status_table.color AS intake_status_color,
                owner_details.owner_name, owner_details.owner_avatar
        FROM $bu_table
        LEFT JOIN $users_table ON $users_table.bu_id = $bu_table.id AND $users_table.deleted=0 AND $users_table.is_primary_contact=1 
        LEFT JOIN (SELECT bu_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 GROUP BY bu_id) AS project_table ON project_table.bu_id= $bu_table.id
        LEFT JOIN (SELECT bu_id, SUM(actuals_table.actuals_received) as actuals_received, $forecast_value_calculation_query as forecast_value FROM $forecast_table
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $forecast_table.tax_id
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $forecast_table.tax_id2 
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table3 ON tax_table3.id = $forecast_table.tax_id3 
                   LEFT JOIN (SELECT forecast_id, SUM(amount) AS actuals_received FROM $forecast_actuals_table WHERE deleted=0 GROUP BY forecast_id) AS actuals_table ON actuals_table.forecast_id=$forecast_table.id AND $forecast_table.deleted=0 AND $forecast_table.status='not_paid'
                   LEFT JOIN (SELECT forecast_id, SUM(total) AS forecast_value FROM $forecast_items_table WHERE deleted=0 GROUP BY forecast_id) AS items_table ON items_table.forecast_id=$forecast_table.id AND $forecast_table.deleted=0 AND $forecast_table.status='not_paid'
                   WHERE $forecast_table.status='not_paid'
                   GROUP BY $forecast_table.bu_id    
                   ) AS forecast_details ON forecast_details.bu_id= $bu_table.id 
        LEFT JOIN $intake_status_table ON $bu_table.intake_status_id = $intake_status_table.id 
        LEFT JOIN (SELECT $users_table.id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar FROM $users_table WHERE $users_table.deleted=0 AND $users_table.user_type='team_member') AS owner_details ON owner_details.id=$bu_table.owner_id
        $join_custom_fieds               
        WHERE $bu_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    private function make_quick_filter_query($filter, $bu_table, $projects_table, $forecast_table, $taxes_table, $forecast_actuals_table, $forecast_items_table) {
        $query = "";

        if ($filter == "has_open_projects" || $filter == "has_completed_projects" || $filter == "has_any_hold_projects") {
            $status = "open";
            if ($filter == "has_completed_projects") {
                $status = "completed";
            } else if ($filter == "has_any_hold_projects") {
                $status = "hold";
            }

            $query = " AND $bu_table.id IN(SELECT $projects_table.bu_id FROM $projects_table WHERE $projects_table.deleted=0 AND $projects_table.status='$status') ";
        } else if ($filter == "has_unpaid_forecast" || $filter == "has_overdue_forecast" || $filter == "has_partially_paid_forecast") {
            $now = get_my_local_time("Y-m-d");
            $forecast_value_calculation_query = $this->_get_forecast_value_calculation_query($forecast_table);
            $forecast_value_calculation = "TRUNCATE($forecast_value_calculation_query,2)";

            $forecast_where = " AND $forecast_table.status !='draft' AND $forecast_table.status!='cancelled' AND IFNULL(actuals_table.actuals_received,0)<=0";
            if ($filter == "has_overdue_forecast") {
                $forecast_where = " AND $forecast_table.status !='draft' AND $forecast_table.status!='cancelled' AND $forecast_table.due_date<'$now' AND TRUNCATE(IFNULL(actuals_table.actuals_received,0),2)<$forecast_value_calculation";
            } else if ($filter == "has_partially_paid_forecast") {
                $forecast_where = " AND IFNULL(actuals_table.actuals_received,0)>0 AND IFNULL(actuals_table.actuals_received,0)<$forecast_value_calculation";
            }

            $query = " AND $bu_table.id IN(
                            SELECT $forecast_table.bu_id FROM $forecast_table 
                            LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $forecast_table.tax_id
                            LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $forecast_table.tax_id2
                            LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table3 ON tax_table3.id = $forecast_table.tax_id3
                            LEFT JOIN (SELECT forecast_id, SUM(amount) AS actuals_received FROM $forecast_actuals_table WHERE deleted=0 GROUP BY forecast_id) AS actuals_table ON actuals_table.forecast_id = $forecast_table.id 
                            LEFT JOIN (SELECT forecast_id, SUM(total) AS forecast_value FROM $forecast_items_table WHERE deleted=0 GROUP BY forecast_id) AS items_table ON items_table.forecast_id = $forecast_table.id 
                            WHERE $forecast_table.deleted=0 $forecast_where
                    ) ";
        }

        return $query;
    }

    function get_primary_contact($bu_id = 0, $info = false) {
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT $users_table.id, $users_table.first_name, $users_table.last_name
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.bu_id=$bu_id AND $users_table.is_primary_contact=1";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            if ($info) {
                return $result->getRow();
            } else {
                return $result->getRow()->id;
            }
        }
    }

    function add_remove_star($project_id, $user_id, $type = "add") {
        $bu_table = $this->db->prefixTable('bu');

        $action = " CONCAT($bu_table.starred_by,',',':$user_id:') ";
        $where = " AND FIND_IN_SET(':$user_id:',$bu_table.starred_by) = 0"; //don't add duplicate

        if ($type != "add") {
            $action = " REPLACE($bu_table.starred_by, ',:$user_id:', '') ";
            $where = "";
        }

        $sql = "UPDATE $bu_table SET $bu_table.starred_by = $action
        WHERE $bu_table.id=$project_id $where";
        return $this->db->query($sql);
    }

    function get_starred_bu($user_id) {
        $bu_table = $this->db->prefixTable('bu');

        $sql = "SELECT $bu_table.id,  $bu_table.bu_name
        FROM $bu_table
        WHERE $bu_table.deleted=0 AND FIND_IN_SET(':$user_id:',$bu_table.starred_by)
        ORDER BY $bu_table.bu_name ASC";
        return $this->db->query($sql);
    }

    function delete_bu_and_sub_items($bu_id) {
        $bu_table = $this->db->prefixTable('bu');
        $general_files_table = $this->db->prefixTable('general_files');
        $users_table = $this->db->prefixTable('users');


        //get bu files info to delete the files from directory 
        $bu_files_sql = "SELECT * FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.bu_id=$bu_id; ";
        $bu_files = $this->db->query($bu_files_sql)->getResult();

        //delete the bu and sub items
        //delete bu
        $delete_bu_sql = "UPDATE $bu_table SET $bu_table.deleted=1 WHERE $bu_table.id=$bu_id; ";
        $this->db->query($delete_bu_sql);

        //delete contacts
        $delete_contacts_sql = "UPDATE $users_table SET $users_table.deleted=1 WHERE $users_table.bu_id=$bu_id; ";
        $this->db->query($delete_contacts_sql);

        //delete the project files from directory
        $file_path = get_general_file_path("bu", $bu_id);
        foreach ($bu_files as $file) {
            delete_app_files($file_path, array(make_array_of_file($file)));
        }

        return true;
    }

    function is_duplicate_bu_name($bu_name, $id = 0) {

        $result = $this->get_all_where(array("bu_name" => $bu_name, "is_intake" => 0, "deleted" => 0));
        if (count($result->getResult()) && $result->getRow()->id != $id) {
            return $result->getRow();
        } else {
            return false;
        }
    }

    function get_intake_kanban_details($options = array()) {
        $bu_table = $this->db->prefixTable('bu');
        $intake_source_table = $this->db->prefixTable('intake_source');
        $users_table = $this->db->prefixTable('users');
        $events_table = $this->db->prefixTable('events');
        $notes_table = $this->db->prefixTable('notes');
        $general_files_table = $this->db->prefixTable('general_files');


        $where = "";

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $bu_table.intake_status_id='$status'";
        }

        $owner_id = get_array_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $bu_table.owner_id='$owner_id'";
        }

        $source = get_array_value($options, "source");
        if ($source) {
            $where .= " AND $bu_table.intake_source_id='$source'";
        }

        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeString($search);
            $where .= " AND $bu_table.bu_name LIKE '%$search%'";
        }

        $users_where = "$users_table.bu_id=$bu_table.id AND $users_table.deleted=0 AND $users_table.user_type='intake'";

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT $bu_table.id, $bu_table.bu_name, $bu_table.sort, IF($bu_table.sort!=0, $bu_table.sort, $bu_table.id) AS new_sort, $bu_table.intake_status_id, $bu_table.owner_id,
                (SELECT $users_table.image FROM $users_table WHERE $users_where AND $users_table.is_primary_contact=1) AS primary_contact_avatar,
                (SELECT COUNT($users_table.id) FROM $users_table WHERE $users_where) AS total_contacts_count,
                (SELECT COUNT($events_table.id) FROM $events_table WHERE $events_table.deleted=0 AND $events_table.bu_id=$bu_table.id) AS total_events_count,
                (SELECT COUNT($notes_table.id) FROM $notes_table WHERE $notes_table.deleted=0 AND $notes_table.bu_id=$bu_table.id) AS total_notes_count,
                (SELECT COUNT($general_files_table.id) FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.bu_id=$bu_table.id) AS total_files_count,
                $intake_source_table.title AS intake_source_title,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name
        FROM $bu_table 
        LEFT JOIN $intake_source_table ON $bu_table.intake_source_id = $intake_source_table.id 
        LEFT JOIN $users_table ON $users_table.id = $bu_table.owner_id AND $users_table.deleted=0 AND $users_table.user_type='team_member' 
        WHERE $bu_table.deleted=0 AND $bu_table.is_intake=1 $where 
        ORDER BY new_sort ASC";

        return $this->db->query($sql);
    }

    function get_search_suggestion($search = "", $options = array()) {
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        $show_own_bu_only_user_id = get_array_value($options, "show_own_bu_only_user_id");
        if ($show_own_bu_only_user_id) {
            $where .= " AND ($bu_table.created_by=$show_own_bu_only_user_id OR $bu_table.owner_id=$show_own_bu_only_user_id)";
        }

        $search = $this->db->escapeString($search);

        $sql = "SELECT $bu_table.id, $bu_table.bu_name AS title
        FROM $bu_table  
        WHERE $bu_table.deleted=0 AND $bu_table.is_intake=0 AND $bu_table.bu_name LIKE '%$search%' $where
        ORDER BY $bu_table.bu_name ASC
        LIMIT 0, 10";

        return $this->db->query($sql);
    }

    function count_total_bu($show_own_bu_only_user_id = "") {
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        if ($show_own_bu_only_user_id) {
            $where .= " AND $bu_table.created_by=$show_own_bu_only_user_id";
        }

        $sql = "SELECT COUNT($bu_table.id) AS total
        FROM $bu_table 
        WHERE $bu_table.deleted=0 AND $bu_table.is_intake=0 $where";
        return $this->db->query($sql)->getRow()->total;
    }

}
