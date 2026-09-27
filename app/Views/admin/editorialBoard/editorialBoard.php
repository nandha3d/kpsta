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
                    <div id="content">
                        <?php echo isset($content) ? $content : '' ?>
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
                    $('#content').html(res.content);
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
});
</script>
