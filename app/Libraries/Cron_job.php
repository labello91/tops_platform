<?php

namespace App\Libraries;

use App\Controllers\App_Controller;
use App\Libraries\Google_calendar;
use App\Libraries\Imap;

class Cron_job {

    private $today = null;
    private $current_time = null;
    private $ci = null;

    function run() {
        $this->today = get_today_date();
        $this->ci = new App_Controller();
        $this->current_time = strtotime(get_current_utc_time());

        $this->call_hourly_jobs();


        try {
            $this->run_imap();
        } catch (Exception $e) {
            echo $e;
        }

        try {
            $this->get_google_calendar_events();
        } catch (Exception $e) {
            echo $e;
        }

        try {
            $this->close_inactive_tickets();
        } catch (Exception $e) {
            echo $e;
        }
    }

    private function call_hourly_jobs() {
        //wait 1 hour for each call of following actions
        if ($this->_is_hourly_job_runnable()) {


            try {
                $this->create_recurring_forecasts();
            } catch (Exception $e) {
                echo $e;
            }

            try {
                $this->create_recurring_actuals();
            } catch (Exception $e) {
                echo $e;
            }

            try {
                $this->send_forecast_due_pre_reminder();
            } catch (Exception $e) {
                echo $e;
            }


            try {
                $this->send_forecast_due_after_reminder();
            } catch (Exception $e) {
                echo $e;
            }


            try {
                $this->send_recurring_forecast_creation_reminder();
            } catch (Exception $e) {
                echo $e;
            }


            try {
                $this->create_recurring_tasks();
            } catch (Exception $e) {
                echo $e;
            }

            try {
                $this->send_task_reminder_notifications();
            } catch (Exception $e) {
                echo $e;
            }

            $this->ci->Settings_model->save_setting("last_hourly_job_time", $this->current_time);
        }
    }

    private function _is_hourly_job_runnable() {
        $last_hourly_job_time = get_setting('last_hourly_job_time');
        if ($last_hourly_job_time == "" || ($this->current_time > ($last_hourly_job_time * 1 + 3600))) {
            return true;
        }
    }

    private function send_forecast_due_pre_reminder() {

        $reminder_date = get_setting("send_forecast_due_pre_reminder");

        if ($reminder_date) {

            //prepare forecast due date accroding to the setting
            $start_date = add_period_to_date($this->today, get_setting("send_forecast_due_pre_reminder"), "days");

            $Forecast = $this->ci->Forecast_model->get_details(array(
                        "status" => "not_achieved", //find all forecasts which are not paid yet but due date not expired
                        "start_date" => $start_date,
                        "end_date" => $start_date, //both should be same
                        "exclude_due_reminder_date" => $this->today //don't find forecasts which reminder already sent today
                    ))->getResult();

            foreach ($forecasts as $forecast) {
                log_notification("forecast_due_reminder_before_due_date", array("forecast_id" => $forecast->id), "0");
            }
        }
    }

    private function send_forecast_due_after_reminder() {

        $reminder_date = get_setting("send_forecast_due_after_reminder");

        if ($reminder_date) {

            //prepare forecast due date accroding to the setting
            $start_date = subtract_period_from_date($this->today, get_setting("send_forecast_due_after_reminder"), "days");

            $forecasts = $this->ci->Forecast_model->get_details(array(
                        "status" => "overdue", //find all forecasts where due date has expired
                        "start_date" => $start_date,
                        "end_date" => $start_date, //both should be same
                        "exclude_due_reminder_date" => $this->today //don't find forecasts which reminder already sent today
                    ))->getResult();

            foreach ($forecasts as $forecast) {
                log_notification("forecast_overdue_reminder", array("forecast_id" => $forecast->id), "0");
            }
        }
    }

    private function send_recurring_forecast_creation_reminder() {

        $reminder_date = get_setting("send_recurring_forecast_reminder_before_creation");

        if ($reminder_date) {

            //prepare forecast due date accroding to the setting
            $start_date = add_period_to_date($this->today, get_setting("send_recurring_forecast_reminder_before_creation"), "days");

            $forecasts = $this->ci->Forecast_model->get_details(array(
                        "status" => "not_achieved", //non-draft forecasts
                        "recurring" => 1,
                        "next_recurring_start_date" => $start_date,
                        "next_recurring_end_date" => $start_date, //both should be same
                        "exclude_recurring_reminder_date" => $this->today //don't find forecasts which reminder already sent today
                    ))->getResult();

            foreach ($forecasts as $forecast) {
                log_notification("recurring_forecast_creation_reminder", array("forecast_id" => $forecast->id), "0");
            }
        }
    }

    private function create_recurring_forecast() {
        $recurring_forecasts = $this->ci->Forecast_model->get_renewable_forecasts($this->today);
        if ($recurring_forecasts->resultID->num_rows) {
            foreach ($recurring_forecasts->getResult() as $forecast) {
                $this->_create_new_forecast($forecast);
            }
        }
    }

