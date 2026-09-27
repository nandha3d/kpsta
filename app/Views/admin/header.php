<?php
/**
 * Admin layout header.
 *
 * Optional vars a controller may pass:
 *   $page_title  - heading shown in the topbar (falls back to the URI segment)
 *   $public_url  - relative public page this screen feeds, adds a "View page" link
 *   $special_css - extra stylesheets under public/
 */
$adminSeg = $this->uri->segment(2);
if (!isset($page_title) || $page_title === '') {
    $page_title = $adminSeg ? ucwords(str_replace(array('_', '-'), ' ', $adminSeg)) : 'Dashboard';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>KPSTA | <?php echo html_escape($page_title); ?></title>
        <meta content="width=device-width, initial-scale=1" name="viewport">

        <link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>public/images/favicon.ico">
        <link rel="apple-touch-icon" href="<?php echo base_url(); ?>public/images/apple-touch-icon.png">

        <!-- Keep Bootstrap 3 & Original Plugins for internal functionality (DataTables/Modals) -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/plugins/font-awesome-4.6.3/css/font-awesome.min.css"/>

        <?php
        if (isset($special_css) && !empty($special_css)) {
            foreach ($special_css as $css) {
                echo '<link rel="stylesheet" type="text/css" href="' . base_url("public/" . $css) . '"  />';
            }
        }
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/plugins/datepicker/datepicker3.css"/>

        <!-- jQuery -->
        <script src="<?php echo base_url(); ?>public/plugins/jQuery/jquery-2.2.3.min.js"></script>

        <!-- New Modern Admin CSS (Overrides AdminLTE) -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/modern-admin.css?v=<?php echo filemtime(FCPATH.'public/css/modern-admin.css'); ?>"/>

        <script>
            // Applied before first paint so a collapsed sidebar does not flash open.
            (function () {
                try {
                    if (localStorage.getItem('kpstaSidebarCollapsed') === '1' && window.innerWidth > 991) {
                        document.documentElement.className += ' pre-collapsed';
                    }
                } catch (e) {}
            }());
        </script>
    </head>
    <body class="modern-admin-body">
        <div class="modern-wrapper" id="modernWrapper">
            <input type="hidden" value="<?php echo html_escape($adminSeg); ?>" id="url_segment" />

            <aside class="modern-sidebar" id="modernSidebar">
                <div class="modern-sidebar-header">
                    <a href="<?php echo site_url("admin/home"); ?>" class="modern-brand">
                        <img src="<?php echo base_url(); ?>public/images/logo.png" alt="KPSTA Logo">
                        <span>KPSTA Admin</span>
                    </a>
                </div>

                <ul class="modern-sidebar-menu">
                    <li class="home-menu">
                        <a href="<?php echo site_url("admin/home"); ?>" data-title="Dashboard">
                            <i class="fa fa-dashboard"></i><span>Dashboard</span>
                        </a>
                    </li>

                    <?php
                    if ($this->session->userdata('group') <> 1) {
                        foreach ($adminMenuList as $row) {
                            $id = $row['id'];
                            $parentId = $row['parent_id'];

                            if ($row['is_tree'] == 1) {
                                ?>
                                <li class="treeview <?php echo $row['urlFirst'] ?>-menu" >
                                    <a href="#" data-title="<?php echo html_escape($row['name']) ?>">
                                        <i class="fa fa-folder-o"></i>
                                        <span><?php echo $row['name'] ?></span>
                                        <i class="fa fa-angle-left modern-caret"></i>
                                    </a>
                                    <ul class="treeview-menu ">
                                        <?php foreach ($row['menuView'] as $menu) { ?>
                                            <li><a href="<?php echo site_url("admin/" . $menu['url']); ?>">
                                                    <i class="fa fa-circle-o"></i><span><?php echo $menu['name'] ?> </span>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <li class="<?php echo $row['urlFirst'] ?>-menu" >
                                    <a href="<?php echo site_url("admin/" . $row['url']); ?>" data-title="<?php echo html_escape($row['name']) ?>">
                                        <i class="fa fa-link"></i> <span><?php echo $row['name'] ?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <li class="modern-menu-section"><span>Organization</span></li>

                        <li class="treeview office_bearer-menu">
                            <a href="#" data-title="Organization">
                                <i class="fa fa-sitemap"></i>
                                <span>Organization</span>
                                <i class="fa fa-angle-left modern-caret"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo site_url("admin/office_bearer"); ?>"><i class="fa fa-circle-o"></i><span>State Office Bearers</span></a></li>
                                <li><a href="<?php echo site_url("admin/district"); ?>"><i class="fa fa-circle-o"></i><span>District Office Bearers</span></a></li>
                                <li><a href="<?php echo site_url("admin/office_bearer?is_former=1"); ?>"><i class="fa fa-circle-o"></i><span>Former Leaders</span></a></li>
                                <li><a href="<?php echo site_url("admin/memorandums"); ?>"><i class="fa fa-circle-o"></i><span>Memorandums</span></a></li>
                                <li><a href="<?php echo site_url("admin/notice_poster"); ?>"><i class="fa fa-circle-o"></i><span>Notices &amp; Posters</span></a></li>
                                <li><a href="<?php echo site_url("admin/office_bearer/designation"); ?>"><i class="fa fa-circle-o"></i><span>Designations</span></a></li>
                            </ul>
                        </li>

                        <li class="modern-menu-section"><span>Orders</span></li>

                        <li class="treeview order-circular-menu" >
                            <a href="#" data-title="Order &amp; Circular">
                                <i class="fa fa-book"></i>
                                <span>Order &amp; Circular</span>
                                <i class="fa fa-angle-left modern-caret"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo site_url("admin/order-circular/general"); ?>"><i class="fa fa-circle-o"></i><span>General</span></a></li>
                                <li><a href="<?php echo site_url("admin/order-circular/hse"); ?>"><i class="fa fa-circle-o"></i><span>HSE</span></a></li>
                                <li><a href="<?php echo site_url("admin/order-circular/vhse"); ?>"><i class="fa fa-circle-o"></i><span>VHSE</span></a></li>
                                <li><a href="<?php echo site_url("admin/order-circular/category"); ?>"><i class="fa fa-circle-o"></i><span>Add category</span></a></li>
                            </ul>
                        </li>

                        <li class="modern-menu-section"><span>Downloads</span></li>

                        <li class="treeview download-menu" >
                            <a href="#" data-title="Downloads">
                                <i class="fa fa-download"></i>
                                <span>Downloads</span>
                                <i class="fa fa-angle-left modern-caret"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo site_url("admin/download/forms"); ?>"><i class="fa fa-circle-o"></i><span>Forms</span></a></li>
                                <li><a href="<?php echo site_url("admin/ServiceCorner"); ?>"><i class="fa fa-circle-o"></i><span>Service Corner</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/academic_corner"); ?>"><i class="fa fa-circle-o"></i><span>Academic Corner</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/softwares"); ?>"><i class="fa fa-circle-o"></i><span>Software Tools</span></a></li>
                                <li><a href="<?php echo site_url("admin/melakal"); ?>"><i class="fa fa-circle-o"></i><span>Melakal</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/fonts"); ?>"><i class="fa fa-circle-o"></i><span>Fonts</span></a></li>
                            </ul>
                        </li>

                        <li class="modern-menu-section"><span>Media &amp; News</span></li>

                        <li class="news-menu" >
                            <a href="<?php echo site_url("admin/news"); ?>" data-title="Latest News">
                                <i class="fa fa-newspaper-o"></i> <span>Latest News</span>
                            </a>
                        </li>

                        <li class="gallery-menu">
                            <a href="<?php echo site_url("admin/gallery"); ?>" data-title="Gallery">
                                <i class="fa fa-picture-o"></i> <span>Gallery</span>
                            </a>
                        </li>

                        <li class="reaction_gallery-menu">
                            <a href="<?php echo site_url("admin/reaction_gallery"); ?>" data-title="Reaction Gallery">
                                <i class="fa fa-comments-o"></i> <span>Reaction Gallery</span>
                            </a>
                        </li>

                        <li class="slider-menu">
                            <a href="<?php echo site_url("admin/slider"); ?>" data-title="Slider &amp; Headings">
                                <i class="fa fa-sliders"></i> <span>Slider &amp; Headings</span>
                            </a>
                        </li>

                        <li class="modern-menu-section"><span>Online Links</span></li>

                        <li class="quicklink-menu" >
                            <a href="<?php echo site_url("admin/quicklink"); ?>" data-title="Quick Links">
                                <i class="fa fa-link"></i> <span>Quick Links</span>
                            </a>
                        </li>

                        <li class="result_link-menu" >
                            <a href="<?php echo site_url("admin/result_link"); ?>" data-title="Result Links">
                                <i class="fa fa-trophy"></i> <span>Result Links</span>
                            </a>
                        </li>

                        <li class="modern-menu-section"><span>Membership</span></li>

                        <li class="membership-menu">
                            <a href="<?php echo site_url("membership/home"); ?>" data-title="Membership">
                                <i class="fa fa-users"></i> <span>Membership Portal</span>
                            </a>
                        </li>

                        <li class="modern-menu-section"><span>System</span></li>

                        <li class="treeview aauth-menu" >
                            <a href="#" data-title="User &amp; Group">
                                <i class="fa fa-user-md"></i>
                                <span>User &amp; Group</span>
                                <i class="fa fa-angle-left modern-caret"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo site_url("admin/aauth/users"); ?>"><i class="fa fa-circle-o"></i><span>Login Users</span></a></li>
                                <li><a href="<?php echo site_url("admin/aauth/group"); ?>"><i class="fa fa-circle-o"></i><span>Group</span></a></li>
                                <li><a href="<?php echo site_url("admin/aauth/group_to_menu"); ?>"><i class="fa fa-circle-o"></i><span>Menus to Group</span></a></li>
                            </ul>
                        </li>

                        <li class="settings-menu" >
                            <a href="<?php echo site_url("admin/settings"); ?>" data-title="Settings">
                                <i class="fa fa-cogs"></i> <span>Settings</span>
                            </a>
                        </li>

                        <li class="backup-menu" >
                            <a href="<?php echo site_url("admin/backup"); ?>" data-title="Back up">
                                <i class="fa fa-database"></i> <span>Back up</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </aside>

            <div class="modern-sidebar-backdrop" id="sidebarBackdrop"></div>

            <div class="modern-main-content" id="modernMainContent">
                <header class="modern-topbar">
                    <div class="modern-topbar-left">
                        <button class="modern-toggle-btn" id="sidebar-toggle" type="button" aria-label="Toggle navigation">
                            <i class="fa fa-bars"></i>
                        </button>
                        <h1 class="modern-topbar-title"><?php echo html_escape($page_title); ?></h1>
                    </div>
                    <div class="modern-topbar-right">
                        <div class="modern-user-menu">
                            <?php if (!empty($public_url)) { ?>
                                <a href="<?php echo site_url($public_url); ?>" target="_blank" rel="noopener" class="modern-topbar-cta" title="Open this section on the public website">
                                    <i class="fa fa-external-link"></i> <span>View page</span>
                                </a>
                            <?php } ?>
                            <a href="<?php echo site_url(); ?>" target="_blank" rel="noopener" title="Open the public website">
                                <i class="fa fa-globe"></i> <span>View Site</span>
                            </a>
                            <a href="<?php echo site_url('admin/change_password'); ?>" title="Change password">
                                <i class="fa fa-key"></i> <span>Password</span>
                            </a>
                            <span class="modern-user-chip" title="Signed in as <?php echo html_escape($this->session->userdata("username")); ?>">
                                <i class="fa fa-user"></i> <?php echo html_escape($this->session->userdata("username")); ?>
                            </span>
                            <a href="<?php echo site_url("admin/logout"); ?>" class="modern-logout" title="Log out">
                                <i class="fa fa-sign-out"></i> <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </header>

                <main class="modern-page-body">
