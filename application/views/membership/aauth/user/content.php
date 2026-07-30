<div class="table-responsive  ">
    <table class="table table-hover table-striped table-bordered no-margin">
        <thead>
            <tr>
                <!--<th>ID</th>-->
                <th>Office</th>
                <th>User Name</th>
                <th>User ID</th>
                <th>Status</th>
                <th>Group</th>
                <th>Last Login</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($content as $user) { ?>
                <tr data-tr="<?php echo $user->id ?>" >
                    <!--<td><?php echo $user->id ?></a></td>-->
                    <td><?php echo $user->officeName ?></td>
                    <td><?php echo $user->name ?></td>
                    <td><?php echo $user->username ?></td>
                    <td>
                        <?php if ($user->banned == 0) { ?>
                            <span class="label label-success">Active</span>
                        <?php } else { ?>
                            <span class="label label-danger">Locked</span>
                        <?php } ?>
                    </td>
                    <td>


                        <?php if ($user->group_id == 1) { ?>
                            <span class="label label-info"><?php echo $user->groupName ?></span>
                        <?php } else { ?>
                            <span class="label label-warning"><?php echo $user->groupName ?></span>
                        <?php } ?>
                    </td>
                    <td><?php echo $user->last_activity ? date_format(date_create($user->last_activity), "d/m/y H:i:s") : '' ?></td>
                    <td><a href="javascript:void(0)" class="edit" data-id="<?php echo $user->id ?>" data-href="<?php echo base_url('membership/aauth/edit/' . $user->id) ?>"><i class="fa fa-edit"></i> Edit</a></td>
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