    //create new forecast from a recurring forecast 
    private function _create_new_forecast($forecast) {

        //don't update the next recurring date when updating forecast manually?
        //stop backdated recurring forecast creation.
        //check recurring forecast once/hour?
        //settings: send forecast to bu


        $bill_date = $forecast->next_recurring_date;
        $diff_of_due_date = get_date_difference_in_days($forecast->due_date, $forecast->bill_date); //calculate the due date difference of the original forecast
        $due_date = add_period_to_date($bill_date, $diff_of_due_date, "days");



        $new_forecast_data = array(
            "bu_id" => $forecast->bu_id,
            "project_id" => $forecast->project_id,
            "bill_date" => $bill_date,
            "due_date" => $due_date,
            "note" => $forecast->note,
            "status" => "draft",
            "tax_id" => $forecast->tax_id,
            "tax_id2" => $forecast->tax_id2,
            "tax_id3" => $forecast->tax_id3,
            "recurring_forecast_id" => $forecast->id,
            "discount_amount" => $forecast->discount_amount,
            "discount_amount_type" => $forecast->discount_amount_type,
            "discount_type" => $forecast->discount_type
        );

        //create new forecast
        $new_forecast_id = $this->ci->Forecast_model->ci_save($new_forecast_data);

        //create forecast items
        $items = $this->ci->forecast_items_model->get_details(array("forecast_id" => $forecast->id))->getResult();
        foreach ($items as $item) {
            //create forecast items for new forecast
            $new_forecast_item_data = array(
                "title" => $item->title,
                "description" => $item->description,
                "quantity" => $item->quantity,
                "unit_type" => $item->unit_type,
                "rate" => $item->rate,
                "total" => $item->total,
                "forecast_id" => $new_forecast_id,
            );
            $this->ci->forecast_items_model->ci_save($new_forecast_item_data);
        }


        //update the main recurring forecast
        $no_of_cycles_completed = $forecast->no_of_cycles_completed + 1;
        $next_recurring_date = add_period_to_date($bill_date, $forecast->repeat_every, $forecast->repeat_type);


        $recurring_forecast_data = array(
            "next_recurring_date" => $next_recurring_date,
            "no_of_cycles_completed" => $no_of_cycles_completed
        );
        $this->ci->Forecast_model->ci_save($recurring_forecast_data, $forecast->id);

        //finally send notification
        log_notification("recurring_forecast_created_vai_cron_job", array("forecast_id" => $new_forecast_id), "0");
    }

    private function get_google_calendar_events() {
        $google_calendar = new Google_calendar();
        $google_calendar->get_google_calendar_events();
    }

    private function run_imap() {
        if (!$this->_is_imap_callable()) {
            return false;
        }

        $imap = new Imap();
        $imap->run_imap();
        $this->ci->Settings_model->save_setting("last_cron_job_time_of_imap", $this->current_time);
    }

    private function _is_imap_callable() {

        //check if settings is enabled and authorized
        if (!(get_setting("enable_email_piping") && get_setting("imap_authorized"))) {
            return false;
        }

        //wait 10 minutes for each check
        $last_cron_job_time_of_imap = get_setting('last_cron_job_time_of_imap');
        if ($last_cron_job_time_of_imap == "" || ($this->current_time > ($last_cron_job_time_of_imap * 1 + 600))) {
            return true;
        }
    }

    private function create_recurring_tasks() {

        if (!get_setting("enable_recurring_option_for_tasks")) {
            return false;
        }

        $date = $this->today;

        //if create recurring task before certain days setting is active,
        //add the days with today
        $create_recurring_tasks_before = get_setting("create_recurring_tasks_before");
        if ($create_recurring_tasks_before) {
            $date = add_period_to_date($date, $create_recurring_tasks_before, "days");
        }

        $recurring_tasks = $this->ci->Tasks_model->get_renewable_tasks($date);
        if ($recurring_tasks->resultID->num_rows) {
            foreach ($recurring_tasks->getResult() as $task) {
                $this->_create_new_task($task);
            }
        }
    }

