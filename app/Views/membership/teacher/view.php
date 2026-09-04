<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel">View details</h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">

    <div class="overlay" >
        <i class="fa fa-refresh fa-spin"></i>
    </div>

    <div class="form-group">
        <label class="col-md-6">Name</label> 
        <?php echo $name ?>
    </div>

    <div class="form-group">
        <label class="col-md-6">Designation</label> 
        <?php echo $designationName ?>
    </div>


    <div class="form-group">
        <label  class="col-md-6">Adhyapaka sabdham subscriber </label> 
        <?php echo ($adhyapaka_sabdham_subscriber == 1 ) ? "Yes" : "No" ?>
    </div>

    <div class="form-group">
        <label class="col-md-6">School</label> 
        <?php echo $schoolName ?>
    </div>
    <div class="form-group">
        <label class="col-md-6">Branch</label> 
        <?php echo $branchName ?>
    </div>
    <div class="form-group">
        <label class="col-md-6">Sub Dist</label> 
        <?php echo $subDist ?>
    </div>
    <div class="form-group">
        <label class="col-md-6">Education Dist</label> 
        <?php echo $eduDist ?>
    </div>
    <div class="form-group">
        <label class="col-md-6">District</label> 
        <?php echo $dist ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
</div>

