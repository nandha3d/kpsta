<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Settings
            <small>Global Configuration</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                
                <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php endif; ?>

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Office Bearers Rollover Setting</h3>
                    </div>
                    
                    <?php echo form_open('admin/settings', ['autocomplete' => 'off']); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Term Rollover Month</label>
                            <p class="help-block">Select the month when the new term automatically begins (e.g., February). Anyone whose 'Year' is older than the current active term will automatically be treated as 'Former'.</p>
                            <select name="rollover_month" class="form-control" style="max-width: 300px;">
                                <?php 
                                $months = [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                ];
                                foreach($months as $num => $name) {
                                    $selected = ($rollover_month == $num) ? 'selected' : '';
                                    echo "<option value=\"$num\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
                
            </div>
        </div>
    </section>
</div>
