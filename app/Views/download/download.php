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
        <div class="download-row <?php echo !empty($showFileIcon) ? 'has-icon' : ''; ?>">
          <div class="download-info">
            <?php if(!empty($showFileIcon)) { echo file_type_badge($item['path'], $item['upload_type']); } ?>
            <span class="download-title"><?php echo htmlspecialchars($item['description']); ?></span>
          </div>
          <a href="<?php echo $url; ?>" target="_blank" class="btn-circle-download" title="Download">
            <span class="material-symbols-outlined">download</span>
          </a>
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