    //create new task from a recurring task 
    private function _create_new_task($task) {

        //don't update the next recurring date when updating task manually
        //stop backdated recurring task creation.
        //check recurring task once/hour?

        $start_date = $task->next_recurring_date;
        $deadline = NULL;

        if ($task->deadline) {
            $task_start_date = $task->start_date ? $task->start_date : $task->created_date;
            $diff_of_deadline = get_date_difference_in_days($task->deadline, $task_start_date); //calculate the deadline difference of the original task
            $deadline = add_period_to_date($start_date, $diff_of_deadline, "days");
        }

        $new_task_data = array(
            "title" => $task->title,
            "description" => $task->description,
            "project_id" => $task->project_id,
            "milestone_id" => $task->milestone_id,
            "points" => $task->points,
            "status_id" => 1, //new tasks should be on ToDo
            "labels" => $task->labels,
            "points" => $task->points,
            "start_date" => $start_date,
            "deadline" => $deadline,
            "recurring_task_id" => $task->id,
            "assigned_to" => $task->assigned_to,
            "collaborators" => $task->collaborators,
            "created_date" => get_current_utc_time(),
            "activity_log_created_by_app" => true
        );

        //create new task
        $new_task_id = $this->ci->Tasks_model->ci_save($new_task_data);

        //create checklist items
        $Checklist_items_model = model("App\Models\Checklist_items_model");
        $checklist_item_options = array("task_id" => $task->id);
        $checklist_items = $Checklist_items_model->get_details($checklist_item_options);
        if ($checklist_items->resultID->num_rows) {
            foreach ($checklist_items->getResult() as $item) {
                $checklist_item_data = array(
                    "title" => $item->title,
                    "is_checked" => $item->is_checked,
                    "task_id" => $new_task_id,
                    "sort" => $item->sort
                );

                $Checklist_items_model->ci_save($checklist_item_data);
            }
        }

        //update the main recurring task
        $no_of_cycles_completed = $task->no_of_cycles_completed + 1;
        $next_recurring_date = add_period_to_date($start_date, $task->repeat_every, $task->repeat_type);

        $recurring_task_data = array(
            "next_recurring_date" => $next_recurring_date,
            "no_of_cycles_completed" => $no_of_cycles_completed
        );
        $this->ci->Tasks_model->save_reminder_date($recurring_task_data, $task->id);

        //send notification
        $notification_option = array("project_id" => $task->project_id, "task_id" => $new_task_id);
        log_notification("recurring_task_created_via_cron_job", $notification_option, "0");
    }

    private function send_task_reminder_notifications() {
        $notification_option = array("notification_multiple_tasks" => true);
        log_notification("project_task_deadline_pre_reminder", $notification_option, "0");
        log_notification("project_task_deadline_overdue_reminder", $notification_option, "0");
        log_notification("project_task_reminder_on_the_day_of_deadline", $notification_option, "0");
    }

    private function close_inactive_tickets() {

        $inactive_ticket_closing_date = get_setting("inactive_ticket_closing_date");
        if (!($inactive_ticket_closing_date == "" || ($inactive_ticket_closing_date != $this->today))) {
            return false;
        }

        $auto_close_ticket_after_days = get_setting("auto_close_ticket_after");

        if ($auto_close_ticket_after_days) {
            //prepare last activity date accroding to the setting
            $last_activity_date = subtract_period_from_date($this->today, get_setting("auto_close_ticket_after"), "days");

            $tickets = $this->ci->Tickets_model->get_details(array(
                        "status" => "open", //don't find closed tickets
                        "last_activity_date_or_before" => $last_activity_date
                    ))->getResult();

            foreach ($tickets as $ticket) {
                //make ticket closed
                $ticket_data = array(
                    "status" => "closed",
                    "closed_at" => get_current_utc_time()
                );

                $this->ci->Tickets_model->ci_save($ticket_data, $ticket->id);

                //send notification
                log_notification("ticket_closed", array("ticket_id" => $ticket->id), "0");
            }
        }

        $this->ci->Settings_model->save_setting("inactive_ticket_closing_date", $this->today);
    }

    private function create_recurring_actuals() {
        $recurring_actuals = $this->ci->Actuals_model->get_renewable_actuals($this->today);
        if ($recurring_actuals->resultID->num_rows) {
            foreach ($recurring_actuals->getResult() as $actuals) {
                $this->_create_new_actuals($actuals);
            }
        }
    }

    //create new actuals from a recurring actuals 
    private function _create_new_actuals($actuals) {

        //don't update the next recurring date when updating actuals manually?
        //stop backdated recurring actuals creation.
        //check recurring actuals once/hour?

        $actuals_date = $actuals->next_recurring_date;

        $new_actuals_data = array(
            "title" => $actuals->title,
            "actuals_date" => $actuals_date,
            "description" => $actuals->description,
            "category_id" => $actuals->category_id,
            "amount" => $actuals->amount,
            "project_id" => $actuals->project_id,
            "user_id" => $actuals->user_id,
            "tax_id" => $actuals->tax_id,
            "tax_id2" => $actuals->tax_id2,
            "recurring_actuals_id" => $actuals->id
        );

        //create new actuals
        $new_actuals_id = $this->ci->Actuals_model->ci_save($new_actuals_data);

        //update the main recurring actuals
        $no_of_cycles_completed = $actuals->no_of_cycles_completed + 1;
        $next_recurring_date = add_period_to_date($actuals_date, $actuals->repeat_every, $actuals->repeat_type);

        $recurring_actuals_data = array(
            "next_recurring_date" => $next_recurring_date,
            "no_of_cycles_completed" => $no_of_cycles_completed
        );

        $this->ci->Actuals_model->ci_save($recurring_actuals_data, $actuals->id);

        //finally send notification
//        log_notification("recurring_actuals_created_vai_cron_job", array("actuals_id" => $new_actuals_id), "0");
    }

}
