<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive  " >
            <table class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Definition</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>


                    <?php foreach ($list as $key => $row) { ?>
                        <tr  data-tr="<?php echo $row['id'] ?>"  >

                            <td>
                                <?php echo $row['name'] ?>
                            </td>


                            <td><?php echo $row['definition'] ?></td>

                            <td>
                                <?php if ($row['id'] != 1) { ?>
                                    <a href="javascript:void(0)"   class="edit"   data-href="<?php echo base_url('admin/aauth/group/edit/' . $row['id']) ?>"  >
                                        <span><i class="fa fa-pencil-square-o"></i>  Edit</span>
                                    </a>
                                <?php } ?>
                            </td>






                        </tr>
                    <?php } ?>
                </tbody>
            </table>











        </div>
    </div>
</div>
<!-- /.table-responsive -->


