<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?php echo isset($service) ? 'Edit' : 'Add'; ?> Service
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('admin/ServiceCorner'); ?>">Service Corner</a></li>
            <li class="active"><?php echo isset($service) ? 'Edit' : 'Add'; ?> Service</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Service Details</h3>
                    </div>
                    <?php echo form_open(isset($service) ? 'admin/ServiceCorner/edit/'.$service['id'] : 'admin/ServiceCorner/add'); ?>
                        <div class="box-body">
                            
                            <?php if(validation_errors()): ?>
                                <div class="alert alert-danger">
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="service_number">Service Number / Label (e.g. Service 1)</label>
                                <input type="text" class="form-control" id="service_number" name="service_number" value="<?php echo isset($service['service_number']) ? set_value('service_number', $service['service_number']) : set_value('service_number'); ?>">
                            </div>

                            <div class="form-group">
                                <label for="title">Service Title *</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($service['title']) ? set_value('title', $service['title']) : set_value('title'); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($service['description']) ? set_value('description', $service['description']) : set_value('description'); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="icon">Icon (Material Symbols Name)</label>
                                <input type="text" class="form-control" id="icon" name="icon" value="<?php echo isset($service['icon']) ? set_value('icon', $service['icon']) : set_value('icon', 'info'); ?>">
                                <small class="help-block">Examples: savings, event_available, payments, elderly. Default is 'info'. Find more at <a href="https://fonts.google.com/icons" target="_blank">Google Fonts</a></small>
                            </div>
                            
                            <div class="form-group">
                                <label for="link">URL Link</label>
                                <input type="text" class="form-control" id="link" name="link" value="<?php echo isset($service['link']) ? set_value('link', $service['link']) : set_value('link', 'Home/service_corner_details'); ?>">
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="status" value="1" <?php echo (!isset($service['status']) || $service['status'] == 1) ? 'checked' : ''; ?>>
                                        Active
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="status" value="0" <?php echo (isset($service['status']) && $service['status'] == 0) ? 'checked' : ''; ?>>
                                        Inactive
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="<?php echo base_url('admin/ServiceCorner'); ?>" class="btn btn-default">Cancel</a>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </section>
</div>
