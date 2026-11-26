<div class="content-wrapper">
    <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="margin:0; font-size:24px; font-weight:700; color:#0f172a;">
                <i class="fa fa-window-restore text-primary" style="margin-right:8px;"></i>Popup Window Management
            </h1>
            <p class="text-muted" style="margin:4px 0 0; font-size:13px;">
                Configure homepage auto-popup modals, schedule pre-determined expiry dates, and choose Reaction Gallery, News, or Custom announcements with file downloaders.
            </p>
        </div>
        <div>
            <span class="label <?php echo $status_badge['class']; ?>" style="font-size:13px; padding:6px 14px; border-radius:20px; font-weight:600;">
                <?php echo htmlspecialchars($status_badge['text']); ?>
            </span>
        </div>
    </section>

    <section class="content" style="padding-top:15px;">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="fa fa-exclamation-triangle"></i> <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo base_url('admin/popup_window/save'); ?>" method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- LEFT COLUMN: Main Controls & Custom Content -->
                <div class="col-md-8">
                    <!-- 1. Master Toggle & Schedule -->
                    <div class="box box-primary" style="border-radius:12px; box-shadow:0 4px 14px rgba(0,0,0,0.06); border-top:3px solid #016D77;">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="font-weight:700; font-size:16px;">
                                <i class="fa fa-clock-o text-primary" style="margin-right:6px;"></i>General &amp; Pre-Determined Expiry Schedule
                            </h3>
                        </div>
                        <div class="box-body" style="padding:20px;">
                            <div class="row">
                                <div class="col-sm-12" style="margin-bottom:18px;">
                                    <div class="checkbox" style="margin-top:0;">
                                        <label style="font-weight:700; font-size:15px; color:#0f172a; cursor:pointer;">
                                            <input type="checkbox" name="popup_enabled" value="1" <?php echo ($popup_enabled == 1) ? 'checked' : ''; ?> style="width:18px; height:18px; margin-top:-1px;">
                                            &nbsp;Enable Auto-Popup Window on Homepage
                                        </label>
                                    </div>
                                    <p class="text-muted" style="font-size:12px; margin:4px 0 0 24px;">When checked, the popup will trigger for site visitors according to the schedule below.</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12" style="margin-bottom:16px;">
                                    <label style="font-weight:600; color:#1e293b;">Start Display Date</label>
                                    <input type="date" name="popup_start_date" class="form-control" value="<?php echo htmlspecialchars($popup_start_date); ?>" style="border-radius:8px;">
                                    <span class="help-block" style="font-size:12px; margin-top:4px;">Popup begins displaying from this date. Leave blank for immediate activation.</span>
                                </div>
                                <div class="col-md-6 col-sm-12" style="margin-bottom:16px;">
                                    <label style="font-weight:600; color:#dc2626;">
                                        <i class="fa fa-calendar-times-o"></i> Pre-Determined Expiry Date (End Date)
                                    </label>
                                    <input type="date" name="popup_end_date" class="form-control" value="<?php echo htmlspecialchars($popup_end_date); ?>" style="border-radius:8px; border-color:#fca5a5;">
                                    <span class="help-block" style="font-size:12px; margin-top:4px; color:#b91c1c;">
                                        <strong>Automatic Cutoff:</strong> Once this pre-determined date has passed, the popup automatically stops displaying to visitors.
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12" style="margin-bottom:10px;">
                                    <label style="font-weight:600; color:#1e293b;">Popup Trigger Delay (Seconds)</label>
                                    <div class="input-group">
                                        <input type="number" name="popup_duration" class="form-control" min="1" max="120" value="<?php echo htmlspecialchars($popup_duration); ?>" style="border-radius:8px 0 0 8px;">
                                        <span class="input-group-addon" style="border-radius:0 8px 8px 0;">seconds</span>
                                    </div>
                                    <span class="help-block" style="font-size:12px; margin-top:4px;">Delay before the popup appears after page load (default 10s).</span>
                                </div>
                                <div class="col-md-6 col-sm-12" style="margin-bottom:10px;">
                                    <label style="font-weight:600; color:#1e293b;">Target Device</label>
                                    <select name="popup_device_target" class="form-control" style="border-radius:8px;">
                                        <option value="both" <?php echo ($popup_device_target === 'both') ? 'selected' : ''; ?>>Both Main Website &amp; Mobile App</option>
                                        <option value="web_only" <?php echo ($popup_device_target === 'web_only') ? 'selected' : ''; ?>>Main Website Only</option>
                                        <option value="app_only" <?php echo ($popup_device_target === 'app_only') ? 'selected' : ''; ?>>Mobile App Only</option>
                                    </select>
                                    <span class="help-block" style="font-size:12px; margin-top:4px;">Controls which platforms display this popup.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Content Source Selection -->
                    <div class="box box-info" style="border-radius:12px; box-shadow:0 4px 14px rgba(0,0,0,0.06); border-top:3px solid #0ea5e9;">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="font-weight:700; font-size:16px;">
                                <i class="fa fa-th-large text-info" style="margin-right:6px;"></i>Content Source Selection
                            </h3>
                        </div>
                        <div class="box-body" style="padding:20px;">
                            <label style="font-weight:600; color:#1e293b; display:block; margin-bottom:10px;">Select What Content To Post in Popup:</label>
                            
                            <div class="row">
                                <div class="col-md-6 col-sm-12" style="margin-bottom:12px;">
                                    <div class="radio-card" style="border:1.5px solid <?php echo ($popup_mode === 'combination') ? '#016D77' : '#e2e8f0'; ?>; padding:12px 14px; border-radius:10px; background:<?php echo ($popup_mode === 'combination') ? '#f0fdfa' : '#fff'; ?>;">
                                        <label style="margin:0; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
                                            <input type="radio" name="popup_mode" value="combination" <?php echo ($popup_mode === 'combination') ? 'checked' : ''; ?> onchange="onPopupModeChange()">
                                            <span>Multi-Tab (Reaction Poster, News &amp; Custom)</span>
                                        </label>
                                        <p class="text-muted" style="margin:4px 0 0 24px; font-size:11.5px;">Displays clean tabs allowing visitors to switch between posters, news, and custom notices.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12" style="margin-bottom:12px;">
                                    <div class="radio-card" style="border:1.5px solid <?php echo ($popup_mode === 'reaction_gallery') ? '#016D77' : '#e2e8f0'; ?>; padding:12px 14px; border-radius:10px; background:<?php echo ($popup_mode === 'reaction_gallery') ? '#f0fdfa' : '#fff'; ?>;">
                                        <label style="margin:0; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
                                            <input type="radio" name="popup_mode" value="reaction_gallery" <?php echo ($popup_mode === 'reaction_gallery') ? 'checked' : ''; ?> onchange="onPopupModeChange()">
                                            <span>Reaction Gallery Only</span>
                                        </label>
                                        <p class="text-muted" style="margin:4px 0 0 24px; font-size:11.5px;">Shows official campaign poster in authentic original aspect ratio.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12" style="margin-bottom:12px;">
                                    <div class="radio-card" style="border:1.5px solid <?php echo ($popup_mode === 'news') ? '#016D77' : '#e2e8f0'; ?>; padding:12px 14px; border-radius:10px; background:<?php echo ($popup_mode === 'news') ? '#f0fdfa' : '#fff'; ?>;">
                                        <label style="margin:0; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
                                            <input type="radio" name="popup_mode" value="news" <?php echo ($popup_mode === 'news') ? 'checked' : ''; ?> onchange="onPopupModeChange()">
                                            <span>Latest News / Bulletin Only</span>
                                        </label>
                                        <p class="text-muted" style="margin:4px 0 0 24px; font-size:11.5px;">Highlights latest association bulletin, news photo, and excerpt.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12" style="margin-bottom:12px;">
                                    <div class="radio-card" style="border:1.5px solid <?php echo ($popup_mode === 'custom') ? '#016D77' : '#e2e8f0'; ?>; padding:12px 14px; border-radius:10px; background:<?php echo ($popup_mode === 'custom') ? '#f0fdfa' : '#fff'; ?>;">
                                        <label style="margin:0; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
                                            <input type="radio" name="popup_mode" value="custom" <?php echo ($popup_mode === 'custom') ? 'checked' : ''; ?> onchange="onPopupModeChange()">
                                            <span>Custom News &amp; File Downloader</span>
                                        </label>
                                        <p class="text-muted" style="margin:4px 0 0 24px; font-size:11.5px;">Dedicated custom notice with text editor, custom image, and file downloader.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Specific Reaction Poster Selector -->
                            <div id="sectionReactionPicker" style="margin-top:16px; padding:14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                                <label style="font-weight:600; color:#1e293b;">
                                    <i class="fa fa-picture-o text-teal"></i> Select Reaction Gallery Item:
                                </label>
                                <select name="popup_reaction_id" class="form-control" style="border-radius:8px;">
                                    <option value="0" <?php echo ($popup_reaction_id == 0) ? 'selected' : ''; ?>>-- Latest Active Reaction Poster (Default) --</option>
                                    <?php if (!empty($reaction_items)): foreach ($reaction_items as $rItem): ?>
                                        <option value="<?php echo $rItem['id']; ?>" <?php echo ($popup_reaction_id == $rItem['id']) ? 'selected' : ''; ?>>
                                            #<?php echo $rItem['id']; ?> - <?php echo htmlspecialchars(mb_substr($rItem['description'] ?? 'Poster', 0, 80)); ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <span class="help-block" style="font-size:12px; margin-top:4px;">
                                    Choose "Latest Active" or pin a specific campaign poster from Reaction Gallery.
                                </span>
                            </div>

                            <!-- Specific News Item Selector -->
                            <div id="sectionNewsPicker" style="margin-top:14px; padding:14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                                <label style="font-weight:600; color:#1e293b;">
                                    <i class="fa fa-newspaper-o text-teal"></i> Select News Bulletin Item:
                                </label>
                                <select name="popup_news_id" class="form-control" style="border-radius:8px;">
                                    <option value="0" <?php echo ($popup_news_id == 0) ? 'selected' : ''; ?>>-- Latest Active News Article (Default) --</option>
                                    <?php if (!empty($news_items)): foreach ($news_items as $nItem): ?>
                                        <option value="<?php echo $nItem['id']; ?>" <?php echo ($popup_news_id == $nItem['id']) ? 'selected' : ''; ?>>
                                            #<?php echo $nItem['id']; ?> - <?php echo htmlspecialchars(mb_substr($nItem['heading'] ?? 'News', 0, 80)); ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <span class="help-block" style="font-size:12px; margin-top:4px;">
                                    Choose "Latest Active" or pin a specific news bulletin article.
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Custom News / Announcement (Text Editor, Image & File Downloader) -->
                    <div class="box box-warning" id="sectionCustomNotice" style="border-radius:12px; box-shadow:0 4px 14px rgba(0,0,0,0.06); border-top:3px solid #f59e0b;">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="font-weight:700; font-size:16px;">
                                <i class="fa fa-edit text-warning" style="margin-right:6px;"></i>Custom News / Announcement (Text Editor, Image &amp; File Downloader)
                            </h3>
                        </div>
                        <div class="box-body" style="padding:20px;">
                            <div class="row">
                                <div class="col-md-4 col-sm-12" style="margin-bottom:15px;">
                                    <label style="font-weight:600; color:#1e293b;">Header Badge / Tag</label>
                                    <input type="text" name="popup_custom_badge" class="form-control" value="<?php echo htmlspecialchars($popup_custom_badge); ?>" placeholder="e.g. Announcement, Circular Alert" style="border-radius:8px;">
                                </div>
                                <div class="col-md-8 col-sm-12" style="margin-bottom:15px;">
                                    <label style="font-weight:600; color:#1e293b;">Title / Heading</label>
                                    <input type="text" name="popup_custom_title" class="form-control" value="<?php echo htmlspecialchars($popup_custom_title); ?>" placeholder="e.g. Urgent Notice to All Members" style="border-radius:8px;">
                                </div>
                            </div>

                            <!-- Text Editor -->
                            <div class="form-group" style="margin-bottom:20px;">
                                <label style="font-weight:600; color:#1e293b;">Announcement Content (Rich Text Editor)</label>
                                <textarea name="popup_custom_content" id="popupCustomEditor" class="form-control" rows="6"><?php echo htmlspecialchars($popup_custom_content); ?></textarea>
                                <span class="help-block" style="font-size:12px;">Format text with bolding, lists, links, or custom announcements.</span>
                            </div>

                            <!-- Image Upload & Preview -->
                            <div class="row" style="margin-bottom:20px; padding:15px 5px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                                <div class="col-md-6 col-sm-12">
                                    <label style="font-weight:600; color:#1e293b;">
                                        <i class="fa fa-file-image-o text-primary"></i> Upload Custom Poster / Image
                                    </label>
                                    <input type="file" name="custom_image" accept="image/*" class="form-control" style="border-radius:8px; padding:6px 12px; height:auto;">
                                    <span class="help-block" style="font-size:12px;">Supported: JPG, PNG, WEBP, GIF. Displays in authentic original aspect ratio.</span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <?php if (!empty($popup_custom_image) && file_exists(FCPATH . $popup_custom_image)): ?>
                                        <label style="font-weight:600; color:#1e293b; display:block;">Current Image:</label>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <img src="<?php echo base_url($popup_custom_image); ?>" alt="Preview" style="max-height:80px; max-width:120px; border-radius:8px; border:1px solid #cbd5e1; object-fit:contain; background:#fff;">
                                            <label style="color:#dc2626; font-size:12px; cursor:pointer;">
                                                <input type="checkbox" name="remove_custom_image" value="1"> Remove Image
                                            </label>
                                        </div>
                                    <?php else: ?>
                                        <div style="padding:15px; color:#94a3b8; font-size:12px; font-style:italic;">No custom image uploaded currently.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- File Downloader Attachment -->
                            <div class="row" style="margin-bottom:20px; padding:15px 5px; background:#f0fdf4; border-radius:10px; border:1px solid #bbf7d0;">
                                <div class="col-md-6 col-sm-12">
                                    <label style="font-weight:600; color:#166534;">
                                        <i class="fa fa-download text-success"></i> File Downloader / Document Attachment
                                    </label>
                                    <input type="file" name="custom_file" class="form-control" style="border-radius:8px; padding:6px 12px; height:auto;">
                                    <span class="help-block" style="font-size:12px; color:#15803d;">
                                        Upload a circular, form, or document (PDF, DOCX, ZIP). Visitors will see a direct <strong>Download Attachment</strong> button inside the popup!
                                    </span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <?php if (!empty($popup_custom_file) && file_exists(FCPATH . $popup_custom_file)): ?>
                                        <label style="font-weight:600; color:#166534; display:block;">Active Attachment:</label>
                                        <div style="display:flex; align-items:center; gap:10px; padding:8px 12px; background:#fff; border-radius:8px; border:1px solid #86efac;">
                                            <i class="fa fa-file-pdf-o text-danger" style="font-size:20px;"></i>
                                            <div style="flex:1; overflow:hidden;">
                                                <strong style="font-size:12px; color:#0f172a; display:block; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;">
                                                    <?php echo htmlspecialchars($popup_custom_file_name ?: basename($popup_custom_file)); ?>
                                                </strong>
                                                <a href="<?php echo base_url($popup_custom_file); ?>" target="_blank" style="font-size:11px; color:#059669;">Download Test</a>
                                            </div>
                                            <label style="color:#dc2626; font-size:11px; margin:0; cursor:pointer;">
                                                <input type="checkbox" name="remove_custom_file" value="1"> Remove
                                            </label>
                                        </div>
                                    <?php else: ?>
                                        <div style="padding:15px; color:#64748b; font-size:12px; font-style:italic;">No downloadable attachment uploaded.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- CTA Button / Link -->
                            <div class="row">
                                <div class="col-md-4 col-sm-12" style="margin-bottom:10px;">
                                    <label style="font-weight:600; color:#1e293b;">Button Text</label>
                                    <input type="text" name="popup_custom_cta_text" class="form-control" value="<?php echo htmlspecialchars($popup_custom_cta_text); ?>" placeholder="e.g. Read Full Circular, Download PDF" style="border-radius:8px;">
                                </div>
                                <div class="col-md-8 col-sm-12" style="margin-bottom:10px;">
                                    <label style="font-weight:600; color:#1e293b;">Button Target URL</label>
                                    <input type="text" name="popup_custom_cta_url" class="form-control" value="<?php echo htmlspecialchars($popup_custom_cta_url); ?>" placeholder="https://... or leave blank to auto-link to the uploaded document" style="border-radius:8px;">
                                    <span class="help-block" style="font-size:12px;">If left blank and a file is uploaded, this button will directly trigger downloading the attached document!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Summary & Live Preview -->
                <div class="col-md-4">
                    <!-- Quick Save Card -->
                    <div class="box box-solid" style="border-radius:12px; box-shadow:0 4px 14px rgba(0,0,0,0.06); background:#ffffff;">
                        <div class="box-body" style="padding:20px;">
                            <button type="submit" class="btn btn-primary btn-block btn-lg" style="background:#016D77; border-color:#016D77; border-radius:10px; font-weight:700; padding:12px;">
                                <i class="fa fa-save" style="margin-right:6px;"></i> Save Popup Settings
                            </button>
                            <a href="<?php echo base_url(); ?>" target="_blank" class="btn btn-default btn-block" style="margin-top:10px; border-radius:10px; font-weight:600;">
                                <i class="fa fa-external-link" style="margin-right:6px;"></i> View Main Website
                            </a>
                        </div>
                    </div>


                    <!-- Live Mockup Preview Box -->
                    <div class="box box-solid" style="border-radius:12px; box-shadow:0 4px 14px rgba(0,0,0,0.06); background:#f8fafc; border:1px solid #e2e8f0;">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="font-weight:700; font-size:14px; color:#334155;">
                                <i class="fa fa-eye"></i> Visual Preview Mockup
                            </h3>
                        </div>
                        <div class="box-body" style="padding:16px;">
                            <div style="background:#0b2025; border-radius:14px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.25); border:1px solid #1e293b;">
                                <div style="background:#016D77; color:#fff; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:700;">
                                        <i class="fa fa-bullhorn"></i> <span id="previewBadgeText"><?php echo htmlspecialchars($popup_custom_badge ?: 'Announcement'); ?></span>
                                    </span>
                                    <span style="font-size:16px; opacity:0.8;">&times;</span>
                                </div>
                                <div style="padding:14px; background:#0F2C33; text-align:center;">
                                    <?php if (!empty($popup_custom_image) && file_exists(FCPATH . $popup_custom_image)): ?>
                                        <img src="<?php echo base_url($popup_custom_image); ?>" alt="Preview" style="max-width:100%; max-height:160px; object-fit:contain; border-radius:8px; display:block; margin:0 auto 10px;">
                                    <?php endif; ?>
                                    <h4 id="previewTitleText" style="color:#ffffff; margin:0 0 6px; font-size:14px; font-weight:700;">
                                        <?php echo htmlspecialchars($popup_custom_title ?: 'Notification Title'); ?>
                                    </h4>
                                    <p style="color:#94a3b8; font-size:11px; margin:0; line-height:1.4;">
                                        (Content formatted with original aspect ratio, downloadable attachments, and direct action buttons)
                                    </p>
                                </div>
                                <div style="padding:10px 14px; background:#ffffff; display:flex; gap:8px;">
                                    <button type="button" class="btn btn-default btn-xs" style="flex:1;">Close</button>
                                    <button type="button" class="btn btn-success btn-xs" style="flex:1.5; background:#22c55e; border-color:#22c55e; font-weight:700;">
                                        <?php echo htmlspecialchars($popup_custom_cta_text ?: 'View Details'); ?> &rarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<!-- Include Summernote Rich Text Editor -->
