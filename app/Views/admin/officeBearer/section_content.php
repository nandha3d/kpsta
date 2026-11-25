<?php
$section = $section ?? 'state';
$sectionMeta = $sectionMeta ?? [];
$activeRole = $activeRole ?? '';
$baseRoute = $sectionMeta['base_route'] ?? 'admin/state_leaders';
$querySuffix = $activeRole !== '' ? ('?role=' . urlencode($activeRole)) : '';
if (count($content)) {
?>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 46px; text-align: center;"><input type="checkbox" class="list-checkbox-all" title="Select all on this page"></th>
                            <th>Leader Name</th>
                            <th>Role / Designation</th>
                            <?php if ($section === 'district') { ?>
                                <th>District</th>
                            <?php } else if ($section === 'former') { ?>
                                <th>Term / Period &amp; History</th>
                            <?php } else { ?>
                                <th>Term / Year</th>
                            <?php } ?>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Publish</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($content as $row) {
                            $desigDisplay = $row['designation'] ?? '';
                            if ($section === 'district' && strtoupper(trim((string)$desigDisplay)) === 'GENERAL SECRETARY') {
                                $desigDisplay = 'Secretary';
                            }
                            $prevPos = [];
                            if (!empty($row['previous_positions'])) {
                                $decoded = is_array($row['previous_positions']) ? $row['previous_positions'] : json_decode($row['previous_positions'], true);
                                if (is_array($decoded)) $prevPos = $decoded;
                            }
                        ?>
                            <tr data-tr="<?php echo (int)$row['id']; ?>">
                                <td style="width: 46px; text-align: center; vertical-align: middle;">
                                    <input type="checkbox" data-target="tbody" data-toggle="selectrow" class="list-checkbox modern-table-checkbox" name="ids[]" value="<?php echo (int)$row['id']; ?>">
                                </td>
                                <td>
                                    <div class="modern-avatar-group">
                                        <?php if (!empty($row['image'])) { ?>
                                            <img src="<?php echo base_url('uploads/office_bearer/' . $row['image']) . '?t=' . time(); ?>" alt="Photo" style="width:36px;height:36px;border-radius:50%;object-fit:cover;margin-right:10px;">
                                        <?php } else { ?>
                                            <div style="width:36px;height:36px;border-radius:50%;background:#e2e8f0;display:inline-flex;align-items:center;justify-content:center;color:#94a3b8;margin-right:10px;"><i class="fa fa-user"></i></div>
                                        <?php } ?>
                                        <span class="modern-avatar-text text-ellipsis" title="<?php echo html_escape($row['name']); ?>"><?php echo html_escape($row['name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: #eef2ff; color: #4338ca; font-weight: 700; padding: 5px 9px;">
                                        <?php echo html_escape($desigDisplay); ?>
                                    </span>
                                    <?php if ($section === 'state' && !empty($row['section_heading'])) { ?>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 3px;"><?php echo html_escape($row['section_heading']); ?></div>
                                    <?php } ?>
                                </td>

                                <?php if ($section === 'district') { ?>
                                    <td>
                                        <span class="badge" style="background: #ecfeff; color: #0e7490; font-weight: 600; padding: 5px 9px;">
                                            <i class="fa fa-map-marker"></i> <?php echo html_escape($row['section_heading'] ?? '-'); ?>
                                        </span>
                                        <?php if (!empty($row['year'])) { ?>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;"><?php echo html_escape($row['year']); ?></div>
                                        <?php } ?>
                                    </td>
                                <?php } else if ($section === 'former') { ?>
                                    <td>
                                        <div style="font-size: 12px; font-weight: 600; color: #0284c7;">
                                            <i class="fa fa-calendar-check-o"></i> <?php echo !empty($row['year']) ? html_escape($row['year']) : 'Previous Term'; ?>
                                        </div>
                                        <?php if (!empty($prevPos)) { ?>
                                            <div style="font-size: 11px; color: #475569; margin-top: 2px;">
                                                <?php
                                                $histLabels = array_map(function ($p) {
                                                    return trim(($p['designation'] ?? '') . (!empty($p['year']) ? ' (' . $p['year'] . ')' : ''));
                                                }, array_slice($prevPos, 0, 2));
                                                echo html_escape(implode(', ', array_filter($histLabels)));
                                                if (count($prevPos) > 2) echo ' +' . (count($prevPos) - 2) . ' more';
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                <?php } else { ?>
                                    <td><?php echo !empty($row['year']) ? html_escape($row['year']) : '<span class="text-muted">Active Term</span>'; ?></td>
                                <?php } ?>

                                <td><?php echo html_escape((string)($row['phone'] ?? '')); ?></td>
                                <td><?php echo html_escape((string)($row['email'] ?? '')); ?></td>

                                <td>
                                    <div class="btn-group publish modern-status-toggle" data-toggle="buttons" data-href="<?php echo base_url($baseRoute . '/publish/' . $row['id']); ?>">
                                        <label class="btn <?php echo ($row['is_publish'] == 1) ? 'active' : ''; ?>" data-active-class="success">
                                            <input type="radio" name="publish" value="1"><span>Active</span>
                                        </label>
                                        <label class="btn <?php echo ($row['is_publish'] == 0) ? 'active' : ''; ?>" data-active-class="danger">
                                            <input type="radio" name="publish" value="0"><span>Inactive</span>
                                        </label>
                                    </div>
                                </td>

                                <td style="text-align: right;">
                                    <div class="modern-actions">
                                        <a href="javascript:void(0)" class="btn btn-edit edit" data-href="<?php echo base_url($baseRoute . '/edit/' . $row['id'] . $querySuffix); ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-delete"
                                           data-href="<?php echo base_url($baseRoute . '/delete/' . $row['id'] . $querySuffix); ?>"
                                           data-toggle="modal" data-target="#delete"
                                           data-precheck=""
                                           data-message="Delete <?php echo html_escape($row['name']); ?> from <?php echo html_escape($sectionMeta['title'] ?? 'this list'); ?>?"
                                           data-confirm-text="Delete"
                                           data-confirm-callback="executeSectionAction"
                                           data-cancel-text="Cancel"
                                           data-cancel-callback="dismissConfirmation">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                        <?php if (!empty($row['image'])) { ?>
                                            <a href="<?php echo base_url(OFFICE_BEARER . '/' . $row['image']); ?>" target="_blank" class="btn btn-view">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" role="status" aria-live="polite">
                <?php if ($config['total_rows']) { ?>
                    Showing <?php echo $config['from'] . ' to ' . $config['to'] . ' of ' . $config['total_rows']; ?> entries
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers">
                <?php echo $links; ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="alert alert-warning col-md-8 col-md-offset-2 mt-md" style="white-space: normal; text-align: center;">
        <h4>No <?php echo html_escape($sectionMeta['title'] ?? 'Leaders'); ?> Found</h4>
        <p>No entries match this role or filter yet. Click <strong>Add</strong> above to add one directly to <?php echo html_escape($sectionMeta['title'] ?? 'this section'); ?>.</p>
    </div>
<?php } ?>
