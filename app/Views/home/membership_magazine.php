  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">Membership &amp; Magazine</h1>
    </div>
  </section>

  <!-- Marquee Ticker -->
  <div class="news-ticker-bar">
    <div class="ticker-badge">KPSTA NEWS</div>
    <marquee class="ticker-content" onmouseover="this.stop();" onmouseout="this.start();">
      <?php 
      $CI =& get_instance();
      $CI->load->model('FlashNews_model');
      $dynamic_news = $CI->FlashNews_model->getAll(array('isPublish' => TRUE, 'limit' => 25));
      if(!empty($dynamic_news)) { foreach($dynamic_news as $n) { 
          $link = !empty($n['url']) ? $n['url'] : (!empty($n['path']) ? $n['path'] : '');
          if(!empty($link)): ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><?php echo $n['description']; ?></a>
        <?php else: ?>
            <span><?php echo $n['description']; ?></span>
        <?php endif; ?>
        &nbsp; &nbsp; | &nbsp; &nbsp; 
      <?php } } else { ?>
        Welcome to Kerala Pradesh School Teachers' Association (KPSTA)
      <?php } ?>
    </marquee>
  </div>

  <main class="page-membership-magazine">
    <div class="container">
      <!-- Breadcrumbs -->
      <div class="page-breadcrumb-wrap">
        <ol class="page-breadcrumb">
          <li><a href="<?php echo base_url(); ?>"><span class="material-symbols-outlined">home</span> Home</a></li>
          <li class="separator">/</li>
          <li class="current">Membership &amp; Magazine</li>
        </ol>
      </div>

      <!-- Section Header -->
      <div class="portal-header">
        <div class="portal-badge-pill">KPSTA Digital Gateway</div>
        <h2 class="portal-heading">Select Service or Publication</h2>
        <p class="portal-subtext">Choose an option below to proceed to the official Membership portal or the Adhyapaka Sabdham digital magazine platform.</p>
      </div>

      <!-- Portal Options Grid -->
      <div class="portal-options-grid">
        <!-- Option 1: Membership -->
        <a href="<?php echo base_url('membership'); ?>" class="portal-option-card card-membership" id="optionMembership">
          <div class="card-accent-bar bar-orange"></div>
          <div class="card-inner">
            <div class="card-top">
              <div class="card-icon-badge icon-orange">
                <span class="material-symbols-outlined">badge</span>
              </div>
              <div class="card-tag">STATE PORTAL</div>
            </div>
            <div class="card-content">
              <h3 class="card-title">Membership</h3>
              <p class="card-desc">Official portal for KPSTA teachers association membership management, branch &amp; district records, teacher directory, verification, and administrative login.</p>
            </div>
            <ul class="card-feature-list">
              <li><span class="material-symbols-outlined check-icon">check_circle</span> Teacher &amp; School Membership</li>
              <li><span class="material-symbols-outlined check-icon">check_circle</span> District &amp; Sub-District Verification</li>
              <li><span class="material-symbols-outlined check-icon">check_circle</span> Member Directory &amp; Reports</li>
            </ul>
            <div class="card-footer">
              <span class="card-btn btn-membership">
                <span>Go to Membership</span>
                <span class="material-symbols-outlined arrow-icon">arrow_forward</span>
              </span>
            </div>
          </div>
        </a>

        <!-- Option 2: ADHYAPAKA SABDHAM -->
        <a href="https://as.kpsta.in/" target="_blank" rel="noopener noreferrer" class="portal-option-card card-magazine" id="optionAdhyapakaSabdham">
          <div class="card-accent-bar bar-teal"></div>
          <div class="card-inner">
            <div class="card-top">
              <div class="card-icon-badge icon-teal">
                <span class="material-symbols-outlined">auto_stories</span>
              </div>
              <div class="card-tag">OFFICIAL MAGAZINE</div>
            </div>
            <div class="card-content">
              <h3 class="card-title">ADHYAPAKA SABDHAM</h3>
              <p class="card-desc">The official monthly organ and prestigious educational magazine of KPSTA. Online new subscription registration, annual renewals, and digital magazine archives.</p>
            </div>
            <ul class="card-feature-list">
              <li><span class="material-symbols-outlined check-icon">check_circle</span> Online New Registration &amp; Renewal</li>
              <li><span class="material-symbols-outlined check-icon">check_circle</span> Monthly Issues &amp; Articles</li>
              <li><span class="material-symbols-outlined check-icon">check_circle</span> Editorial Board &amp; Archives</li>
            </ul>
            <div class="card-footer">
              <span class="card-btn btn-magazine">
                <span>Go to ADHYAPAKA SABDHAM</span>
                <span class="material-symbols-outlined arrow-icon">open_in_new</span>
              </span>
            </div>
          </div>
        </a>
      </div>

      <!-- Support / Contact Card -->
      <div class="portal-help-box">
        <div class="help-icon"><span class="material-symbols-outlined">contact_support</span></div>
        <div class="help-text">
          <strong>Need Assistance?</strong> For any questions regarding Membership registration or Adhyapaka Sabdham magazine subscriptions, contact the KPSTA State Committee office at <strong>0471 - 2575797</strong> or email <strong>kpsta.in@gmail.com</strong>.
        </div>
      </div>
    </div>
  </main>
