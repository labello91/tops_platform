<?php

/**
 * convert a number to currency format
 * 
 * @param number $number
 * @param string $currency
 * @return number with currency symbol
 */
if (!function_exists('to_currency')) {

    function to_currency($number = 0, $currency = "", $no_of_decimals = 2) {
        $decimal_separator = get_setting("decimal_separator");
        $thousand_separator = get_setting("thousand_separator");

        
        if(get_setting("no_of_decimals")=="0"){
            $no_of_decimals = 0;
        }
           
        $negative_sign = "";
        if ($number < 0) {
            $number = $number * -1;
            $negative_sign = "-";
        }
        if (!$currency) {
            $currency = get_setting("currency_symbol");
        }

        $currency_position = get_setting("currency_position");
        if (!$currency_position) {
            $currency_position = "left";
        }

        if ($decimal_separator === ",") {
            if ($thousand_separator !== " ") {
                $thousand_separator = ".";
            }

            if ($currency_position === "right") {
                return $negative_sign . number_format($number, $no_of_decimals, ",", $thousand_separator) . $currency;
            } else {
                return $negative_sign . $currency . number_format($number, $no_of_decimals, ",", $thousand_separator);
            }
        } else {
            if ($thousand_separator !== " ") {
                $thousand_separator = ",";
            }

            if ($currency_position === "right") {
                return $negative_sign . number_format($number, $no_of_decimals, ".", $thousand_separator) . $currency;
            } else {
                return $negative_sign . $currency . number_format($number, $no_of_decimals, ".", $thousand_separator);
            }
        }
    }

}

/**
 * convert a number to quantity format
 * 
 * @param number $number
 * @return number
 */
if (!function_exists('to_decimal_format')) {

    function to_decimal_format($number = 0) {
        $decimal_separator = get_setting("decimal_separator");

        $decimal = 0;
        if (is_numeric($number) && floor($number) != $number) {
            $decimal = get_setting("no_of_decimals")=="0" ? 0 : 2;
        }
        if ($decimal_separator === ",") {
            return number_format($number, $decimal, ",", ".");
        } else {
            return number_format($number, $decimal, ".", ",");
        }
    }

}

/**
 * convert a currency value to data format
 *  
 * @param number $currency
 * @return number
 */
if (!function_exists('unformat_currency')) {

    function unformat_currency($currency = "") {
// remove everything except a digit "0-9", a comma ",", and a dot "."
        $new_money = preg_replace('/[^\d,-\.]/', '', $currency);
        $decimal_separator = get_setting("decimal_separator");
        if ($decimal_separator === ",") {
            $new_money = str_replace(".", "", $new_money);
            $new_money = str_replace(",", ".", $new_money);
        } else {
            $new_money = str_replace(",", "", $new_money);
        }
        return $new_money;
    }

}

/**
 * get array of international currency codes
 * 
 * @return array
 */
if (!function_exists('get_international_currency_code_list')) {

    function get_international_currency_code_list() {
        return array(
            "AUD",
            "COP",
            "USD",
            "EUR"
        );
    }

};


/**
 * get dropdown list fro international currency code
 * 
 * @return array
 */
if (!function_exists('get_international_currency_code_dropdown')) {

    function get_international_currency_code_dropdown() {
        $result = array();
        foreach (get_international_currency_code_list() as $value) {
            $result[$value] = $value;
        }
        return $result;
    }

};


/**
 * get ignor minor amount 
 * 
 * @return int
 */
if (!function_exists('ignor_minor_value')) {

    function ignor_minor_value($value) {
        if(abs($value)<0.05){
            $value = 0;
        }
        return $value;
    }

};
