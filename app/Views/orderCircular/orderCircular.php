  <!-- Hero Section -->
  <section class="hero-banner">
    <div class="container">
      <h1 class="hero-title">ORDER & CIRCULAR<?php echo (!empty($contentTitle) && strtolower($contentTitle) !== 'general') ? ' - ' . strtoupper($contentTitle) : ''; ?></h1>
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

  <main class="page-list">
    <div class="container">

      <!-- Label Filter Bar with Scrollable Searchable Dropdown -->
      <div class="order-filter-toolbar" style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 2rem; position: relative;">
        <div class="custom-searchable-dropdown" id="labelDropdown" style="position: relative; width: 100%; max-width: 320px;">
          <button type="button" class="dropdown-trigger-btn" id="labelDropdownBtn" style="width: 100%; height: 44px; padding: 0 16px; background: white; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 15px; color: var(--color-primary-dark); display: flex; align-items: center; justify-content: space-between; cursor: pointer; box-shadow: var(--shadow-sm); transition: all 0.2s;">
            <span id="labelDropdownText" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
              <?php 
                $activeLabel = is_array($selectedCategoryName) ? ($selectedCategoryName['name'] ?? '') : (string)$selectedCategoryName;
                echo !empty($activeLabel) ? htmlspecialchars($activeLabel) : 'Search By Label'; 
              ?>
            </span>
            <span class="material-symbols-outlined" style="font-size: 1.2rem; color: #64748b;">arrow_drop_down</span>
          </button>

          <div class="dropdown-menu-box" id="labelDropdownMenu" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: white; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 100; overflow: hidden;">
            <div style="padding: 10px; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
              <input type="text" id="labelSearchFilterInput" placeholder="Type to search labels..." style="width: 100%; height: 36px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none;">
            </div>
            <div class="dropdown-items-list" id="labelItemsList" style="max-height: 250px; overflow-y: auto; padding: 6px 0;">
              <a href="<?php echo base_url('order-circular'); ?>" class="dropdown-item-link <?php echo empty($selectedCategory) ? 'selected' : ''; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; font-size: 14px; color: var(--color-primary-dark); text-decoration: none; cursor: pointer;">
                <span>All Labels</span>
                <?php if(empty($selectedCategory)): ?><span class="material-symbols-outlined" style="font-size:1.1rem; color:var(--color-primary);">check</span><?php endif; ?>
              </a>
              <?php if(!empty($categories)) { foreach($categories as $cat) { ?>
                <a href="<?php echo base_url('order-circular?category=' . $cat['id']); ?>" data-label="<?php echo htmlspecialchars(strtolower($cat['name'])); ?>" class="dropdown-item-link <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; font-size: 14px; color: #334155; text-decoration: none; cursor: pointer; transition: background 0.15s;">
                  <span><?php echo htmlspecialchars($cat['name']); ?></span>
                  <?php if($selectedCategory == $cat['id']): ?><span class="material-symbols-outlined" style="font-size:1.1rem; color:var(--color-orange);">check</span><?php endif; ?>
                </a>
              <?php } } ?>
            </div>
          </div>
        </div>

        <?php if(!empty($selectedCategory)): ?>
        <a href="<?php echo base_url('order-circular'); ?>" style="margin-left: 12px; display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: #e11d48; text-decoration: none; font-weight: 600; padding: 8px 12px; background: #fff1f2; border-radius: 6px; border: 1px solid #ffe4e6;">
          <span class="material-symbols-outlined" style="font-size: 1rem;">close</span> Clear Filter
        </a>
        <?php endif; ?>
      </div>

      <script>
        document.addEventListener('DOMContentLoaded', () => {
          const btn = document.getElementById('labelDropdownBtn');
          const menu = document.getElementById('labelDropdownMenu');
          const filterInput = document.getElementById('labelSearchFilterInput');
          const items = document.querySelectorAll('#labelItemsList .dropdown-item-link');

          if (btn && menu) {
            btn.addEventListener('click', (e) => {
              e.stopPropagation();
              const isOpen = menu.style.display === 'block';
              menu.style.display = isOpen ? 'none' : 'block';
              if (!isOpen && filterInput) {
                setTimeout(() => filterInput.focus(), 50);
              }
            });

            document.addEventListener('click', (e) => {
              if (!menu.contains(e.target) && e.target !== btn) {
                menu.style.display = 'none';
              }
            });

            if (filterInput) {
              filterInput.addEventListener('input', () => {
                const q = filterInput.value.toLowerCase().trim();
                items.forEach(item => {
                  const label = item.getAttribute('data-label') || '';
                  if (!label || label.includes(q)) {
                    item.style.display = 'flex';
                  } else {
                    item.style.display = 'none';
                  }
                });
              });
            }
          }
        });
      </script>

      <?php if(!empty($orders)) { foreach($orders as $monthYear => $orderGroup) { ?>
      <!-- Ribbon -->
      <div class="ribbon-section-header">
        <div class="ribbon-badge"><?php echo $monthYear; ?></div>
        <div class="ribbon-line"></div>
      </div>

      <div class="circular-list">
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
            <?php if(!empty($order['categories_list'])): foreach($order['categories_list'] as $catItem): ?>
              <a href="<?php echo base_url('order-circular?category=' . $catItem['id']); ?>" class="tag-pill" style="text-decoration:none;" title="Filter by <?php echo htmlspecialchars($catItem['name']); ?>">
                <?php echo htmlspecialchars($catItem['name']); ?>
              </a>
            <?php endforeach; elseif(!empty($order['category'])): ?>
              <a href="<?php echo base_url('order-circular?search=' . urlencode($order['category'])); ?>" class="tag-pill" style="text-decoration:none;" title="Filter by <?php echo htmlspecialchars($order['category']); ?>">
                <?php echo htmlspecialchars($order['category']); ?>
              </a>
            <?php endif; ?>
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
