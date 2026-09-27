<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-6 col-sm-6 col-md-6 va-m">
                    <h3 class="pull-left" style="margin: 0; font-weight: 700; color: #1e293b;">Editorial Board Members</h3>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 va-m">
                    <div id="toolbar" class="toolbar text-right">
                        <div class="std-toolbar btn-group">
                            <a class="btn btn-primary" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="Add Editorial Member" style="border-radius: 6px; font-weight: 600;">
                                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Add Member</span>
                            </a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for Add/Edit -->
    <div class="modal fade" id="modal" role="dialog" tabindex="-1" aria-labelledby="editorialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content modal-content-form">
                <?php echo isset($form) ? $form : '' ?>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-top: 3px solid #6366f1;">
                    <div class="box-header with-border" style="padding: 16px 20px;">
                        <div class="row" style="margin-bottom: 5px;">
                            <!-- Search -->
                            <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #475569;">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by name, designation..." name="search" data-href="<?php echo base_url('admin/editorial_board') ?>" style="border-radius: 6px 0 0 6px;">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary btn-flat" name="btn-search" style="border-radius: 0 6px 6px 0;"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>

                            <!-- Designation Filter -->
                            <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #475569;">Designation</label>
                                <select name="designation-search" class="form-control filter-control" style="border-radius: 6px;">
                                    <option value="">All Designations</option>
                                    <?php if (!empty($designations)) {
                                        foreach ($designations as $desig) { ?>
                                            <option value="<?php echo htmlspecialchars($desig); ?>"><?php echo htmlspecialchars($desig); ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>

                            <!-- Sort By -->
                            <div class="col-md-4 col-sm-12" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #475569;">Sort By</label>
                                <select name="sort-search" class="form-control filter-control" style="border-radius: 6px;">
                                    <option value="position-asc">Position (Default)</option>
                                    <option value="position-desc">Position (Highest First)</option>
                                    <option value="name-asc">Name (A - Z)</option>
                                    <option value="name-desc">Name (Z - A)</option>
                                    <option value="designation-asc">Designation (A - Z)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action row: Reset & Batch Delete -->
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-sm btn-default btn-reset-filters" style="margin-right: 8px; border-radius: 6px;">
                                    <i class="fa fa-refresh"></i> Reset Filters
                                </button>
                                <button type="button" class="btn btn-sm btn-danger executeBatchAction" data-toggle="modal" data-target="#delete" data-href="<?php echo base_url('admin/editorial_board/batch_delete'); ?>" data-message="Are you sure you want to delete the selected editorial members?" style="border-radius: 6px; font-weight: 600;">
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table content container -->
                    <div id="table-content">
                        <div id="content">
                            <?php echo isset($content) ? $content : '' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    function refreshContent() {
        var search = $('input[name="search"]').val();
        var designation = $('select[name="designation-search"]').val();
        var sort = $('select[name="sort-search"]').val();

        var url = '<?php echo base_url("admin/editorial_board"); ?>' + '?search=' + encodeURIComponent(search) + '&designation=' + encodeURIComponent(designation) + '&sort=' + encodeURIComponent(sort);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res && res.content) {
                    $('#table-content, #content').html(res.content);
                }
            }
        });
    }

    $('button[name="btn-search"]').on('click', refreshContent);
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            refreshContent();
        }
    });

    $('select[name="designation-search"], select[name="sort-search"]').on('change', refreshContent);

    $('.btn-reset-filters').on('click', function() {
        $('input[name="search"]').val('');
        $('select[name="designation-search"]').val('');
        $('select[name="sort-search"]').val('position-asc');
        refreshContent();
    });

    // Check all checkbox handler
    $(document).on('change', '.checkall', function() {
        $('.checkall-item').prop('checked', $(this).prop('checked'));
    });
    $(document).on('change', '.checkall-item', function() {
        var total = $('.checkall-item').length;
        var checked = $('.checkall-item:checked').length;
        $('.checkall').prop('checked', total > 0 && total === checked);
    });

    // Reset modal when "+ Add Member" is clicked
    $(document).on('click', '[data-target="#modal"][data-id="new"]', function() {
        var $modal = $('#modal');
        var addUrl = '<?php echo base_url("admin/editorial_board/add"); ?>';
        $modal.find('.modal-title').text('Add Editorial Member');
        var $form = $modal.find('form');
        if ($form.length) {
            $form.attr('action', addUrl).data('href', addUrl).attr('data-href', addUrl);
            $form.find('input[name="name"]').val('');
            $form.find('input[name="designation"]').val('');
            $form.find('input[name="position"]').val('');
            $form.find('input[name="phone"]').val('');
            $form.find('input[name="email"]').val('');
            $form.find('input[name="is_publish"][value="1"]').prop('checked', true);
            $form.find('.alert, .text-danger').remove();
            $form.find('.form-group').removeClass('has-error');
        } else {
            // Reload fresh add form if needed
            $.ajax({
                url: addUrl,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res && res.form) {
                        $('#modal .modal-content-form').html(res.form);
                    }
                }
            });
        }
    });

    // EDIT Member Click Handler
    $(document).on('click', '.btn-edit, .edit', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var editUrl = $btn.data('href') || $btn.attr('href');
        if (!editUrl || editUrl === '#' || editUrl === 'javascript:void(0)') return;

        var origHtml = $btn.html();
        $btn.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: editUrl,
            type: 'GET',
            dataType: 'json',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(res) {
                $btn.removeClass('disabled').html(origHtml);
                if (res && res.code === 'success' && res.form) {
                    $('#modal .modal-content-form').html(res.form);
                    $('#modal').modal('show');
                } else {
                    alert((res && res.message) || 'Unable to load member data.');
                }
            },
            error: function(xhr, status, error) {
                $btn.removeClass('disabled').html(origHtml);
                alert('An error occurred while loading member details.');
            }
        });
    });

    // SUBMIT Form (both Add and Edit via AJAX)
    $(document).on('submit', '#modal form, #modal #save', function(e) {
        e.preventDefault();
        var $form = $(this);
        var submitUrl = $form.attr('data-href') || $form.attr('action');
        var $submitBtn = $form.find('button[type="submit"]');

        var origBtnHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(res) {
                $submitBtn.prop('disabled', false).html(origBtnHtml);
                if (res && res.code === 'success') {
                    if (res.content) {
                        $('#table-content, #content').html(res.content);
                    } else {
                        refreshContent();
                    }
                    $('#modal').modal('hide');
                    if (res.lastId && typeof $.fn.effect === 'function') {
                        $("[data-tr='" + res.lastId + "']").effect("highlight", {color: '#6366f1'}, 2000);
                    }
                } else if (res && res.code === 'error') {
                    if (res.form) {
                        $('#modal .modal-content-form').html(res.form);
                    } else {
                        alert(res.message || 'Error saving member.');
                    }
                }
            },
            error: function(xhr, status, error) {
                $submitBtn.prop('disabled', false).html(origBtnHtml);
                alert('An error occurred while saving.');
            }
        });
    });

    // Publish / Inactive status toggle
    $(document).on('change', '#table-content .publish input[name="publish"], #content .publish input[name="publish"]', function() {
        var $radio = $(this);
        var $group = $radio.closest('.publish');
        var pubUrl = $group.data('href');

        $.ajax({
            url: pubUrl,
            type: 'GET',
            dataType: 'json',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function() {
                // Status updated
            }
        });
    });

    // Single Delete click -> confirm modal handler
    $(document).on('click', '.btn-delete', function(e) {
        var $btn = $(this);
        var delUrl = $btn.data('href');
        var msg = $btn.data('message') || 'Are you sure you want to delete this member?';
        $('#delete .modal-title').text(msg);
        $('#delete .delete').off('click').on('click', function() {
            var $delBtn = $(this);
            $delBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            $.ajax({
                url: delUrl,
                type: 'GET',
                dataType: 'json',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                success: function(res) {
                    $delBtn.prop('disabled', false).html('Delete');
                    $('#delete').modal('hide');
                    if (res && res.code === 'success') {
                        if (res.content) {
                            $('#table-content, #content').html(res.content);
                        } else {
                            refreshContent();
                        }
                    } else {
                        alert((res && res.message) || 'Error deleting member.');
                    }
                },
                error: function() {
                    $delBtn.prop('disabled', false).html('Delete');
                    $('#delete').modal('hide');
                    alert('Error deleting member.');
                }
            });
        });
    });

    // Batch Delete confirmation
    $(document).on('click', '.executeBatchAction', function(e) {
        e.preventDefault();
        var ids = [];
        $('.checkall-item:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.length === 0) {
            alert('Please select at least one member to delete.');
            return false;
        }

        var batchUrl = $(this).data('href');
        var msg = $(this).data('message') || 'Are you sure you want to delete the selected members? (' + ids.length + ' selected)';
        $('#delete .modal-title').text(msg);
        $('#delete').modal('show');

        $('#delete .delete').off('click').on('click', function() {
            var $delBtn = $(this);
            $delBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            $.ajax({
                url: batchUrl,
                type: 'POST',
                data: {ids: ids},
                dataType: 'json',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                success: function(res) {
                    $delBtn.prop('disabled', false).html('Delete');
                    $('#delete').modal('hide');
                    if (res && res.code === 'success') {
                        if (res.content) {
                            $('#table-content, #content').html(res.content);
                        } else {
                            refreshContent();
                        }
                    } else {
                        alert((res && res.message) || 'Error deleting members.');
                    }
                },
                error: function() {
                    $delBtn.prop('disabled', false).html('Delete');
                    $('#delete').modal('hide');
                    alert('Error performing batch delete.');
                }
            });
        });
    });
});
</script>
