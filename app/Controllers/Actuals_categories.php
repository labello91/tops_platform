<?php
namespace App\Controllers;

class Actuals_categories extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    //load actuals categories list view
    function index() {
        return $this->template->rander("actuals_categories/index");
    }

    //load actuals category add/edit modal form
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Actuals_categories_model->get_one($this->request->getPost('id'));
        return $this->template->view('actuals_categories/modal_form', $view_data);
    }

    //save expense category
    function save() {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id)');
        $data = array(
            "title" => $this->request->getPost('title')
        );
        $save_id = $this->Actuals_categories_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_ocurred')));
        }
    }

    //delede/undo an actual category
    function delete() {
        $this->validate_submitted_array(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Actuals_categories_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    //get an actual category list row
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Actuals_categories_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    //prepare an actual category list row
    private function _make_row($data) {
        return array($data->title,
        modal_anchor(get_uri("actuals_categories/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_actuals_category'), "data-post-id" => $data->id))
        . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_actuals_category'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("actuals_categories/delete"), "data-action" => "delete"))
        );
    }
}

/* End of file actuals_categories.ph */
/* Location: ./app/controllers/actuals_categories.php */