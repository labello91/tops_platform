<?php
if ($login_user->user_type == "team_member") {

    $access_bu = get_array_value($login_user->permissions, "bu");
    if ($login_user->is_admin || $access_bu) {
        ?>
        <li class="nav-item dropdown hidden-xs">
            <?php echo ajax_anchor(get_uri("bu/show_my_starred_bu/"), "<i data-feather='briefcase' class='icon'></i>", array("class" => "nav-link dropdown-toggle", "data-bs-toggle" => "dropdown", "data-real-target" => "#bu-quick-list-container")); ?>
            <div class="dropdown-menu dropdown-menu-start w400">
                <div id="bu-quick-list-container">
                    <div class="list-group">
                        <span class="list-group-item inline-loader p20"></span>                          
                    </div>
                </div>
            </div>
        </li>

        <?php
    }
}