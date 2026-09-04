
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left">Gallery</h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>
                <div class="col-xs-7 col-sm-6 col-md-7 va-m">
                    <div id="toolbar" class="toolbar text-right">
                        <div class="std-toolbar btn-group">
                            <a  class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="new" data-title="Add New Album">
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
                                    <input type="text" class="form-control" placeholder="Search..." name="search">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-flat"><i class="fa fa-search fa-fw"></i></button>
                                    </span>
                                </div>
                                <div class="form-group col-md-6 hidden-xs hidden-sm ">
                                    <?php // unset($category['']) ?>
                                    <?php // echo form_dropdown('category-search[]', $category, '', 'class="form-control category-search" style="width: 100%;"  multiple="multiple" data-placeholder="Search by category" '); ?>
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





<script type="text/javascript">
    //Add and update gallery album
    $("body").on('submit', '#modal #save', function (e) {
        var _label = $(this).find('button[type="submit"]');
        $(_label).find('i').remove().end().prepend('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            context: this,
            complete: function () {
                $(_label).find('i.fa-spinner').remove();
                $(_label).prepend('<i class="fa fa-save "></i>');
            },
            success: function (result) {
                $(this).find('.modal-body .alert').remove();
                if (result.code == 'success') {
                    $('#table-content').html(result.content);
                    $('#modal').modal('hide');
                    if (typeof $.fn.effect === 'function') {
                        $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
                    }
                } else {
                    $(this).closest('form').replaceWith(result.form);
                }
            },
            error: function(xhr, status, error) {
                console.error('Gallery save error:', xhr.responseText);
                alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
            }
        });
        return false;
    });

    $("body").on('click', '.edit', function (e) {
        $(this).closest("tr").effect("highlight", {color: '#ecf0f5'}, 1000);
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                if (result.code == 'success') {
                    $("body .modal-content-form").html(result.form);
                    $('#modal').modal({show: true});
                }
            }
        });
        return false;
    });

    //delete
    $('.confirmation-modal').on('show.bs.modal', function (e) {
        $('.confirmation-modal').unbind('click');
        $(e.target).find('.modal-title').text($(e.relatedTarget).data('message'));
        var _href = $(e.relatedTarget).data('href');
        $(e.target).on('click', '.delete', function () {
            var _label = $($(e.target).find('.delete'));
            $(_label).addSpinner();
            $.ajax({
                url: _href,
                type: 'GET',
                dataType: 'json',
                context: this,
                success: function (result) {
                    $(_label).removeSpinner();
                    if (result.code === 'success') {
                        $('#table-content').html(result.content);
                        $('.confirmation-modal').modal('hide');
                    } else {
                        $("body").find('#modal form').replaceWith(result.form);
                    }
                }
            });
            $(e.target).off('click', '.delete');
            return false;
        });
    });


</script>
