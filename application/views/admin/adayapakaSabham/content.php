
<?php if (count($content)) { ?> 

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive  " >
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Upload Type</th>
                            <th>Publish</th>
                            <th style="text-align: right;">Action</th>
                            </tr>
                    </thead>
                    <tbody>


                        <?php foreach ($content as $row) { ?>
                            <tr  data-tr="<?php echo $row['id'] ?>"  >

                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon">
                                            <input type="checkbox" data-target="tbody" data-toggle="selectrow" class="list-checkbox" name="cb4" value="4">
                                        </span>

                                        
                                    </div>
                                </td>


                                <td  class="text-ellipsis" title="<?php echo $row['description'] ?>"><?php echo $row['description'] ?></td>

                                <td>
                                    <?php if ($row['upload_type'] == "file") { ?>
                                        <label class="btn btn-xs btn-success">pdf</label>
                                    <?php } else { ?>
                                        <label class="btn btn-xs btn-info">url</label>
                                    <?php } ?>
                                </td>






                                <td>
                                    <div class="btn-group publish" data-toggle="buttons" data-href="<?php echo base_url('admin/adayapaka_sabham/publish/' . $row['id']) ?>" >
                                        <label class="btn btn-xs btn-default <?php echo ($row['is_publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                                            <input type="radio" name="publish" value="1"  ><span> Yes</span>
                                        </label>
                                        <label class="btn btn-xs btn-default <?php echo ($row['is_publish'] == 0) ? 'active' : '' ?>" data-active-class="danger">
                                            <input type="radio" name="publish" value="0" > <span> No</span>
                                        </label>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <div class="modern-actions">
                                        <a href="javascript:void(0)"   class="edit btn btn-edit"   data-href="<?php echo base_url('admin/adayapaka_sabham/edit/' . $row['id']) ?>"><i class="fa fa-pencil"></i> Edit</a>
                                        <a  href="javascript:void(0)" class=" btn btn-delete" data-href="<?php echo base_url('admin/adayapaka_sabham/delete/' . $row['id']) ?>" data-toggle="modal" data-target="#delete" data-precheck="" data-message="Are you sure you want to delete this?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation"><i class="fa fa-trash"></i> Delete</a>
                                        <a href="<?php echo base_url(ADAYAPAKA_SABHAM_IMAGE . '/' . $row['image']) ?>" class="btn btn-view" target="_blank" data-toggle="ajax"><i class="fa fa-eye"></i> View</a>
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