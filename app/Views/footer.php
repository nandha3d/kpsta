  <!-- Footer -->
  <footer class="main-footer">
    <div class="footer-watermark">KPSTA</div>
    <div class="container">
      <div class="footer-top">
        <div class="brand-logo" style="justify-content:center;">
          <img src="<?php echo base_url('public/images/logo-wide-new.png'); ?>" alt="KPSTA - Kerala Pradesh School Teachers' Association" class="brand-lockup footer-lockup">
        </div>

        <div class="footer-divider"></div>

        <ul class="footer-nav">
          <li><a href="<?php echo base_url(); ?>">Home</a></li>
          <li><a href="<?php echo base_url('office_bearer'); ?>">Organization</a></li>
          <li><a href="<?php echo base_url('order-circular'); ?>">Order & Circular</a></li>
          <li><a href="<?php echo base_url('download/forms'); ?>">Downloads</a></li>
          <li><a href="<?php echo base_url('gallery'); ?>">Gallery</a></li>
          <li><a href="<?php echo base_url('quicklink'); ?>">Online Links</a></li>
          <li><a href="<?php echo base_url('contact'); ?>">Contact</a></li>
        </ul>

        <div class="footer-contacts">
          <div class="footer-contact-item">
            <span class="material-symbols-outlined">mail</span>
            <span>kpsta.in@gmail.com</span>
          </div>
          <div class="footer-contact-item">
            <span class="material-symbols-outlined">location_on</span>
            <span>KPSTA BHAVAN, Chinmaya School Lane, Kunnumpuram, Trivandrum -1</span>
          </div>
          <div class="footer-contact-item">
            <span class="material-symbols-outlined">call</span>
            <span>0471 - 2575797</span>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        Copyright © <?php echo date('Y'); ?>, All Rights Reserved
      </div>
    </div>
  </footer>

  <?php 
    // Android Play Store URL placeholder (can be updated here anytime)
    $android_playstore_url = "https://play.google.com/store/apps/details?id=in.kpsta.app";
  ?>
  <!-- Sticky Android App Floating Button -->
  <div class="kpsta-app-sticky-wrapper" id="kpstaAppStickyWrapper">
    <!-- Notification Tooltip Bubble -->
    <div class="kpsta-app-sticky-bubble" id="kpstaAppBubble">
      <div class="bubble-content">
        <span class="bubble-badge">NEW</span>
        <span class="bubble-text">Download Official KPSTA App on Play Store!</span>
      </div>
      <button type="button" class="bubble-close-btn" id="kpstaAppBubbleClose" aria-label="Dismiss">&times;</button>
    </div>

    <!-- Main Floating Action Button -->
    <button type="button" class="kpsta-app-sticky-btn" id="kpstaAppStickyBtn" aria-label="Download KPSTA Android App">
      <span class="sticky-btn-pulse"></span>
      <span class="sticky-btn-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M17.6 9.48l1.84-3.18a.5.5 0 1 0-.87-.5l-1.87 3.24a10.87 10.87 0 0 0-9.4 0L5.43 5.8a.5.5 0 0 0-.87.5l1.84 3.18C3.84 10.96 2 13.74 2 17h20c0-3.26-1.84-6.04-4.4-7.52zM7 14.5a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5zm10 0a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5z"/>
        </svg>
      </span>
      <span class="sticky-btn-text">
        <span class="sticky-btn-sub">GET APP</span>
        <span class="sticky-btn-main">Google Play</span>
      </span>
    </button>
  </div>

  <!-- Android App Download Notification Modal -->
  <div class="kpsta-app-modal-overlay" id="kpstaAppModal" aria-hidden="true" role="dialog" aria-labelledby="kpstaAppModalTitle">
    <div class="kpsta-app-modal-card">
      <button type="button" class="modal-close-btn" id="kpstaAppModalClose" aria-label="Close dialog">
        <span class="material-symbols-outlined">close</span>
      </button>
      
      <div class="modal-card-banner">
        <div class="banner-glow"></div>
        <div class="banner-icons">
          <div class="app-icon-box">
            <img src="<?php echo base_url('public/images/logo.png'); ?>" alt="KPSTA Logo" class="app-modal-logo">
          </div>
          <div class="app-icon-divider">
            <span class="material-symbols-outlined">add</span>
          </div>
          <div class="play-icon-box">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
              <path d="M3.609 1.814L13.793 12 3.61 22.186A2.25 2.25 0 0 1 3 20.6V3.4c0-.624.238-1.2.609-1.586z" fill="#00E676"/>
              <path d="M17.156 8.637L5.05 1.646A2.27 2.27 0 0 1 6.182 1.34c.732 0 1.442.274 2.016.602l8.958 5.17-2.016 1.525z" fill="#FFD600"/>
              <path d="M17.156 15.363l2.016 1.525-8.958 5.17a3.86 3.86 0 0 1-2.016.602c-.407 0-.79-.098-1.132-.275l12.09-7.022z" fill="#FF1744"/>
              <path d="M21.575 11.136l-2.403-1.388-2.016 2.252 2.403-1.388a1.69 1.69 0 0 0 0-3.003l-.001.001-.001.001-.001.001z" fill="#00B0FF"/>
            </svg>
          </div>
        </div>
        <div class="banner-badge">OFFICIAL ANDROID APP</div>
      </div>

      <div class="modal-card-body">
        <h3 class="modal-app-title" id="kpstaAppModalTitle">Download KPSTA Android App</h3>
        <p class="modal-app-subtitle">Stay connected with Kerala's largest school teachers' association anytime, anywhere on your Android phone.</p>

        <div class="modal-app-features">
          <div class="feature-item">
            <span class="material-symbols-outlined feature-icon">notifications_active</span>
            <div class="feature-text">
              <strong>Instant Notifications</strong>
              <span>Get latest orders, circulars, and association alerts instantly.</span>
            </div>
          </div>
          <div class="feature-item">
            <span class="material-symbols-outlined feature-icon">calculate</span>
            <div class="feature-text">
              <strong>Teacher Utility Tools</strong>
              <span>Salary &amp; pension calculators, PF, tax &amp; service benefits.</span>
            </div>
          </div>
          <div class="feature-item">
            <span class="material-symbols-outlined feature-icon">badge</span>
            <div class="feature-text">
              <strong>Membership Services</strong>
              <span>Easy access to member directory, digital ID &amp; verification.</span>
            </div>
          </div>
        </div>

        <div class="modal-card-actions">
          <!-- Placeholder Google Play Store Link (change url here or in $android_playstore_url) -->
          <a href="<?php echo $android_playstore_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-playstore" id="btnPlaystoreDownload">
            <svg class="play-svg" width="26" height="26" viewBox="0 0 24 24" fill="none">
              <path d="M3.609 1.814L13.793 12 3.61 22.186A2.25 2.25 0 0 1 3 20.6V3.4c0-.624.238-1.2.609-1.586z" fill="#00E676"/>
              <path d="M17.156 8.637L5.05 1.646A2.27 2.27 0 0 1 6.182 1.34c.732 0 1.442.274 2.016.602l8.958 5.17-2.016 1.525z" fill="#FFD600"/>
              <path d="M17.156 15.363l2.016 1.525-8.958 5.17a3.86 3.86 0 0 1-2.016.602c-.407 0-.79-.098-1.132-.275l12.09-7.022z" fill="#FF1744"/>
              <path d="M21.575 11.136l-2.403-1.388-2.016 2.252 2.403-1.388a1.69 1.69 0 0 0 0-3.003l-.001.001-.001.001-.001.001z" fill="#00B0FF"/>
            </svg>
            <div class="btn-playstore-text">
              <span class="playstore-label">GET IT ON</span>
              <span class="playstore-name">Google Play</span>
            </div>
          </a>
        </div>

        <div class="modal-card-footer">
          <button type="button" class="btn-modal-dismiss" id="kpstaAppModalDismiss">Maybe Later</button>
        </div>
      </div>
    </div>
  </div>

  <script src="<?php echo base_url('public/js/main.js?v=' . (file_exists(FCPATH . 'js/main.js') ? filemtime(FCPATH . 'js/main.js') : time())); ?>"></script>
</body>
</html>
