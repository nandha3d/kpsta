
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left">Office Bearers</h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>
                <div class="col-xs-7 col-sm-6 col-md-7 va-m">
                    <div id="toolbar" class="toolbar text-right">
                        <div class="std-toolbar btn-group">
                            <a class="btn btn-default" data-toggle="modal" data-target="#modal" data-id="new" data-title-new="Add">
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
                <div class="box box-primary">
                    <div class="box-header with-border" style="padding: 15px;">
                        <div class="row" style="margin-bottom: 10px;">
                            <!-- Keyword Search -->
                            <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by name, email, phone..." name="search" data-href="<?php echo base_url('admin/office_bearer') ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary btn-flat" name="btn-search"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>

                            <!-- Designation Filter -->
                            <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Designation</label>
                                <?php unset($designation['']); ?>
                                <?php echo form_dropdown('designation-search[]', $designation, '', 'class="form-control category-search" style="width: 100%;" multiple="multiple" data-placeholder="Filter by Designation"'); ?>
                            </div>

                            <!-- Level / Category Filter -->
                            <div class="col-md-2 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Category / Level</label>
                                <select name="level-search" class="form-control filter-control">
                                    <option value="">All Categories</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                </select>
                            </div>

                            <!-- District / Place Filter -->
                            <div class="col-md-2 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Place / District</label>
                                <select name="district-search" class="form-control filter-control">
                                    <option value="">All Places / Districts</option>
                                    <?php if(!empty($districts)) { foreach($districts as $d) { ?>
                                        <option value="<?php echo htmlspecialchars($d); ?>"><?php echo htmlspecialchars($d); ?></option>
                                    <?php } } ?>
                                </select>
                            </div>

                            <!-- Former vs Active Filter -->
                            <div class="col-md-2 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Leader Type</label>
                                <select name="former-search" class="form-control filter-control">
                                    <option value="">All Leaders</option>
                                    <option value="0">Active Leaders</option>
                                    <option value="1">Former Leaders</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Status Filter -->
                            <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Publish Status</label>
                                <select name="publish-search" class="form-control filter-control">
                                    <option value="">All Status</option>
                                    <option value="1">Active / Published</option>
                                    <option value="0">Inactive / Unpublished</option>
                                </select>
                            </div>

                            <!-- Sort Options -->
                            <div class="col-md-3 col-sm-6" style="margin-bottom: 10px;">
                                <label style="font-weight: 600; font-size: 12px; color: #555;">Sort By</label>
                                <select name="sort-search" class="form-control filter-control">
                                    <option value="position-asc">Position (Default)</option>
                                    <option value="position-desc">Position (Descending)</option>
                                    <option value="name-asc">Name (A to Z)</option>
                                    <option value="name-desc">Name (Z to A)</option>
                                    <option value="designation-asc">Designation (A to Z)</option>
                                    <option value="level-asc">Category (State / District)</option>
                                </select>
                            </div>

                            <!-- Filter Actions -->
                            <div class="col-md-6 col-sm-12 text-right" style="margin-top: 24px;">
                                <button type="button" class="btn btn-default" id="btn-reset-filters">
                                    <i class="fa fa-refresh"></i> Reset Filters
                                </button>
                                <a class="btn btn-danger" href="" data-toggle="confirmation" data-precheck="batchActionPrecheck" data-message="Delete the selected items?" data-confirm-text="Delete" data-confirm-callback="executeBatchAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation">
                                    <i class="fa fa-trash-o"></i> Delete Selected
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
