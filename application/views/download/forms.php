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

  <main style="padding: 4rem 0 7rem;">
    <div class="container">

      <!-- Search & Filter Bar (Optional if needed based on design) -->
      <?php if(!empty($categories)) { ?>
      <form action="<?php echo $urlString; ?>" method="GET" class="search-filter-bar" style="margin-bottom: 2rem;">
        <select name="category" class="filter-select" onchange="this.form.submit()" style="max-width: 300px; margin-left: auto;">
          <option value="">Search By Category ▼</option>
          <?php foreach($categories as $cat) { ?>
            <option value="<?php echo $cat['id']; ?>" <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>>
              <?php echo $cat['name']; ?>
            </option>
          <?php } ?>
        </select>
      </form>
      <?php } ?>

      <?php if(!empty($forms)) { foreach($forms as $catName => $items) { ?>
      <!-- Category Ribbon -->
      <div class="ribbon-section-header">
        <div class="ribbon-badge"><?php echo htmlspecialchars($catName); ?></div>
        <div class="ribbon-line"></div>
      </div>

      <div class="grid-large-cards">
        <?php foreach($items as $item) { 
            if ($item['upload_type'] == 'file') {
                $url = base_url('public/downloads/' . $item['path']);
            } else if ($item['upload_type'] == 'url') {
                $url = $item['path'];
            } else {
                $url = "#";
            }
        ?>
        <a href="<?php echo $url; ?>" target="_blank" class="action-block-card">
          <span><?php echo $item['description']; ?></span>
        </a>
        <?php } ?>
      </div>
      <div style="height: 2rem;"></div> <!-- Spacing -->
      <?php } } else { ?>
        <p>No forms available.</p>
      <?php } ?>

      <!-- Pagination -->
      <?php if(!empty($links)) { ?>
      <div class="pagination" style="margin-top: 3rem;">
        <?php echo $links; ?>
      </div>
      <?php } ?>

    </div>
  </main>
