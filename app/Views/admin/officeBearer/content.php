
<?php if (count($content)) { ?> 

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive  " >
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;"><input type="checkbox" class="list-checkbox-all" title="Select all on this page"></th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Designation</th>
                            <th>Section</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Publish</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>


                        <?php foreach ($content as $row) { ?>
                            <tr data-tr="<?php echo $row['id'] ?>">
                                <!-- Modern Standalone Checkbox -->
                                <td style="width: 50px; text-align: center; vertical-align: middle;">
                                    <input type="checkbox" data-target="tbody" data-toggle="selectrow" class="list-checkbox modern-table-checkbox" name="ids[]" value="<?php echo $row['id'] ?>">
                                </td>

                                <!-- Modern Avatar inline with Name -->
                                <td>
                                    <div class="modern-avatar-group">
                                        <?php if (!empty($row['image'])) { ?>
                                            <img src="<?php echo base_url('uploads/office_bearer/'.$row['image']) . '?t=' . time(); ?>" alt="Photo" style="width:36px;height:36px;border-radius:50%;object-fit:cover;margin-right:10px;">
                                        <?php } else { ?>
                                            <div style="width:36px;height:36px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><i class="fa fa-user"></i></div>
                                        <?php } ?>
                                        <span class="modern-avatar-text text-ellipsis" title="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></span>
                                    </div>
                                </td>

                                <td><span class="badge bg-<?php echo (isset($row['level']) && $row['level'] == 'District') ? 'info' : 'primary'; ?>"><?php echo isset($row['level']) ? $row['level'] : 'State'; ?></span></td>
                                <td style="padding-top: 4px; padding-bottom: 4px;">
                                    <?php
                                    $isFormerView = (isset($is_former_filter) && $is_former_filter === '1');
                                    $prevPos = [];
                                    if (!empty($row['previous_positions'])) {
                                        $prevPos = is_array($row['previous_positions']) ? $row['previous_positions'] : json_decode($row['previous_positions'], true);
                                    }
                                    if (!is_array($prevPos)) $prevPos = [];
                                    ?>

                                    <?php if ($isFormerView && !empty($prevPos)) { ?>
                                        <!-- FORMER LEADERS VIEW: Show first previous position, rest collapsed -->
                                        <?php
                                        // Sort by position order if available
                                        usort($prevPos, function($a, $b) {
                                            $posA = isset($a['position']) ? (int)$a['position'] : 25;
                                            $posB = isset($b['position']) ? (int)$b['position'] : 25;
                                            return $posA - $posB;
                                        });
                                        $firstPos = $prevPos[0];
                                        $firstEnabled = (!isset($firstPos['is_enabled']) || $firstPos['is_enabled'] === 1 || $firstPos['is_enabled'] === '1' || $firstPos['is_enabled'] === true);
                                        $firstDesig = isset($firstPos['designation']) ? $firstPos['designation'] : '';
                                        $firstYear = !empty($firstPos['year']) ? $firstPos['year'] : 'Previous Term';
                                        $firstLvl = (!empty($firstPos['level']) && $firstPos['level'] !== 'State') ? $firstPos['level'] . (!empty($firstPos['section_heading']) ? ' - ' . $firstPos['section_heading'] : '') : '';
                                        $firstOpacity = !$firstEnabled ? 'opacity: 0.5; text-decoration: line-through;' : '';
                                        ?>
                                        <!-- Primary: first previous position shown inline -->
                                        <div style="font-weight: 600; color: #1e293b; font-size: 12px; line-height: 1.3; <?php echo $firstOpacity; ?>">
                                            <i class="fa fa-history text-muted" style="font-size: 8px; margin-right: 2px;"></i>
                                            <?php echo htmlspecialchars($firstDesig); ?>
                                            <?php if (!$firstEnabled) { ?>
                                                <span class="badge" style="font-size: 8px; padding: 0px 3px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;"><i class="fa fa-ban"></i> Inactive</span>
                                            <?php } ?>
                                        </div>
                                        <div style="font-size: 10px; color: #0284c7; margin-top: 1px; font-weight: 500; display: flex; align-items: center; gap: 3px;">
                                            <i class="fa fa-calendar-check-o text-muted" style="font-size: 9px;"></i>
                                            <span><strong>Period:</strong> <?php echo htmlspecialchars($firstYear); ?></span>
                                        </div>
                                        <?php if (!empty($firstLvl)) { ?>
                                            <span class="badge" style="font-size: 8px; padding: 0px 4px; background: #e0f2fe; color: #0369a1; margin-top: 1px; display: inline-block;"><?php echo htmlspecialchars($firstLvl); ?></span>
                                        <?php } ?>

                                        <?php
                                        // Remaining previous positions + active = collapsible
                                        $remainingPrev = array_slice($prevPos, 1);
                                        $moreCount = count($remainingPrev) + 1; // +1 for active position
                                        $formerCollapseId = 'former_pos_' . $row['id'];
                                        ?>
                                        <div style="margin-top: 3px;">
                                            <button type="button" class="btn btn-xs prev-positions-toggle" data-toggle="collapse" data-target="#<?php echo $formerCollapseId; ?>" aria-expanded="false" style="padding: 1px 6px; font-size: 10px; border-radius: 3px; background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; text-decoration: none; line-height: 1.4;">
                                                <i class="fa fa-chevron-right prev-pos-arrow" style="font-size: 8px; color: #6366f1;"></i>
                                                <span style="font-weight: 600;"><?php echo $moreCount; ?> More Position<?php echo $moreCount > 1 ? 's' : ''; ?></span>
                                            </button>

                                            <div class="collapse prev-positions-drawer" id="<?php echo $formerCollapseId; ?>" style="margin-top: 3px; padding: 4px 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; border-left: 2px solid #6366f1; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                                <?php foreach ($remainingPrev as $p) {
                                                    $isEnabled = (!isset($p['is_enabled']) || $p['is_enabled'] === 1 || $p['is_enabled'] === '1' || $p['is_enabled'] === true);
                                                    $pDesig = isset($p['designation']) ? $p['designation'] : '';
                                                    $pYear = !empty($p['year']) ? $p['year'] : 'Previous Term';
                                                    $pLvl = (!empty($p['level']) && $p['level'] !== 'State') ? ' <span class="badge" style="font-size: 8px; padding: 0px 4px; background: #e0f2fe; color: #0369a1;">' . htmlspecialchars($p['level'] . (!empty($p['section_heading']) ? ' - ' . $p['section_heading'] : '')) . '</span>' : '';
                                                    $pStatus = !$isEnabled ? ' <span class="badge" style="font-size: 8px; padding: 0px 3px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;"><i class="fa fa-ban"></i> Inactive</span>' : '';
                                                    $rowStyle = !$isEnabled ? 'opacity: 0.55; text-decoration: line-through;' : '';
                                                ?>
                                                    <div style="margin-bottom: 3px; padding-bottom: 2px; border-bottom: 1px dashed #e2e8f0; font-size: 10.5px; <?php echo $rowStyle; ?>">
                                                        <div style="font-weight: 600; color: #1e293b; line-height: 1.3;">
                                                            <i class="fa fa-history text-muted" style="font-size: 8px; margin-right: 2px;"></i>
                                                            <?php echo htmlspecialchars($pDesig); ?>
                                                            <?php echo $pLvl . $pStatus; ?>
                                                        </div>
                                                        <div style="font-size: 10px; color: #0284c7; margin-left: 12px; margin-top: 1px;">
                                                            <i class="fa fa-clock-o text-muted" style="font-size: 9px;"></i> <strong>Period:</strong> <?php echo htmlspecialchars($pYear); ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <!-- Active/Current Position -->
                                                <div style="margin-top: 2px; padding: 3px 6px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #6ee7b7; border-radius: 4px; display: flex; align-items: center; gap: 5px;">
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; background: #10b981; border-radius: 50%; flex-shrink: 0;">
                                                        <i class="fa fa-check" style="color: #fff; font-size: 7px;"></i>
                                                    </span>
                                                    <div style="line-height: 1.3;">
                                                        <div style="font-weight: 700; color: #065f46; font-size: 11px;">
                                                            <?php echo htmlspecialchars($row['designation']); ?>
                                                            <span class="badge" style="font-size: 7px; padding: 0px 4px; background: #10b981; color: #fff; border-radius: 3px; margin-left: 2px;">ACTIVE</span>
                                                        </div>
                                                        <div style="font-size: 10px; color: #047857;">
                                                            <i class="fa fa-calendar-check-o" style="font-size: 8px;"></i>
                                                            <strong>Period:</strong> <?php echo !empty($row['year']) ? htmlspecialchars($row['year']) : '<span style="color: #059669;">Current</span>'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } else { ?>
                                        <!-- NORMAL VIEW: Current designation as primary, previous in collapsible -->
                                        <div style="font-weight: 700; color: #1e293b; font-size: 12px; line-height: 1.3;">
                                            <?php echo htmlspecialchars($row['designation']); ?>
                                        </div>
                                        <!-- Time Period of the Position -->
                                        <div style="font-size: 10.5px; color: #0284c7; margin-top: 1px; font-weight: 500; display: flex; align-items: center; gap: 3px;">
                                            <i class="fa fa-calendar-check-o text-muted" style="font-size: 9px;"></i>
                                            <span><strong>Period:</strong> <?php echo !empty($row['year']) ? htmlspecialchars($row['year']) : '<span class="text-muted">Current</span>'; ?></span>
                                        </div>

                                        <?php
                                        if (!empty($prevPos)) {
                                            $posCount = count($prevPos);
                                            $collapseId = 'prev_pos_' . $row['id'];
                                        ?>
                                            <div style="margin-top: 3px;">
                                                <button type="button" class="btn btn-xs prev-positions-toggle" data-toggle="collapse" data-target="#<?php echo $collapseId; ?>" aria-expanded="false" style="padding: 1px 6px; font-size: 10px; border-radius: 3px; background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; text-decoration: none; line-height: 1.4;">
                                                    <i class="fa fa-chevron-right prev-pos-arrow" style="font-size: 8px; color: #6366f1;"></i>
                                                    <span style="font-weight: 600;"><?php echo $posCount; ?> Previous Position<?php echo $posCount > 1 ? 's' : ''; ?></span>
                                                </button>

                                                <div class="collapse prev-positions-drawer" id="<?php echo $collapseId; ?>" style="margin-top: 3px; padding: 4px 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; border-left: 2px solid #6366f1; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                                    <?php foreach ($prevPos as $p) {
                                                        $isEnabled = (!isset($p['is_enabled']) || $p['is_enabled'] === 1 || $p['is_enabled'] === '1' || $p['is_enabled'] === true);
                                                        $pDesig = isset($p['designation']) ? $p['designation'] : '';
                                                        $pYear = !empty($p['year']) ? $p['year'] : 'Previous Term';
                                                        $pPos = (!empty($p['position']) && (int)$p['position'] < 25) ? ' <span class="badge" style="font-size: 8px; padding: 0px 4px; background: #fef3c7; color: #92400e;" title="Position Order">#' . htmlspecialchars($p['position']) . '</span>' : '';
                                                        $pLvl = (!empty($p['level']) && $p['level'] !== 'State') ? ' <span class="badge" style="font-size: 8px; padding: 0px 4px; background: #e0f2fe; color: #0369a1;">' . htmlspecialchars($p['level'] . (!empty($p['section_heading']) ? ' - ' . $p['section_heading'] : '')) . '</span>' : '';
                                                        $pStatus = !$isEnabled ? ' <span class="badge" style="font-size: 8px; padding: 0px 3px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;"><i class="fa fa-ban"></i> Inactive</span>' : '';
                                                        $rowStyle = !$isEnabled ? 'opacity: 0.55; text-decoration: line-through;' : '';
                                                    ?>
                                                        <div style="margin-bottom: 3px; padding-bottom: 2px; border-bottom: 1px dashed #e2e8f0; font-size: 10.5px; <?php echo $rowStyle; ?>">
                                                            <div style="font-weight: 600; color: #1e293b; line-height: 1.3;">
                                                                <i class="fa fa-history text-muted" style="font-size: 8px; margin-right: 2px;"></i>
                                                                <?php echo htmlspecialchars($pDesig); ?>
                                                                <?php echo $pPos . $pLvl . $pStatus; ?>
                                                            </div>
                                                            <div style="font-size: 10px; color: #0284c7; margin-left: 12px; margin-top: 1px;">
                                                                <i class="fa fa-clock-o text-muted" style="font-size: 9px;"></i> <strong>Period:</strong> <?php echo htmlspecialchars($pYear); ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td><?php echo isset($row['section_heading']) ? $row['section_heading'] : '' ?></td>
                                <td><?php echo $row['email'] ?></td>
                                <td><?php echo $row['phone'] ?></td>

                                <!-- Modern Status Toggle -->
                                <td>
                                    <div class="btn-group publish modern-status-toggle" data-toggle="buttons" data-href="<?php echo base_url('admin/office_bearer/publish/' . $row['id']) ?>">
                                        <label class="btn <?php echo ($row['is_publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                                            <input type="radio" name="publish" value="1"><span>Active</span>
                                        </label>
                                        <label class="btn <?php echo ($row['is_publish'] == 0) ? 'active' : '' ?>" data-active-class="danger">
                                            <input type="radio" name="publish" value="0"><span>Inactive</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- Modern Action Buttons -->
                                <td style="text-align: right;">
                                    <div class="modern-actions">
                                        <a href="javascript:void(0)" class="btn btn-edit edit" data-href="<?php echo base_url('admin/office_bearer/edit/' . $row['id']) ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-delete" data-href="<?php echo base_url('admin/office_bearer/delete/' . $row['id']) ?>" data-toggle="modal" data-target="#delete" data-precheck="" data-message="Delete this Office Bearer ?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                        <?php $view = base_url(OFFICE_BEARER) . '/' . $row['image'] ?>
                                        <a href="<?php echo $view ?>" target="_blank" data-toggle="ajax" class="btn btn-view">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>











            </div>
        </div>
    </div>
    <!-- /.table-responsive -->

    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info"  role="status" aria-live="polite">
                <?php if ($config['total_rows']) { ?>
                    Showing <?php echo $config['from'] . ' to  ' . ($config['to']) . ' of ' . $config['total_rows'] ?>  entries
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" >
                <?php echo $links ?>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="alert alert-warning col-md-6 col-md-offset-3 mt-md" style="white-space: normal;">
        <h4>No Results Found</h4>
        <p>Seems there are none! Try changing a filter (if applicable) or how about creating a new one?</p>
    </div>
<?php } ?>