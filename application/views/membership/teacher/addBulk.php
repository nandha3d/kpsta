
<!-- Content Wrapper. Contains page content -->
<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="page-header">
            <div class="box-layout">
                <div class="col-xs-5 col-sm-6 col-md-5 va-m">
                    <h3 class="pull-left">Add Member</h3>
                    <div class="col-xs-2 text-right pull-left">
                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <?php echo form_open('membership/teacher/add_bulk', ['autocomplete' => 'off', 'id' => "save", "data-href" => isset($url) ? $url : '', 'class' => 'form-horizontal']) ?> 
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-layout">
                            <div class="col-xs-6 col-lg-8 va-m form-inline">
                                <h4><b>School & Member Details</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="overlay" style="display: none;">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>

                    <div class="box-body" id="table-content">
                        <div class="col-md-12">
                            <fieldset class="well the-fieldset">


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
                                    <?php $selected = isset($branch) ? $branch : '' ?>
                                    <div class="<?php echo $class ?>">
                                        <label class="col-sm-3" for="heading">Branch</label>
                                        <div class="col-sm-9">
                                            <?php echo form_dropdown('branch', $branchSelect, $selected, 'class="form-control select2 " id="select2-branch" style="width: 100%;"   '); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php $class = form_error('school') ? 'form-group has-error' : 'form-group' ?>
                                <?php $selected = isset($school) ? $school : '' ?>
                                <div class="<?php echo $class ?>">
                                    <label class="col-sm-3" for="heading">School <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <?php echo form_dropdown('school', $schoolSelect, $selected, 'class="form-control select2 " id="select2-office" style="width: 100%;" required="required"   '); ?>
                                    </div>
                                </div>
                            </fieldset>

                            <div id="teacher-details">
                                <?php
                                $arrayKeys = array_keys($name);
                                $lastElement = array_pop($arrayKeys);
                                foreach ($name as $key => $row) {
                                    ?>
                                    <div class="teacher-entry">
                                        <fieldset class="well the-fieldset">
                                            <?php $class = form_error('name[' . $key . ']') ? 'form-group has-error' : 'form-group' ?>
                                            <div class="<?php echo $class ?>">
                                                <label class="col-sm-3" >Name <span class="text-danger">*</span></label>
                                                <div class="col-sm-9">
                                                    <input  class="form-control" name="name[]" placeholder="Enter Name"  value="<?php echo isset($name[$key]) ? $name[$key] : '' ?>"  required="required" >
                                                </div>
                                            </div>

                                            <?php $class = form_error('designation[' . $key . ']') ? 'form-group has-error' : 'form-group' ?>
                                            <?php $selected = isset($designation[$key]) ? $designation[$key] : '' ?>
                                            <div class="<?php echo $class ?>">
                                                <label class="col-sm-3" for="heading">Designation <span class="text-danger">*</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_dropdown('designation[]', $designationSelect, $selected, 'class="form-control select2 select2-designation" style="width: 100%;"   '); ?>
                                                </div>
                                            </div>

                                            <?php $class = form_error('mobile[' . $key . ']') ? 'form-group has-error' : 'form-group' ?>
                                            <div class="<?php echo $class ?>">
                                                <label class="col-sm-3" >Phone No </label>
                                                <div class="col-sm-9">
                                                    <input  class="form-control" name="mobile[]" placeholder="Enter Phone no"  value="<?php echo isset($mobile[$key]) ? $mobile[$key] : '' ?>"   >
                                                </div>
                                            </div>

                                            <?php $class = form_error('teacher_type[' . $key . ']') ? 'form-group has-error' : 'form-group' ?>
                                            <?php $selected = isset($teacher_type[$key]) ? $teacher_type[$key] : '' ?>
                                            <div class="<?php echo $class ?> "  >
                                                <label class="col-sm-3" >Teacher Type <span class="text-danger"> *</span></label>
                                                <div class="col-sm-9">
                                                    <?php echo form_dropdown('teacher_type[]', [0 => 'SELECT', 1 => 'Govt', 2 => 'Aided'], $selected, 'class="form-control select2 select2-type"   style="width: 100%;"     '); ?>
                                                </div>
                                            </div>

                                            <?php $checked = (isset($adhyapaka_sabdham[$key]) && $adhyapaka_sabdham[$key] ) ? 'checked="checked"' : '' ?>
                                            <div class="form-group">
                                                <label class="col-sm-3" >Adhyapaka sabdham subscriber </label>
                                                <div class="col-sm-9">
                                                    <input   name="adhyapaka_sabdham[]"  type="checkbox" <?php echo $checked ?> >
                                                </div>
                                            </div>
                                            <?php if ($lastElement == $key) { ?>
                                                <button type="button" class="btn btn-info pull-right btn-add"  ><i class="fa fa-plus "></i> Add More</button>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-info pull-right btn-danger btn-remove"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remove</button>
                                            <?php } ?>
                                        </fieldset>
                                    </div>

                                    <?php
                                }
                                ?>

                            </div>


                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <div class="<?php echo $class ?>">
                            <div class="col-md-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-primary submit-btn"><i class="fa fa-save "></i> Save</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-footer -->
                </div>

            </div>
            <?php echo form_close(); ?>
        </div>


    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script type="text/javascript">
    $(function () {
        $('.select2').select2();

        $("form").on("submit", function () {
            var $this = $(this);
            $this.find(".submit-btn").attr("disabled", true);
            $this.find(".submit-btn").html('<i class="fa fa-save "></i> Saving');
        });

        $(document).on('click', '.btn-add', function (e) {
            e.preventDefault();
            var controlForm = $('#teacher-details:first'),
                    currentEntry = $(this).parents('.teacher-entry:first');
            currentEntry.find('.select2-designation, .select2-type').select2('destroy');
            var clone = currentEntry.clone();
            var newEntry = $(clone).appendTo(controlForm);

            newEntry.find('input').val('');
            controlForm.find('.btn-add:not(:last)')
                    .removeClass('btn-default').addClass('btn-danger')
                    .removeClass('btn-add').addClass('btn-remove')
                    .html('<span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remove');
            clone.find('.select2-designation, .select2-type').select2();
            clone.find('.has-error').removeClass('has-error');
            clone.find('.select2-designation, .select2-type').val(0).trigger('change');
            currentEntry.find('.select2-designation, .select2-type').select2();
        }).on('click', '.btn-remove', function (e) {
            $(this).parents('.teacher-entry:first').remove();
            e.preventDefault();
            return false;
        });




        $('.category-search').select2().on("change", function (e) {
            console.log($('.category-search').select2("val"));
            seach(0);
        });



        $('#select2-branch').select2().on("change", function (e) {
            var val = $(this).select2("val");
            $('.overlay').show();
            if (val > 0) {
                var path = "<?php echo base_url('membership/aauth/getOffice') ?>";
                $.ajax({
                    url: path,
                    type: 'POST',
                    dataType: 'json',
                    data: {groupId: 6, officeId: val},
                    context: this,
                    complete: function(){
                        $('.overlay').hide();
                    },
                    success: function (result) {
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