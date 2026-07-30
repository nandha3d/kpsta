<div class="subpages-banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <h3>ORDER & CIRCULAR  <small> / <?php echo $contentTitle; ?></small></h3>
            </div>
            <div class="col-sm-5 ">
                <?php if ($selectedCategory || $search) { ?>
                    <div class="search-by"> Search By:

                        <?php if ($selectedCategory) { ?> 
                            <span class="tag label label-default">
                                <span><?php echo isset($selectedCategoryName['name']) ? $selectedCategoryName['name'] : '' ?></span>
                                <?php
                                $parsed = parse_url($urlString . '?' . $_SERVER['QUERY_STRING']);
                                $query = $parsed['query'];
                                parse_str($query, $params);
                                unset($params['category']);
                                unset($params['page']);

                                if (count($params)) {
                                    $categoryRemoveUrl = $urlString . '?' . http_build_query($params);
                                } else {
                                    $categoryRemoveUrl = $urlString;
                                }

//                                $tempUrl = $urlString . '?' . $_SERVER['QUERY_STRING'];
//                                preg_replace('/(?:&|(\?))category=[^&]*(?(1)&|)?/i', "$1", $tempUrl);
                                ?>
                                <a href="<?php echo $categoryRemoveUrl; ?>"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a> 
                            </span>
                        <?php } ?>

                        <?php if ($search) { ?> 
                            <span class="tag label label-info">
                                <span><?php echo $search ?></span>
                                <?php
                                $parsed = parse_url($urlString . '?' . $_SERVER['QUERY_STRING']);
                                $query = $parsed['query'];
                                parse_str($query, $params);
                                unset($params['search']);
                                unset($params['page']);

                                if (count($params)) {
                                    $seachRemoveUrl = $urlString . '?' . http_build_query($params);
                                } else {
                                    $seachRemoveUrl = $urlString;
                                }
                                ?>
                                <a href="<?php echo $seachRemoveUrl ?>"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a> 
                            </span>
                        <?php } ?>

                    </div>
                <?php } ?>
            </div>

            <div class="col-sm-3 hidden-xs hidden-sm ">
                <ol class="breadcrumb pull-right">
                    <li class="breadcrumb-item"> <a href="<?php echo base_url() ?>"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">ORDER & CIRCULAR</li>
                </ol>
            </div>


        </div>
    </div>
</div>



<div class="container">
    <div class="row">



        <div class="col-md-3 col-md-push-9">

            <div class="right-side-bar">
                <h4>Search</h4>
                <?php echo form_open(base_url('order-circular/' . $this->uri->segment(2)), array('method' => 'GET', 'autocomplete' => false)) ?>
                <div class="form-group">

                    <div class="input-group date">
                        <input class="form-control" placeholder="Search..." name="search"  type="text" value="<?php echo $search ?>">

                        <?php if ($selectedCategory) { ?>
                            <input name="category"  type="hidden" value="<?php echo $selectedCategory ?>">
                        <?php } ?>

                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default btn-flat" ><i class="fa fa-search fa-fw"></i></button>
                        </span>
                    </div>

                </div>
                <?php echo form_close() ?>

                <?php echo form_open(base_url('order-circular/' . $this->uri->segment(2)), array('method' => 'GET', 'autocomplete' => false)) ?>
                <div class="form-group">

                    <?php if ($search) { ?>
                        <input name="search"  type="hidden" value="<?php echo $search ?>">
                    <?php } ?>

                    <label for="heading">Search By Label</label>
                    <?php
                    $categoryDropDown[''] = "- - SELECT LABEL - -";
                    foreach ($categories as $value) {
                        $categoryDropDown[$value['id']] = $value['name'];
                    }
                    ?>

                    <?php echo form_dropdown('category', $categoryDropDown, $selectedCategory, 'class="form-control select2-category" style="width: 100%;"  required= "required" '); ?>
                </div>
                <?php echo form_close() ?>



            </div>
        </div>



        <div class="col-md-9 col-md-pull-3">

            <?php
            foreach ($orders as $name => $month) {
                echo "<h4 class='date-title'><span>$name</span></h4>";
                ?>

                <div class="order-listing">
                    <ul>

                        <?php
                        foreach ($month as $key => $order) {
                            try {
                                if ($order['upload_type'] == 'file') {
                                    $url = base_url() . ORDER_CIRCULAR_PATH . $order['path'];
                                } else if ($order['upload_type'] == 'url') {
                                    $url = $order['path'];
                                } else {
                                    $url = "#";
                                }

                                $liClass = $key % 2 == 0 ? 'odd' : 'even';
                                $dataExp = explode('-', $order['date']);
                                $date = $dataExp[0];
                                ?>


                                <li class="<?php echo $liClass ?>">
                                    <div class="dateCol">
                                        <span class="homeDate"><span class="hDate"><?php echo $date; ?></span></span>
                                    </div>
                                    <div class="newsColPage">
                                        <p>
                                            <a href="<?php echo $url; ?>" target="_blank"><?php echo $order['description']; ?> 
                                                <?php echo $selectedCategory ? '' : '<span>' . $order['category'] . '</span> '; ?>
                                            </a> 
                                        </p>
                                    </div>
                                </li>


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














    </div>
</div>


<script>
    (function ($) {
        $('select[name="category"]').change(function () {
            this.form.submit();
        });
    }(jQuery));
</script>