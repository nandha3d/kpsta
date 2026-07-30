<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>KPSTA | Dashboard</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

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
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/modern-admin.css"/>
    </head>
    <body class="modern-admin-body">
        <div class="modern-wrapper">
            <input type="hidden" value="<?php echo $this->uri->segment(2); ?>" id="url_segment" />
            
            <aside class="modern-sidebar" id="modernSidebar">
                <div class="modern-sidebar-header">
                    <img src="<?php echo base_url(); ?>public/images/logo.png" alt="KPSTA Logo">
                    <span>KPSTA Admin</span>
                </div>
                
                <ul class="modern-sidebar-menu">
                    <li>
                        <a href="<?php echo site_url("admin/home"); ?>">
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
                                    <a href="#">
                                        <i class="fa fa-link"></i> 
                                        <span><?php echo $row['name'] ?></span>
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
                                    <a href="<?php echo site_url("admin/" . $row['url']); ?>">
                                        <i class="fa fa-link"></i> <span><?php echo $row['name'] ?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <!-- All the other menus for Admin -->
                        <li class="treeview flash_news-menu" >
                            <a href="#">
                                <i class="fa fa-newspaper-o"></i> 
                                <span>Flash News</span>
                            </a>
                            <ul class="treeview-menu ">
                                <li><a href="<?php echo site_url("admin/flash_news/kpsta"); ?>"><i class="fa fa-circle-o"></i><span>Kpsta </span></a></li>
                                <li><a href="<?php echo site_url("admin/flash_news/flash"); ?>"><i class="fa fa-circle-o"></i><span>Flash</span></a></li>
                            </ul>
                        </li>

                        <li class="treeview order-circular-menu" >
                            <a href="#">
                                <i class="fa fa-book"></i> 
                                <span>ORDER & CIRCULAR</span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo site_url("admin/order-circular/general"); ?>"><i class="fa fa-circle-o"></i><span>General</span></a></li>
                                <li><a href="<?php echo site_url("admin/order-circular/hse"); ?>"><i class="fa fa-circle-o"></i><span>HSE</span></a></li>
                                <li><a href="<?php echo site_url("admin/order-circular/vhse"); ?>"><i class="fa fa-circle-o"></i><span>VHSE</span></a></li>
                                <li><a href="<?php echo site_url("admin/order-circular/category"); ?>"><i class="fa fa-circle-o"></i><span>Add category</span></a></li>
                            </ul>
                        </li>

                        <li class="quicklink-menu" >
                            <a href="<?php echo site_url("admin/quicklink"); ?>">
                                <i class="fa fa-link"></i> <span>QUICK LINKS</span>
                            </a>
                        </li>

                        <li class="melakal-menu">
                            <a href="<?php echo site_url("admin/melakal"); ?>">
                                <i class="fa fa-smile-o"></i> <span>MELAKAL</span>
                            </a>
                        </li>

                        <li class="membership-menu">
                            <a href="<?php echo site_url("admin/membership"); ?>">
                                <i class="fa fa-users"></i> <span>MEMBERSHIP</span>
                            </a>
                        </li>

                        <li class="treeview aauth-menu" >
                            <a href="#">
                                <i class="fa fa-user-md"></i> 
                                <span>USER & GROUP</span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo site_url("admin/aauth/users"); ?>"><i class="fa fa-circle-o"></i><span>Login Users</span></a></li>
                                <li><a href="<?php echo site_url("admin/aauth/group"); ?>"><i class="fa fa-circle-o"></i><span>Group</span></a></li>
                                <li><a href="<?php echo site_url("admin/aauth/group_to_menu"); ?>"><i class="fa fa-circle-o"></i><span>Menus to Group</span></a></li>
                            </ul>
                        </li>

                        <li class="news-menu" >
                            <a href="<?php echo site_url("admin/news"); ?>">
                                <i class="fa fa-newspaper-o"></i> <span>LATEST NEWS</span>
                            </a>
                        </li>

                        <li class="treeview download-menu" >
                            <a href="#">
                                <i class="fa fa-download"></i> 
                                <span>DOWNLOADS</span>
                            </a>
                            <ul class="treeview-menu ">
                                <li><a href="<?php echo site_url("admin/download/act_rules"); ?>"><i class="fa fa-circle-o"></i><span>Act & Rules</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/softwares"); ?>"><i class="fa fa-circle-o"></i><span>Software</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/fonts"); ?>"><i class="fa fa-circle-o"></i><span>Fonts</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/forms"); ?>"><i class="fa fa-circle-o"></i><span>Forms</span></a></li>
                                <li><a href="<?php echo site_url("admin/download/academic_corner"); ?>"><i class="fa fa-circle-o"></i><span>Academic Corner</span></a></li>
                            </ul>
                        </li>

                        <li class="gallery-menu">
                            <a href="<?php echo site_url("admin/gallery"); ?>">
                                <i class="fa fa-picture-o"></i> <span>GALLERY</span>
                            </a>
                        </li>
                        <li class="service-corner-menu">
                            <a href="<?php echo site_url("admin/ServiceCorner"); ?>">
                                <i class="fa fa-cubes"></i> <span>SERVICE CORNER</span>
                            </a>
                        </li>

                        <li class="adayapaka_sabham-menu">
                            <a href="<?php echo site_url("admin/adayapaka_sabham"); ?>">
                                <i class="fa fa-share-alt"></i> <span>ADAYAPAKA SABHAM</span>
                            </a>
                        </li>
                        <li class="notice_poster-menu">
                            <a href="<?php echo site_url("admin/notice_poster"); ?>">
                                <i class="fa fa-picture-o"></i> <span>NOTICE POSTER</span>
                            </a>
                        </li>
                        <li class="office_bearer-menu">
                            <a href="<?php echo site_url("admin/office_bearer"); ?>">
                                <i class="fa fa-picture-o"></i> <span>Office Bearers</span>
                            </a>
                        </li>
                        <li class="official_outlook-menu">
                            <a href="<?php echo site_url("admin/official_outlook"); ?>">
                                <i class="fa fa-group"></i> <span>official outlook</span>
                            </a>
                        </li>

                        <li class="slider-menu">
                            <a href="<?php echo site_url("admin/slider"); ?>">
                                <i class="fa fa-picture-o"></i> <span>Slider</span>
                            </a>
                        </li>

                        <li class="district-menu">
                            <a href="<?php echo site_url("admin/reaction_gallery"); ?>">
                                <i class="fa fa-share-alt"></i> <span>Reaction Gallery</span>
                            </a>
                        </li>

                        <li class="result_link-menu" >
                            <a href="<?php echo site_url("admin/result_link"); ?>">
                                <i class="fa fa-link"></i> <span>RESULT LINKS</span>
                            </a>
                        </li>
                        <li class="settings-menu" >
                            <a href="<?php echo site_url("admin/settings"); ?>">
                                <i class="fa fa-cogs"></i> <span>Settings</span>
                            </a>
                        </li>
                        <li class="backup-menu" >
                            <a href="<?php echo site_url("admin/backup"); ?>">
                                <i class="fa fa-database"></i> <span>Back up</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </aside>

            <div class="modern-main-content" id="modernMainContent">
                <header class="modern-topbar">
                    <div class="modern-topbar-left">
                        <button class="modern-toggle-btn" id="sidebar-toggle">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <div class="modern-topbar-right">
                        <div class="modern-user-menu">
                            <a href="<?php echo site_url(); ?>" target="_blank">
                                <i class="fa fa-eye"></i> View Site
                            </a>
                            <a href="<?php echo site_url('admin/change_password'); ?>">
                                <i class="fa fa-key"></i> Password
                            </a>
                            <a href="<?php echo site_url("admin/logout"); ?>">
                                <i class="fa fa-sign-out"></i> Logout (<?php echo $this->session->userdata("username"); ?>)
                            </a>
                        </div>
                    </div>
                </header>
                
                <main class="modern-page-body">
