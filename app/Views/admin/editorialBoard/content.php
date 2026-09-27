<div class="box-body" style="padding: 0 15px 15px;">
    <div class="table-responsive">
        <table class="table table-hover table-striped modern-table" style="margin-bottom: 0; vertical-align: middle;">
            <thead>
                <tr style="background: #6366f1; color: #fff;">
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" class="checkall" title="Select All">
                    </th>
                    <th style="width: 70px; text-align: center;">ORDER</th>
                    <th style="min-width: 200px;">POSITION / DESIGNATION</th>
                    <th style="min-width: 220px;">MEMBER NAME</th>
                    <th style="width: 140px; text-align: center;">STATUS</th>
                    <th style="width: 150px; text-align: right;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($members)) {
                    foreach ($members as $row) { ?>
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" class="checkall-item">
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <span class="badge" style="background: #e0e7ff; color: #4338ca; font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: 600;">
                                    #<?php echo htmlspecialchars($row['position']); ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 26px; height: 26px; border-radius: 50%; background: #ff7518; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                        <i class="fa fa-pencil" style="font-size: 11px;"></i>
                                    </div>
                                    <strong style="color: #0f172a; font-size: 13.5px;"><?php echo htmlspecialchars($row['designation']); ?></strong>
                                </div>
                            </td>
                            <td style="vertical-align: middle;">
                                <div class="modern-avatar-group" style="display: flex; align-items: center;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b; margin-right: 10px; font-size: 12px;">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <span style="font-weight: 600; color: #1e293b; font-size: 13px;"><?php echo htmlspecialchars($row['name']); ?></span>
                                </div>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div class="btn-group publish modern-status-toggle" data-toggle="buttons" data-href="<?php echo base_url('admin/editorial_board/publish/' . $row['id']) ?>">
                                    <label class="btn btn-xs <?php echo ($row['is_publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                                        <input type="radio" name="publish" value="1"><span>Active</span>
                                    </label>
                                    <label class="btn btn-xs <?php echo ($row['is_publish'] == 0) ? 'active' : '' ?>" data-active-class="danger">
                                        <input type="radio" name="publish" value="0"><span>Inactive</span>
                                    </label>
                                </div>
                            </td>
                            <td style="text-align: right; vertical-align: middle;">
                                <div class="modern-actions" style="display: inline-flex; gap: 6px;">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-edit edit" data-href="<?php echo base_url('admin/editorial_board/edit/' . $row['id']) ?>" title="Edit Member" style="border-radius: 4px; padding: 4px 10px;">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-delete" data-href="<?php echo base_url('admin/editorial_board/delete/' . $row['id']) ?>" data-toggle="modal" data-target="#delete" data-message="Are you sure you want to delete '<?php echo htmlspecialchars($row['name']); ?>' (<?php echo htmlspecialchars($row['designation']); ?>)?" title="Delete Member" style="border-radius: 4px; padding: 4px 10px;">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px; color: #94a3b8;">
                            <i class="fa fa-info-circle" style="font-size: 24px; margin-bottom: 8px;"></i>
                            <p style="margin: 0; font-size: 14px;">No editorial board members found.</p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination & Bottom Info -->
    <div class="row" style="margin-top: 15px; align-items: center;">
        <div class="col-sm-6 text-muted" style="font-size: 13px;">
            <?php if (isset($config) && $config['total_rows'] > 0) { ?>
                Showing <?php echo $config['from']; ?> to <?php echo $config['to']; ?> of <?php echo $config['total_rows']; ?> members
            <?php } ?>
        </div>
        <div class="col-sm-6 text-right">
            <?php echo isset($links) ? $links : ''; ?>
        </div>
    </div>
</div>
