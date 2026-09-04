
<!-- Content Wrapper. Contains page content -->
<div class="container">
    <div class="row">
        <input type="hidden" name="url_type" id="url_type" value="<?php echo base_url('admin/membership/aauth'); ?>">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-md-12">

                    <div class="page-header  box-background ">
                        <div class="box-layout ">
                            <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                                <h3 class="pull-left">Users</h3>
                                <div class="col-xs-2 text-right pull-left">
                                </div>
                            </div>
                            <div class="col-xs-7 col-sm-6 col-md-7 va-m">
                                <div id="toolbar" class="toolbar text-right">
                                    <div class="std-toolbar btn-group">
                                        <a  class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="Add">
                                            <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">New</span>
                                        </a>
                                        <div class="dropdown-toolbar btn-group">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-nospin  dropdown-toggle" type="button"><i class="fa fa-caret-down"></i></button>
                                            <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                </li>
                                            </ul>
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


        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-content-form">
                    <?php echo $userForm ? $userForm : '' ?>
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
                                <div class="col-xs-10 col-lg-10 va-m form-inline">

                                    <div class="input-group no-margin pull-left">
                                        <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button></span>
                                        <input type="text" class="form-control" placeholder="Search..." name="search" data-href="<?php echo base_url('membership/aauth/users') ?>">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-flat" name="search"><i class="fa fa-search fa-fw"></i></button>
                                        </span>
                                    </div>

                                    <div class="form-group col-md-4 hidden-xs hidden-sm ">
                                        <?php $selected = isset($groupId) ? $groupId : '' ?>
                                        <?php echo form_dropdown('group-search', $groupSearch, $selected, 'class="form-control " id="group-search" style="width: 100%;"  data-placeholder="Search by Group" data-href="' . base_url('membership/aauth/getOffice') . '" '); ?>
                                    </div>

                                    <div class="form-group col-md-4 hidden-xs hidden-sm ">
                                        <?php $selected = isset($officeId) ? $officeId : '' ?>
                                        <?php echo form_dropdown('office-search', $officeSearch, $selected, 'class="form-control" id="office-search" style="width: 100%;"  data-placeholder="Search by Office" '); ?>
                                    </div>


                                </div>



                                <div class="col-xs-2 col-lg-2 va-m text-right">
                                    <a class="btn btn-sm btn-danger" href="" data-toggle="confirmation" data-precheck="batchActionPrecheck" data-message="Delete the selected campaigns?" data-confirm-text="Delete" data-confirm-callback="executeBatchAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation">
                                        <span data-toggle="tooltip" title="" data-placement="left" data-original-title="Delete the selected items"><i class="fa fa-fw fa-trash-o"></i> <span class=""></span></span>
                                    </a>        
                                </div>
                            </div>


                        </div>


                        <div class="overlay" >
                            <i class="fa fa-refresh fa-spin"></i>
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


//ADD new record and Update record
    $("body").on('submit', '#modal #save', function (e) {
        var _saveBtn = $('#modal button[type="submit"]');
        $(_saveBtn).addSpinner();
        $.ajax({
            url: $(this).data('href') + '?page=' + getUrlParamByName('page'),
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            context: this,
            complete: function () {
                $(_saveBtn).removeSpinner();
            },
            success: function (result) {
                $(this).find('.modal-body .alert').remove();
                if (result.code == 'success') {
                    $('#table-content').html(result.content);
                    $('#modal').modal('hide');
                    $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
                } else {
                    $("body").find('#modal form').replaceWith(result.form);
                }

            }
        });
        return false;
    });




    $('#modal').on('hidden.bs.modal', function (e) {
        $(e.target).find('.modal-body .alert').remove();
        $(e.target).find('input').removeAttr('value');
    });
    $('#modal').on('show.bs.modal', function (e) {
        $clickedButton = $(e.relatedTarget).data('id');
        if ($clickedButton === 'newUser') {
            $(e.target).find('.modal-title').text('New User');
        }
    });


    $("body").on('click', '.edit', function (e) {

        $(this).closest("tr").effect("highlight", {
            color: '#ecf0f5'
        }, 1000);
        $(this).addSpinner();
        var _id = $(this).data('id');
        var _href = $(this).data('href');
        $.ajax({
            url: "edit",
            type: 'POST',
            dataType: 'json',
            data: {_id: _id},
            context: this,
            complete: function () {
                $(this).removeSpinner();
            },
            success: function (result) {
                if (result.message == 'success') {
                    $("#modal .modal-content").html(result.form);
                    $('#modal').modal({show: true});
                }
            }
        });
        return false;
    });








</script>