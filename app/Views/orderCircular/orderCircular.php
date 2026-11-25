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

      <?php
        $baseUrl = base_url('order-circular/' . ($this->uri->segment(2) ?? ''));
        $hasFilters = !empty($selectedCategory) || !empty($selectedYear) || !empty($selectedMonth) || !empty($search);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        function buildFilterUrl($base, $cat, $yr, $mo, $srch) {
            $p = array();
            if (!empty($cat)) $p['category'] = $cat;
            if (!empty($yr)) $p['year'] = $yr;
            if (!empty($mo)) $p['month'] = $mo;
            if (!empty($srch)) $p['search'] = $srch;
            return $base . (!empty($p) ? '?' . http_build_query($p) : '');
        }
      ?>

      <!-- Search & Filter Toolbar: Year, Month, Label Dropdown & Clear Action -->
      <div class="order-filter-toolbar" style="display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 12px; margin-bottom: 2rem; position: relative;">
        
        <!-- Year Filter -->
        <div style="min-width: 140px; flex: 1; max-width: 170px;">
          <select id="yearFilterSelect" onchange="location.href=this.value;" aria-label="Filter by Year" style="width: 100%; height: 44px; padding: 0 14px; background: white; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 14px; color: var(--color-primary-dark); cursor: pointer; box-shadow: var(--shadow-sm); outline: none;">
            <option value="<?php echo buildFilterUrl($baseUrl, $selectedCategory, '', $selectedMonth, $search); ?>">Search by Year</option>
            <?php if(!empty($years)) { foreach($years as $yr) { ?>
              <option value="<?php echo buildFilterUrl($baseUrl, $selectedCategory, $yr, $selectedMonth, $search); ?>" <?php echo ($selectedYear == $yr) ? 'selected' : ''; ?>><?php echo $yr; ?></option>
            <?php } } ?>
          </select>
        </div>

        <!-- Month Filter -->
        <div style="min-width: 150px; flex: 1; max-width: 180px;">
          <select id="monthFilterSelect" onchange="location.href=this.value;" aria-label="Filter by Month" style="width: 100%; height: 44px; padding: 0 14px; background: white; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 14px; color: var(--color-primary-dark); cursor: pointer; box-shadow: var(--shadow-sm); outline: none;">
            <option value="<?php echo buildFilterUrl($baseUrl, $selectedCategory, $selectedYear, '', $search); ?>">Search by Month</option>
            <?php foreach($months as $num => $name) { ?>
              <option value="<?php echo buildFilterUrl($baseUrl, $selectedCategory, $selectedYear, $num, $search); ?>" <?php echo ($selectedMonth == $num) ? 'selected' : ''; ?>><?php echo $name; ?></option>
            <?php } ?>
          </select>
        </div>

        <!-- Label Filter Bar with Scrollable Searchable Dropdown -->
        <div class="custom-searchable-dropdown" id="labelDropdown" style="position: relative; width: 100%; max-width: 270px; flex: 1;">
          <button type="button" class="dropdown-trigger-btn" id="labelDropdownBtn" style="width: 100%; height: 44px; padding: 0 16px; background: white; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 14px; color: var(--color-primary-dark); display: flex; align-items: center; justify-content: space-between; cursor: pointer; box-shadow: var(--shadow-sm); transition: all 0.2s;">
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
              <a href="<?php echo buildFilterUrl($baseUrl, '', $selectedYear, $selectedMonth, $search); ?>" class="dropdown-item-link <?php echo empty($selectedCategory) ? 'selected' : ''; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; font-size: 14px; color: var(--color-primary-dark); text-decoration: none; cursor: pointer;">
                <span>All Labels</span>
                <?php if(empty($selectedCategory)): ?><span class="material-symbols-outlined" style="font-size:1.1rem; color:var(--color-primary);">check</span><?php endif; ?>
              </a>
              <?php if(!empty($categories)) { foreach($categories as $cat) { ?>
                <a href="<?php echo buildFilterUrl($baseUrl, $cat['id'], $selectedYear, $selectedMonth, $search); ?>" data-label="<?php echo htmlspecialchars(strtolower($cat['name'])); ?>" class="dropdown-item-link <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; font-size: 14px; color: #334155; text-decoration: none; cursor: pointer; transition: background 0.15s;">
                  <span><?php echo htmlspecialchars($cat['name']); ?></span>
                  <?php if($selectedCategory == $cat['id']): ?><span class="material-symbols-outlined" style="font-size:1.1rem; color:var(--color-orange);">check</span><?php endif; ?>
                </a>
              <?php } } ?>
            </div>
          </div>
        </div>

        <?php if($hasFilters): ?>
        <a href="<?php echo $baseUrl; ?>" style="display: inline-flex; align-items: center; gap: 4px; height: 44px; font-size: 13px; color: #e11d48; text-decoration: none; font-weight: 600; padding: 0 14px; background: #fff1f2; border-radius: 8px; border: 1.5px solid #ffe4e6; box-shadow: var(--shadow-sm); white-space: nowrap;">
          <span class="material-symbols-outlined" style="font-size: 1rem;">close</span> Clear Filters
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
        <?php foreach($orderGroup as $order) { 
          $link = !empty($order['path']) ? (($order['upload_type'] == 'file') ? base_url('uploads/order_circular/'.$order['path']) : $order['path']) : '';
          $cleanDesc = trim(strip_tags($order['description']));
          $waShareLink = !empty($link) ? $link : base_url('order-circular');
          $waText = "*KPSTA - Order & Circular*\n" . $cleanDesc . "\n" . $waShareLink;
          $waUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($waText);
        ?>
        <div class="circular-item">
          <div class="circular-date"><?php echo date('d', strtotime($order['date_unformat'])); ?></div>
          <div class="circular-content">
            <?php if(!empty($link)) { ?>
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
          <div class="circular-share">
            <a href="<?php echo $waUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-circle" title="Share on WhatsApp" aria-label="Share on WhatsApp">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="white" aria-hidden="true">
                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.15c-1.38 0-2.74-.35-3.94-1.06l-.29-.17-3.12.82.83-3.04-.19-.31a8.19 8.19 0 0 1-1.25-4.48c0-4.54 3.69-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.24-8.24 8.24zm4.51-6.17c-.25-.12-1.47-.72-1.7-.8-.23-.08-.4-.12-.56.12-.17.25-.64.8-.79.97-.14.16-.29.19-.53.07-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.46-1.36-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.83-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.3-.23.25-.87.85-.87 2.07s.89 2.4 1.01 2.56c.13.17 1.74 2.66 4.22 3.73.59.25 1.05.4 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.29z"/>
              </svg>
            </a>
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
