

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left">Change Password</h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>
                <div class="col-xs-7 col-sm-6 col-md-7 va-m">

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-6">
                <div class="box ">
                    <?php echo form_open(site_url('membership/settings/change_password'), ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'class' => 'form-horizontal']) ?> 
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body ">

                        <?php if (isset($formValues['error'])) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $formValues['error'] ?>  
                            </div>
                            <?php
                        }

                        if (isset($formValues['success'])) {
                            ?>
                            <div class="alert alert-success alert-dismissible">
                                <h4><i class="icon fa fa-send"></i> Success !</h4> <?php echo $formValues['success'] ?>  
                            </div>
                            <?php
                        }
                        if (validation_errors()) {
                            ?>
                            <div class = "alert alert-danger alert-dismissible">
                                <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
                            </div>
                        <?php } ?>


                        <?php $class = form_error('current_password') ? 'form-group has-error' : 'form-group' ?>

                        <div class="<?php echo $class ?>">
                            <label  class="col-sm-4 control-label">Current Password</label>
                            <div class="col-sm-8">
                                <input class="form-control" name="current_password"  placeholder="Current Password" type="text">
                            </div>
                        </div>

                        <?php $class = form_error('password') ? 'form-group has-error' : 'form-group' ?>
                        <div class="<?php echo $class ?>">
                            <label  class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-8">
                                <input class="form-control" name="password"  placeholder="Password" type="text">
                            </div>
                        </div>


                        <?php $class = form_error('confirm_password') ? 'form-group has-error' : 'form-group' ?>
                        <div class="<?php echo $class ?>">
                            <label  class="col-sm-4 control-label">Confirm Password</label>
                            <div class="col-sm-8">
                                <input class="form-control" name="confirm_password"  placeholder="Confirm Password" type="text">
                            </div>
                        </div>

                    </div>

                    <div class="box-footer text-center">
                        <button type="submit" class="col-sm-offset-4 btn btn-primary flash-news"   ><i class="fa fa-save "></i> Save</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>



        </div>

        <div class="row">

        </div>








    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

