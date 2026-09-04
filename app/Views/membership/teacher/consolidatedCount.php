<label  ><u> Designation wise count:</u></label>
<div class="row">
    <?php foreach ($designationCount as $row) { ?>
        <div class="col-sm-3">
            <div class="form-group">
                <label class="col-sm-10" for="heading"> <?php echo $row['designation'] ?> :</label>
                <div class="col-sm-2">
                    <?php echo $row['designationCount'] ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<label  ><u> School type count:</u></label>
<div class="row">
    <?php
    $total = 0;
    foreach ($schoolCount as $row) {
        ?>
        <div class="col-sm-3">
            <div class="form-group">
                <label class="col-sm-10" for="heading">
                    <?php
                    $total  = $row['count'] + $total;
                    if ($row['school_type'] == 1) {
                        echo 'Government';
                    } else if ($row['school_type'] == 2) {
                        echo 'Aided';
                    }else{
                        echo "Others";
                    }
                    ?>:
                </label>
                <div class="col-sm-2">
                    <?php echo $row['count'] ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col-sm-3">
        <div class="form-group">
            <label class="col-sm-10" for="heading">
                Total:
            </label>
            <div class="col-sm-2"> <?php echo $total ?> </div>
        </div>

    </div>
</div>