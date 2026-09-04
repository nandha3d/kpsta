
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left">Add Menu to Group</h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>
                <div class="col-xs-7 col-sm-6 col-md-7 va-m">
                    <div id="toolbar" class="toolbar text-right">



                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-form">
                <?php echo isset($form) ? $form : '' ?>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box">

                    <?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => base_url('admin/aauth/group_to_menu/add')]) ?>

                    <div class="box-header with-border">
                        <div class="box-layout">

                            <div class="row">
                                <div class="col-lg-4 col-lg-offset-4">

                                    <div class="form-group center-block text-center btn btn-info">
                                        <?php echo form_dropdown('group', $group, '', 'class=" form-control group" style="width: 100%;" data-placeholder="Select Group"     data-href="' . base_url('admin/aauth/group_to_menu/menu') . '"'); ?>
                                    </div>
                                </div><!-- /.col-lg-4 -->
                            </div><!-- /.row -->





                        </div>


                    </div>



                    <!-- /.box-header -->
                    <div class="box-body" id="table-content">
                        <?php echo $content; ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix text-center">

                    </div>

                    <?php echo form_close(); ?>

                    <!-- /.box-footer -->


                </div>
            </div>
        </div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
