<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Service Corner
            <small>Manage Services</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Service Corner</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Service List</h3>
                        <div class="box-tools">
                            <a href="<?php echo base_url('admin/ServiceCorner/add'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Service</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <?php if($this->session->flashdata('success_msg')): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $this->session->flashdata('success_msg'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error_msg')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $this->session->flashdata('error_msg'); ?>
                            </div>
                        <?php endif; ?>

                        <table class="table table-hover table-bordered table-striped" id="serviceCornerTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service Number</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Icon</th>
                                    <th>Status</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($services)): foreach($services as $key => $service): ?>
                                    <tr id="row-<?php echo $service['id']; ?>">
                                        <td><?php echo $key + 1; ?></td>
                                        <td><?php echo $service['service_number']; ?></td>
                                        <td><?php echo $service['title']; ?></td>
                                        <td><?php echo mb_strimwidth($service['description'], 0, 50, "..."); ?></td>
                                        <td>
                                            <span class="label label-info"><i class="fa fa-info-circle"></i> <?php echo $service['icon']; ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group publish" data-toggle="buttons" data-href="<?php echo base_url('admin/ServiceCorner/publish/' . $service['id']); ?>">
                                                <label class="btn btn-xs btn-default <?php echo ($service['status'] == 1) ? 'active' : ''; ?>" data-active-class="success">
                                                    <input type="radio" name="publish" value="1"><span> Yes</span>
                                                </label>
                                                <label class="btn btn-xs btn-default <?php echo ($service['status'] == 0) ? 'active' : ''; ?>" data-active-class="danger">
                                                    <input type="radio" name="publish" value="0"><span> No</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <div class="modern-actions" style="display: inline-flex; align-items: center; gap: 4px;">
                                                <a href="<?php echo base_url('admin/ServiceCorner/rules/'.$service['id']); ?>" class="btn btn-info" title="Manage Items" style="padding: 6px 10px; font-size: 13px;"><i class="fa fa-list"></i></a>
                                                <a href="<?php echo base_url('admin/ServiceCorner/edit/'.$service['id']); ?>" class="btn btn-edit" title="Edit Service"><i class="fa fa-pencil"></i></a>
                                                <a href="javascript:void(0);" onclick="deleteService(<?php echo $service['id']; ?>)" class="btn btn-delete" title="Delete Service"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="7" class="text-center">No services found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'); ?>">
<script src="<?php echo base_url('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'); ?>"></script>
<script>
    $(function () {
        $('#serviceCornerTable').DataTable({
            'paging'      : true,
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false
        })
    })

    $(document).on('click', '.publish label', function (e) {
        e.preventDefault();
        var $label = $(this);
        var $group = $label.closest('.publish');
        var href = $group.data('href');
        
        $.ajax({
            url: href,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.code === 'success') {
                    $group.find('label').removeClass('active');
                    if (response.status == 1) {
                        $group.find('label:first-child').addClass('active');
                    } else {
                        $group.find('label:last-child').addClass('active');
                    }
                }
            }
        });
    });

    function deleteService(id) {
        if(confirm("Are you sure you want to delete this service?")) {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('admin/ServiceCorner/deleteAction'); ?>",
                data: {id: id},
                dataType: "json",
                success: function(response) {
                    if(response.code == 'success') {
                        $('#row-'+id).remove();
                        alert(response.msg);
                    } else {
                        alert(response.msg);
                    }
                }
            });
        }
    }
</script>
