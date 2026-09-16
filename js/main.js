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

  // 1b. Mobile / Tablet Dropdown Submenu Toggle
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle-btn');
  dropdownToggles.forEach(toggleBtn => {
    toggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const dropdownLi = toggleBtn.closest('.dropdown');
      if (!dropdownLi) return;

      const isOpen = dropdownLi.classList.contains('open');

      // Close other open dropdowns at this level
      document.querySelectorAll('.dropdown.open').forEach(other => {
        if (other !== dropdownLi) {
          other.classList.remove('open');
          const otherBtn = other.querySelector('.dropdown-toggle-btn');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      if (isOpen) {
        dropdownLi.classList.remove('open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      } else {
        dropdownLi.classList.add('open');
        toggleBtn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // Close mobile nav and open dropdowns when clicking outside header
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.main-header')) {
      if (navLinksList && navLinksList.classList.contains('open')) {
        navLinksList.classList.remove('open');
        const icon = navToggleBtn ? navToggleBtn.querySelector('.material-symbols-outlined') : null;
        if (icon) icon.textContent = 'menu';
      }
      document.querySelectorAll('.dropdown.open').forEach(d => {
        d.classList.remove('open');
        const btn = d.querySelector('.dropdown-toggle-btn');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      });
    }
  });

  // 2. Tab Switching (Melakal page)
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabPanels = document.querySelectorAll('.tab-panel');
  
  if (tabButtons.length > 0) {
    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetTab = btn.getAttribute('data-tab');
        const placeholderMsg = document.getElementById('tab-placeholder-msg');
        if (placeholderMsg) {
          placeholderMsg.style.display = 'none';
        }
        
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
