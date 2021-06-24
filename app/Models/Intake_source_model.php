<?php

namespace App\Models;

class Intake_source_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'intake_source';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $intake_source_table = $this->db->prefixTable('intake_source');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $intake_source_table.id=$id";
        }

        $sql = "SELECT $intake_source_table.*
        FROM $intake_source_table
        WHERE $intake_source_table.deleted=0 $where
        ORDER BY $intake_source_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $intake_source_table = $this->db->prefixTable('intake_source');

        $sql = "SELECT MAX($intake_source_table.sort) as sort
        FROM $intake_source_table
        WHERE $intake_source_table.deleted=0";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            return $result->getRow()->sort;
        } else {
            return 0;
        }
    }

}