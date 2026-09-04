<?php 
$is_edit = isset($rule) && !empty($rule);
$action_url = $is_edit ? base_url('admin/ServiceCorner/edit_rule/' . $rule['id']) : base_url('admin/ServiceCorner/add_rule/' . $service['id']);
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?php echo $is_edit ? 'Edit Service Item' : 'Add Service Item'; ?>
            <small>Service: <?php echo htmlspecialchars($service['title']); ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('admin/ServiceCorner'); ?>">Service Corner</a></li>
            <li><a href="<?php echo base_url('admin/ServiceCorner/rules/' . $service['id']); ?>">Items</a></li>
            <li class="active"><?php echo $is_edit ? 'Edit' : 'Add'; ?> Item</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $is_edit ? 'Edit Item Details' : 'New Item Details'; ?></h3>
                    </div>
                    
                    <form action="<?php echo $action_url; ?>" method="post" role="form">
                        <div class="box-body">
                            <?php if(validation_errors()): ?>
                                <div class="alert alert-danger">
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="rule_number">Item Number / Prefix</label>
                                        <input type="text" class="form-control" id="rule_number" name="rule_number" placeholder="e.g. 1, 2, 3" value="<?php echo set_value('rule_number', $rule['rule_number'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="title">Item Title (Accordion Header) *</label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. General Provident Fund (GPF) Temporary Advance" value="<?php echo set_value('title', $rule['title'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="content">Item Content & Details (Accordion Body)</label>
                                <textarea class="form-control" id="content" name="content" rows="6" placeholder="Enter detailed rules, conditions, and procedures..."><?php echo set_value('content', $rule['content'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="form_link">Form Download Link / URL (Optional)</label>
                                        <input type="text" class="form-control" id="form_link" name="form_link" placeholder="e.g. forms or https://..." value="<?php echo set_value('form_link', $rule['form_link'] ?? ''); ?>">
                                        <p class="help-block">Optional link to a downloadable form or external application page.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="position">Display Position Order</label>
                                        <input type="number" class="form-control" id="position" name="position" value="<?php echo set_value('position', $rule['position'] ?? 100); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Item</button>
                            <a href="<?php echo base_url('admin/ServiceCorner/rules/' . $service['id']); ?>" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
