<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive  " >
            <table class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>


                    <?php foreach ($categories as $key => $row) { ?>
                        <tr  data-tr="<?php echo $key ?>"  >

                            <td>
                                <?php echo $key ?>
                            </td>


                            <td><?php echo $row ?></td>

                            <td> <a href="javascript:void(0)"   class="edit"   data-href="<?php echo base_url('admin/order-circular/category/edit/' . $key) ?>"  >
                                    <span><i class="fa fa-pencil-square-o"></i>  Edit</span>
                                </a>
                            </td>






                        </tr>
                    <?php } ?>
                </tbody>
            </table>











        </div>
    </div>
</div>
<!-- /.table-responsive -->


