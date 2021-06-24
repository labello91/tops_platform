<?php

namespace App\Models;

class BU_groups_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'bu_groups';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $bu_groups_table = $this->db->prefixTable('bu_groups');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $bu_groups_table.id=$id";
        }

        $sql = "SELECT $bu_groups_table.*
        FROM $bu_groups_table
        WHERE $bu_groups_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}