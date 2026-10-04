<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">
    <?php if (isset($formValues['error'])) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $formValues['error'] ?>  
        </div>
        <?php
    }
    if (validation_errors()) {
        ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>


    <div class="form-group">
        <label for="level">Category Level</label>
        <?php $selectedLevel = isset($formValues['level']) ? $formValues['level'] : 'State'; ?>
        <select name="level" id="ob_level" class="form-control" style="width: 100%;">
            <option value="State" <?php echo $selectedLevel == 'State' ? 'selected' : ''; ?>>State Office Bearer</option>
            <option value="District" <?php echo $selectedLevel == 'District' ? 'selected' : ''; ?>>District Office Bearer</option>
        </select>
    </div>

    <?php $class = form_error('designation') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['designation']) ? $formValues['designation'] : '' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">Designation</label>
        <?php echo form_dropdown('designation', $designation, $selected, 'class="form-control select2-category" style="width: 100%;" id="main_designation"'); ?>
    </div>

    <?php $class = form_error('section_heading') ? 'form-group has-error' : 'form-group' ?>
    <?php $selectedHeading = isset($formValues['section_heading']) ? $formValues['section_heading'] : '' ?>
    <div class="<?php echo $class ?>">
        <label for="section_heading">Section Heading (Optional)</label>
        <div id="sh_state" style="<?php echo $selectedLevel == 'State' ? 'display:block;' : 'display:none;'; ?>">
            <?php echo form_dropdown('section_heading_state', $section_headings, $selectedLevel == 'State' ? $selectedHeading : '', 'class="form-control select2-category" style="width: 100%;" id="sh_state_select"'); ?>
        </div>
        <div id="sh_district" style="<?php echo $selectedLevel == 'District' ? 'display:block;' : 'display:none;'; ?>">
            <?php echo form_dropdown('section_heading_district', $districts, $selectedLevel == 'District' ? $selectedHeading : '', 'class="form-control select2-category" style="width: 100%;" id="sh_district_select"'); ?>
        </div>
        <input type="hidden" name="section_heading" id="actual_section_heading" value="<?php echo $selectedHeading; ?>">
    </div>
    
    <script>
    $(document).ready(function() {
        function updateSectionHeading() {
            var level = $('#ob_level').val();
            if (level === 'State') {
                $('#sh_state').show();
                $('#sh_district').hide();
                $('#actual_section_heading').val($('#sh_state_select').val());
            } else {
                $('#sh_state').hide();
                $('#sh_district').show();
                $('#actual_section_heading').val($('#sh_district_select').val());
            }
        }
        
        $('#ob_level').change(updateSectionHeading);
        $('#sh_state_select, #sh_district_select').change(function() {
            updateSectionHeading();
        });
    });
    </script>

    <div class="row">
        <div class="col-md-6">
            <?php $class = form_error('name') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="name">Leader Name</label>
                <input class="form-control" name="name" placeholder="Enter Full Name" value="<?php echo isset($formValues['name']) ? $formValues['name'] : '' ?>" required="required">
            </div>
        </div>
        <div class="col-md-3">
            <?php $class = form_error('phone') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="phone">Phone</label>
                <input class="form-control" name="phone" placeholder="Enter Phone Number" value="<?php echo isset($formValues['phone']) ? $formValues['phone'] : '' ?>">
            </div>
        </div>
        <div class="col-md-3">
            <?php $class = form_error('email') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="email">Email</label>
                <input class="form-control" name="email" placeholder="Enter Email" value="<?php echo isset($formValues['email']) ? $formValues['email'] : '' ?>">
            </div>
        </div>
    </div>

    <!-- Real-time Duplicate / Existing Person Alert -->
    <div id="person_dup_alert" class="alert alert-info" style="display: none; margin-top: 5px; margin-bottom: 15px; border-left: 4px solid #0284c7; background: #f0f9ff; color: #0369a1; padding: 10px 14px; border-radius: 6px;">
        <div style="font-weight: 700; font-size: 13px;"><i class="fa fa-info-circle"></i> Existing Leader Profile Found</div>
        <div id="person_dup_message" style="font-size: 12px; margin-top: 4px; line-height: 1.4;"></div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php $class = form_error('year') ? 'form-group has-error' : 'form-group' ?>
            <div class="<?php echo $class ?>">
                <label for="year">Year / Term</label>
                <input type="text" class="form-control" name="year" id="year" placeholder="e.g. 2024-2025 or 2024" list="year-suggestions" value="<?php echo isset($formValues['year']) ? html_escape($formValues['year']) : ''; ?>">
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Single year (e.g. 2024) or period (2024-2025)</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="position">Position / Rank Order for this Year</label>
                <?php $formValues['position'] = isset($formValues['position']) ? $formValues['position'] : 25; ?>
                <?php 
                    $posLabels = [];
                    for ($p = 1; $p <= 25; $p++) {
                        $label = $p === 1 ? '1 (Top / President)' : ($p === 2 ? '2 (General Secretary)' : ($p === 3 ? '3 (Treasurer)' : ($p === 4 ? '4 (Senior Vice President)' : ($p === 6 ? '6 (Vice President)' : ($p === 7 ? '7 (Secretary)' : ($p === 9 ? '9 (Secretariat Member)' : (string)$p))))));
                        $posLabels[$p] = $label;
                    }
                    $isManualMainPos = (!empty($formValues['id']) && isset($formValues['position']) && (int)$formValues['position'] !== 25) ? '1' : '0';
                    echo form_dropdown('position', $posLabels, $formValues['position'], 'class="form-control" style="width: 100%;" id="main_position" data-manual-pos="' . $isManualMainPos . '"'); 
                ?>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                    Order in that year (1 = President, 2 = Gen Sec, etc.)
                    <span id="main_pos_manual_hint" style="<?php echo $isManualMainPos === '1' ? 'display:inline;' : 'display:none;'; ?> color: #b45309; margin-left: 6px;">
                        (Manual &bull; <a href="javascript:void(0)" id="btn_reset_main_auto_pos" style="text-decoration: underline; color: #0284c7;">auto-sync</a>)
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="is_former">Leader Status</label>
                <div class="btn-group" data-toggle="buttons" style="display:block;">
                    <label class="btn btn-default <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 0) ? 'active' : '' ?>" data-active-class="success">
                        <input type="radio" name="is_former" value="0" autocomplete="off" <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 0) ? 'checked' : '' ?> > Active
                    </label>
                    <label class="btn btn-default <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 1 ) ? 'active' : '' ?>" data-active-class="danger">
                        <input type="radio" name="is_former" value="1" autocomplete="off" <?php echo (isset($formValues['is_former']) && $formValues['is_former'] == 1 ) ? 'checked' : '' ?> > Former
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Datalist Suggestions for Year, Designation, District -->
    <datalist id="year-suggestions">
        <?php 
            $current_year = (int)date('Y');
            // 1. Single individual years (e.g. 2027, 2026, 2025 ... down to 1960)
            for($i = $current_year + 1; $i >= 1960; $i--) {
                echo "<option value=\"$i\">$i</option>\n";
            }
            // 2. Consecutive 2-year periods (e.g. 2024-2025 ... down to 1960-1961)
            for($i = $current_year + 1; $i >= 1960; $i--) {
                $yr2 = $i . '-' . ($i+1);
                echo "<option value=\"$yr2\">$yr2</option>\n";
            }
            // 3. Multi-year spans (e.g. 3-year term: 2021-2024, 2018-2021, etc.)
            for($i = $current_year + 1; $i >= 1960; $i--) {
                $yr3 = ($i-3) . '-' . $i;
                echo "<option value=\"$yr3\">$yr3</option>\n";
            }
        ?>
    </datalist>
    <datalist id="desig-suggestions">
        <?php if(!empty($designation)) { foreach($designation as $k => $d) { if(!empty($d) && strpos($d, '---') === false) { ?>
            <option value="<?php echo htmlspecialchars($d); ?>">
        <?php } } } ?>
    </datalist>
    <datalist id="district-suggestions">
        <?php if(!empty($districts)) { foreach($districts as $k => $d) { if(!empty($d) && strpos($d, '---') === false) { ?>
            <option value="<?php echo htmlspecialchars($d); ?>">
        <?php } } } ?>
    </datalist>

    <!-- Additional / Previous Positions Section -->
    <div class="form-group" style="margin-top: 15px; margin-bottom: 20px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
            <div>
                <label style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0;">
                    <i class="fa fa-history text-primary" style="margin-right: 4px;"></i> Additional / Previous Positions Held
                </label>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                    Add previous positions with period, designation, level, and position order (e.g. served as General Secretary in 2015-2018).
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="btn_add_prev_pos" style="font-weight: 600; padding: 5px 12px;">
                <i class="fa fa-plus"></i> Add Position
            </button>
        </div>

        <div id="previous_positions_list">
            <!-- Position rows added dynamically via JS -->
        </div>
        <div id="no_prev_positions_msg" style="text-align: center; padding: 12px; color: #94a3b8; font-size: 12px; font-style: italic;">
            No additional positions added yet. Click "+ Add Position" to add past terms.
        </div>
    </div>


    <?php $class = form_error('content') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label for="photo">Upload Photo </label>
        <div style="margin-bottom: 15px;">
            <div class="btn btn-primary btn-file" tabindex="500">
                <i class="glyphicon glyphicon-folder-open"></i>&nbsp; <span class="hidden-xs">Browse …</span>
                <input type="file" data-show-preview="true" class="file" name="image" >
            </div>
            <!-- Assuming there's a script that clears the file input, we keep a remove button just in case -->
            <button class="btn btn-default fileinput-remove-button" onclick="document.querySelector('input[name=image]').value=''; return false;" title="Clear selected files" type="button"><i class="glyphicon glyphicon-trash"></i> Remove</button>
        </div>
    </div>


    <div class="row" >
        <div class="col-md-7" >
            <div class="form-group"  >
                <label class="col-md-12">Original Image </label>
                <div class="pull-left thumbnail-div">
                    <img src="<?php echo isset($formValues['image']) ? base_url(OFFICE_BEARER . '/' . $formValues['image']) . '?t=' . time() : '' ?>" id="thumbnail" style="max-width:100%;max-height: 300px;"  alt=""/>
                </div>
            </div>
        </div>


        <div class="col-md-5">
            <div class="form-group">
                <label class="col-md-12">Thumbnail Preview </label>
                <div style=" float:left; position:relative; overflow:hidden; width:173px; height:214px;" class="thumbnail_preview_loader">
                    <img  style="position: relative; max-width: none !important;" id="thumbnail_preview"  src="<?php echo isset($formValues['image']) ? base_url(OFFICE_BEARER . '/' . $formValues['image']) . '?t=' . time() : '' ?>" />
                    <input type="hidden"  id="x1">
                    <input type="hidden"  id="y1">
                    <input type="hidden"  id="x2">
                    <input type="hidden"  id="y2">
                    <input type="hidden"  id="w">
                    <input type="hidden"  id="h">
                </div>
            </div>
        </div>
    </div>



    <div class="form-group">
        <label for="content">Published</label>
        <div class="btn-group margin" data-toggle="buttons">
            <label class="btn btn-default <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                <input type="radio" name="is_publish" value="1" autocomplete="off" <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 1) ? 'checked' : '' ?> > Yes
            </label>
            <label class="btn  btn-default <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 0 ) ? 'active' : '' ?>" data-active-class="danger">
                <input type="radio" name="is_publish" value="0" id="option2" autocomplete="off" <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 0 ) ? 'checked' : '' ?> > No
            </label>
        </div>

    </div>


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
    <button type="submit" class="btn btn-primary"  ><i class="fa fa-save "></i> Save</button>
