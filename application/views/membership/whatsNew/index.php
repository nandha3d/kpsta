
<!-- Content Wrapper. Contains page content -->
<div class="container">
    <div class="row">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-md-12">

                    <div class="page-header  box-background ">
                        <div class="box-layout ">
                            <div class="col-xs-6 col-sm-6 col-md-6 va-m">
                                <!--<h3 class="pull-left"> Member's List</h3>-->

                                <div id="toolbar" class="toolbar text-right pull-left">
                                    <div class="std-toolbar btn-group">
                                            What's New
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6 col-sm-6 col-md-6 va-m">
                                <div id="toolbar" class="toolbar text-right">
                                    <div class="std-toolbar btn-group">
                                        
                                         
                                    </div>
                                    <div class="std-toolbar btn-group">
                                        <a  class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="Add">
                                            <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">New</span>
                                        </a>
                                        <div class="dropdown-toolbar btn-group">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
<!--                                            <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                </li>
                                            </ul>-->
                                        </div>
                                    </div>


                                </div>
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
                                        <input type="text" class="form-control" placeholder="Search..." name="search" data-href="<?php echo base_url('membership/teacher') ?>">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-flat" name="search"><i class="fa fa-search fa-fw"></i></button>
                                        </span>
                                    </div>
                                    <div class="form-group col-md-6 hidden-xs hidden-sm ">
                                        <?php // unset($category['']) ?>
                                        <?php // echo form_dropdown('category-search[]', $category, '', 'class="form-control category-search" style="width: 100%;"  multiple="multiple" data-placeholder="Search by Designation" '); ?>
                                    </div>


                                </div>



                                <div class="col-xs-6 col-lg-4 va-m text-right">
                                    <a class="btn btn-sm btn-danger" href="" data-toggle="confirmation" data-precheck="batchActionPrecheck" data-message="Delete the selected campaigns?" data-confirm-text="Delete" data-confirm-callback="executeBatchAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation">
                                        <span data-toggle="tooltip" title="" data-placement="left" data-original-title="Delete the selected items"><i class="fa fa-fw fa-trash-o"></i> <span class=""></span></span>
                                    </a>        
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
</div>


<script type="text/javascript">
    $(function () {

        $(".select2-reg").select2({
            dropdownParent: $("#modalProcess")
        });

        $('.category-search').select2().on("change", function (e) {
            console.log($('.category-search').select2("val"));
            seach(0);
        });
    
    });
</script>