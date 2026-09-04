<?php if (count($listNews)) { ?> 

    <div class="table-responsive  ">
        <table class="table table-hover table-striped table-bordered no-margin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Content</th>
                    <th>Position</th>
                    <th>Group</th>
                    <th>File</th>
                    <th>Created</th>
                    <th>Publish</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listNews as $news) { ?>
                    <tr data-tr="<?php echo $news['id'] ?>" >
                        <td width="8%" class="">

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">
                                    <input type="checkbox" data-target="tbody" data-toggle="selectrow" class="list-checkbox" name="cb4" value="4">
                                </span>

                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle btn-nospin" data-toggle="dropdown">
                                        <i class="fa fa-angle-down "></i>
                                    </button>
                                    <ul class="pull-left page-list-actions dropdown-menu" role="menu">

                                        <li>
                                            <a href="javascript:void(0)"   class="edit"   data-href="<?php echo base_url('membership/whats_new/edit/' . $news['id']) ?>" >
                                                <span><i class="fa fa-pencil-square-o"></i> Edit</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a  href="javascript:void(0)" class="" data-href="<?php echo base_url('membership/whats_new/delete/' . $news['id']) ?>"   data-toggle="modal" data-target="#delete"  data-precheck="" data-message="Delete this News ?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation" >
                                                <span><i class="fa fa-fw fa-trash-o text-danger"></i> <span class="">Delete</span></span>
                                            </a>            
                                        </li>

                                        <?php if ($news['file_name']) { ?>
                                            <li>
                                                <?php $view = base_url(MEMBERSHIP_PATH) . '/' . $news['file_name'] ?>
                                                <a href="<?php echo $view ?>"  target="_blank" data-toggle="ajax"  >
                                                    <span><i class="fa fa-eye"></i>  View</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a  href="javascript:void(0)"  data-href="<?php echo base_url('membership/whats_new/fileremove/' . $news['id']) ?>"   class="file-remove"   >
                                                    <span><i class="fa fa-remove"></i>  Delete file</span>
                                                </a>
                                            </li>
                                        <?php } ?>

                                    </ul>
                                </div>
                            </div>



                            <?php // echo $news->id ?>


                        </td>
                        <td width="40%" >
                            <?php
                            echo $news['content'];
                            if (strlen($news['content']) > 100) {
//                                echo '<span class="collapse-group"><div class="collapse">' . $news['content'] . '</div> <p><a class="btn btn-sm btn-default content-collapse" href="#">View more &raquo;</a></p></span>';
                            } else {
//                                echo $news['content'];
                            }
                            ?>
                        </td>
                        <td width="5%"><?php echo $news['position'] ? $news['position'] : "" ?></td>
                        <td width="12%"><?php echo $news['group'] ?></td>
                        <td width="14%">
                            <?php if ($news['file_name']) { ?>
                                <?php $view = base_url(MEMBERSHIP_PATH) . '/' . $news['file_name'] ?>
                                <a href="<?php echo $view ?>"  target="_blank" data-toggle="ajax"  >
                                    View</span>
                                </a>
                            <?php }
                            ?>


                        </td>
                        <td width="14%"><?php echo date('d-m-Y', strtotime($news['created_at'])) ?></td>
                        <td width="8%">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-xs btn-default <?php echo ($news['publish'] == 1) ? 'active' : '' ?>" data-active-class="success">
                                    <input type="radio" name="publish" value="1"  data-id="<?php echo $news['id'] ?>"><span> Yes</span>
                                </label>
                                <label class="btn btn-xs btn-default <?php echo ($news['publish'] == 0) ? 'active' : '' ?>" data-active-class="danger">
                                    <input type="radio" name="publish" value="0"  data-id="<?php echo $news['id'] ?>"> <span> No</span>
                                </label>
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
