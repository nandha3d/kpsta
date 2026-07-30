
<?php if (count($content)) { ?> 

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive  " >
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
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
                                    <input type="checkbox" data-target="tbody" data-toggle="selectrow" class="list-checkbox modern-table-checkbox" name="cb4" value="4">
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
                                <td><?php echo $row['designation'] ?></td>
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