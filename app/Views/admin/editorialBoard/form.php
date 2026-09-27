<form action="<?php echo $url ?>" data-href="<?php echo $url ?>" data-href-add="<?php echo $addUrl ?>" method="post" id="save" class="editorialBoardForm">
    <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 6px 6px 0 0;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" style="font-weight: 700; color: #1e293b;">
            <?php echo isset($title) ? $title : 'Add Member' ?>
        </h4>
    </div>

    <div class="modal-body" style="padding: 24px;">
        <div class="form-group">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">
                Member Name <span class="text-danger">*</span>
            </label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Abdul Majeed K" value="<?php echo isset($formValues['name']) ? htmlspecialchars($formValues['name']) : ''; ?>" required style="border-radius: 6px; height: 38px;">
        </div>

        <div class="form-group">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">
                Member Position / Designation <span class="text-danger">*</span>
            </label>
            <input type="text" list="editorial_designations" name="designation" class="form-control" placeholder="Select or type designation..." value="<?php echo isset($formValues['designation']) ? htmlspecialchars($formValues['designation']) : ''; ?>" required style="border-radius: 6px; height: 38px;">
            <datalist id="editorial_designations">
                <option value="Editor-in-Chief">
                <option value="Associate Editor">
                <option value="Technical Editor">
                <option value="Editorial Advisor">
                <option value="Editorial Member">
                <?php if (!empty($existingDesignations)) {
                    foreach ($existingDesignations as $ed) {
                        if (!in_array($ed, ['Editor-in-Chief', 'Associate Editor', 'Technical Editor', 'Editorial Advisor', 'Editorial Member'])) { ?>
                            <option value="<?php echo htmlspecialchars($ed); ?>">
                        <?php }
                    }
                } ?>
            </datalist>
            <small class="text-muted" style="font-size: 11.5px; margin-top: 4px; display: block;">
                Common positions: <em>Editor-in-Chief, Associate Editor, Technical Editor, Editorial Advisor, Editorial Member</em>.
            </small>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label style="font-weight: 600; color: #334155; font-size: 13px;">Display Order / Position</label>
                    <input type="number" name="position" class="form-control" placeholder="e.g. 1" value="<?php echo (isset($formValues['position']) && $formValues['position'] != 1000) ? (int)$formValues['position'] : ''; ?>" min="1" max="999" style="border-radius: 6px; height: 38px;">
                    <small class="text-muted" style="font-size: 11px;">Lower numbers appear first (e.g. 1 for Editor-in-Chief).</small>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label style="font-weight: 600; color: #334155; font-size: 13px;">Publish Status</label>
                    <div style="margin-top: 8px;">
                        <label class="radio-inline" style="font-weight: 500; color: #1e293b;">
                            <input type="radio" name="is_publish" value="1" <?php echo (!isset($formValues['is_publish']) || $formValues['is_publish'] == 1) ? 'checked' : ''; ?>> Active
                        </label>
                        <label class="radio-inline" style="font-weight: 500; color: #1e293b; margin-left: 15px;">
                            <input type="radio" name="is_publish" value="0" <?php echo (isset($formValues['is_publish']) && $formValues['is_publish'] == 0) ? 'checked' : ''; ?>> Inactive
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label style="font-weight: 600; color: #334155; font-size: 13px;">Phone (Optional)</label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. 9876543210" value="<?php echo isset($formValues['phone']) ? htmlspecialchars($formValues['phone']) : ''; ?>" style="border-radius: 6px; height: 38px;">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label style="font-weight: 600; color: #334155; font-size: 13px;">Email (Optional)</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. editor@kpsta.in" value="<?php echo isset($formValues['email']) ? htmlspecialchars($formValues['email']) : ''; ?>" style="border-radius: 6px; height: 38px;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 6px 6px; padding: 15px 24px;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; padding: 6px 16px;">Cancel</button>
        <button type="submit" class="btn btn-primary" style="border-radius: 6px; padding: 6px 20px; font-weight: 600;">Save Member</button>
    </div>
</form>
