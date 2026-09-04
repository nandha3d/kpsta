
<?php if (isset($data['officeLabel']) && $data['officeLabel']) { ?> 

    <?php if ($groupId == 6) { ?>
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="well the-fieldset">
                    <label  ><u> School Count:</u></label>
                    <div class="row">
                        <?php foreach ($consolidated as $row) { ?>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-sm-10" for="heading">
                                        <?php
                                        if ($row['school_type'] == 1) {
                                            $label = 'Government';
                                        } else if ($row['school_type'] == 2) {
                                            $label = 'Aided';
                                        } else {
                                            $label = 'Other';
                                        }
                                        echo $label . ':';
                                        ?>
                                    </label>
                                    <div class="col-sm-2">
                                        <?php echo $row['count'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="col-sm-10" for="heading">
                                    Total:
                                </label>
                                <div class="col-sm-2">
                                    <?php echo $total_rows ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </fieldset>

            </div>
        </div>
    <?php } ?>


    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive  " >
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <?php
                            if (isset($data['officeLabel']) && $data['officeLabel']) {
                                echo " <th>" . $data['previousLabel'] . "</th>";
                            } else {
                                echo " <th>Office</th>";
                            }
                            ?>
                            <?php
                            if ($groupId == 6) {
                                if ($this->session->userdata('group') != 4) {
                                    echo "<th>Sub Dist</th>";
                                }
                                echo "<th>School Type</th>";
                            }
                            ?>
                            <td>Action</td>
                        </tr>


                    </thead>
                    <tbody>


                        <?php foreach ($data['officeSelect'] as $index => $row) { ?>
                            <tr  data-tr="<?php echo $row['id'] ?>"  >
                                <td><?php echo $index + 1 ?></td>

                                <?php
                                if ($groupId == 6) {
                                    echo '<td><a target="_blank" href="' . base_url('membership/teacher?group=6&office=' . $row['id'] . '&view=1') . '">' . $row['name'] . '</a></td>';
                                } else {
                                    ?>
                                    <td  class="text-ellipsis" title="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></td>
                                <?php } ?>

                                <?php
                                if (isset($data['officeLabel']) && $data['officeLabel']) {
                                    echo " <td>" . $row['office'] . "</td>";
                                } else {
                                    echo " <td>Office</td>";
                                }
                                ?>

                                <?php
                                if ($groupId == 6) {

                                    if ($row['school_type'] == 1) {
                                        $schoolType = "Govt";
                                    } else if ($row['school_type'] == 2) {
                                        $schoolType = "Aided";
                                    }
//                                    echo "<td>".$row['sdName']."</td><td>".$schoolType."</td>";
                                    if ($this->session->userdata('group') != 4) {
                                        echo "<td>" . $row['sdName'] . "</td>";
                                    }
                                    echo "<td>" . $schoolType . "</td>";
                                }
                                ?>
                                <td>
        <?php if ($groupId == 6) { ?>
                                        <a href="javascript:void(0)"   class="edit"   data-href="<?php echo base_url('membership/main/edit/' . $row['id'] . '?group_id=' . $groupId) ?>"  >
                                            <span><i class="fa fa-pencil-square-o"></i>  Edit</span>
                                        </a>
                                        <a  href="javascript:void(0)" class="" data-href="<?php echo base_url('membership/main/delete/' . $row['id'] . '?group_id=' . $groupId) ?>"   data-toggle="modal" data-target="#delete"  data-precheck="" data-message="Delete this  ?" data-confirm-text="Delete" data-confirm-callback="executeAction" data-cancel-text="Cancel" data-cancel-callback="dismissConfirmation" >
                                            <span><i class="fa fa-fw fa-trash-o text-danger"></i> <span class="">Delete</span></span>
                                        </a>   
        <?php }
        ?>
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