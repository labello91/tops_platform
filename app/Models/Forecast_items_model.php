<?php

namespace App\Models;

class Forecast_items_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forecast_items';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $forecast_items_table = $this->db->prefixTable('forecast_items');
        $forecast_table = $this->db->prefixTable('forecast');
        $bu_table = $this->db->prefixTable('bu');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $forecast_items_table.id=$id";
        }
        $forecast_id = get_array_value($options, "forecast_id");
        if ($forecast_id) {
            $where .= " AND $forecast_items_table.forecast_id=$forecast_id";
        }

        $sql = "SELECT $forecast_items_table.*, (SELECT $bu_table.currency_symbol FROM $bu_table WHERE $bu_table.id=$forecast_table.bu_id limit 1) AS currency_symbol
        FROM $forecast_items_table
        LEFT JOIN $forecast_table ON $forecast_table.id=$forecast_items_table.forecast_id
        WHERE $forecast_items_table.deleted=0 $where
        ORDER BY $forecast_items_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_item_suggestion($keyword = "", $user_type = "") {
        $items_table = $this->db->prefixTable('items');

        $keyword = $this->db->escapeString($keyword);

        $where = "";
        if ($user_type && $user_type === "bu") {
            $where = " AND $items_table.show_in_bu_portal=1";
        }

        $sql = "SELECT $items_table.title
        FROM $items_table
        WHERE $items_table.deleted=0  AND $items_table.title LIKE '%$keyword%' $where
        LIMIT 10 
        ";
        return $this->db->query($sql)->getResult();
    }

    function get_item_info_suggestion($item_name = "", $user_type = "") {

        $items_table = $this->db->prefixTable('items');

        $item_name = $this->db->escapeString($item_name);

        $where = "";
        if ($user_type && $user_type === "bu") {
            $where = " AND $items_table.show_in_bu_portal=1";
        }

        $sql = "SELECT $items_table.*
        FROM $items_table
        WHERE $items_table.deleted=0  AND $items_table.title LIKE '%$item_name%' $where
        ORDER BY id DESC LIMIT 1
        ";

        $result = $this->db->query($sql);

        if ($result->resultID->num_rows) {
            return $result->getRow();
        }
    }

}