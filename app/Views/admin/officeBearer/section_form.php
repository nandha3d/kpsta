<?php
$section = $section ?? 'state';
$sectionMeta = $sectionMeta ?? [];
$formValues = is_array($formValues ?? null) ? $formValues : [];
$activeRole = $activeRole ?? '';
$activeDistrict = $activeDistrict ?? '';
$defaultDesignation = $defaultDesignation ?? '';
$defaultPosition = $defaultPosition ?? 25;

echo form_open('#', [
    'autocomplete' => 'off',
    'id' => 'save',
    'data-href' => isset($url) ? $url : '',
    'data-href-add' => $addUrl ?? '',
    'data-default-designation' => $defaultDesignation,
    'data-default-district' => $activeDistrict,
    'data-default-position' => $defaultPosition,
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo html_escape($title ?? 'Add'); ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">
    <input type="hidden" name="active_role" class="has-default-select" value="<?php echo html_escape($activeRole); ?>">
    <input type="hidden" name="active_district" class="has-default-select" value="<?php echo html_escape($activeDistrict); ?>">

    <!-- Locked Section Banner so editors never mix State, Former State, and District leaders -->
    <div class="alert" style="background: #eef2ff; border-left: 4px solid #6366f1; color: #312e81; padding: 9px 14px; margin-bottom: 16px; border-radius: 6px; font-size: 13px;">
        <strong>Section:</strong> <?php echo html_escape($sectionMeta['badge'] ?? ($sectionMeta['title'] ?? '')); ?>
        <span class="text-muted" style="margin-left: 8px; font-size: 12px;">(Entries saved here stay strictly inside <?php echo html_escape($sectionMeta['title'] ?? ''); ?>.)</span>
    </div>

    <?php if (isset($formValues['error'])) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error!</h4> <?php echo $formValues['error']; ?>
        </div>
    <?php }
    if (validation_errors()) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors(); ?>
        </div>
    <?php } ?>

    <div class="row">
        <div class="<?php echo $section === 'district' ? 'col-md-6' : 'col-md-12'; ?>">
            <div class="<?php echo form_error('designation') ? 'form-group has-error' : 'form-group'; ?>">
                <label for="section_designation">Role / Designation *</label>
                <?php
                $selectedDesig = isset($formValues['designation']) ? $formValues['designation'] : $defaultDesignation;
                echo form_dropdown(
                    'designation',
                    $designation ?? [],
                    $selectedDesig,
                    'class="form-control has-default-select" id="section_designation" required="required" style="width: 100%;"'
                );
                ?>
            </div>
        </div>

        <?php if ($section === 'district') { ?>
            <div class="col-md-6">
                <div class="<?php echo form_error('section_heading') ? 'form-group has-error' : 'form-group'; ?>">
                    <label for="section_district">District *</label>
                    <?php
                    $selectedDist = isset($formValues['section_heading']) && $formValues['section_heading'] !== ''
                        ? $formValues['section_heading']
                        : $activeDistrict;
                    echo form_dropdown(
                        'section_heading',
                        $districts ?? [],
                        $selectedDist,
                        'class="form-control has-default-select" id="section_district" required="required" style="width: 100%;"'
                    );
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="<?php echo form_error('name') ? 'form-group has-error' : 'form-group'; ?>">
                <label for="name">Leader Name *</label>
                <input class="form-control" name="name" placeholder="Enter Full Name" value="<?php echo html_escape((string)($formValues['name'] ?? '')); ?>" required="required">
            </div>
        </div>
        <div class="col-md-3">
            <div class="<?php echo form_error('phone') ? 'form-group has-error' : 'form-group'; ?>">
                <label for="phone">Phone</label>
                <input class="form-control" name="phone" placeholder="Phone Number" value="<?php echo html_escape((string)($formValues['phone'] ?? '')); ?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="<?php echo form_error('email') ? 'form-group has-error' : 'form-group'; ?>">
                <label for="email">Email</label>
                <input class="form-control" name="email" placeholder="Email Address" value="<?php echo html_escape((string)($formValues['email'] ?? '')); ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="<?php echo form_error('year') ? 'form-group has-error' : 'form-group'; ?>">
                <label for="year">Term / Year <?php echo $section === 'former' ? '(e.g. 2018-2021)' : '(Optional)'; ?></label>
                <input type="text" class="form-control" name="year" id="year" list="section-year-suggestions"
                       placeholder="e.g. 2024-2025 or 2024"
                       value="<?php echo html_escape((string)($formValues['year'] ?? '')); ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="section_position">Display Order</label>
                <?php
                $curPos = isset($formValues['position']) && is_numeric($formValues['position'])
                    ? (int)$formValues['position']
                    : (int)$defaultPosition;
                $posLabels = [];
                for ($p = 1; $p <= 25; $p++) {
                    $posLabels[$p] = (string)$p;
                }
                echo form_dropdown('position', $posLabels, $curPos, 'class="form-control has-default-select" id="section_position" style="width: 100%;"');
                ?>
            </div>
        </div>
    </div>

    <datalist id="section-year-suggestions">
        <?php
        $cy = (int)date('Y');
        for ($i = $cy + 1; $i >= 1960; $i--) {
            $yr2 = $i . '-' . ($i + 1);
            echo "<option value=\"{$yr2}\">{$yr2}</option>\n";
        }
        for ($i = $cy + 1; $i >= 1960; $i--) {
            echo "<option value=\"{$i}\">{$i}</option>\n";
        }
        ?>
    </datalist>

    <div class="form-group">
        <label for="photo">Upload Photo</label>
        <div style="margin-bottom: 12px;">
            <div class="btn btn-primary btn-file" tabindex="500">
                <i class="glyphicon glyphicon-folder-open"></i>&nbsp; <span class="hidden-xs">Browse …</span>
                <input type="file" data-show-preview="true" class="file" name="image">
            </div>
            <button class="btn btn-default fileinput-remove-button" onclick="document.querySelector('#modal input[name=image]').value=''; return false;" title="Clear selected file" type="button">
                <i class="glyphicon glyphicon-trash"></i> Remove
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                <label class="col-md-12">Original Image</label>
                <div class="pull-left thumbnail-div">
                    <img src="<?php echo !empty($formValues['image']) ? base_url(OFFICE_BEARER . '/' . $formValues['image']) . '?t=' . time() : ''; ?>" id="thumbnail" style="max-width:100%;max-height:260px;" alt="">
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="col-md-12">Thumbnail Preview</label>
                <div style="float:left; position:relative; overflow:hidden; width:173px; height:214px;" class="thumbnail_preview_loader">
                    <img style="position: relative; max-width: none !important;" id="thumbnail_preview" src="<?php echo !empty($formValues['image']) ? base_url(OFFICE_BEARER . '/' . $formValues['image']) . '?t=' . time() : ''; ?>" alt="">
                    <input type="hidden" id="x1">
                    <input type="hidden" id="y1">
                    <input type="hidden" id="x2">
                    <input type="hidden" id="y2">
                    <input type="hidden" id="w">
                    <input type="hidden" id="h">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="content">Published</label>
        <?php $isPub = isset($formValues['is_publish']) ? (int)$formValues['is_publish'] : 1; ?>
        <div class="btn-group margin" data-toggle="buttons">
            <label class="btn btn-default <?php echo $isPub === 1 ? 'active' : ''; ?>" data-active-class="success">
                <input type="radio" name="is_publish" value="1" autocomplete="off" <?php echo $isPub === 1 ? 'checked' : ''; ?>> Yes
            </label>
            <label class="btn btn-default <?php echo $isPub === 0 ? 'active' : ''; ?>" data-active-class="danger">
                <input type="radio" name="is_publish" value="0" autocomplete="off" <?php echo $isPub === 0 ? 'checked' : ''; ?>> No
            </label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger"></i> Close</button>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
</div>
<?php echo form_close(); ?>
