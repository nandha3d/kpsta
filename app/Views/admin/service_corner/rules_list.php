<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Service Items
            <small>Service: <?php echo htmlspecialchars($service['title']); ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('admin/ServiceCorner'); ?>">Service Corner</a></li>
            <li class="active">Items</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Items under "<?php echo htmlspecialchars($service['title']); ?>"</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('admin/ServiceCorner/add_rule/' . $service['id']); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add New Item</a>
                            <a href="<?php echo base_url('admin/ServiceCorner'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to Services</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <?php if($this->session->flashdata('success_msg')): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $this->session->flashdata('success_msg'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error_msg')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $this->session->flashdata('error_msg'); ?>
                            </div>
                        <?php endif; ?>

                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="60">No.</th>
                                    <th width="80">Order</th>
                                    <th>Item Title (Accordion Header)</th>
                                    <th>Content / Details</th>
                                    <th>Form Download Link</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($rules)): foreach($rules as $key => $rule): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($rule['rule_number'] ?: ($key + 1)); ?></strong></td>
                                        <td><?php echo $rule['position']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($rule['title']); ?></strong></td>
                                        <td><?php echo mb_strimwidth(strip_tags($rule['content']), 0, 90, "..."); ?></td>
                                        <td>
                                            <?php if(!empty($rule['form_link'])): ?>
                                                <a href="<?php echo base_url($rule['form_link']); ?>" target="_blank" class="label label-info"><i class="fa fa-download"></i> <?php echo htmlspecialchars($rule['form_link']); ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <div class="modern-actions" style="display: inline-flex; align-items: center; gap: 4px;">
                                                <a href="<?php echo base_url('admin/ServiceCorner/edit_rule/' . $rule['id']); ?>" class="btn btn-edit" title="Edit Item"><i class="fa fa-pencil"></i></a>
                                                <a href="<?php echo base_url('admin/ServiceCorner/delete_rule/' . $rule['id']); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-delete" title="Delete Item"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="6" class="text-center">No items added for this service yet. Click "Add New Item" above to add one!</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
