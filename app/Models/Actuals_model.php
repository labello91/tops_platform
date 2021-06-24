<?php

namespace App\Models;

class Actuals_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'actuals';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $actuals_table = $this->db->prefixTable('actuals');
        $actuals_categories_table = $this->db->prefixTable('actuals_categories');
        $projects_table = $this->db->prefixTable('projects');
        $users_table = $this->db->prefixTable('users');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $actuals_table.id=$id";
        }
        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if ($start_date && $end_date) {
            $where .= " AND ($actuals_table.actuals_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $category_id = get_array_value($options, "category_id");
        if ($category_id) {
            $where .= " AND $actuals_table.category_id=$category_id";
        }

        $project_id = get_array_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $actuals_table.project_id=$project_id";
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $actuals_table.user_id=$user_id";
        }

        $bu_id = get_array_value($options, "bu_id");
        if ($bu_id) {
            $where .= " AND $actuals_table.bu_id=$bu_id";
        }

        $recurring = get_array_value($options, "recurring");
        if ($recurring) {
            $where .= " AND $actuals_table.recurring=1";
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_query_info = $this->prepare_custom_field_query_string("actuals", $custom_fields, $actuals_table);
        $select_custom_fields = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fields = get_array_value($custom_field_query_info, "join_string");


        $sql = "SELECT $actuals_table.*, $actuals_categories_table.title as category_title, 
                 CONCAT($users_table.first_name, ' ', $users_table.last_name) AS linked_user_name,
                 $bu_table.bu_name AS linked_bu_name,
                 $projects_table.title AS project_title,
                 $select_custom_fields
        FROM $actuals_table
        LEFT JOIN $actuals_categories_table ON $actuals_categories_table.id= $actuals_table.category_id
        LEFT JOIN $bu_table ON $bu_table.id= $actuals_table.bu_id
        LEFT JOIN $projects_table ON $projects_table.id= $actuals_table.project_id
        LEFT JOIN $users_table ON $users_table.id= $actuals_table.user_id
            $join_custom_fields
        WHERE $actuals_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_forecast_actuals_info() {
        $actuals_table = $this->db->prefixTable('actuals');
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $info = new \stdClass();

        $sql1 = "SELECT SUM($forecast_actuals_table.amount) as total_forecast
        FROM $forecast_actuals_table
        WHERE $forecast_actuals_table.deleted=0";
        $forecast = $this->db->query($sql1)->getRow();

        $sql2 = "SELECT SUM($actuals_table.amount/100*IFNULL($actuals_table.amount,0)/100*IFNULL($actuals_table.amount,0)) AS total_actuals
        FROM $actuals_table
        WHERE $actuals_table.deleted=0";
        $actuals = $this->db->query($sql2)->getRow();

        $info->forecast = $forecast->total_forecast;
        $info->actuals = $actuals->total_actuals;
        return $info;
    }

    function get_yearly_actuals_chart($year, $project_id = 0) {
        $actuals_table = $this->db->prefixTable('actuals');

        $where = "";
        if ($project_id) {
            $where = " AND $actuals_table.project_id=$project_id";
        }

        $actuals = "SELECT SUM($actuals_table.amount/100*IFNULL($actuals_table.amount,0)/100*IFNULL($actuals_table.amount,0)) AS total, MONTH($actuals_table.actuals_date) AS month
        FROM $actuals_table
        WHERE $actuals_table.deleted=0 AND YEAR($actuals_table.actuals_date)= $year $where
        GROUP BY MONTH($actuals_table.actuals_date)";

        return $this->db->query($actuals)->getResult();
    }

    //get the recurring actuals which are ready to renew as on a given date
    function get_renewable_actuals($date) {
        $actuals_table = $this->db->prefixTable('actuals');

        $sql = "SELECT * FROM $actuals_table
                        WHERE $actuals_table.deleted=0 AND $actuals_table.recurring=1
                        AND $actuals_table.next_recurring_date IS NOT NULL AND $actuals_table.next_recurring_date<='$date'
                        AND ($actuals_table.no_of_cycles < 1 OR ($actuals_table.no_of_cycles_completed < $actuals_table.no_of_cycles ))";

        return $this->db->query($sql);
    }

}