</div>
<?php echo form_close(); ?>


<script type="text/javascript">
$(document).ready(function() {
    var existingPositions = <?php 
        $posArr = [];
        if (!empty($formValues['previous_positions'])) {
            if (is_array($formValues['previous_positions'])) {
                $posArr = $formValues['previous_positions'];
            } else if (is_string($formValues['previous_positions'])) {
                $dec = json_decode($formValues['previous_positions'], true);
                if (is_array($dec)) $posArr = $dec;
            }
        }
        echo json_encode(array_values($posArr)); 
    ?>;
    var currentPersonId = <?php echo isset($formValues['id']) ? (int)$formValues['id'] : 0; ?>;

    var posIndex = 0;

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getSuggestedPositionForDesignation(desigText) {
        if (!desigText) return null;
        var d = desigText.toLowerCase().trim();
        if (d.indexOf('president') !== -1 && d.indexOf('vice') === -1) return 1;
        if (d.indexOf('general secretary') !== -1 || d.indexOf('gen secretary') !== -1 || d.indexOf('gen. secretary') !== -1) return 2;
        if (d.indexOf('treasurer') !== -1) return 3;
        if (d.indexOf('senior vice president') !== -1 || d.indexOf('sr. vice president') !== -1 || d.indexOf('sr vice president') !== -1) return 4;
        if (d.indexOf('associate general secretary') !== -1 || d.indexOf('assoc. general secretary') !== -1) return 5;
        if (d.indexOf('vice president') !== -1) return 6;
        if (d.indexOf('secretary') !== -1 && d.indexOf('general') === -1 && d.indexOf('associate') === -1) return 7;
        if (d.indexOf('secretariat') !== -1 || d.indexOf('secretariate') !== -1) return 8;
        if (d.indexOf('executive') !== -1 || d.indexOf('committee') !== -1) return 9;
        return null;
    }

    function updateCardHeaderTitle($row) {
        var idx = parseInt($row.data('idx'), 10) + 1;
        var desig = $row.find('.pos-desig-input').val() ? $row.find('.pos-desig-input').val().trim() : '';
        var yr = $row.find('.pos-year-input').val() ? $row.find('.pos-year-input').val().trim() : '';
        var lvl = $row.find('.pos-level-select').val();
        var sec = $row.find('.pos-sec-input').val() ? $row.find('.pos-sec-input').val().trim() : '';
        var isEnabled = $row.find('.pos-enabled-input').val() === '1';

        var title = '<i class="fa fa-briefcase text-primary" style="margin-right: 6px;"></i> ';
        if (desig) {
            title += '<strong style="color:#0f172a;">' + escapeHtml(desig) + '</strong>';
        } else {
            title += '<span style="color:#64748b;">Additional Role #' + idx + ' (Click to edit)</span>';
        }

        if (yr) {
            title += ' <span class="text-muted" style="font-weight: 500; font-size: 12px;">(' + escapeHtml(yr) + ')</span>';
        }

        if (lvl && lvl !== 'State') {
            var placeText = sec ? (lvl + ' - ' + sec) : lvl;
            title += ' <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 10px; font-weight: 600; margin-left: 6px;">' + escapeHtml(placeText) + '</span>';
        } else if (sec) {
            title += ' <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 10px; font-weight: 600; margin-left: 6px;">' + escapeHtml(sec) + '</span>';
        }

        if (!isEnabled) {
            title += ' <span class="badge" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-size: 10px; font-weight: 600; margin-left: 6px;"><i class="fa fa-ban"></i> Disabled</span>';
        }

        $row.find('.pos-header-title').html(title);
    }

    window.togglePositionActive = function(btn) {
        var $btn = $(btn);
        var $row = $btn.closest('.prev-pos-row');
        var $input = $row.find('.pos-enabled-input');
        var currentVal = $input.val() === '1';
        var newVal = !currentVal;

        $input.val(newVal ? '1' : '0');
        if (newVal) {
            $btn.css({
                'background': '#ecfdf5',
                'color': '#047857',
                'border-color': '#86efac'
            });
            $btn.find('i').removeClass('fa-toggle-off text-muted').addClass('fa-toggle-on text-success');
            $btn.find('.enable-label').text('Active');
            $btn.attr('title', 'Position is active on public page. Click to disable.');
            $row.removeClass('pos-disabled').css({
                'opacity': '1',
                'border-left-color': '#0284c7'
            });
        } else {
            $btn.css({
                'background': '#f8fafc',
                'color': '#64748b',
                'border-color': '#cbd5e1'
            });
            $btn.find('i').removeClass('fa-toggle-on text-success').addClass('fa-toggle-off text-muted');
            $btn.find('.enable-label').text('Disabled');
            $btn.attr('title', 'Position is disabled on public page. Click to enable.');
            $row.addClass('pos-disabled').css({
                'opacity': '0.75',
                'border-left-color': '#94a3b8'
            });
        }
        updateCardHeaderTitle($row);
    };

    window.removePositionRow = function(btn) {
        var $btn = $(btn);
        var $row = $btn.closest('.prev-pos-row');
        $row.slideUp(180, function() {
            $row.remove();
            if ($('#previous_positions_list .prev-pos-row').length === 0) {
                $('#no_prev_positions_msg').show();
            }
        });
    };

    function renderPositionRow(pos, openByDefault) {
        pos = pos || { level: 'State', designation: '', section_heading: '', year: '', position: 25, is_enabled: 1 };
        var idx = posIndex++;
        var lvl = pos.level || 'State';
        var desig = escapeHtml(pos.designation || '');
        var sec = escapeHtml(pos.section_heading || '');
        var yr = escapeHtml(pos.year || '');
        var isEnabled = (pos.is_enabled === undefined || pos.is_enabled === null || pos.is_enabled === '1' || pos.is_enabled === 1 || pos.is_enabled === true);
        var curPos = (pos.position !== undefined && pos.position !== '' && pos.position !== null) ? parseInt(pos.position, 10) : (getSuggestedPositionForDesignation(pos.designation) || 25);
        var suggestedForCur = getSuggestedPositionForDesignation(pos.designation);
        var isManualPos = (pos.position !== undefined && pos.position !== '' && pos.position !== null && suggestedForCur !== null && parseInt(pos.position, 10) !== suggestedForCur);
        var isExpanded = (openByDefault !== undefined) ? openByDefault : true;

        var posOptions = '';
        for (var p = 1; p <= 25; p++) {
            var label = p === 1 ? '1 (Top / President)' : (p === 2 ? '2 (General Secretary)' : (p === 3 ? '3 (Treasurer)' : (p === 4 ? '4 (Senior Vice President)' : (p === 6 ? '6 (Vice President)' : (p === 7 ? '7 (Secretary)' : (p === 9 ? '9 (Secretariat Member)' : p))))));
            posOptions += '<option value="' + p + '"' + (curPos === p ? ' selected' : '') + '>' + label + '</option>';
        }

        var html = '<div class="prev-pos-row ' + (isEnabled ? '' : 'pos-disabled') + '" data-idx="' + idx + '" style="background: #ffffff; border: 1px solid #cbd5e1; border-left: 4px solid ' + (isEnabled ? '#0284c7' : '#94a3b8') + '; border-radius: 6px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; transition: all 0.2s; ' + (isEnabled ? '' : 'opacity: 0.75;') + '">' +
            '<!-- Toggleable Section Header -->' +
            '<div class="pos-card-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border-bottom: ' + (isExpanded ? '1px solid #e2e8f0' : 'none') + '; user-select: none;">' +
                '<div style="display: flex; align-items: center; gap: 8px; flex: 1;">' +
                    '<i class="fa ' + (isExpanded ? 'fa-chevron-down' : 'fa-chevron-right') + ' pos-chevron text-primary" style="font-size: 12px; width: 14px;"></i>' +
                    '<span class="pos-header-title" style="font-weight: 600; font-size: 13px; color: #1e293b;"></span>' +
                '</div>' +
                '<div class="pos-card-actions" style="display: flex; align-items: center; gap: 8px;">' +
                    '<input type="hidden" name="previous_positions[' + idx + '][is_enabled]" class="pos-enabled-input" value="' + (isEnabled ? '1' : '0') + '">' +
                    '<button type="button" class="btn btn-xs btn-toggle-enable" onclick="event.stopPropagation(); window.togglePositionActive(this);" style="cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; padding: 4px 10px; border-radius: 4px; border: 1px solid ' + (isEnabled ? '#86efac' : '#cbd5e1') + '; background: ' + (isEnabled ? '#ecfdf5' : '#f8fafc') + '; color: ' + (isEnabled ? '#047857' : '#64748b') + ';" title="' + (isEnabled ? 'Position is active on public page. Click to disable.' : 'Position is disabled on public page. Click to enable.') + '">' +
                        '<i class="fa ' + (isEnabled ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted') + '" style="font-size: 14px;"></i> <span class="enable-label">' + (isEnabled ? 'Active' : 'Disabled') + '</span>' +
                    '</button>' +
                    '<button type="button" class="btn btn-xs btn-danger btn-remove-pos" onclick="event.stopPropagation(); window.removePositionRow(this);" style="cursor: pointer; font-size: 11px; padding: 4px 10px; border-radius: 4px;" title="Remove this position completely">' +
                        '<i class="fa fa-trash"></i> Remove' +
                    '</button>' +
                '</div>' +
            '</div>' +

            '<!-- Toggleable Section Body -->' +
            '<div class="pos-card-body" style="padding: 14px; background: #ffffff; ' + (isExpanded ? '' : 'display: none;') + '">' +
                '<!-- Line 1: Classification & Designation (3 spacious columns) -->' +
                '<div class="row" style="margin-bottom: 10px;">' +
                    '<div class="col-sm-4">' +
                        '<label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 3px;">Category Level</label>' +
                        '<select name="previous_positions[' + idx + '][level]" class="form-control input-sm pos-level-select">' +
                            '<option value="State"' + (lvl === 'State' ? ' selected' : '') + '>State Level</option>' +
                            '<option value="District"' + (lvl === 'District' ? ' selected' : '') + '>District Level</option>' +
                            '<option value="Educational District"' + (lvl === 'Educational District' ? ' selected' : '') + '>Educational District</option>' +
                            '<option value="Sub District"' + (lvl === 'Sub District' ? ' selected' : '') + '>Sub District</option>' +
                        '</select>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                        '<label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 3px;">District / Section</label>' +
                        '<input type="text" name="previous_positions[' + idx + '][section_heading]" class="form-control input-sm pos-sec-input" list="district-suggestions" placeholder="e.g. Kollam / State" value="' + sec + '">' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                        '<label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 3px;">Designation</label>' +
                        '<input type="text" name="previous_positions[' + idx + '][designation]" class="form-control input-sm pos-desig-input" list="desig-suggestions" placeholder="e.g. President" value="' + desig + '">' +
                    '</div>' +
                '</div>' +
                '<!-- Line 2: Year / Period and Position / Rank Order in that Year (2 spacious columns) -->' +
                '<div class="row">' +
                    '<div class="col-sm-6">' +
                        '<label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 3px;">Period / Year for this Position</label>' +
                        '<input type="text" name="previous_positions[' + idx + '][year]" class="form-control input-sm pos-year-input" list="year-suggestions" placeholder="e.g. 2012-2015 or 2024" value="' + yr + '">' +
                        '<div style="font-size: 11px; color: #64748b; margin-top: 2px;">Enter single year (e.g. 2024) or span (e.g. 2012-2015)</div>' +
                    '</div>' +
                    '<div class="col-sm-6">' +
                        '<label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 3px;">' +
                            'Position / Order in that Year ' +
                            '<span class="pos-manual-hint" style="' + (isManualPos ? 'display:inline;' : 'display:none;') + ' color: #b45309; font-weight: normal; font-size: 11px;">' +
                                '(Manual &bull; <a href="javascript:void(0)" class="btn-reset-pos-auto" style="text-decoration: underline; color: #0284c7;">auto-sync</a>)' +
                            '</span>' +
                        '</label>' +
                        '<select name="previous_positions[' + idx + '][position]" class="form-control input-sm pos-position-select" data-manual-pos="' + (isManualPos ? '1' : '0') + '">' +
                            posOptions +
                        '</select>' +
                        '<div style="font-size: 11px; color: #64748b; margin-top: 2px;">Auto-sets with designation (or override manually)</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        var $newRow = $(html);
        $('#previous_positions_list').append($newRow);
        updateCardHeaderTitle($newRow);
        $('#no_prev_positions_msg').hide();
    }

    if (existingPositions && existingPositions.length > 0) {
        for (var i = 0; i < existingPositions.length; i++) {
            // Keep first position expanded, collapse others for a clean accordion
            renderPositionRow(existingPositions[i], i === 0);
        }
    }

    // Toggle accordion section
    $('#previous_positions_list').on('click', '.pos-card-header', function(e) {
        if ($(e.target).closest('.pos-card-actions, .btn-toggle-enable, .btn-remove-pos').length) {
            return;
        }
        var $row = $(this).closest('.prev-pos-row');
        var $body = $row.find('.pos-card-body');
        var $chevron = $(this).find('.pos-chevron');
        var $header = $(this);

        $body.slideToggle(180, function() {
            if ($body.is(':visible')) {
                $chevron.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                $header.css('border-bottom', '1px solid #e2e8f0');
            } else {
                $chevron.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                $header.css('border-bottom', 'none');
            }
        });
    });

    // Delegated fallbacks
    $(document).off('click.toggleActive', '.btn-toggle-enable').on('click.toggleActive', '.btn-toggle-enable', function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.togglePositionActive(this);
    });

    $(document).off('click.removePos', '.btn-remove-pos').on('click.removePos', '.btn-remove-pos', function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.removePositionRow(this);
    });

    // Designation changed -> auto-update position until changed manually
    $('#previous_positions_list').on('input change', '.pos-desig-input', function() {
        var $row = $(this).closest('.prev-pos-row');
        var $posSelect = $row.find('.pos-position-select');
        var desigVal = $(this).val().trim();

        if ($posSelect.data('manual-pos') !== '1' && $posSelect.data('manual-pos') !== 1) {
            var suggested = getSuggestedPositionForDesignation(desigVal);
            if (suggested !== null) {
                $posSelect.val(suggested).trigger('change.auto');
            }
        }
        updateCardHeaderTitle($row);
    });

    // When user manually alters position dropdown
    $('#previous_positions_list').on('change', '.pos-position-select', function(e) {
        if (e.namespace === 'auto') return;
        $(this).data('manual-pos', '1');
        var $row = $(this).closest('.prev-pos-row');
        $row.find('.pos-manual-hint').show();
    });

    // Reset auto-sync for previous position
    $('#previous_positions_list').on('click', '.btn-reset-pos-auto', function(e) {
        e.preventDefault();
        var $row = $(this).closest('.prev-pos-row');
        var $posSelect = $row.find('.pos-position-select');
        $posSelect.data('manual-pos', '0');
        var desigVal = $row.find('.pos-desig-input').val().trim();
        var suggested = getSuggestedPositionForDesignation(desigVal);
        if (suggested !== null) {
            $posSelect.val(suggested).trigger('change.auto');
        }
        $row.find('.pos-manual-hint').hide();
    });

    // Dynamic header title update when typing year, level, or section
    $('#previous_positions_list').on('input change', '.pos-year-input, .pos-level-select, .pos-sec-input', function() {
        var $row = $(this).closest('.prev-pos-row');
        updateCardHeaderTitle($row);
    });

    // Main form designation auto-updates main position and section heading until manually changed
    $(document).on('change select2:select', '#main_designation', function() {
        var desigText = $('#main_designation option:selected').text();
        if (!desigText || desigText === 'Select' || desigText === '-- Select --') {
            desigText = $('#main_designation').val();
        }
        var $mainPos = $('#main_position');
        if ($mainPos.data('manual-pos') !== '1' && $mainPos.data('manual-pos') !== 1) {
            var suggested = getSuggestedPositionForDesignation(desigText);
            if (suggested !== null) {
                $mainPos.val(suggested).trigger('change.auto');
            }
        }

        // Auto-select matching Section Heading for State level
        if ($('#ob_level').val() === 'State') {
            var dt = (desigText || '').toLowerCase();
            var targetHeading = '';
            if ((dt.indexOf('president') !== -1 && dt.indexOf('vice') === -1) || dt.indexOf('general secretary') !== -1 || dt.indexOf('treasurer') !== -1) {
                targetHeading = 'President / General Secretary / Treasurer';
            } else if (dt.indexOf('senior vice') !== -1 || dt.indexOf('associate general') !== -1) {
                targetHeading = 'Senior Vice President / Associate General Secretary';
            } else if (dt.indexOf('vice president') !== -1) {
                targetHeading = 'Vice President';
            } else if (dt.indexOf('secretariat') !== -1 || dt.indexOf('secretariate') !== -1) {
                targetHeading = 'Secretariate Members';
            } else if (dt.indexOf('secretary') !== -1 && dt.indexOf('general') === -1 && dt.indexOf('associate') === -1) {
                targetHeading = 'Secretary';
            }
            if (targetHeading) {
                $('#sh_state_select').val(targetHeading).trigger('change');
                $('#actual_section_heading').val(targetHeading);
            }
        }
    });

    $('#main_position').on('change', function(e) {
        if (e.namespace === 'auto') return;
        $(this).data('manual-pos', '1');
        $('#main_pos_manual_hint').show();
    });

    $('#btn_reset_main_auto_pos').on('click', function(e) {
        e.preventDefault();
        var $mainPos = $('#main_position');
        $mainPos.data('manual-pos', '0');
        var desigText = $('#main_designation option:selected').text();
        var suggested = getSuggestedPositionForDesignation(desigText);
        if (suggested !== null) {
            $mainPos.val(suggested).trigger('change.auto');
        }
        $('#main_pos_manual_hint').hide();
    });

    $('#btn_add_prev_pos').on('click', function(e) {
        e.preventDefault();
        // Collapse previous ones to keep it tidy
        $('#previous_positions_list .prev-pos-row .pos-card-body').slideUp(180);
        $('#previous_positions_list .prev-pos-row .pos-chevron').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        $('#previous_positions_list .prev-pos-row .pos-card-header').css('border-bottom', 'none');

        // Add new expanded row
        renderPositionRow(null, true);

        // Auto-focus designation input on the newly added section
        setTimeout(function() {
            var $last = $('#previous_positions_list .prev-pos-row:last-child');
            $last.find('.pos-desig-input').focus();
            if ($last.length && $last.offset()) {
                $last[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 150);
    });

    $('#previous_positions_list').on('click', '.btn-remove-pos', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $row = $(this).closest('.prev-pos-row');
        $row.slideUp(180, function() {
            $row.remove();
            if ($('#previous_positions_list .prev-pos-row').length === 0) {
                $('#no_prev_positions_msg').show();
            }
        });
    });

    // Real-time check for duplicate person by Name and Phone
    var checkTimer = null;
    function checkPersonDuplicate() {
        var name = $('input[name="name"]').val() ? $('input[name="name"]').val().trim() : '';
        var phone = $('input[name="phone"]').val() ? $('input[name="phone"]').val().trim() : '';

        if (name.length >= 2 && phone.length >= 5) {
            $.ajax({
                url: '<?php echo base_url("admin/office_bearer/check_person"); ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    name: name,
                    phone: phone,
                    exclude_id: currentPersonId
                },
                success: function(res) {
                    if (res && res.exists && res.person) {
                        var p = res.person;
                        var msg = '<strong>' + escapeHtml(p.name) + '</strong> (Phone: ' + escapeHtml(p.phone) + ') is already in the database.';
                        if (p.designation) {
                            msg += '<br>Current Role: <em>' + escapeHtml(p.designation) + (p.year ? ' (' + escapeHtml(p.year) + ')' : '') + '</em>';
                        }
                        if (p.previous_positions && p.previous_positions.length > 0) {
                            msg += '<br>Has ' + p.previous_positions.length + ' previous position(s) already recorded.';
                        }
                        msg += '<br><span style="color:#0284c7; font-weight:600;">Saving this form will merge and add any new position(s) to this person\'s existing profile without creating a duplicate record.</span>';
                        $('#person_dup_message').html(msg);
                        $('#person_dup_alert').slideDown(200);
                    } else {
                        $('#person_dup_alert').slideUp(200);
                    }
                }
            });
        } else {
            $('#person_dup_alert').slideUp(200);
        }
    }

    $('input[name="name"], input[name="phone"]').on('input blur', function() {
        clearTimeout(checkTimer);
        checkTimer = setTimeout(checkPersonDuplicate, 400);
    });
});
</script>