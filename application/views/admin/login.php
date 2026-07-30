<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>KPSTA | Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>public/images/favicon.ico">
        <link rel="apple-touch-icon" href="<?php echo base_url(); ?>public/images/apple-touch-icon.png">
        
        <!-- Modern Custom Admin CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/modern-admin.css"/>
    </head>
    <body class="login-page-modern">
        <div class="modern-login-box">
            
            <div class="modern-login-logo">
                <img src="<?php echo base_url(); ?>public/images/logo.png" alt="KPSTA Logo">
                <h1>KPSTA Admin</h1>
            </div>

            <?php if (isset($data['aauthErrors'])) { ?>
                <div class="login-error-msg">
                    <?php
                    foreach ($data['aauthErrors'] as $error) {
                        echo "<p>$error</p>";
                    }
                    ?>
                </div>
            <?php } ?>

            <?php echo form_open($this->uri->segment(1) . '/login', array('autocomplete' => 'off')) ?> 
            
            <div class="modern-form-group">
                <label>Username</label>
                <input name="username" class="modern-input" placeholder="Enter your username" required="required">
            </div>
            
            <div class="modern-form-group">
                <label>Password</label>
                <input name="password" type="password" class="modern-input" placeholder="Enter your password" required="required">
            </div>

            <div class="modern-form-group">
                <?php
                if (isset($data['recaptcha'])) {
                    echo $data['recaptcha'];
                }
                ?>
            </div>
            
            <div class="modern-form-group modern-flex-between">
                <label class="modern-checkbox-label">
                    <input type="checkbox" name="remember" id="remember"> Keep me logged in
                </label>
                <a href="#" class="modern-link">Forgot password?</a>
            </div>
            
            <button type="submit" class="modern-btn-primary">Log In</button>
            
            <?php echo form_close(); ?>

        </div>
    </body>
</html>
