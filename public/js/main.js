document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Navigation Toggle
  const navToggleBtn = document.getElementById('mobileNavToggle');
  const navLinksList = document.getElementById('navLinks');
  
  if (navToggleBtn && navLinksList) {
    navToggleBtn.addEventListener('click', () => {
      navLinksList.classList.toggle('open');
      const icon = navToggleBtn.querySelector('.material-symbols-outlined');
      if (icon) {
        icon.textContent = navLinksList.classList.contains('open') ? 'close' : 'menu';
      }
    });
  }

  // 1b. Mobile dropdown submenus (Organization, Downloads) open on tap
  // instead of hover, since touch devices have no hover state. Tapping the
  // arrow toggles the submenu open; tapping the label text still navigates.
  document.querySelectorAll('.dropdown > .nav-link').forEach((link) => {
    const arrow = link.querySelector('.material-symbols-outlined');
    if (!arrow) return;
    arrow.addEventListener('click', (e) => {
      if (window.innerWidth > 768) return;
      e.preventDefault();
      e.stopPropagation();
      const parent = link.parentElement;
      document.querySelectorAll('.dropdown.open').forEach((d) => {
        if (d !== parent) d.classList.remove('open');
      });
      parent.classList.toggle('open');
    });
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
