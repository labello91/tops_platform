<?php

namespace App\Models;

class Forecast_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forecast';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $forecast_table = $this->db->prefixTable('forecast');
        $bu_table = $this->db->prefixTable('bu');
        $projects_table = $this->db->prefixTable('projects');
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_items_table = $this->db->prefixTable('forecast_items');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $forecast_table.id=$id";
        }
        $bu_id = get_array_value($options, "bu_id");
        if ($bu_id) {
            $where .= " AND $forecast_table.bu_id=$bu_id";
        }

        $exclude_draft = get_array_value($options, "exclude_draft");
        if ($exclude_draft) {
            $where .= " AND $forecast_table.status!='draft' ";
        }

        $project_id = get_array_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $forecast_table.project_id=$project_id";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if ($start_date && $end_date) {
            $where .= " AND ($forecast_table.due_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $next_recurring_start_date = get_array_value($options, "next_recurring_start_date");
        $next_recurring_end_date = get_array_value($options, "next_recurring_end_date");
        if ($next_recurring_start_date && $next_recurring_start_date) {
            $where .= " AND ($forecast_table.next_recurring_date BETWEEN '$next_recurring_start_date' AND '$next_recurring_end_date') ";
        } else if ($next_recurring_start_date) {
            $where .= " AND $forecast_table.next_recurring_date >= '$next_recurring_start_date' ";
        } else if ($next_recurring_end_date) {
            $where .= " AND $forecast_table.next_recurring_date <= '$next_recurring_end_date' ";
        }

        $recurring_forecast_id = get_array_value($options, "recurring_forecast_id");
        if ($recurring_forecast_id) {
            $where .= " AND $forecast_table.recurring_forecast_id=$recurring_forecast_id";
        }

        $now = get_my_local_time("Y-m-d");
        //  $options['status'] = "draft";
        $status = get_array_value($options, "status");


        $forecast_value_calculation_query = $this->_get_forecast_value_calculation_query($forecast_table);


        $forecast_value_calculation = "TRUNCATE($forecast_value_calculation_query,2)";

        if ($status === "draft") {
            $where .= " AND $forecast_table.status='draft' AND IFNULL(actuals_table.actuals_received,0)<=0";
        } else if ($status === "not_achieved") {
            $where .= " AND $forecast_table.status !='draft' AND $forecast_table.status!='cancelled' AND IFNULL(actuals_table.actuals_received,0)<=0";
        } else if ($status === "partially_achieved") {
            $where .= " AND IFNULL(actuals_table.actuals_received,0)>0 AND IFNULL(actuals_table.actuals_received,0)<$forecast_value_calculation";
        } else if ($status === "fully_achieved") {
            $where .= " AND TRUNCATE(IFNULL(actuals_table.actuals_received,0),2)>=$forecast_value_calculation";
        } else if ($status === "overdue") {
            $where .= " AND $forecast_table.status !='draft' AND $forecast_table.status!='cancelled' AND $forecast_table.due_date<'$now' AND TRUNCATE(IFNULL(actuals_table.actuals_received,0),2)<$forecast_value_calculation";
        } else if ($status === "cancelled") {
            $where .= " AND $forecast_table.status='cancelled' ";
        }


        $recurring = get_array_value($options, "recurring");
        if ($recurring) {
            $where .= " AND $forecast_table.recurring=1";
        }

        $currency = get_array_value($options, "currency");
        if ($currency) {
            $where .= $this->_get_bu_of_currency_query($currency, $forecast_table, $bu_table);
        }

        $exclude_due_reminder_date = get_array_value($options, "exclude_due_reminder_date");
        if ($exclude_due_reminder_date) {
            $where .= " AND ($forecast_table.due_reminder_date !='$exclude_due_reminder_date') ";
        }

        $exclude_recurring_reminder_date = get_array_value($options, "exclude_recurring_reminder_date");
        if ($exclude_recurring_reminder_date) {
            $where .= " AND ($forecast_table.recurring_reminder_date !='$exclude_recurring_reminder_date') ";
        }

        $select_labels_data_query = $this->get_labels_data_query();

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_query_info = $this->prepare_custom_field_query_string("forecast", $custom_fields, $forecast_table);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");




        $sql = "SELECT $forecast_table.*, $bu_table.currency, $bu_table.currency_symbol, $bu_table.bu_name, $projects_table.title AS project_title,
           $forecast_value_calculation_query AS forecast_value, IFNULL(actuals_table.actuals_received,0) AS actuals_received, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS cancelled_by_user, $select_labels_data_query $select_custom_fieds
        FROM $forecast_table
        LEFT JOIN $bu_table ON $bu_table.id= $forecast_table.bu_id
        LEFT JOIN $projects_table ON $projects_table.id= $forecast_table.project_id
        LEFT JOIN $users_table ON $users_table.id= $forecast_table.cancelled_by
        LEFT JOIN (SELECT forecast_id, SUM(amount) AS actuals_received FROM $forecast_actuals_table WHERE deleted=0 GROUP BY forecast_id) AS actuals_table ON actuals_table.forecast_id = $forecast_table.id 
        LEFT JOIN (SELECT forecast_id, SUM(total) AS forecast_value FROM $forecast_items_table WHERE deleted=0 GROUP BY forecast_id) AS items_table ON items_table.forecast_id = $forecast_table.id 
        $join_custom_fieds
        WHERE $forecast_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_forecast_total_summary($forecast_id = 0) {
        $forecast_items_table = $this->db->prefixTable('forecast_items');
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_table = $this->db->prefixTable('forecast');
        $bu_table = $this->db->prefixTable('bu');

        $item_sql = "SELECT SUM($forecast_items_table.total) AS forecast_subtotal
        FROM $forecast_items_table
        LEFT JOIN $forecast_table ON $forecast_table.id= $forecast_items_table.forecast_id    
        WHERE $forecast_items_table.deleted=0 AND $forecast_items_table.forecast_id=$forecast_id AND $forecast_table.deleted=0";
        $item = $this->db->query($item_sql)->getRow();

        $actuals_sql = "SELECT SUM($forecast_actuals_table.amount) AS total_achieved
        FROM $forecast_actuals_table
        WHERE $forecast_actuals_table.deleted=0 AND $forecast_actuals_table.forecast_id=$forecast_id";
        $actuals = $this->db->query($actuals_sql)->getRow();

        $bu_sql = "SELECT $bu_table.currency_symbol, $bu_table.currency FROM $bu_table WHERE $bu_table.id=$forecast->bu_id";
        $bu = $this->db->query($bu_sql)->getRow();


        $result = new \stdClass();
        $result->forecast_subtotal = $item->forecast_subtotal;

        $result->total_achieved = $actuals->total_achieved;

        $result->currency_symbol = $bu->currency_symbol ? $bu->currency_symbol : get_setting("currency_symbol");
        $result->currency = $bu->currency ? $bu->currency : get_setting("default_currency");

        
    }

    function forecast_statistics($options = array()) {
        $forecast_table = $this->db->prefixTable('forecast');
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_items_table = $this->db->prefixTable('forecast_items');
        $bu_table = $this->db->prefixTable('bu');

        $info = new \stdClass();
        $year = get_my_local_time("Y");

        $where = "";
        $actuals_where = "";
        $forecast_where = "";

        $bu_id = get_array_value($options, "bu_id");
        if ($bu_id) {
            $where .= " AND $forecast_table.bu_id=$bu_id";
        } else {
            $forecast_where = $this->_get_bu_of_currency_query(get_array_value($options, "currency_symbol"), $forecast_table, $bu_table);

            $actuals_where = " AND $forecast_actuals_table.forecast_id IN(SELECT $forecast_table.id FROM $forecast_table WHERE $forecast_table.deleted=0 $forecast_where)";
        }

        $actuals = "SELECT SUM($forecast_actuals_table.amount) AS total, MONTH($forecast_actuals_table.actuals_date) AS month
            FROM $forecast_actuals_table
            LEFT JOIN $forecast_table ON $forecast_table.id=$forecast_actuals_table.forecast_id    
            WHERE $forecast_actuals_table.deleted=0 AND YEAR($forecast_actuals_table.actuals_date)=$year AND $forecast_table.deleted=0 $where $actuals_where
            GROUP BY MONTH($forecast_actuals_table.actuals_date)";

        $forecast_value_calculation_query = $this->_get_forecast_value_calculation_query($forecast_table);

        $forecast = "SELECT SUM(total) AS total, MONTH(due_date) AS month FROM (SELECT $forecast_value_calculation_query AS total ,$forecast_table.due_date
            FROM $forecast_table    
            LEFT JOIN (SELECT forecast_id, SUM(total) AS forecast_value FROM $forecast_items_table WHERE deleted=0 GROUP BY forecast_id) AS items_table ON items_table.forecast_id = $forecast_table.id 
            WHERE $forecast_table.deleted=0 AND $forecast_table.status='not_achieved' $where AND YEAR($forecast_table.due_date)=$year $forecast_where) as details_table
            GROUP BY  MONTH(due_date)";

        $info->actuals = $this->db->query($actuals)->getResult();
        $info->forecast = $this->db->query($forecast)->getResult();
        $info->currencies = $this->get_used_currencies_of_bu()->getResult();

        return $info;
    }

    function get_used_currencies_of_bu() {
        $bu_table = $this->db->prefixTable('bu');
        $default_currency = get_setting("default_currency");

        $sql = "SELECT $bu_table.currency
            FROM $bu_table
            WHERE $bu_table.deleted=0 AND $bu_table.currency!='' AND $bu_table.currency!='$default_currency'
            GROUP BY $bu_table.currency";

        return $this->db->query($sql);
    }

    function get_forecast_total_and_actuals() {
        $forecast_table = $this->db->prefixTable('forecast');
        $forecast_actuals_table = $this->db->prefixTable('forecast_actuals');
        $forecast_items_table = $this->db->prefixTable('forecast_items');
        $taxes_table = $this->db->prefixTable('taxes');
        $info = new \stdClass();


        $actuals = "SELECT SUM($forecast_actuals_table.amount) AS total
            FROM $forecast_actuals_table
            LEFT JOIN $forecast_table ON $forecast_table.id=$forecast_actuals_table.forecast_id    
            WHERE $forecast_actuals_table.deleted=0 AND $forecast_table.deleted=0";
        $info->actuals = $this->db->query($actuals)->getResult();

        $forecast_value_calculation_query = $this->_get_forecast_value_calculation_query($forecast_table);

        $forecast = "SELECT SUM(total) AS total FROM (SELECT $forecast_value_calculation_query AS total
            FROM $forecast_table
            LEFT JOIN (SELECT forecast_id, SUM(total) AS forecast_value FROM $forecast_items_table WHERE deleted=0 GROUP BY forecast_id) AS items_table ON items_table.forecast_id = $forecast_table.id 
            WHERE $forecast_table.deleted=0 AND $forecast_table.status='not_achieved') as details_table";

        $draft = "SELECT SUM(total) AS total FROM (SELECT $forecast_value_calculation_query AS total
            FROM $forecast_table
            LEFT JOIN (SELECT forecast_id, SUM(total) AS forecast_value FROM $forecast_items_table WHERE deleted=0 GROUP BY forecast_id) AS items_table ON items_table.forecast_id = $forecast_table.id 
            WHERE $forecast_table.deleted=0 AND $forecast_table.status='draft') as details_table";

        $actuals_total = $this->db->query($actuals)->getRow()->total;
        $forecast_total = $this->db->query($forecast)->getRow()->total;
        $draft_total = $this->db->query($draft)->getRow()->total;

        $info->actuals_total = $actuals_total;
        $info->forecast_total = (($forecast_total > $actuals_total) && ($forecast_total - $actuals_total) < 0.05 ) ? $actuals_total : $forecast_total;
        $info->due = $info->forecast_total - $info->actuals_total;
        $info->draft_total = $draft_total;
        return $info;
    }

    //update forecast status
    function update_forecast_status($forecast_id = 0, $status = "not_achieved") {
        $status_data = array("status" => $status);
        return $this->ci_save($status_data, $forecast_id);
    }

    //get the recurring forecast which are ready to renew as on a given date
    function get_renewable_forecast($date) {
        $forecast_table = $this->db->prefixTable('forecast');

        $sql = "SELECT * FROM $forecast_table
                        WHERE $forecast_table.deleted=0 AND $forecast_table.recurring=1
                        AND $forecast_table.next_recurring_date IS NOT NULL AND $forecast_table.next_recurring_date<='$date'
                        AND ($forecast_table.no_of_cycles < 1 OR ($forecast_table.no_of_cycles_completed < $forecast_table.no_of_cycles ))";

        return $this->db->query($sql);
    }

    //get forecast dropdown list
    function get_forecast_dropdown_list() {
        $forecast_table = $this->db->prefixTable('forecast');

        $sql = "SELECT $forecast_table.id FROM $forecast_table
                        WHERE $forecast_table.deleted=0 
                        ORDER BY $forecast_table.id DESC";

        return $this->db->query($sql);
    }

    //get label suggestions
    function get_label_suggestions() {
        $forecast_table = $this->db->prefixTable('forecast');
        $sql = "SELECT GROUP_CONCAT(labels) as label_groups
        FROM $forecast_table
        WHERE $forecast_table.deleted=0";
        return $this->db->query($sql)->getRow()->label_groups;
    }

    //get forecast last id
    function get_last_forecast_id() {
        $forecast_table = $this->db->prefixTable('forecast');

        $sql = "SELECT MAX($forecast_table.id) AS last_id FROM $forecast_table";

        return $this->db->query($sql)->getRow()->last_id;
    }

    //save initial number of forecast
    function save_initial_number_of_forecast($value) {
        $forecast_table = $this->db->prefixTable('forecast');

        $sql = "ALTER TABLE $forecast_table AUTO_INCREMENT=$value;";

        return $this->db->query($sql);
    }

    //get draft forecast
    function count_draft_forecast() {
        $forecast_table = $this->db->prefixTable('forecast');
        $sql = "SELECT COUNT($forecast_table.id) AS total
        FROM $forecast_table 
        WHERE $forecast_table.deleted=0 AND $forecast_table.status='draft'";
        return $this->db->query($sql)->getRow()->total;
    }

}
