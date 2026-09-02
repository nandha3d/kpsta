document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Navigation Toggle
  const navToggleBtn = document.getElementById('mobileNavToggle');
  const navLinksList = document.getElementById('navLinks');
  
  // The flyout nav replaces the desktop bar at 1024px (see styles.css), so the
  // tap-to-expand submenus below have to use that same breakpoint.
  const isFlyoutNav = () => window.matchMedia('(max-width: 1024px)').matches;

  if (navToggleBtn && navLinksList) {
    navToggleBtn.addEventListener('click', () => {
      navLinksList.classList.toggle('open');
      const icon = navToggleBtn.querySelector('.material-symbols-outlined');
      if (icon) {
        icon.textContent = navLinksList.classList.contains('open') ? 'close' : 'menu';
      }
      // Collapse any expanded submenu so the flyout reopens in a known state
      if (!navLinksList.classList.contains('open')) {
        navLinksList.querySelectorAll('.dropdown.open').forEach((d) => d.classList.remove('open'));
      }
    });
  }

  // 1b. Mobile dropdown submenus (Organization, Downloads) open on tap instead
  // of hover, since touch devices have no hover state. The whole row is the
  // target -- the arrow glyph alone was too small to hit reliably. Both parent
  // links repeat their own destination as the first submenu item, so nothing
  // becomes unreachable by suppressing navigation here.
  document.querySelectorAll('.dropdown > .nav-link').forEach((link) => {
    link.addEventListener('click', (e) => {
      if (!isFlyoutNav()) return;
      e.preventDefault();
      const parent = link.parentElement;
      document.querySelectorAll('.dropdown.open').forEach((d) => {
        if (d !== parent) d.classList.remove('open');
      });
      parent.classList.toggle('open');
    });
  });

  // 1c. Bearer photos whose upload is missing used to render as a broken-image
  // icon with the alt text sitting inside the card frame. Fall back to the same
  // placeholder the views use when no image is set at all.
  document.querySelectorAll('.bearer-photo-wrapper img').forEach((img) => {
    const usePlaceholder = () => {
      if (!img.isConnected) return;
      const placeholder = document.createElement('div');
      placeholder.className = 'bearer-placeholder';
      placeholder.innerHTML = '<span class="material-symbols-outlined">person</span>';
      img.replaceWith(placeholder);
    };
    img.addEventListener('error', usePlaceholder);
    // Images that already failed before this script ran fire no error event
    if (img.complete && img.naturalWidth === 0) usePlaceholder();
  });

  // 2. Tab Switching (Melakal page)
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabPanels = document.querySelectorAll('.tab-panel');
  
  if (tabButtons.length > 0) {
    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetTab = btn.getAttribute('data-tab');
        
        tabButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        tabPanels.forEach(panel => {
          if (panel.getAttribute('id') === targetTab) {
            panel.style.display = 'block';
          } else {
            panel.style.display = 'none';
          }
        });
      });
    });
  }

  // 3. Accordion FAQ (Service Corner Details page)
  const accordionHeaders = document.querySelectorAll('.accordion-header');
  
  if (accordionHeaders.length > 0) {
    accordionHeaders.forEach(header => {
      header.addEventListener('click', () => {
        const item = header.parentElement;
        const toggleBtn = header.querySelector('.accordion-toggle-btn');
        const isOpen = item.classList.contains('open');

        // Close all siblings
        document.querySelectorAll('.accordion-item.open').forEach(sibling => {
          if (sibling !== item) {
            sibling.classList.remove('open');
            const sibBtn = sibling.querySelector('.accordion-toggle-btn');
            if (sibBtn) sibBtn.textContent = '+';
          }
        });
        
        if (isOpen) {
          item.classList.remove('open');
          if (toggleBtn) toggleBtn.textContent = '+';
        } else {
          item.classList.add('open');
          if (toggleBtn) toggleBtn.textContent = '-';
        }
      });
    });
  }

  // 4. Search Filtering (Order & Circular page)
  const searchInput = document.getElementById('circularSearchInput');
  const circularItems = document.querySelectorAll('.circular-item');
  
  if (searchInput && circularItems.length > 0) {
    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();
      circularItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(query)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }
});
