<?php if (count($listNews)) { ?> 

    <div class="table-responsive  ">
        <table class="table table-hover table-striped table-bordered no-margin">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;"><input type="checkbox" class="list-checkbox-all" title="Select all on this page"></th>
                    <th>Heading</th>
                    <th>Image</th>
                    <th>Content</th>
                    <th>Created</th>
                    <th>Publish</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listNews as $news) { ?>
                    <tr data-tr="<?php echo $news['id'] ?>" >
                        <td width="8%" class="">

                            <div class="text-center"><input type="checkbox" data-target="tbody" data-toggle="selectrow" class="list-checkbox" name="ids[]" value="<?php echo $news['id'] ?>"></div>



                            <?php // echo $news->id ?>


                        </td>
                        <td width="20%" class="text-ellipsis" title="<?php echo $news['heading'] ?>"><?php echo $news['heading'] ?></td>
                        <td width="10%">
                            <?php if (!empty($news['image'])) { ?>
                                <img src="<?php echo base_url('uploads/news/'.$news['image']); ?>" alt="News Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php } ?>
                        </td>
                        <td width="40%" >
                            <?php
                            if (strlen($news['content']) > 100) {
                                echo '<span class="collapse-group"><div class="collapse">' . $news['content'] . '</div> <p><a class="btn btn-sm btn-default content-collapse" href="#">View more &raquo;</a></p></span>';
                            } else {
                                echo $news['content'];
                            }
                            ?>
                        </td>
                        <td width="14%"><?php echo date('d-m-Y', strtotime($news['created_at'])) ?></td>
                        <td width="8%">
                            <div class="btn-group publish modern-status-toggle" data-toggle="buttons">
                                <label class="btn <?php echo ($news['publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                                    <input type="radio" name="publish" value="1"  data-id="<?php echo $news['id'] ?>"><span>Yes</span>
                                </label>
                                <label class="btn <?php echo ($news['publish'] == 0) ? 'active' : '' ?>" data-active-class="danger">
                                    <input type="radio" name="publish" value="0"  data-id="<?php echo $news['id'] ?>"><span>No</span>
                                </label>
                            </div>
                        </td>
                                <td style="text-align: right;">
                                    <div class="modern-actions">
                                        <a href="javascript:void(0)"   class="edit btn btn-edit"   data-href="<?php echo base_url('admin/news/edit/' . $news['id']) ?>"><i class="fa fa-pencil"></i> Edit</a>
                                        <a  href="javascript:void(0)" class=" btn btn-delete" data-href="<?php echo base_url('admin/news/delete/' . $news['id']) ?>" data-toggle="modal" data-target="#delete" data-precheck="" data-message="Are you sure you want to delete this?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation"><i class="fa fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            

                    </tr>
                <?php } ?>
            </tbody>
        </table>
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
