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

      <?php if(isset($route) && $route === 'melakal'): ?>
      <!-- Melakal Festival Navigation (Neutral on initial load, shows items on click) -->
      <div style="display: flex; justify-content: flex-start; margin-bottom: 2rem; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <div class="tab-header tab-header--joined">
          <?php if(!empty($categories)) { foreach($categories as $cat) { ?>
            <a href="<?php echo $base_url . '?category=' . $cat['id']; ?>" class="tab-btn <?php echo (!empty($selectedCategory) && $selectedCategory == $cat['id']) ? 'active' : ''; ?>">
              <span class="tab-btn-label"><?php echo htmlspecialchars($cat['name']); ?></span>
            </a>
          <?php } } ?>
        </div>
      </div>

      <?php if(empty($selectedCategory)): ?>
      <div class="alert alert-info text-center" style="background: #ffffff; border: 1px solid #e2e8f0; padding: 3rem; border-radius: 12px; color: #64748b; margin-top: 1rem;">
        <p style="font-size: 1.1rem; font-weight: 600; margin: 0;">Please select a category above to view relevant documents and circulars.</p>
      </div>
      <?php else: ?>
      
      <!-- Download File List for Selected Melakal Category -->
      <div class="download-list">
        <?php if(!empty($forms)) { 
          foreach($forms as $catName => $items) { 
            foreach($items as $item) {
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
        <?php } } } else { ?>
          <div class="alert alert-info text-center" style="background: #ffffff; border: 1px solid #e2e8f0; padding: 3rem; border-radius: 12px; color: #64748b; margin-top: 1rem;">
            <p style="font-size: 1.1rem; font-weight: 600; margin: 0;">No documents available under this category.</p>
          </div>
        <?php } ?>
      </div>
      <?php endif; ?>

      <?php else: ?>

      <!-- Downloads: Forms & Academic Corner (Category Cards Hub) -->
      <?php if(empty($selectedCategory)): ?>
      <div class="category-hub-container" style="padding: 1rem 0 3rem;">
        <div class="category-hub-intro" style="text-align: center; margin-bottom: 2.5rem;">
          <h2 style="font-size: 1.6rem; font-weight: 700; color: var(--color-primary-dark); margin-bottom: 0.5rem;">
            Select Category
          </h2>
          <p style="color: #64748b; font-size: 1rem;">Choose a category below to view and download relevant documents.</p>
        </div>

        <div class="service-card-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
          <?php if(!empty($categories)) { foreach($categories as $cat) { ?>
            <a href="<?php echo $base_url . '?category=' . $cat['id']; ?>" class="service-hub-card" style="min-height: 80px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; text-decoration: none; background: white; border-radius: 10px; border: 2px solid #e2e8f0; box-shadow: var(--shadow-sm); transition: all 0.25s ease;">
              <span style="font-size: 1.1rem; font-weight: 700; color: var(--color-primary-dark); text-align: left;"><?php echo htmlspecialchars($cat['name']); ?></span>
              <span class="material-symbols-outlined" style="color: var(--color-orange); font-size: 1.4rem;">arrow_forward</span>
            </a>
          <?php } } else { ?>
            <p>No categories found.</p>
          <?php } ?>
        </div>
      </div>

      <?php else: ?>

      <!-- Active Category Header & Back Button -->
      <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="<?php echo $base_url; ?>" class="btn-back-hub" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-primary-dark); font-weight: 700; text-decoration: none; padding: 8px 18px; background: white; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
          <span class="material-symbols-outlined" style="font-size: 1.2rem;">arrow_back</span> Back to Categories
        </a>
        <?php 
          $displayCat = is_array($selectedCategoryName) ? ($selectedCategoryName['name'] ?? '') : (string)$selectedCategoryName;
          if(!empty($displayCat)): 
        ?>
        <div style="font-size: 1.2rem; font-weight: 800; color: var(--color-primary-dark);">
          Category: <span style="color: var(--color-orange);"><?php echo htmlspecialchars($displayCat); ?></span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Download File List -->
      <div class="download-list">
        <?php if(!empty($forms)) { 
          foreach($forms as $catName => $items) { 
            foreach($items as $item) {
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
        <?php } } } else { ?>
          <div class="alert alert-info text-center" style="background: #ffffff; border: 1px solid #e2e8f0; padding: 3rem; border-radius: 12px; color: #64748b; margin-top: 1rem;">
            <p style="font-size: 1.1rem; font-weight: 600; margin: 0;">No documents available under this category.</p>
          </div>
        <?php } ?>
      </div>

      <!-- Pagination -->
      <?php if(!empty($links)) { ?>
      <div class="pagination" style="margin-top: 3rem;">
        <?php echo $links; ?>
      </div>
      <?php } ?>

      <?php endif; ?>
      <?php endif; ?>

    </div>
  </main>
