<script type="text/javascript">

    $(document).ready(function () {

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("intake/all_intake_kanban_data") ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            search: {name: "search"},
            filterDropdown: [
                {name: "source", class: "w200", options: <?php echo view("intake/intake_sources"); ?>},
<?php if (get_array_value($login_user->permissions, "intake") !== "own") { ?>
                    {name: "owner_id", class: "w200", options: <?php echo json_encode($owners_dropdown); ?>}
<?php } ?>
            ],
            beforeRelaodCallback: function () {
                scrollLeft = $("#kanban-wrapper").scrollLeft();
            },
            afterRelaodCallback: function () {
                setTimeout(function () {
                    $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
                }, 500);
            }
        });

    });

</script>