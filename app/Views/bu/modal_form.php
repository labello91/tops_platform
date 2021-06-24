<?php echo form_open(get_uri("bu/save"), array("id" => "bu-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />
        <?php echo view("bu/bu_form_fields"); ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var ticket_id = "<?php echo $ticket_id; ?>";

        $("#bu-form").appForm({
            onSuccess: function (result) {
                if (result.view === "details" || ticket_id) {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function () {
                        location.reload();
                    }, 500);

                } else {
                    $("#bu-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
        setTimeout(function () {
            $("#bu_name").focus();
        }, 200);
    });
</script>    