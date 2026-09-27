

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left">Users</h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>
                <div class="col-xs-7 col-sm-6 col-md-7 va-m">
                    <div id="toolbar" class="toolbar text-right">
                        <div class="std-toolbar btn-group">
                            <a  class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="newUser">
                                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">New</span>
                            </a>
                        </div>


                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>








    </section>


    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
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
                            <div class="col-xs-6 col-lg-8 va-m form-inline">

                                <div class="input-group no-margin">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-flat"><i class="fa fa-search fa-fw"></i></button>
                                    </span>
                                </div>

                            </div>

                            <div class="col-xs-6 col-lg-4 va-m text-right">
                                <a class="btn btn-sm btn-danger" href="/munich/s/campaigns/batchDelete" data-toggle="confirmation" data-precheck="batchActionPrecheck" data-message="Delete the selected campaigns?" data-confirm-text="Delete" data-confirm-callback="executeBatchAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation">
                                    <span data-toggle="tooltip" title="" data-placement="left" data-original-title="Delete the selected items"><i class="fa fa-fw fa-trash-o"></i> <span class=""></span></span>
                                </a>        
                            </div>
                        </div>






                    </div>





                    <!-- /.box-header -->

                    <div class="box-body" id="usersManage">
                        <?php echo $content; ?>
                    </div>

                </div>
            </div>
        </div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">

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


    $("body").on('submit', '#addNewUserForm', function (e) {
        var _label = $(this).find('button[type="submit"]');
        $(_label).find('i').remove().end().prepend('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: '<?php echo base_url("admin/aauth/add"); ?>',
            type: 'POST',
            dataType: 'json',
            data: $("#addNewUserForm").serialize(),
            context: this,
            complete: function () {
                $(_label).find('i.fa-spinner').remove();
                $(_label).prepend('<i class="fa fa-save "></i>');
            },
            success: function (result) {
                $(this).find('.modal-body .alert').remove();
                if (result.message === 'success') {
                    $('#modal').modal('hide');
                    $('#usersManage').html(result.content);
                    if (typeof $.fn.effect === 'function') {
                        $('#usersManage').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
                    }
                } else {
                    var error = '';
                    $.each(result.data, function (index, val) {
                        error += val;
                    });
                    $(this).find('.modal-body').prepend('<div class="alert alert-danger alert-dismissible"><h4><i class="icon fa fa-ban"></i> Error !</h4>' + error + '</div>');
                }

            },
            error: function(xhr, status, error) {
                console.error('User add error:', xhr.responseText);
                alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
            }
        });
        return false;
    });


    $("body").on('click', '#edit', function (e) {

        $(this).closest("tr").effect("highlight", {
            color: '#ecf0f5'
        }, 1000);

        var _id = $(this).data('id');
        $.ajax({
            url: '<?php echo base_url("admin/aauth/edit"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {"_id": _id},
            success: function (result) {
                if (result.message == 'success') {
                    $("body .modal-content").html(result.form);
                    $('#modal').modal({show: true});
                }
            },
            error: function(xhr, status, error) {
                console.error('User edit error:', xhr.responseText);
            }
        });
        return false;
    });


    $("body").on('submit', '#editUserForm', function (e) {
        var _id = $(this).data('id');
        var _label = $(this).find('button[type="submit"]');
        $(_label).find('i').remove().end().prepend('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: '<?php echo base_url("admin/aauth/update"); ?>',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize() + '&_id=' + _id,
            context: this,
            complete: function () {
                $(_label).find('i.fa-spinner').remove();
                $(_label).prepend('<i class="fa fa-save "></i>');
            },
            success: function (result) {
                $(this).find('.modal-body .alert').remove();
                $(this).find('.modal-body').prepend('<div class="alert alert-danger alert-dismissible">' + result.data + '</div>');
                if (result.message == 'success') {
                    $('#modal').modal('hide');
                    $('#usersManage').html(result.content);
                }
            },
            error: function(xhr, status, error) {
                console.error('User update error:', xhr.responseText);
                alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
            }
        });
        return false;
    });

    function _row(userInfo, newUser = false) {
        var html = '<tr><td>' + userInfo.id + '</td><td>' + userInfo.username + '</td><td>' + userInfo.email + '</td>';
        if (userInfo.status == 0) {
            html += '<td><span class="label label-success">Active</span></td>';
        } else {
            html += '<td><span class="label label-danger">Locked</span></td>';
        }
        if (userInfo.group == 1) {
            html += '<td><span class="label label-info">Admin</span></td>';
        } else if (userInfo.group == 2) {
            html += '<td><span class="label label-warning">Public</span></td>';
        }
        html += '<td>' + userInfo.lastLogin + '</td>';
        html += '<td><a data-id="' + userInfo.id + '" id="edit" href="javascript:void(0)"><i class="fa fa-edit"></i> Edit</a></td><tr>';

        if (newUser == true) {
            $('#usersManage table').prepend(html);
            var _tr = $('#usersManage table tbody').find('tr:first');

        } else {
            $('#usersManage table').find('a[data-id="' + userInfo.id + '"]').closest('tr').replaceWith(html);
            var _tr = $('#usersManage table').find('a[data-id="' + userInfo.id + '"]').closest('tr');
        }

        $(_tr).effect("highlight", {color: '#00a65a'}, 2000);
    }


</script>