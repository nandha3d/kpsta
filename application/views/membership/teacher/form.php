<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl, "data-title" => $title]) ?> 
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
    <div class="modal-loading-bar"></div>
</div>
<div class="modal-body">

    <div class="overlay" >
        <i class="fa fa-refresh fa-spin"></i>
    </div>


    <?php if (isset($error)) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $error ?>  
        </div>
    <?php } else if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>



    <?php if (is_array($branchSelect)) { ?>
        <?php $class = form_error('branch') ? 'form-group has-error' : 'form-group' ?>
        <?php $selected = isset($formValues['branch']) ? $formValues['branch'] : '' ?>
        <div class="<?php echo $class ?>">
            <label for="heading">Branch </label>
            <?php echo form_dropdown('branch', $branchSelect, $selected, 'class="form-control select2" style="width: 100%;"   id="select2-branch" '); ?>
        </div>
    <?php } ?>

    <?php $class = form_error('school') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['school']) ? $formValues['school'] : '' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">School <span class="text-danger">*</span></label>
        <?php echo form_dropdown('school', $schoolSelect, $selected, 'class="form-control select2" id="select2-office" style="width: 100%;"  '); ?>
    </div>


    <?php $class = form_error('name') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Name <span class="text-danger">*</span></label>
        <input  class="form-control" name="name" placeholder="Enter Name"  value="<?php echo isset($formValues['name']) ? $formValues['name'] : '' ?>"  required="required">
    </div>


    <?php $class = form_error('designation') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['designation']) ? $formValues['designation'] : '' ?>
    <div class="<?php echo $class ?>">
        <label for="heading">Designation <span class="text-danger">*</span></label>
        <?php echo form_dropdown('designation', $designationSelect, $selected, 'class="form-control select2" style="width: 100%;"   '); ?>
    </div>


    <?php $class = form_error('mobile') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label >Phone No </label>
        <input  class="form-control" name="mobile" placeholder="Enter phone"  value="<?php echo isset($formValues['mobile']) ? $formValues['mobile'] : '' ?>"  >
    </div>


    <?php $class = form_error('teacher_type') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['teacher_type']) ? $formValues['teacher_type'] : '' ?>
    <div class="<?php echo $class ?> "  >
        <label>Teacher Type <span class="text-danger"> *</span></label>
        <?php echo form_dropdown('teacher_type', [0 => 'SELECT', 1 => 'Govt', 2 => 'Aided'], $selected, 'class="form-control select2"   style="width: 100%;"     '); ?>
    </div>


    <?php $checked = (isset($formValues['adhyapaka_sabdham_subscriber']) && $formValues['adhyapaka_sabdham_subscriber']) ? 'checked="checked"' : '' ?>
    <div class="form-group">
        <label>Adhyapaka sabdham subscriber </label>&nbsp;&nbsp;&nbsp;
        <input type="checkbox"  name="adhyapaka_sabdham" id="group-checkbox"   value="1"  <?php echo $checked ?>>
    </div>





</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times text-danger "></i> Close</button>
    <button type="submit" class="btn btn-primary"  ><i class="fa fa-save "></i> Save</button>
</div>
<?php echo form_close(); ?>


<script type="text/javascript">

    $(function () {
        $(".moda").find(".select2").select2({
            dropdownParent: $("#modal")
        });
        $('.datepicker').datepicker({
            autoclose: true
        });



        $('#select2-branch').select2().on("change", function (e) {
            var val = $(this).select2("val");
            if (val > 0) {
                $('#modal .modal-loading-bar').addClass('active');
                $('#modal .overlay').show();
                var path = "<?php echo base_url('membership/aauth/getOffice') ?>";
                var text = $(this).select2("data")[0].text;
                $("#label-office").text(text);
                $.ajax({
                    url: path,
                    type: 'POST',
                    dataType: 'json',
                    data: {groupId: 6, officeId: val},
                    context: this,
                    complete: function () {
                        $('#modal .modal-loading-bar').removeClass('active');
                        $('#modal .overlay').hide();
                    },
                    success: function (result) {
                        $(this).find('.modal-body .alert').remove();
                        if (result.code === 'success') {
                            var option = '';
                            $.each(result.officeSelect, function (k, val) {
                                option += '<option value="' + k + '">' + val + '</option> ';
                            });
                            $("#select2-office").find('option').remove().end().append(option);
                            $("#select2-office").select2();
                        }

                    }
                });
                return false;
            }

        });

    });

</script>