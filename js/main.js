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
