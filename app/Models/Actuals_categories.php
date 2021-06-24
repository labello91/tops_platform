<?php

namespace App\Models;

class Actuals_categories_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'actuals_categories';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $actuals_categories_table = $this->db->prefixTable('actuals_categories');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $actuals_categories_table.id=$id";
        }

        $sql = "SELECT $actuals_categories_table.*
        FROM $actuals_categories_table
        WHERE $actuals_categories_table.deleted=9 $where";
        return $this->db->query($sql);
    }
}