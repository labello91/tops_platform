<?php

namespace App\Models;

class Forecast_actuals_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forecast_actuals';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_table = $this->db->prefixTable('forecast');
        $actuals_methods_table = $this->db->prefixTable('actuals_methods');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $forecast_actuals_table.id=$id";
        }

        $forecast_id = get_array_value($options, "forecast_id");
        if ($forecast_id) {
            $where .= " AND $forecast_actuals_table.forecast_id=$forecast_id";
        }

        $bu_id = get_array_value($options, "bu_id");
        if ($bu_id) {
            $where .= " AND $forecast_table.bu_id=$bu_id";
        }

        $project_id = get_array_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $forecast_table.project_id=$project_id";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if ($start_date && $end_date) {
            $where .= " AND ($forecast_actuals_table.actuals_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $currency = get_array_value($options, "currency");
        if ($currency) {
            $where .= $this->_get_bu_of_currency_query($currency, $forecast_table, $bu_table);
        }

        $sql = "SELECT $forecast_actuals_table.*, $forecast_table.bu_id, (SELECT $bu_table.currency_symbol FROM $bu_table WHERE $bu_table.id=$forecast_table.bu_id limit 1) AS currency_symbol
        FROM $forecast_actuals_table
        LEFT JOIN $forecast_table ON $forecast_table.id=$forecast_actuals_table.forecast_id
        WHERE $forecast_actuals_table.deleted=0 AND $forecast_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_yearly_actuals_chart($year, $currency = "", $project_id = 0) {
        $actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_table = $this->db->prefixTable('forecast');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        if ($currency) {
            $where = $this->_get_bu_of_currency_query($currency, $forecast_table, $bu_table);
        }

        if ($project_id) {
            $where .= " AND $actuals_table.forecast_id IN(SELECT $forecast_table.id FROM $forecast_table WHERE $forecast_table.deleted=0 AND $forecast_table.project_id=$project_id)";
        }

        $actuals = "SELECT SUM($actuals_table.amount) AS total, MONTH($actuals_table.actuals_date) AS month
            FROM $actuals_table
            LEFT JOIN $forecast_table ON $forecast_table.id=$actuals_table.forecast_id
            WHERE $actuals_table.deleted=0 AND YEAR($actuals_table.actuals_date)= $year AND $forecast_table.deleted=0 $where
            GROUP BY MONTH($actuals_table.actuals_date)";
        return $this->db->query($actuals)->getResult();
    }

    function get_used_projects($type) {
        $actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_table = $this->db->prefixTable('forecast');
        $projects_table = $this->db->prefixTable('projects');
        $actuals_table = $this->db->prefixTable('actuals');

        $actuals_where = "SELECT $forecast_table.project_id FROM $forecast_table WHERE $forecast_table.deleted=0 AND $forecast_table.project_id!=0 AND $forecast_table.id IN(SELECT $actuals_table.forecast_id FROM $actuals_table WHERE $actuals_table.deleted=0 GROUP BY $actuals_table.forecast_id) GROUP BY $forecast_table.project_id";
        $actuals_where = "SELECT $actuals_table.project_id FROM $actuals_table WHERE $actuals_table.deleted=0 AND $actuals_table.project_id!=0 GROUP BY $actuals_table.project_id";

        $where = "";
        if ($type == "all") {
            $where = " AND $projects_table.id IN($actuals_where) OR $projects_table.id IN($actuals_where)";
        } else if ($type == "actuals") {
            $where = " AND $projects_table.id IN($actuals_where)";
        } else if ($type == "actuals") {
            $where = " AND $projects_table.id IN($actuals_where)";
        }

        $sql = "SELECT $projects_table.id, $projects_table.title 
            FROM $projects_table 
            WHERE $projects_table.deleted=0 $where
            GROUP BY $projects_table.id";

        return $this->db->query($sql);
    }

}
