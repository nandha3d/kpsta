
<?php if (isset($error)) { ?>
        <div class="alert alert-danger alert-dismissible">
            <h4><i class="icon fa fa-ban"></i> Error !</h4> <?php echo $error ?>  
        </div>
    <?php } else if (validation_errors()) { ?>
        <div class = "alert alert-danger alert-dismissible">
            <h4><i class = "icon fa fa-ban"></i> Error!</h4> <?php echo validation_errors() ?>  
        </div>
<?php } ?>



<?php echo form_open('#', ['autocomplete' => 'off', 'id' => "save", "data-href" => base_url("donation/pay"), "data-title" => ""]) ?> 

<div class="payment__field">
  <input  type="text" name="name" id=""
    placeholder="Name*"
    required
    autofocus
    value="<?php echo isset($formValues['name']) ? $formValues['name'] : '' ?>" 
  />
</div>
<div class="payment__field">
  <input
    type="email"
    name="email"
    id=""
    placeholder="Email*"
    required
    value="<?php echo isset($formValues['email']) ? $formValues['email'] : '' ?>" 
  />
</div>
<div class="payment__field">
  <input type="tel" name="phone" id="" placeholder="Mobile*" required value="<?php echo isset($formValues['phone']) ? $formValues['phone'] : '' ?>" />
</div>
<div class="payment__field">
  <input type="tel" name="place" id="" placeholder="Place" value="<?php echo isset($formValues['place']) ? $formValues['place'] : '' ?>" />
</div>

<div class="payment__field dropdown">
    <?php $class = form_error('designation') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['designation']) ? $formValues['designation'] : '' ?>
    <?php echo form_dropdown('designation', $designationSelect, $selected, 'class="select2 select2-designation" style="width: 100%;"   '); ?>
</div>

<div class="payment__field dropdown">
    <?php $class = form_error('district') ? 'form-group has-error' : 'form-group' ?>
    <?php $selected = isset($formValues['district']) ? $formValues['district'] : '' ?>
    <?php echo form_dropdown('district', $districtSelect, $selected, 'class="select2 select2-designation" style="width: 100%;"   '); ?>
</div>

<div class="payment__field flex__right">
  <button type="submit">
    Donate &nbsp;<span class="large">&#8377;100</span>
  </button>
</div>
<?php echo form_close()?>