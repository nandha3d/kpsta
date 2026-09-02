<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KPSTA - Kerala Pradesh School Teachers' Association | Home</title>
  <meta name="description" content="Official website of Kerala Pradesh School Teachers' Association (KPSTA), the largest school teachers association in Kerala united for quality education.">
  <link rel="icon" href="<?php echo base_url('public/Page References/logo.png'); ?>" type="image/png">
  <link rel="shortcut icon" href="<?php echo base_url('public/Page References/logo.png'); ?>" type="image/png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0..1,0" />
  <link rel="stylesheet" href="<?php echo base_url('public/css/styles.css?v=4.4'); ?>">
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
        <a href="#" class="social-circle" aria-label="Facebook">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8.2h2.8l.42-3.2H13.5V7.55c0-.93.26-1.56 1.6-1.56h1.7V3.13A23 23 0 0 0 14.31 3c-2.46 0-4.15 1.5-4.15 4.26V9.6H7.35v3.2h2.81V21h3.34Z"/></svg>
        </a>
        <a href="#" class="social-circle" aria-label="X">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.53 3h3.02l-6.6 7.54L21.7 21h-6.07l-4.76-6.22L5.42 21H2.4l7.05-8.06L2.3 3h6.23l4.3 5.69L17.53 3Zm-1.06 16.2h1.67L7.6 4.71H5.81L16.47 19.2Z"/></svg>
        </a>
        <a href="#" class="social-circle" aria-label="YouTube">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.58 7.19a2.51 2.51 0 0 0-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42a2.51 2.51 0 0 0-1.77 1.77A26.1 26.1 0 0 0 2 12a26.1 26.1 0 0 0 .42 4.81 2.51 2.51 0 0 0 1.77 1.77C5.75 19 12 19 12 19s6.25 0 7.81-.42a2.51 2.51 0 0 0 1.77-1.77A26.1 26.1 0 0 0 22 12a26.1 26.1 0 0 0-.42-4.81ZM10.2 15V9l5.02 3-5.02 3Z"/></svg>
        </a>
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
