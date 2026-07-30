  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">ORDER & CIRCULAR - <?php echo strtoupper($contentTitle); ?></h1>
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

  <main style="padding: 3.5rem 0 5rem;">
    <div class="container">

      <!-- Search & Filter Bar -->
      <form action="<?php echo $urlString; ?>" method="GET" class="search-filter-bar">
        <div class="search-input-group">
          <input type="text" name="search" id="circularSearchInput" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="btn-search" aria-label="Search"><span class="material-symbols-outlined">search</span></button>
        </div>
        <select name="category" class="filter-select" onchange="this.form.submit()">
          <option value="">Search By Label ▼</option>
          <?php if(!empty($categories)) { foreach($categories as $cat) { ?>
            <option value="<?php echo $cat['id']; ?>" <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>>
              <?php echo $cat['name']; ?>
            </option>
          <?php } } ?>
        </select>
      </form>

      <?php if(!empty($orders)) { foreach($orders as $monthYear => $orderGroup) { ?>
      <!-- Ribbon -->
      <div class="ribbon-section-header">
        <div class="ribbon-badge"><?php echo $monthYear; ?></div>
        <div class="ribbon-line"></div>
      </div>

      <div>
        <?php foreach($orderGroup as $order) { ?>
        <div class="circular-item">
          <div class="circular-date"><?php echo date('d', strtotime($order['date_unformat'])); ?></div>
          <div class="circular-content">
            <?php 
            if(!empty($order['path'])) { 
                $link = ($order['upload_type'] == 'file') ? base_url('uploads/order_circular/'.$order['path']) : $order['path'];
            ?>
              <a href="<?php echo $link; ?>" target="_blank" class="circular-title" style="color:inherit; text-decoration:none;"><?php echo $order['description']; ?></a>
            <?php } else { ?>
              <span class="circular-title"><?php echo $order['description']; ?></span>
            <?php } ?>
            <?php if(!empty($order['category'])) { ?>
            <span class="tag-pill"><?php echo $order['category']; ?></span>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>
      <?php } } else { ?>
        <p>No orders or circulars found.</p>
      <?php } ?>

      <!-- Pagination -->
      <?php if(!empty($links)) { ?>
      <div class="pagination">
        <?php echo $links; ?>
      </div>
      <?php } ?>

    </div>
  </main>
