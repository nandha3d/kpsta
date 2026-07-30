
<?php if (count($content)) { ?> 

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive  " >
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>District</th>
                            <th>Website url</th>
                            <th style="text-align: right;">Action</th>
                            </tr>
                    </thead>
                    <tbody>


                        <?php foreach ($content as $row) { ?>
                            <tr  data-tr="<?php echo $row['id'] ?>"  >

                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon sm">
                                        </span>

                                        
                                    </div>
                                </td>



                                <td  class="text-ellipsis" title="<?php echo $row['district'] ?>"><?php echo $row['district'] ?></td>

                                <td><?php echo $row['website_url'] ?></td>
                                <td style="text-align: right;">
                                    <div class="modern-actions">
                                        <a href="javascript:void(0)"   class="edit btn btn-edit"   data-href="<?php echo base_url('admin/district/edit/' . $row['id']) ?>"><i class="fa fa-pencil"></i> Edit</a>
                                        <a  href="javascript:void(0)" class=" btn btn-delete" data-href="<?php // echo base_url('admin/district/delete/' . $row['id'])     ?>" data-toggle="modal" data-target="#delete" data-precheck="" data-message="Are you sure you want to delete this?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation"><i class="fa fa-trash"></i> Delete</a>
                                        <a  href="<?php echo base_url('admin/district/' . $row['id']) ?>"  >
                                                        <span><i class="fa fa-group"></i> <span class="">Add Office Br.</span></span>
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