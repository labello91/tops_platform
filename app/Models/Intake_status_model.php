<?php

namespace App\Models;

class Intake_status_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'intake_status';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $intake_status_table = $this->db->prefixTable('intake_status');
        $bu_table = $this->db->prefixTable('bu');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $intake_status_table.id=$id";
        }

        $sql = "SELECT $intake_status_table.*, (SELECT COUNT($bu_table.id) FROM $bu_table WHERE $bu_table.deleted=0 AND $bu_table.is_intake=1 AND $bu_table.intake_status_id=$intake_status_table.id) AS total_intakes
        FROM $intake_status_table
        WHERE $intake_status_table.deleted=0 $where
        ORDER BY $intake_status_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $intake_status_table = $this->db->prefixTable('intake_status');

        $sql = "SELECT MAX($intake_status_table.sort) as sort
        FROM $intake_status_table
        WHERE $intake_status_table.deleted=0";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            return $result->getRow()->sort;
        } else {
            return 0;
        }
    }

    function get_first_status() {
        $intake_status_table = $this->db->prefixTable('intake_status');

        $sql = "SELECT $intake_status_table.id AS first_intake_status
        FROM $intake_status_table
        WHERE $intake_status_table.deleted=0
        ORDER BY $intake_status_table.sort ASC
        LIMIT 1";

        return $this->db->query($sql)->getRow()->first_intake_status;
    }

}
