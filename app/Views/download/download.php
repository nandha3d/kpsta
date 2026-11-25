  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title"><?php echo $contentTitle; ?></h1>
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
        KPSTA സംസ്ഥാന ഭാരവാഹികളുടെ സത്യപ്രതിജ്ഞ @ എറണാകുളം 22.02.2026
      <?php } ?>
    </marquee>
  </div>

  <main class="page-download">
    <div class="container">
      
      <?php if((isset($type) && $type == 5) || (isset($contentTitle) && stripos($contentTitle, 'notic') !== false)): ?>
      <!-- KPSTA Official Logo Download Card -->
      <div class="logo-download-banner" style="background: linear-gradient(135deg, #0b4f57 0%, #17717a 100%); border-radius: 14px; padding: 2rem 2.5rem; margin-bottom: 2.5rem; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.75rem; box-shadow: 0 10px 25px rgba(11, 79, 87, 0.25);">
        <div style="display: flex; align-items: center; gap: 1.75rem; flex-wrap: wrap;">
          <div style="width: 88px; height: 88px; background: white; border-radius: 50%; padding: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(0,0,0,0.15); flex-shrink: 0;">
            <img src="<?php echo base_url('public/images/logo.png'); ?>" alt="KPSTA Official Logo" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #fed7aa; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Official Emblem</div>
            <h2 style="font-size: 1.6rem; font-weight: 800; color: white; margin: 0 0 0.4rem;">KPSTA Official Logo</h2>
            <p style="color: #e2e8f0; font-size: 0.95rem; margin: 0; max-width: 540px; line-height: 1.5;">Download high-resolution transparent PNG KPSTA emblem for official posters, flex boards, certificates, and notices.</p>
          </div>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
          <a href="<?php echo base_url('public/images/logo.png'); ?>" download="KPSTA_Official_Logo.png" class="btn-logo-dl" style="display: inline-flex; align-items: center; gap: 0.6rem; background: var(--color-orange); color: white; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 1rem; text-decoration: none; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35); transition: all 0.2s ease;">
            <span class="material-symbols-outlined" style="font-size: 1.3rem;">download</span> Download Logo (PNG)
          </a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Category Navigation Tabs (Each category is a tab) -->
      <?php if(!empty($categories)) { 
        $activeCatId = !empty($selectedCategory) ? $selectedCategory : $categories[0]['id'];
      ?>
      <div class="tab-header <?php echo (isset($route) && $route === 'melakal') ? 'tab-header--joined' : ''; ?>">
        <?php foreach($categories as $cat) { ?>
          <a href="<?php echo $urlString . '?category=' . $cat['id']; ?>" class="tab-btn <?php echo ($activeCatId == $cat['id']) ? 'active' : ''; ?>">
            <span class="tab-btn-label"><?php echo htmlspecialchars($cat['name']); ?></span>
          </a>
        <?php } ?>
      </div>
      <?php } ?>

      <div class="download-list">
        <?php if(!empty($data)) { foreach($data as $item) { 
            if ($item['upload_type'] == 'file') {
                $url = base_url(DOWNLOAD_PATH . $item['path']);
            } else if ($item['upload_type'] == 'url') {
                $url = $item['path'];
            } else {
                $url = "#";
            }
        ?>
        <?php
          $cleanDesc = trim(strip_tags($item['description']));
          $titlePrefix = !empty($contentTitle) ? htmlspecialchars_decode($contentTitle) : 'Downloads';
          $shareUrl = ($url !== '#') ? $url : base_url('download/forms');
          $waDlText = "*KPSTA - " . $titlePrefix . "*\n" . $cleanDesc . "\n" . $shareUrl;
          $waDlUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($waDlText);
        ?>
        <div class="download-row <?php echo !empty($showFileIcon) ? 'has-icon' : ''; ?>">
          <div class="download-info">
            <?php if(!empty($showFileIcon)) { echo file_type_badge($item['path'], $item['upload_type']); } ?>
            <span class="download-title"><?php echo htmlspecialchars($item['description']); ?></span>
          </div>
          <div class="download-actions">
            <a href="<?php echo $waDlUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-circle" title="Share on WhatsApp" aria-label="Share on WhatsApp">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="white" aria-hidden="true">
                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.15c-1.38 0-2.74-.35-3.94-1.06l-.29-.17-3.12.82.83-3.04-.19-.31a8.19 8.19 0 0 1-1.25-4.48c0-4.54 3.69-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.24-8.24 8.24zm4.51-6.17c-.25-.12-1.47-.72-1.7-.8-.23-.08-.4-.12-.56.12-.17.25-.64.8-.79.97-.14.16-.29.19-.53.07-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.46-1.36-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.83-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.3-.23.25-.87.85-.87 2.07s.89 2.4 1.01 2.56c.13.17 1.74 2.66 4.22 3.73.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.29z"/>
              </svg>
            </a>
            <a href="<?php echo $url; ?>" target="_blank" class="btn-circle-download" title="Download" aria-label="Download">
              <span class="material-symbols-outlined">download</span>
            </a>
          </div>
        </div>
        <?php } } else { ?>
          <div class="alert alert-info text-center" style="background: #ffffff; border: 1px solid #e2e8f0; padding: 3rem; border-radius: 12px; color: #64748b; margin-top: 1rem;">
            <p style="font-size: 1.1rem; font-weight: 600; margin: 0;">No downloads available.</p>
          </div>
        <?php } ?>
      </div>

      <!-- Pagination -->
      <?php if(!empty($links)) { ?>
      <div class="pagination" style="margin-top: 3rem;">
        <?php echo $links; ?>
      </div>
      <?php } ?>

    </div>
  </main>
