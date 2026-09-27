
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
                                <td>
                                    <strong><?php echo htmlspecialchars($row['designation']); ?></strong>
                                    <?php if (!empty($row['year'])) { ?>
                                        <span class="text-muted" style="font-size: 11px;">(<?php echo htmlspecialchars($row['year']); ?>)</span>
                                    <?php } ?>
                                    <?php
                                    if (!empty($row['previous_positions'])) {
                                        $prevPos = is_array($row['previous_positions']) ? $row['previous_positions'] : json_decode($row['previous_positions'], true);
                                        if (!empty($prevPos) && is_array($prevPos)) {
                                            echo '<div style="margin-top: 4px; padding-top: 4px; border-top: 1px dashed #cbd5e1; font-size: 11px; color: #475569;">';
                                            foreach ($prevPos as $p) {
                                                $isEnabled = (!isset($p['is_enabled']) || $p['is_enabled'] === 1 || $p['is_enabled'] === '1' || $p['is_enabled'] === true);
                                                $pDesig = isset($p['designation']) ? $p['designation'] : '';
                                                $pYear = !empty($p['year']) ? ' (' . $p['year'] . ')' : '';
                                                $pPos = (!empty($p['position']) && (int)$p['position'] < 25) ? ' <span class="badge" style="font-size: 9px; padding: 1px 4px; background: #fef3c7; color: #92400e;" title="Position Order">#' . htmlspecialchars($p['position']) . '</span>' : '';
                                                $pLvl = (!empty($p['level']) && $p['level'] !== 'State') ? ' <span class="badge" style="font-size: 9px; padding: 1px 4px; background: #e0f2fe; color: #0369a1;">' . htmlspecialchars($p['level'] . (!empty($p['section_heading']) ? ' - ' . $p['section_heading'] : '')) . '</span>' : '';
                                                $pStatus = !$isEnabled ? ' <span class="badge" style="font-size: 9px; padding: 1px 4px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;"><i class="fa fa-ban"></i> Disabled</span>' : '';
                                                $rowStyle = !$isEnabled ? 'opacity: 0.55; text-decoration: line-through;' : '';
                                                echo '<div style="margin-bottom: 2px; ' . $rowStyle . '"><i class="fa fa-history text-muted" style="font-size: 10px;"></i> ' . htmlspecialchars($pDesig) . htmlspecialchars($pYear) . $pPos . $pLvl . $pStatus . '</div>';
                                            }
                                            echo '</div>';
                                        }
                                    }
                                    ?>
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