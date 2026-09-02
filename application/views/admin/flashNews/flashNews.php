
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left"><?php echo $menuType['contentTitle'] ?></h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>
                <div class="col-xs-7 col-sm-6 col-md-7 va-m">
                    <div id="toolbar" class="toolbar text-right">
                        <div class="std-toolbar btn-group">
                            <a  class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="Add">
                                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">New</span>
                            </a>
                        </div>


                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content modal-content-form" >
                <?php echo isset($form) ? $form : '' ?>
            </div>
        </div>
    </div>


    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-layout">
                            <div class="col-xs-6 col-lg-8 va-m form-inline">

                                <div class="input-group no-margin pull-left">
                                    <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button></span>
                                    <input type="text" class="form-control" placeholder="Search..." name="search" data-href="<?php echo base_url('admin/flash_news/' . $this->uri->segment(3)) ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-flat" name="search"><i class="fa fa-search fa-fw"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body" id="table-content">
                        <?php echo $content; ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix">
                    </div>
                    <!-- /.box-footer -->
                </div>
            </div>
        </div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

