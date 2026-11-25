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
                <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
                <?php endif; ?>

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-sliders"></i> Global System Configuration</h3>
                    </div>
                    
                    <?php echo form_open('admin/settings', ['autocomplete' => 'off']); ?>
                    <div class="box-body">
                        <!-- Office Bearers Rollover Section -->
                        <div class="form-group" style="padding-bottom: 18px; border-bottom: 1px solid #f0f0f0;">
                            <label><i class="fa fa-users text-primary"></i> Term Rollover Month</label>
                            <p class="help-block">Select the month when the new term automatically begins (e.g., February). Anyone whose 'Year' is older than the current active term will automatically be treated as 'Former'.</p>
                            <select name="rollover_month" class="form-control" style="max-width: 320px;">
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

                        <!-- Flash News Scrolling Speed Section -->
                        <div class="form-group" style="padding-top: 18px; padding-bottom: 18px; border-bottom: 1px solid #f0f0f0;">
                            <label><i class="fa fa-bolt text-warning"></i> Flash News Scrolling Speed (Website &amp; Mobile App)</label>
                            <p class="help-block">Adjust the scrolling speed of the flash news ticker. You can select a unified preset to keep both identical, or customize the website and mobile app speeds individually.</p>

                            <div class="row">
                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;">Speed Preset</label>
                                    <select id="tickerSpeedPreset" class="form-control" onchange="applySpeedPreset(this.value)">
                                        <option value="">-- Choose Preset or Customize --</option>
                                        <option value="slow">Slow (Web: 4, Mobile: 45 px/s)</option>
                                        <option value="medium" selected>Medium / Standard (Web: 7, Mobile: 65 px/s)</option>
                                        <option value="fast">Fast (Web: 10, Mobile: 95 px/s)</option>
                                        <option value="very_fast">Very Fast (Web: 14, Mobile: 130 px/s)</option>
                                    </select>
                                    <span class="help-block" style="font-size: 12px;">Selecting a preset automatically configures both speeds.</span>
                                </div>

                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;"><i class="fa fa-globe text-info"></i> Main Website Scroll Speed</label>
                                    <div class="input-group">
                                        <input type="number" id="webSpeedInput" name="flash_news_speed_web" class="form-control" min="1" max="30" value="<?php echo htmlspecialchars($flash_news_speed_web ?? 7); ?>">
                                        <span class="input-group-addon">step/tick</span>
                                    </div>
                                    <span class="help-block" style="font-size: 12px;">Controls &lt;marquee scrollamount&gt; on the website. Default: 7.</span>
                                </div>

                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;"><i class="fa fa-mobile text-success"></i> Mobile App Scroll Speed</label>
                                    <div class="input-group">
                                        <input type="number" id="mobileSpeedInput" name="flash_news_speed_mobile" class="form-control" min="20" max="250" value="<?php echo htmlspecialchars($flash_news_speed_mobile ?? 65); ?>">
                                        <span class="input-group-addon">px/sec</span>
                                    </div>
                                    <span class="help-block" style="font-size: 12px;">Controls continuous animation speed on the mobile app. Default: 65 px/s.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Home Auto-Popup Modal Section -->
                        <div class="form-group" style="margin-top: 18px;">
                            <label><i class="fa fa-picture-o text-primary"></i> Home Pop-Up Window (Mobile & Web App)</label>
                            <p class="help-block">Configure whether the auto-popup modal displays Reaction Gallery posters, Latest News Window highlights, or both, along with optional Start &amp; End display dates.</p>
                            
                            <div class="checkbox" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; font-size: 14px;">
                                    <input type="checkbox" name="reaction_popup_enabled" value="1" <?php echo (!empty($reaction_popup_enabled) && $reaction_popup_enabled == 1) ? 'checked' : ''; ?>>
                                    Enable Auto-Popup Window on Home Page
                                </label>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;">Popup Content Source</label>
                                    <select name="reaction_popup_source" class="form-control">
                                        <option value="both" <?php echo (($reaction_popup_source ?? 'both') === 'both') ? 'selected' : ''; ?>>Both Reaction Gallery &amp; News Window</option>
                                        <option value="reaction_gallery" <?php echo (($reaction_popup_source ?? '') === 'reaction_gallery') ? 'selected' : ''; ?>>Reaction Gallery Poster Only</option>
                                        <option value="news" <?php echo (($reaction_popup_source ?? '') === 'news') ? 'selected' : ''; ?>>News Window Only</option>
                                    </select>
                                    <span class="help-block" style="font-size: 12px;">Includes poster/image &amp; direct navigation link inside the popup modal.</span>
                                </div>

                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;">Popup Trigger Delay (Seconds)</label>
                                    <div class="input-group">
                                        <input type="number" name="reaction_popup_duration" class="form-control" min="1" max="300" value="<?php echo htmlspecialchars($reaction_popup_duration ?? 10); ?>" placeholder="10">
                                        <span class="input-group-addon">seconds</span>
                                    </div>
                                    <span class="help-block" style="font-size: 12px;">Default is 10 seconds. Visitors can close the popup anytime.</span>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 6px;">
                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;"><i class="fa fa-calendar-check-o text-success"></i> Start Date (Optional)</label>
                                    <input type="date" name="reaction_popup_start_date" class="form-control" value="<?php echo htmlspecialchars($reaction_popup_start_date ?? ''); ?>">
                                    <span class="help-block" style="font-size: 12px;">Popup starts showing from this date. Leave blank for immediate start.</span>
                                </div>

                                <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                    <label style="font-weight: 500;"><i class="fa fa-calendar-times-o text-danger"></i> End Date (Optional)</label>
                                    <input type="date" name="reaction_popup_end_date" class="form-control" value="<?php echo htmlspecialchars($reaction_popup_end_date ?? ''); ?>">
                                    <span class="help-block" style="font-size: 12px;">Popup automatically hides after this date. Leave blank for no expiry.</span>
                                </div>
                            </div>
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

<script>
function applySpeedPreset(val) {
    var web = document.getElementById('webSpeedInput');
    var mobile = document.getElementById('mobileSpeedInput');
    if (!web || !mobile) return;
    if (val === 'slow') {
        web.value = 4;
        mobile.value = 45;
    } else if (val === 'medium') {
        web.value = 7;
        mobile.value = 65;
    } else if (val === 'fast') {
        web.value = 10;
        mobile.value = 95;
    } else if (val === 'very_fast') {
        web.value = 14;
        mobile.value = 130;
    }
}
</script>
