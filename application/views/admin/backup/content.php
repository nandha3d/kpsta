

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


<!--                    <tr>
                        <td class="col-sm-1">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">
                                    <input data-target="tbody" data-toggle="selectrow" class="list-checkbox" name="cb4" value="4" type="checkbox">
                                </span>
                            </div>
                        </td>

                        <td>Website Files</td>
                        <td>Download</td>
                    </tr>-->

                    <tr>
                        <td class="col-sm-1">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">
                                    <input data-target="tbody" data-toggle="selectrow" class="list-checkbox" name="cb4" value="4" type="checkbox">
                                </span>
                            </div>
                        </td>

                        <td>Membership DB</td>
                        <td><a href="<?php echo base_url("admin/backup/download_db?db=membership") ?>"> Download</a></td>
                    </tr>
                    <tr>
                        <td class="col-sm-1">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">
                                    <input data-target="tbody" data-toggle="selectrow" class="list-checkbox" name="cb4" value="4" type="checkbox">
                                </span>
                            </div>
                        </td>

                        <td>Kpsta DB</td>
                        <td><a href="<?php echo base_url("admin/backup/download_db?db=kpsta") ?>"> Download</a></td>
                    </tr>


                </tbody>


            </table>











        </div>
    </div>
</div>
<!-- /.table-responsive -->

