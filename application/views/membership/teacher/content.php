<div class="data"  data-year="<?php echo isset($year) ? $year : '' ?>" data-year-config="<?php echo isset($yearConfig) ? $yearConfig : '' ?>" data-view="<?php echo isset($view) ? $view : '' ?>"></div>
<?php if (count($orders)) { ?> 
    <div class="row">
        <div class="col-sm-12">
            <fieldset class="well the-fieldset">

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-sm-10" for="heading">Number of Aided:</label>
                            <div class="col-sm-2">
                                <?php echo $count['aidedMembers'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-sm-10" for="heading">Number of Govt: </label>
                            <div class="col-sm-2 text-left">
                                <?php echo $count['govtMembers'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-sm-10" for="heading">Total:</label>
                            <div class="col-sm-2">
                                <?php echo $count['totalCount'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3  text-center">
                        <a  href="javascript:void(0)" data-href="<?php echo base_url('membership/teacher/consoliated') ?>" class="btn btn-sm btn-default btn-nospin" id="consoliated-count" type="button"><i class="fa fa-arrow-down"></i> View more</a>
                    </div>
                </div>


                <div class="consolidated-count-content">

                </div>


            </fieldset>

        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive  " >
                <table class="table table-hover table-striped table-bordered" data-year="<?php echo isset($year) ? $year : '' ?>" data-year-config="<?php echo isset($yearConfig) ? $yearConfig : '' ?>" data-view="<?php echo isset($view) ? $view : '' ?>">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <input type="checkbox"   class="checkbox-all" name="checkbox-all" value="all"> 
                            </th>
                            <th>Sl.No</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>School</th>
                            <th>Type</th>
                            <th>A.S Subscriber</th>
                            <th>Branch</th>

                        </tr>
                    </thead>
                    <tbody>


                        <?php
                        $slNo = $config['from'];
                        foreach ($orders as $row) {
                            ?>
                            <tr  data-tr="<?php echo $row['id'] ?>"  >

                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon">
                                            <input type="checkbox" data-target="tbody" data-toggle="selectrow" class="checkbox-list checkbox" name="checkbox" value="<?php echo $row['id'] ?>">
                                        </span>

                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle btn-nospin" data-toggle="dropdown">
                                                <i class="fa fa-angle-down "></i>
                                            </button>
                                            <ul class="pull-left page-list-actions dropdown-menu" role="menu">
                                                <?php if ($row['is_confirmed'] != 1 && $this->session->userdata('group') != 7) { ?>
                                                    <li>
                                                        <a href="javascript:void(0)"   class="edit"   data-href="<?php echo base_url('membership/teacher/edit/' . $row['id']) ?>"  >
                                                            <span><i class="fa fa-pencil-square-o"></i>  Edit</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a  href="javascript:void(0)" class="" data-select="single" data-href="<?php echo base_url('membership/teacher/delete/' . $row['id']) ?>"   data-toggle="modal" data-target="#delete"  data-precheck="" data-name="<?php echo $row['name'] ?>" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation" >
                                                            <span><i class="fa fa-fw fa-trash-o text-danger"></i> <span class="">Delete</span></span>
                                                        </a>            
                                                    </li>
                                                <?php } ?>

                                                <li>
                                                    <a href="javascript:void(0)"   class="edit"   data-href="<?php echo base_url('membership/teacher/view/' . $row['id']) ?>"  >
                                                        <span><i class="fa fa-adjust"></i>  View</span>
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </td>



                                <td><?php echo $slNo++ ?></td>
                                <td  class="text-ellipsis" title="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></td>
                                <td  class="text-ellipsis" title="<?php echo $row['designation'] ?>"><?php echo $row['designation'] ?></td>
                                <td  class="text-ellipsis"  ><?php echo $row['mobile'] ?></td>

                                <td  class="text-ellipsis" title="<?php echo $row['school'] ?>"><?php echo $row['school'] ?></td>
                                <td><?php
                                    if ($row['teacherType'] == 1) {
                                        echo "Govt";
                                    } else if ($row['teacherType'] == 2) {
                                        echo "Aided";
                                    }
                                    ?></td>
                                <td><?php echo $row['adhyapaka_sabdham_subscriber'] ? "Yes" : "No" ?></td>
                                <td  class="text-ellipsis"  ><?php echo $row['branch'] ?></td>




                            </tr>
                        <?php } ?>
                    </tbody>
                </table>











            </div>
        </div>
    </div>
    <!-- /.table-responsive -->

    <div class="row">
        <div class="col-sm-3">
            <div class="dataTables_length">
                <label>Show 
                    
                    <?php
                    $selected = isset($limit) ? $limit : '';
                    $dropdown = [20, 50, 100];
                    $limit = array_combine($dropdown, $dropdown);

                    echo form_dropdown('year-search', $limit, $selected, 'class="form-control input-sm select2 limit" ');
                    ?>
                    entries
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="dataTables_info text-left"  role="status" aria-live="polite">
                <?php if ($config['total_rows']) { ?>
                    Showing <?php echo $config['from'] . ' to  ' . ($config['to']) . ' of ' . $config['total_rows'] ?>  entries
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-6">
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