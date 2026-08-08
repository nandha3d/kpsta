<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KPSTA - Kerala Pradesh School Teachers' Association | Home</title>
  <meta name="description" content="Official website of Kerala Pradesh School Teachers' Association (KPSTA), the largest school teachers association in Kerala united for quality education.">
  <link rel="icon" href="<?php echo base_url('public/Page References/logo.png'); ?>" type="image/png">
  <link rel="shortcut icon" href="<?php echo base_url('public/Page References/logo.png'); ?>" type="image/png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="<?php echo base_url('public/css/styles.css?v=2.3'); ?>">
  <?php if (!empty($heading_bg_image)): ?>
  <style>
    /* The hero artwork is reused, heavily washed out, behind the welcome card and footer */
    :root { --hero-image: url('<?php echo $heading_bg_image; ?>'); }
    .hero-banner:not(.home-hero) {
      background: linear-gradient(180deg, rgba(3, 38, 42, 0.88) 0%, rgba(13, 13, 40, 0.90) 50%, rgba(62, 40, 34, 0.82) 100%), url('<?php echo $heading_bg_image; ?>') !important;
      background-size: cover !important;
      background-position: center !important;
    }
  </style>
  <?php endif; ?>
</head>
<body>

  <!-- Top Bar -->
  <div class="top-bar">
    <div class="container">
      <div class="top-bar-info">
        <div class="top-bar-item">
          <span class="material-symbols-outlined">location_on</span>
          <span>KPSTA BHAVAN, Chinmaya School Lane, Kunnumpuram, Trivandrum -1</span>
        </div>
        <div class="top-bar-item">
          <span class="material-symbols-outlined">mail</span>
          <span>kpsta.in@gmail.com</span>
        </div>
        <div class="top-bar-item">
          <span class="material-symbols-outlined">call</span>
          <span>0471 - 2575797</span>
        </div>
      </div>
      <div class="top-bar-socials">
        <a href="#" class="social-circle">f</a>
        <a href="#" class="social-circle">𝕏</a>
        <a href="#" class="social-circle">▶</a>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header class="main-header">
    <div class="container header-container">
      <a href="<?php echo base_url(); ?>" class="brand-logo">
        <img src="<?php echo base_url('public/images/logo-wide-dark.png'); ?>" alt="KPSTA - Kerala Pradesh School Teachers' Association" class="brand-lockup">
      </a>
      
      <button class="mobile-toggle" id="mobileNavToggle" aria-label="Toggle navigation">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <ul class="nav-links" id="navLinks">
        <?php 
          $seg1 = $this->uri->segment(1); 
          $seg2 = $this->uri->segment(2);
        ?>
        <li><a href="<?php echo base_url(); ?>" class="nav-link <?php echo empty($seg1) ? 'active' : ''; ?>">Home</a></li>
        <li class="dropdown">
          <a href="<?php echo base_url('OfficeBearer'); ?>" class="nav-link <?php echo (in_array($seg1, ['OfficeBearer', 'District', 'melakal']) || ($seg1 == 'Home' && $seg2 == 'former_leaders')) ? 'active' : ''; ?>">Organization <span class="material-symbols-outlined" style="font-size:1.1rem;">arrow_drop_down</span></a>
          <div class="dropdown-menu">
            <a href="<?php echo base_url('OfficeBearer'); ?>" class="dropdown-item">State Office Bearers</a>
            <a href="<?php echo base_url('District'); ?>" class="dropdown-item">District Office Bearers</a>
            <a href="<?php echo base_url('Home/former_leaders'); ?>" class="dropdown-item">Former Leaders</a>
            <a href="<?php echo base_url('melakal'); ?>" class="dropdown-item">Melakal (Kalolsavam)</a>
          </div>
        </li>
        <li><a href="<?php echo base_url('order-circular'); ?>" class="nav-link <?php echo ($seg1 == 'order-circular') ? 'active' : ''; ?>">Order & Circular</a></li>
        <li class="dropdown">
          <a href="<?php echo base_url('download/forms'); ?>" class="nav-link <?php echo (in_array($seg1, ['download', 'notice_poster']) || ($seg1 == 'Home' && in_array($seg2, ['service_corner', 'memorandums']))) ? 'active' : ''; ?>">Downloads <span class="material-symbols-outlined" style="font-size:1.1rem;">arrow_drop_down</span></a>
          <div class="dropdown-menu">
            <a href="<?php echo base_url('download/forms'); ?>" class="dropdown-item">Forms</a>
            <a href="<?php echo base_url('Home/service_corner'); ?>" class="dropdown-item">Service Corner</a>
            <a href="<?php echo base_url('download/academic_corner'); ?>" class="dropdown-item">Academic Corner</a>
            <a href="<?php echo base_url('download/softwares'); ?>" class="dropdown-item">Software Tools</a>
            <a href="<?php echo base_url('Home/memorandums'); ?>" class="dropdown-item">Memorandums</a>
            <a href="<?php echo base_url('notice_poster'); ?>" class="dropdown-item">Notices & Posters</a>
          </div>
        </li>
        <li><a href="<?php echo base_url('Gallery'); ?>" class="nav-link <?php echo ($seg1 == 'Gallery') ? 'active' : ''; ?>">Gallery</a></li>
        <li><a href="<?php echo base_url('Quicklink'); ?>" class="nav-link <?php echo ($seg1 == 'Quicklink') ? 'active' : ''; ?>">Online Links</a></li>
        <li><a href="<?php echo base_url('Contact'); ?>" class="nav-link <?php echo ($seg1 == 'Contact') ? 'active' : ''; ?>">Contact Us</a></li>
      </ul>
    </div>
    <div class="header-bottom-gradient"></div>
  </header>
