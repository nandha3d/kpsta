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


    <?php if (isset($formValues['error'])) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4>
            <?php echo is_array($formValues['error']) ? implode("<br>", $formValues['error']) : $formValues['error'] ?>  
        </div>
    <?php } else if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
    <?php } ?>

    <div class="form-group">
        <?php
        $selected = isset($formValues['group_id']) ? $formValues['group_id'] : '';
        $disabled = (isset($formValues['disabled']) && $formValues['disabled']) ? 'disabled = "disabled"' : '';
        ?>
        <label>Group</label>
        <?php echo form_dropdown('group_id', $groupSelect, $selected, 'class="form-control select2 " id="select2-group" style="width: 100%;"  required= "required" ' . $disabled); ?>
    </div>

    <div class="form-group">
        <?php $selected = isset($formValues['office_id']) ? $formValues['office_id'] : '' ?>
        <label id="label-office"><?php echo isset($officeLabel) ? $officeLabel : 'Office' ?></label>
        <?php echo form_dropdown('office_id', $officeSelect, $selected, 'class="form-control select2 " id="select2-office" style="width: 100%;"  required= "required" '. $disabled); ?>
    </div>


    <div class="form-group">
        <label for="exampleInputEmail1">Admin Name<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" placeholder="Enter Name" value="<?php echo isset($formValues['name']) ? $formValues['name'] : '' ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">User ID<span class="text-danger">*</span></label>
        <input  class="form-control" name="username" placeholder="Enter Username"  value="<?php echo isset($formValues['username']) ? $formValues['username'] : '' ?>" disabled="disabled">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Password</label>
        <input   class="form-control" name="password" placeholder="Enter Password">
    </div>



    <div class="form-group">
        <label>Status<span class="text-danger">*</span></label>
        <select class="form-control select2" name="status" required="required" style="width: 100%;"> 
            <option value="" > - - SELECT STATUS - - </option>
            <option class="text-success" <?php echo isset($formValues['banned']) ? ($formValues['banned'] == 0 ? 'selected' : '') : '' ?> value="0">Active</option>
            <option class="text-danger" <?php echo isset($formValues['banned']) ? ($formValues['banned'] == 1 ? 'selected' : '') : '' ?>  value="1">Lock</option>
        </select>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary"  >Save</button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

    $(function () {
        $("#modal .select2").select2({
            dropdownParent: $("#modal")
        });
        $('.datepicker').datepicker({
            autoclose: true
        });


        $('#select2-group').select2().on("change", function (e) {
            var val = $(this).select2("val");
            if (val > 0) {
                $('#modal .modal-loading-bar').addClass('active');
                $('#modal .overlay').show();
                var path = "getOffice";
                var text = $('#select2-group').select2("data")[0].text;
                $("#label-office").text(text);
                $.ajax({
                    url: path,
                    type: 'POST',
                    dataType: 'json',
                    data: {groupId: val},
                    context: this,
                    complete: function () {
                        $('#modal .modal-loading-bar').removeClass('active');
                        $('#modal .overlay').hide();
                    },
                    success: function (result) {
                        $("#modal").find("input[name='username']").val('');
                        $(this).find('.modal-body .alert').remove();
                        if (typeof result.officeSelect !== 'undefined') {
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

        var $modal = $("#modal");
        $('#select2-office').select2().on("change", function (e) {
            var officeId = $(this).select2("val");
            var groupId = $('#select2-group').select2("val");
            if (officeId > 0 && groupId > 0) {
                $('#modal .modal-loading-bar').addClass('active');
                $('#modal .overlay').show();
                var path = "username";
                $.ajax({
                    url: path,
                    type: 'POST',
                    dataType: 'json',
                    data: {officeId: officeId, groupId: groupId},
                    context: this,
                    complete: function () {
                        $('#modal .modal-loading-bar').removeClass('active');
                        $('#modal .overlay').hide();
                    },
                    success: function (result) {
                        $modal.find('.modal-body .alert').remove();
                        if (result.code == 'success') {
                            $modal.find("input[name='username']").val(result.data);
                        } else {
                            $modal.find("input[name='username']").val('');
                            var error = '';
                            $.each(result.error, function (k, val) {
                                error += val + '<br>';
                            });
                            console.log(error);
                            var html = ' <div class="alert alert-danger alert-dismissible">  <h4><i class="icon fa fa-ban"></i> Error !</h4>' + error + '  </div>';
                            $modal.find('.modal-body').prepend(html)
                        }

                    }
                });
                return false;
            }

        });
    });





</script>