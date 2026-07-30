<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <h3><?php echo $contentTitle ?></h3>
            </div>
            <div class="col-sm-5 ">
                <?php if ($selectedCategory) { ?>
                    <h4 class="search-by"> Search By:
                        <span class="tag label label-info">
                            <span><?php echo isset($selectedCategoryName['name']) ? $selectedCategoryName['name'] : '' ?></span>
                            <?php
                            $tempUrl = base_url();
                            preg_replace('/(?:&|(\?))category=[^&]*(?(1)&|)?/i', "$1", $tempUrl);
                            ?>
                            <!-- <a href="<?php // echo $tempUrl . 'download/forms' ?>"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a>  -->
                            <a href="<?php echo $base_url ?>"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a> 
                        </span>
                    </h4>
                <?php } ?>
            </div>


            <div class="col-sm-3 hidden-xs hidden-sm ">
                <ol class="breadcrumb pull-right">
                    <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"><?php echo $contentTitle ?></li>
                </ol>
            </div>

        </div>
    </div>
</div>



<div class="container">
    <div class="row">
        <div class="col-sm-9">

            <?php
            foreach ($forms as $name => $single) {

                echo "<h4 class='date-title'><span>$name</span></h4>";
                ?>

                <!--<div class="order-listing forms">-->
                <div class=" psd-listing">
                    <ul>

                        <?php
                        foreach ($single as $key => $order) {
                            try {
                                if ($order['upload_type'] == 'file') {
                                    $url = base_url() . DOWNLOAD_PATH . $order['path'];
                                } else if ($order['upload_type'] == 'url') {
                                    $url = $order['path'];
                                } else {
                                    $url = "#";
                                }

                                $liClass = $key % 2 == 0 ? 'odd' : 'even';
                                $dataExp = explode('-', $order['date']);
                                $date = $dataExp[0];
                                ?>



                                <li class="<?php echo $liClass ?> download">
                                    <h6><?php echo $order['description'] ?></h6>
                                    <p><a href="<?php echo $url ?>" target="_blank"  class="download-links">Downloads Files</a></p>
                                </li>
            <!--                                <li class="<?php echo $liClass ?>">
                                    <div class="newsColPage">
                                        <p><a href="<?php // echo $url;  ?>" target="_blank"><?php // echo $order['description'];  ?>  </a> </p>
                                    </div>
                                </li>-->


                                <?php
                            } catch (\Exception $exc) {
                                
                            }
                        }
                        ?>

                    </ul>
                </div>

            <?php } ?>











            <div class="order-list-pagination">
                <?php echo $links ?>
            </div>


        </div>




        <div class="col-sm-3">
            <div class="right-side-bar">
                <h4>Search</h4>
                <div class="panel-default1">
                    <?php
                    foreach ($categories as $value) {
                        $aClass = ($selectedCategory == $value['id'] ) ? 'active' : '';
                        ?>
                        <div class="panel-heading">
                            <a href="<?php echo $urlString . '?category=' . $value['id']; ?>" class="<?php echo $aClass; ?>">
                                <h5 class="panel-title faq-title"><?php echo $value['name']; ?><i></i></h5>
                            </a>
                        </div>

                    <?php }
                    ?>
                </div>
            </div>
        </div>









    </div>
</div>