<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive  " >
            <!--//form--> 
            <table class="table table-hover table-striped table-bordered">

                <tbody>
                    <!--<tr>-->

                    <?php
                    $i = 1;
                    foreach ($list as $key => $row) {
                        $i = ($i == 4) ? 1 : $i;
                        if ($i == 1) {
                            echo "<tr>";
                        }

                        $checked = isset($menuId[$row['id']]) ? "checked" : "";
                        ?>

                    <td data-tr="<?php echo $row['id'] ?>" class="margin">
                        <span>
                            <input type="checkbox"  class="list-checkbox"  <?php echo $checked ?>     name="menu[]" value="<?php echo $row['id'] ?>">
                            <?php echo $row['name'] ?>
                        </span>
                    </td>



                    <?php
                    if ($i == 4) {
                        echo "</tr>";
                    }


                    $i++;
                }
                ?>
                </tbody>
            </table>

            <div class="col-md-12 text-center">
                <button type="submit" id="save" class="btn btn-primary  "  ><i class="fa fa-save "></i> Save</button>
            </div>

            <!--//form end-->




        </div>
    </div>
</div>
<!-- /.table-responsive -->