<link rel="stylesheet" href="<?php echo base_url('public/plugins/summernote/summernote.css'); ?>">
<script src="<?php echo base_url('public/plugins/summernote/summernote.js'); ?>"></script>

<script>
    $(document).ready(function() {
        if ($('#popupCustomEditor').length) {
            $('#popupCustomEditor').summernote({
                height: 180,
                placeholder: 'Type custom notice, announcement text, or details here...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });
        }
        onPopupModeChange();
    });

    function onPopupModeChange() {
        const mode = $('input[name="popup_mode"]:checked').val();
        if (mode === 'combination') {
            $('#sectionReactionPicker').slideDown(200);
            $('#sectionNewsPicker').slideDown(200);
            $('#sectionCustomNotice').slideDown(200);
        } else if (mode === 'reaction_gallery') {
            $('#sectionReactionPicker').slideDown(200);
            $('#sectionNewsPicker').slideUp(200);
            $('#sectionCustomNotice').slideUp(200);
        } else if (mode === 'news') {
            $('#sectionReactionPicker').slideUp(200);
            $('#sectionNewsPicker').slideDown(200);
            $('#sectionCustomNotice').slideUp(200);
        } else if (mode === 'custom') {
            $('#sectionReactionPicker').slideUp(200);
            $('#sectionNewsPicker').slideUp(200);
            $('#sectionCustomNotice').slideDown(200);
        }
    }
</script>
