<?php echo form_open('', ['autocomplete' => 'off', "name" => "editUserForm", 'id' => "save", "data-href" => isset($url) ? $url : '', 'data-href-add' => $addUrl]) ?> 
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



    <?php $class = form_error('group_id') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?> ">
        <?php $disabled = (isset($formValues['disabled']) && $formValues['disabled']) ? 'disabled = "disabled"' : ''; ?>
        <?php $selected = isset($formValues['group_id']) ? $formValues['group_id'] : '' ?>
        <label>Group</label>
        <?php echo form_dropdown('group_id', $groupSelect, $selected, 'class="form-control select2 select2-group enable-on-new" id="select2-group"  style="width: 100%;"  required= "required" data-event-name="group" ' . $disabled); ?>
    </div>

    <?php if (is_array($officeSelect)) { ?>
        <?php $class = form_error('office_id') ? 'form-group has-error' : 'form-group' ?>
        <?php $display = isset($officeLabel) ? $officeLabel : false; ?>
        <div class="<?php echo $class ?> hide-on-new group-id-2 f-g-select2-office"  style="display: <?php echo $display ? "block" : "none" ?>">
            <?php $selected = isset($formValues['office_id']) ? $formValues['office_id'] : '' ?>
            <label class="label-office"><?php echo isset($officeLabel) ? $officeLabel : 'Office' ?></label>
            <?php echo form_dropdown('office_id', $officeSelect, $selected, 'class="form-control select2 select2-office"   style="width: 100%;"    data-event-name="office"  '); ?>
        </div>
    <?php } ?>



    <?php $class = form_error('name') ? 'form-group has-error' : 'form-group' ?>
    <div class="<?php echo $class ?>">
        <label>Name<span class="text-danger"> *</span></label>
        <input type="text" class="form-control" name="name" placeholder="Enter Name" value="<?php echo isset($formValues['name']) ? $formValues['name'] : '' ?>">
    </div>

    <?php if (!is_array($formValues) || !in_array($formValues['group_id'], [5, 6])) { ?>
        <?php $class = form_error('code') ? 'form-group has-error' : 'form-group' ?>
        <div class="<?php echo $class ?> " style="display:none">
            <label>Short Code<span class="text-danger"> *</span></label>
            <input type="text" class="form-control" name="code" placeholder="Enter short code" value="<?php echo isset($formValues['code']) ? $formValues['code'] : '' ?>">
        </div>
    <?php } ?>


    <?php $class = form_error('school_type') ? 'form-group has-error' : 'form-group' ?>
    <?php $display = isset($formValues['group_id']) ? ($formValues['group_id'] == 6 ? true : false ) : false; ?>
    <?php $selected = isset($formValues['school_type']) ? $formValues['school_type'] : '' ?>
    <div class="<?php echo $class ?> hide-on-new" style="display: <?php echo $display ? "block" : "none" ?>">
        <label>School Type <span class="text-danger"> *</span></label>
        <?php echo form_dropdown('school_type', [0 => 'SELECT', 1 => 'Govt', 2 => 'Aided'], $selected, 'class="form-control select2 school-type"   style="width: 100%;"    data-event-name="office"  '); ?>

    </div>


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary"  >Save</button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

    $(function () {

        var $modal = $('#modal');
        var $modalLoadingBar = $modal.find('.modal-loading-bar');
        var $modalOverlay = $modal.find('.overlay');
        var $select2Office = $modal.find(".select2-office");
        var $formGroup = $select2Office.closest('.form-group');
        $modal.find(".select2").select2({
            dropdownParent: $("#modal")
        });
        $modal.find('.datepicker').datepicker({
            autoclose: true
        });

        $modal.on('show.bs.modal', function (e) {
            $(e.target).find("select[name='temp']").attr("name", "office_id");
        });

        $modal.on("change", '#select2-group, .select2-office', function (e) {
            $modal.find('.modal-body .alert').remove();
            var val = $(this).select2("val");
            var eventName = $(this).data('event-name');
            var officeId, groupId = false;
            var selectedGroupId = $modal.find('#select2-group').select2("val");
            if (selectedGroupId == 6) {
                $modal.find('.school-type').closest('.form-group').fadeIn();
            } else {
                $modal.find('.school-type').closest('.form-group').fadeOut();
            }

            if (selectedGroupId == 6 || selectedGroupId == 5 || selectedGroupId == null) {
                $modal.find('input[name="code"]').closest('.form-group').fadeOut();
            } else {
                $modal.find('input[name="code"]').closest('.form-group').fadeIn();
            }

            console.log(eventName);
            if (eventName == "group") {
                $modal.find('.f-g-select2-office').not(':first').remove();
                if (val == 2) {
                    $modal.find('.f-g-select2-office').hide();
                    return false;
                }
                groupId = 2;
            } else {
                officeId = val;
                groupId = Number($(this).closest('.form-group').data('group-id')) + Number(1);
                if ((selectedGroupId - 1) == $(this).closest('.form-group').data('group-id')) {
                    $modal.find(".select2-office").not(':last').attr("name", "temp");
                    return false;
                }
            }

            if (val < 1 || isNaN(groupId)) {
                return false;
            }


            $modalLoadingBar.addClass('active');
            $modalOverlay.show();
            $.ajax({
                url: "<?php echo base_url('membership/aauth/getOffice') ?>",
                type: 'POST',
                dataType: 'json',
                data: {groupId: groupId, officeId: officeId, selectedGroupId: selectedGroupId},
                context: this,
                complete: function () {
                    $modalLoadingBar.removeClass('active');
                    $modalOverlay.hide();
                    $(this).find('.modal-body .alert').remove();
                },
                success: function (result) {
                    if (result.code === 'success') {
                        var officeLabel = result.officeLabel;
                        if (result.officeSelect == false) {
                            $modal.find('.f-g-select2-office').hide();
                            return false;
                        }
                        if (officeLabel == false) {
                            $select2Office.closest('.form-group').hide();
                        } else {

                            if (eventName == "office") {
                                var controlForm = $('#teacher-details:first'),
                                        currentEntry = $(this).closest('.form-group');
                                currentEntry.find('.select2-office').select2('destroy');
                                var clone = $(currentEntry).clone();
                                clone.find('.select2-office').select2();
//                                clone.addClass('has-error');
                                clone.find('.select2-office').val(0).trigger('change');
                                currentEntry.find('.select2-office').select2();
                                $select2Office = clone.find('.select2-office');
                                $formGroup = $select2Office.closest('.form-group');
                                $modal.find('.group-id-' + groupId).remove();
                                var newEntry = $(currentEntry).after(clone);
                            }


                            $modal.find('.f-g-select2-office').show();

                            $formGroup.data('group-id', result.groupId);
                            $formGroup.addClass('group-id-' + groupId);
                            $formGroup.find(".label-office").text(officeLabel);
                            var option = '';
                            $.each(result.officeSelect, function (k, val) {
                                option += '<option value="' + k + '">' + val + '</option> ';
                            });
                            $select2Office.find('option').remove().end().append(option);
                            $select2Office.select2();
                        }
                    }

                }
            });
            return false;

        });
    });





</script>