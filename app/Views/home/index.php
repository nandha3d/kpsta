  <?php if(!empty($sliderImages)): ?>
  <style>
    /* Home reuses the first slider frame as the wash behind the welcome card and footer */
    :root { --hero-image: url('<?php echo base_url('uploads/slider/' . $sliderImages[0]['image']); ?>'); }
  </style>
  <?php endif; ?>

  <!-- Hero Section -->
  <section class="hero-banner home-hero is-first-slide" id="heroBanner">
    <!-- Background Slider Elements -->
    <?php
      // Only slides that actually carry an image file are rendered
      $heroSlides = array();
      if(!empty($sliderImages)) {
        foreach($sliderImages as $image) {
          if(!empty($image['image'])) { $heroSlides[] = $image; }
        }
      }
    ?>
    <div class="hero-slider-bg" id="heroBgSlider">
      <?php if(!empty($heroSlides)) { foreach($heroSlides as $key => $image) { ?>
        <div class="hero-slide <?php echo $key === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo base_url('uploads/slider/' . $image['image']); ?>');"></div>
      <?php } } else { ?>
        <div class="hero-slide active" style="background-color: #276269;"></div>
      <?php } ?>
      <div class="hero-overlay"></div>
    </div>

    <?php if(count($heroSlides) > 1) { ?>
    <div class="hero-dots" id="heroDots">
      <?php foreach($heroSlides as $key => $image) { ?>
        <button type="button" class="hero-dot <?php echo $key === 0 ? 'active' : ''; ?>" data-index="<?php echo $key; ?>" aria-label="Slide <?php echo $key + 1; ?>"></button>
      <?php } ?>
    </div>
    <?php } ?>
    
    <div class="container hero-content">
      <div class="hero-flag">
        <picture>
          <source srcset="<?php echo base_url('public/images/flag.webp?v=' . (file_exists(FCPATH . 'public/images/flag.webp') ? filemtime(FCPATH . 'public/images/flag.webp') : time())); ?>" type="image/webp">
          <img src="<?php echo base_url('public/images/flag.png?v=' . (file_exists(FCPATH . 'public/images/flag.png') ? filemtime(FCPATH . 'public/images/flag.png') : time())); ?>" alt="KPSTA Flag" style="margin: 0 auto; filter: drop-shadow(0 8px 14px rgba(0,0,0,0.4));">
        </picture>
      </div>
      <h1 class="hero-title">KPSTA</h1>
      <p class="hero-subtitle">Kerala Pradesh School Teacher's Association</p>
      <div class="hero-tagline">Unite for Quality Education</div>
      <p class="hero-subtagline">Better education for a better world</p>
    </div>
  </section>

  <!-- Hero Background Slider Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const banner = document.getElementById('heroBanner');
      const slides = document.querySelectorAll('#heroBgSlider .hero-slide');
      const dots = document.querySelectorAll('#heroDots .hero-dot');
      if (slides.length > 1) {
        let currentSlide = 0;
        const show = (index) => {
          slides[currentSlide].classList.remove('active');
          if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
          currentSlide = (index + slides.length) % slides.length;
          slides[currentSlide].classList.add('active');
          if (dots[currentSlide]) dots[currentSlide].classList.add('active');

          if (banner) {
            if (currentSlide === 0) {
              banner.classList.add('is-first-slide');
              banner.classList.remove('is-subsequent-slide');
            } else {
              banner.classList.remove('is-first-slide');
              banner.classList.add('is-subsequent-slide');
            }
          }
        };
        let timer = setInterval(() => show(currentSlide + 1), 5000);
        dots.forEach((dot) => {
          dot.addEventListener('click', () => {
            clearInterval(timer);
            show(parseInt(dot.getAttribute('data-index'), 10));
            timer = setInterval(() => show(currentSlide + 1), 5000);
          });
        });
      }
    });
  </script>

  <!-- Marquee Ticker -->
  <?php
    $ci = &get_instance();
    $ci->load->model('Settings_model');
    $webTickerSpeed = (int)($ci->Settings_model->get('flash_news_speed_web') ?: 7);
    if ($webTickerSpeed < 1) $webTickerSpeed = 7;
  ?>
  <div class="news-ticker-bar">
    <div class="ticker-badge">KPSTA NEWS</div>
    <marquee class="ticker-content" scrollamount="<?php echo $webTickerSpeed; ?>" onmouseover="this.stop();" onmouseout="this.start();">
      <?php if(!empty($news)) { foreach($news as $n) { ?>
        <?php $link = !empty($n['url']) ? $n['url'] : (!empty($n['path']) ? $n['path'] : ''); ?>
        <?php if(!empty($link)): ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><?php echo $n['description']; ?></a>
        <?php else: ?>
            <?php echo $n['description']; ?>
        <?php endif; ?>
         &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
      <?php } } else { ?>
        Welcome to KPSTA
      <?php } ?>
    </marquee>
  </div>

  <main class="page-home">

  <!-- Welcome Section -->
  <section class="welcome-section">
    <div class="container">
      <div class="welcome-card">
        <h2>Welcome To KPSTA</h2>
        <p>KPSTA - Kerala Pradesh School Teachers Association is the largest and most prestigious organization of school teachers in Kerala. Various organizations representing school teachers at various levels in the state and formed over a long period of time since 1931 came under a single umbrella called 'Kerala Pradesh School Teachers Association'. These were formed with the aim of providing better services to the school teachers of the kerala state.</p>
        <a href="<?php echo base_url('office_bearer'); ?>" class="btn-outline">History</a>
      </div>
    </div>
  </section>

  <!-- Office Bearers Section -->
  <section class="home-office-section">
    <div class="container">
      <div class="home-office-bearers-layout">
        <div class="home-bearers-block">
          <h2 class="section-title office-bearers-title">Office Bearers</h2>
          <div class="home-office-bearers-grid">
            <?php if(!empty($officeBearer)) { foreach($officeBearer as $ob) { ?>
            <div class="bearer-card">
              <div class="bearer-photo-wrapper">
                <?php if(!empty($ob['image'])) { ?>
                  <img src="<?php echo base_url('uploads/office_bearer/'.$ob['image']); ?>" alt="<?php echo htmlspecialchars($ob['name']); ?>">
                <?php } else { ?>
                  <div class="bearer-placeholder"><span class="material-symbols-outlined">person</span></div>
                <?php } ?>
              </div>
              <div class="bearer-name"><?php echo htmlspecialchars($ob['name']); ?></div>
              <div class="bearer-role"><?php echo htmlspecialchars($ob['designation']); ?></div>
              <?php if(!empty($ob['phone'])) { 
                $cleanPhone = preg_replace('/[^0-9+]/', '', $ob['phone']);
              ?>
                <a href="tel:<?php echo $cleanPhone; ?>" class="bearer-phone-pill" title="Call <?php echo htmlspecialchars($ob['name']); ?>">
                  <span class="bearer-phone-circle"><span class="material-symbols-outlined">call</span></span>
                  <span class="bearer-phone-number"><?php echo htmlspecialchars($ob['phone']); ?></span>
                </a>
              <?php } ?>
            </div>
            <?php } } ?>
          </div>
          <div class="home-bearers-action">
            <a href="<?php echo base_url('office_bearer'); ?>" class="btn-green">More Office Bearers</a>
          </div>
        </div>

        <div class="home-actions-block">
          <div class="quick-menu-wrapper">
            <button type="button" class="quick-menu-header-btn" id="quickMenuToggleBtn" aria-expanded="true" onclick="toggleQuickMenu()">
              <div class="quick-menu-header-left">
                <div class="quick-menu-header-icon">
                  <span class="material-symbols-outlined">grid_view</span>
                </div>
                <div>
                  <h3 class="quick-menu-title-text">Quick Services</h3>
                  <p class="quick-menu-subtitle-text">Membership · Circulars · Academic · Services</p>
                </div>
              </div>
              <div class="quick-menu-badge-pill" id="quickMenuBadge">
                <span>Hide</span>
                <span class="material-symbols-outlined" id="quickMenuArrow">expand_less</span>
              </div>
            </button>
            <div class="quick-menu-content-collapsible is-open" id="quickMenuContent">
              <div class="action-box-grid">
                <a href="<?php echo base_url('membership-magazine'); ?>" class="action-card-link">
                  <div class="action-icon"><span class="material-symbols-outlined">workspace_premium</span></div>
                  <div class="action-title">Membership & Magazine</div>
                </a>
                <a href="<?php echo base_url('order-circular'); ?>" class="action-card-link">
                  <div class="action-icon"><span class="material-symbols-outlined">description</span></div>
                  <div class="action-title">Order & Circular</div>
                </a>
                <a href="<?php echo base_url('download/academic_corner'); ?>" class="action-card-link">
                  <div class="action-icon"><span class="material-symbols-outlined">school</span></div>
                  <div class="action-title">Academic Corner</div>
                </a>
                <a href="<?php echo base_url('service-corner'); ?>" class="action-card-link">
                  <div class="action-icon"><span class="material-symbols-outlined">support_agent</span></div>
                  <div class="action-title">Service Corner</div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Split News Window & Reaction Gallery -->
  <div class="split-section">
    <div class="news-window-side">
      <div class="news-header">
        <h2>News Window</h2>
        <a href="<?php echo base_url('news'); ?>" class="btn-view-all">View All</a>
      </div>
      <div class="news-list">
        <?php if(!empty($listNews)) { foreach($listNews as $newsItem) { ?>
        <div class="news-item-card">
          <div class="news-thumb">
             <?php if(!empty($newsItem['image'])) { ?>
                <img src="<?php echo base_url('uploads/news/'.$newsItem['image']); ?>" alt="News thumbnail">
             <?php } else { ?>
                <img src="<?php echo base_url('public/images/home_01.jpg'); ?>" alt="News thumbnail">
             <?php } ?>
          </div>
          <div class="news-info">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div class="news-date" style="margin-bottom: 0;"><?php echo isset($newsItem['created_at']) ? display_date($newsItem['created_at']) : ''; ?></div>
              <?php 
                $cleanHomeHeading = trim(strip_tags($newsItem['heading'] ?? ''));
                $homeNewsShareText = "*KPSTA News*\n*" . $cleanHomeHeading . "*\n" . base_url('news');
                $homeNewsWaUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($homeNewsShareText);
              ?>
              <a href="<?php echo $homeNewsWaUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-circle btn-whatsapp-circle-sm" title="Share on WhatsApp" aria-label="Share on WhatsApp">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="white" aria-hidden="true">
                  <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.15c-1.38 0-2.74-.35-3.94-1.06l-.29-.17-3.12.82.83-3.04-.19-.31a8.19 8.19 0 0 1-1.25-4.48c0-4.54 3.69-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.24-8.24 8.24zm4.51-6.17c-.25-.12-1.47-.72-1.7-.8-.23-.08-.4-.12-.56.12-.17.25-.64.8-.79.97-.14.16-.29.19-.53.07-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.46-1.36-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.83-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.3-.23.25-.87.85-.87 2.07s.89 2.4 1.01 2.56c.13.17 1.74 2.66 4.22 3.73.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.29z"/>
                </svg>
              </a>
            </div>
            <div class="news-title"><?php echo isset($newsItem['heading']) ? $newsItem['heading'] : ''; ?></div>
            <!-- If news details exists, use its route, else point to news page -->
            <a href="<?php echo base_url('news'); ?>" class="news-readmore">Read More</a>
          </div>
        </div>
        <?php } } ?>
      </div>
    </div>

    <div class="reaction-gallery-side">
      <div class="reaction-header">
        <h2>Reaction Gallery</h2>
      </div>
      <?php if(!empty($reactionGalleryImages)) { ?>
        <div class="reaction-slider-container">
          <?php foreach($reactionGalleryImages as $idx => $rg) { ?>
            <div class="poster-card reaction-slide no-title" style="<?php echo $idx === 0 ? 'display: flex;' : 'display: none;'; ?>">
              <?php if(!empty($rg['image'])) { ?>
                <a href="<?php echo base_url('uploads/reaction_gallery/'.$rg['image']); ?>" target="_blank">
                  <img src="<?php echo base_url('uploads/reaction_gallery/'.$rg['image']); ?>" alt="<?php echo htmlspecialchars($rg['description']); ?>">
                </a>
              <?php } else { ?>
                <div style="background: linear-gradient(135deg, #d97706, #ea580c); color:white; padding:1.5rem; border-radius:var(--radius-lg); font-weight:800; font-size:1.3rem; text-align:center; height: 100%; display: flex; align-items: center; justify-content: center;">
                  <?php echo htmlspecialchars($rg['description']); ?>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
          <?php if(count($reactionGalleryImages) > 1) { ?>
            <div class="reaction-dots">
              <?php foreach($reactionGalleryImages as $idx => $rg) { ?>
                <span class="rg-dot <?php echo $idx === 0 ? 'active' : ''; ?>" onclick="showReactionSlide(<?php echo $idx; ?>)"></span>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
        <script>
          let currentRgSlide = 0;
          const rgSlides = document.querySelectorAll('.reaction-slide');
          const rgDots = document.querySelectorAll('.rg-dot');
          function showReactionSlide(index) {
            rgSlides.forEach((slide, i) => {
              slide.style.display = (i === index) ? 'flex' : 'none';
            });
            rgDots.forEach((dot, i) => {
              dot.style.background = (i === index) ? '#f97316' : 'rgba(255,255,255,0.4)';
            });
            currentRgSlide = index;
          }
          if (rgSlides.length > 1) {
            setInterval(() => {
              currentRgSlide = (currentRgSlide + 1) % rgSlides.length;
              showReactionSlide(currentRgSlide);
            }, 4000);
          }
        </script>
      <?php } else { ?>
        <p>No recent reactions.</p>
      <?php } ?>
    </div>
  </div>

  <!-- Gallery Preview Section -->
  <section style="padding: 5rem 0;">
    <div class="container">
      <div class="home-gallery-head">
        <h2 class="section-title">Gallery</h2>
        <div class="home-gallery-nav">
          <a href="<?php echo base_url('gallery'); ?>" class="gallery-nav-circle prev" aria-label="Previous"></a>
          <a href="<?php echo base_url('gallery'); ?>" class="gallery-nav-circle next" aria-label="Next"></a>
        </div>
      </div>

      <div class="home-gallery-grid">
        <?php if(!empty($images)) { foreach(array_slice($images, 0, 5) as $img) { ?>
        <a href="<?php echo base_url('Gallery/singleAlbum/'.$img['guId']); ?>" class="home-gallery-item">
          <div class="home-gallery-thumb">
             <img src="<?php echo base_url('uploads/gallery/original/'.$img['image']); ?>" alt="Gallery Image">
          </div>
          <div class="home-gallery-caption"><?php echo !empty($img['title']) ? $img['title'] : $img['name']; ?></div>
        </a>
        <?php } } ?>
      </div>
    </div>
  </section>

  <!-- Quick Menu Toggle Script -->
  <script>
    function toggleQuickMenu() {
      const content = document.getElementById('quickMenuContent');
      const arrow = document.getElementById('quickMenuArrow');
      const badge = document.getElementById('quickMenuBadge');
      if (!content) return;
      const isOpen = content.classList.contains('is-open');
      if (isOpen) {
        content.classList.remove('is-open');
        if (arrow) arrow.textContent = 'expand_more';
        if (badge) badge.querySelector('span').textContent = 'Expand';
      } else {
        content.classList.add('is-open');
        if (arrow) arrow.textContent = 'expand_less';
        if (badge) badge.querySelector('span').textContent = 'Hide';
      }
    }
  </script>

  <!-- Home Auto-Popup Modal (Customized in Admin Panel: Reaction Gallery / News Window / Custom News & File Downloader) -->
  <?php
    $ci = &get_instance();
    $ci->load->model('Settings_model');
    
    // Check enabled status
    $popupEnabledSetting = $ci->Settings_model->get('popup_enabled');
    if ($popupEnabledSetting === false || $popupEnabledSetting === null) {
      $popupEnabledSetting = $ci->Settings_model->get('reaction_popup_enabled');
    }
    $isPopupEnabled = ($popupEnabledSetting === false || $popupEnabledSetting === null) ? true : (bool)(int)$popupEnabledSetting;

    // Check device target
    $deviceTarget = (string)($ci->Settings_model->get('popup_device_target') ?: 'both');
    if ($deviceTarget === 'app_only') {
      $isPopupEnabled = false;
    }

    $popupDuration = (int)($ci->Settings_model->get('popup_duration') ?: ($ci->Settings_model->get('reaction_popup_duration') ?: 10));
    $popupMode = (string)($ci->Settings_model->get('popup_mode') ?: ($ci->Settings_model->get('reaction_popup_source') ?: 'combination'));
    if ($popupMode === 'both') {
      $popupMode = 'combination';
    }

    $popupStartDate = trim((string)($ci->Settings_model->get('popup_start_date') ?: ($ci->Settings_model->get('reaction_popup_start_date') ?: '')));
    $popupEndDate = trim((string)($ci->Settings_model->get('popup_end_date') ?: ($ci->Settings_model->get('reaction_popup_end_date') ?: '')));
    $todayIst = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Kolkata')))->format('Y-m-d');
    
    // Pre-Determined Expiry & Schedule Check
    $inSchedule = true;
    if ($popupStartDate !== '' && $todayIst < $popupStartDate) {
      $inSchedule = false;
    }
    if ($popupEndDate !== '' && $todayIst > $popupEndDate) {
      $inSchedule = false; // Strictly expired: once pre-determined date has passed, do not show
    }

    $webPopupItems = [];

    // 1. Reaction Poster Tab
    if (($popupMode === 'combination' || $popupMode === 'reaction_gallery')) {
      $reactionId = (int)($ci->Settings_model->get('popup_reaction_id') ?: 0);
      $chosenImg = null;
      if ($reactionId > 0) {
        $ci->load->model('ReactionGallery_model');
        $specReaction = $ci->ReactionGallery_model->getById($reactionId);
        if (!empty($specReaction) && !empty($specReaction['image'])) {
          $chosenImg = [
            'description' => $specReaction['description'] ?? 'Reaction Poster',
            'image' => $specReaction['image']
          ];
        }
      }
      if (!$chosenImg && !empty($reactionGalleryImages)) {
        $chosenImg = $reactionGalleryImages[0];
      }

      if ($chosenImg) {
        $webPopupItems[] = [
          'type'  => 'reaction',
          'tab'   => 'Reaction Poster',
          'badge' => 'KPSTA Reaction Poster',
          'title' => $chosenImg['description'] ?? 'Reaction Poster',
          'image' => base_url('uploads/reaction_gallery/' . $chosenImg['image']),
          'url'   => base_url('gallery'),
          'cta'   => 'Reaction Gallery &rarr;',
        ];
      }
    }

    // 2. News Bulletin Tab
    if (($popupMode === 'combination' || $popupMode === 'news')) {
      $newsId = (int)($ci->Settings_model->get('popup_news_id') ?: 0);
      $chosenNews = null;
      if ($newsId > 0) {
        $ci->load->model('News_model');
        $specNews = $ci->News_model->getById($newsId);
        if (!empty($specNews)) {
          $chosenNews = [
            'heading' => $specNews['heading'] ?? 'Latest News',
            'image' => $specNews['image'] ?? '',
            'content' => $specNews['content'] ?? '',
          ];
        }
      }
      if (!$chosenNews && !empty($listNews)) {
        $chosenNews = $listNews[0];
      }

      if ($chosenNews) {
        $webPopupItems[] = [
          'type'    => 'news',
          'tab'     => 'News Window',
          'badge'   => 'KPSTA News Bulletin',
          'title'   => $chosenNews['heading'] ?? 'Latest News',
          'image'   => !empty($chosenNews['image']) ? base_url('uploads/news/' . $chosenNews['image']) : '',
          'excerpt' => trim(strip_tags((string)($chosenNews['content'] ?? ''))),
          'url'     => base_url('news'),
          'cta'     => 'News Window &rarr;',
        ];
      }
    }

    // 3. Custom Announcement & File Downloader Tab
    if (($popupMode === 'combination' || $popupMode === 'custom')) {
      $customTitle = trim((string)($ci->Settings_model->get('popup_custom_title') ?: ''));
      $customContent = trim((string)($ci->Settings_model->get('popup_custom_content') ?: ''));
      $customImage = trim((string)($ci->Settings_model->get('popup_custom_image') ?: ''));
      $customFile = trim((string)($ci->Settings_model->get('popup_custom_file') ?: ''));
      $customFileName = trim((string)($ci->Settings_model->get('popup_custom_file_name') ?: 'Download Attachment'));
      $customBadge = trim((string)($ci->Settings_model->get('popup_custom_badge') ?: 'Announcement'));
      $customCtaText = trim((string)($ci->Settings_model->get('popup_custom_cta_text') ?: 'View More'));
      $customCtaUrl = trim((string)($ci->Settings_model->get('popup_custom_cta_url') ?: ''));

      if ($customTitle !== '' || $customContent !== '' || $customImage !== '' || $customFile !== '') {
        $ctaTarget = $customCtaUrl;
        if ($ctaTarget === '' && $customFile !== '') {
          $ctaTarget = base_url($customFile);
          if ($customCtaText === 'View More') {
            $customCtaText = 'Download File';
          }
        } elseif ($ctaTarget === '') {
          $ctaTarget = base_url();
        }

        $webPopupItems[] = [
          'type'      => 'custom',
          'tab'       => $customBadge ?: 'Announcement',
          'badge'     => $customBadge ?: 'KPSTA Announcement',
          'title'     => $customTitle ?: 'Official Announcement',
          'image'     => ($customImage !== '' && file_exists(FCPATH . $customImage)) ? base_url($customImage) : '',
          'content'   => $customContent,
          'file_url'  => ($customFile !== '' && file_exists(FCPATH . $customFile)) ? base_url($customFile) : '',
          'file_name' => $customFileName,
          'url'       => $ctaTarget,
          'cta'       => $customCtaText . ' &rarr;',
        ];
      }
    }
  ?>
  <?php if ($isPopupEnabled && $inSchedule && !empty($webPopupItems)) { ?>
  <div class="reaction-popup-backdrop" id="reactionPopupModal" role="dialog" aria-modal="true">
    <div class="reaction-popup-card">
      <div class="reaction-popup-header">
        <div style="display:flex; align-items:center; gap:8px;">
          <span class="material-symbols-outlined" style="font-size:1.2rem;">campaign</span>
          <strong id="popupHeaderTitle"><?php echo htmlspecialchars($webPopupItems[0]['badge']); ?></strong>
        </div>
        <button type="button" onclick="closeReactionPopup()" style="background:transparent; border:none; color:white; font-size:1.4rem; cursor:pointer; line-height:1;" aria-label="Close Popup">&times;</button>
      </div>
      <?php if (count($webPopupItems) > 1): ?>
      <div style="display:flex; gap:8px; padding:10px 14px; background:#f8fafc; border-bottom:1px solid #e2e8f0; overflow-x:auto;">
        <?php foreach ($webPopupItems as $idx => $pItem): ?>
        <button type="button"
          class="web-popup-tab-btn"
          style="flex:1; white-space:nowrap; padding:9px 16px; font-size:13px; font-weight:700; border-radius:20px; cursor:pointer; transition:all 0.2s ease; <?php echo $idx === 0 ? 'background:#016D77; color:#ffffff; border:1.5px solid #016D77; box-shadow:0 2px 6px rgba(1,109,119,0.3);' : 'background:#e2e8f0; color:#1e293b; border:1.5px solid #cbd5e1;'; ?>"
          onclick="switchWebPopupTab(<?php echo $idx; ?>)"
          id="popupTabBtn<?php echo $idx; ?>">
          <?php echo htmlspecialchars($pItem['tab'] ?? $pItem['badge']); ?>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php foreach ($webPopupItems as $idx => $pItem): ?>
      <div class="reaction-popup-body web-popup-pane" id="popupPane<?php echo $idx; ?>" style="<?php echo $idx === 0 ? '' : 'display:none;'; ?>">
        <?php if (!empty($pItem['image'])): ?>
          <img src="<?php echo $pItem['image']; ?>" alt="<?php echo htmlspecialchars($pItem['title']); ?>">
        <?php endif; ?>

        <?php if ($pItem['type'] === 'custom'): ?>
          <div style="padding:16px 20px; text-align:left; background:#ffffff; width:100%; box-sizing:border-box;">
            <h3 style="margin:0 0 10px; color:#0f172a; font-size:16px; font-weight:700;"><?php echo htmlspecialchars($pItem['title']); ?></h3>
            <?php if (!empty($pItem['content'])): ?>
              <div style="margin:0 0 12px; font-size:13.5px; color:#334155; line-height:1.6; max-height:220px; overflow-y:auto;">
                <?php echo $pItem['content']; ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($pItem['file_url'])): ?>
              <div style="margin-top:10px; padding-top:10px; border-top:1px dashed #cbd5e1;">
                <a href="<?php echo $pItem['file_url']; ?>" download class="btn-green" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; font-size:12px; font-weight:700; text-decoration:none; border-radius:8px;">
                  <span class="material-symbols-outlined" style="font-size:16px;">download</span>
                  <span><?php echo htmlspecialchars($pItem['file_name'] ?: 'Download Attached Document'); ?></span>
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php elseif (empty($pItem['image'])): ?>
          <div style="padding:20px; text-align:left; background:#f8fafc; border-radius:12px; margin:10px;">
            <h4 style="margin:0 0 8px; color:#0f172a;"><?php echo htmlspecialchars($pItem['title']); ?></h4>
            <p style="margin:0; font-size:14px; color:#475569; line-height:1.5;"><?php echo htmlspecialchars(mb_substr($pItem['excerpt'] ?? '', 0, 260)); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($pItem['image']) && !empty($pItem['title']) && $pItem['type'] !== 'custom'): ?>
          <p style="margin:8px 12px 10px; font-size:13px; font-weight:600; color:#fff; text-align:center;"><?php echo htmlspecialchars($pItem['title']); ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      <div class="reaction-popup-footer" style="padding:14px 20px; background:#ffffff; border-top:1px solid #e2e8f0; display:flex; justify-content:center; align-items:center;">
        <a href="<?php echo $webPopupItems[0]['url']; ?>" id="popupCtaLink" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; background:linear-gradient(135deg, #016D77 0%, #008080 100%); color:#ffffff !important; font-size:14.5px; font-weight:700; text-decoration:none; padding:11px 32px; border-radius:30px; box-shadow:0 4px 14px rgba(1,109,119,0.35); text-align:center; line-height:1.2; letter-spacing:0.3px; transition:transform 0.2s, box-shadow 0.2s;">
          <span id="popupCtaText"><?php echo $webPopupItems[0]['cta']; ?></span>
        </a>
      </div>
    </div>
  </div>
  <script>
    const webPopupMeta = <?php echo json_encode(array_map(function($item) {
      return ['badge' => $item['badge'], 'url' => $item['url'], 'cta' => html_entity_decode($item['cta'])];
    }, $webPopupItems)); ?>;
    function switchWebPopupTab(idx) {
      document.querySelectorAll('.web-popup-pane').forEach(function(el, i) {
        el.style.display = (i === idx) ? '' : 'none';
      });
      webPopupMeta.forEach(function(_, i) {
        var btn = document.getElementById('popupTabBtn' + i);
        if (!btn) return;
        if (i === idx) {
          btn.style.background = '#016D77';
          btn.style.color = '#ffffff';
          btn.style.borderColor = '#016D77';
          btn.style.boxShadow = '0 2px 6px rgba(1,109,119,0.3)';
        } else {
          btn.style.background = '#e2e8f0';
          btn.style.color = '#1e293b';
          btn.style.borderColor = '#cbd5e1';
          btn.style.boxShadow = 'none';
        }
      });
      var titleEl = document.getElementById('popupHeaderTitle');
      var ctaEl = document.getElementById('popupCtaLink');
      var ctaTextEl = document.getElementById('popupCtaText');
      if (webPopupMeta[idx]) {
        if (titleEl) titleEl.textContent = webPopupMeta[idx].badge;
        if (ctaEl) ctaEl.setAttribute('href', webPopupMeta[idx].url);
        if (ctaTextEl) ctaTextEl.innerHTML = webPopupMeta[idx].cta;
      }
    }
    function closeReactionPopup() {
      const modal = document.getElementById('reactionPopupModal');
      if (modal) modal.classList.remove('is-visible');
    }
    setTimeout(function() {
      const modal = document.getElementById('reactionPopupModal');
      if (modal && !sessionStorage.getItem('kpsta_reaction_shown')) {
        modal.classList.add('is-visible');
        sessionStorage.setItem('kpsta_reaction_shown', '1');
      }
    }, <?php echo $popupDuration * 1000; ?>);
  </script>
  <?php } ?>

  </main>
