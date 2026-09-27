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

  // 1a. Helper to detect and mark active menu items & auto-expand active dropdowns
  function initActiveNavState() {
    const currentUrl = window.location.href;
    const currentPath = window.location.pathname.toLowerCase().replace(/\/$/, '') || '/';
    const currentFile = currentPath.split('/').pop() || 'index.html';

    // Check if we navigated via mobile expand arrow (restore open mobile drawer)
    const keepNavOpen = sessionStorage.getItem('kpsta_keep_mobile_nav_open') === 'true';
    if (keepNavOpen) {
      sessionStorage.removeItem('kpsta_keep_mobile_nav_open');
      if (window.innerWidth <= 991 && navLinksList) {
        navLinksList.classList.add('open');
        const icon = navToggleBtn ? navToggleBtn.querySelector('.material-symbols-outlined') : null;
        if (icon) icon.textContent = 'close';
      }
    }

    // Mark active dropdown items and keep parent dropdown open on mobile
    document.querySelectorAll('.dropdown').forEach(dropdown => {
      let hasActiveChild = false;
      const items = dropdown.querySelectorAll('.dropdown-menu .dropdown-item');

      items.forEach(item => {
        const itemHref = item.getAttribute('href');
        if (!itemHref || itemHref === 'javascript:void(0)') return;

        let isMatch = false;
        try {
          const itemUrl = new URL(item.href, currentUrl);
          const itemPath = itemUrl.pathname.toLowerCase().replace(/\/$/, '') || '/';
          const itemFile = itemPath.split('/').pop();

          isMatch = (currentPath === itemPath) ||
                    (currentFile && itemFile && currentFile === itemFile) ||
                    (itemPath !== '/' && currentPath.endsWith(itemPath));
        } catch (e) {}

        if (isMatch) {
          item.classList.add('active');
          hasActiveChild = true;
        } else {
          item.classList.remove('active');
        }
      });

      const parentNavLink = dropdown.querySelector('.nav-link');
      const isParentActive = parentNavLink && parentNavLink.classList.contains('active');

      if (hasActiveChild || isParentActive) {
        dropdown.classList.add('open');
        const toggleBtn = dropdown.querySelector('.dropdown-toggle-btn');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
      }
    });
  }

  initActiveNavState();

  // 1b. Mobile / Tablet Dropdown Submenu Toggle
  const dropdownWrappers = document.querySelectorAll('.dropdown .nav-item-wrapper');
  dropdownWrappers.forEach(wrapper => {
    wrapper.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const dropdownLi = wrapper.closest('.dropdown');
      if (!dropdownLi) return;

      const isOpen = dropdownLi.classList.contains('open');

      if (isOpen) {
        // If already open, clicking again closes this dropdown ("once we close ... the menu dissappears")
        dropdownLi.classList.remove('open');
        const toggleBtn = dropdownLi.querySelector('.dropdown-toggle-btn');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
      } else {
        // Close other open dropdowns ("select other menu the menu dissappears")
        document.querySelectorAll('.dropdown.open').forEach(other => {
          if (other !== dropdownLi) {
            other.classList.remove('open');
            const otherBtn = other.querySelector('.dropdown-toggle-btn');
            if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
          }
        });

        // Expand this dropdown
        dropdownLi.classList.add('open');
        const toggleBtn = dropdownLi.querySelector('.dropdown-toggle-btn');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');

        // On mobile view, automatically point to (navigate to) the first menu in that submenu
        if (window.innerWidth <= 991) {
          const firstItem = dropdownLi.querySelector('.dropdown-menu .dropdown-item');
          if (firstItem && firstItem.getAttribute('href') && firstItem.getAttribute('href') !== 'javascript:void(0)') {
            let isAlreadyOnTarget = false;
            try {
              const currentPath = window.location.pathname.toLowerCase().replace(/\/$/, '') || '/';
              const currentFile = currentPath.split('/').pop() || 'index.html';
              const targetUrl = new URL(firstItem.href, window.location.href);
              const targetPath = targetUrl.pathname.toLowerCase().replace(/\/$/, '') || '/';
              const targetFile = targetPath.split('/').pop();

              isAlreadyOnTarget = (currentPath === targetPath) ||
                                  (currentFile && targetFile && currentFile === targetFile) ||
                                  (targetPath !== '/' && currentPath.endsWith(targetPath));
            } catch (err) {}

            if (!isAlreadyOnTarget) {
              // Persist mobile nav open state so the menu remains expanded on destination page
              sessionStorage.setItem('kpsta_keep_mobile_nav_open', 'true');
              window.location.href = firstItem.href;
              return;
            } else {
              firstItem.classList.add('active');
            }
          }
        }
      }
    });
  });

  // 1c. Submenu item click: allow normal navigation
  document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
    item.addEventListener('click', () => {
      sessionStorage.removeItem('kpsta_keep_mobile_nav_open');
      if (window.innerWidth <= 991 && navLinksList) {
        navLinksList.classList.remove('open');
        const icon = navToggleBtn ? navToggleBtn.querySelector('.material-symbols-outlined') : null;
        if (icon) icon.textContent = 'menu';
      }
    });
  });

  // 1d. Top-level single nav links: close dropdowns and mobile menu
  document.querySelectorAll('.nav-links > li:not(.dropdown) > .nav-link').forEach(link => {
    link.addEventListener('click', () => {
      sessionStorage.removeItem('kpsta_keep_mobile_nav_open');
      document.querySelectorAll('.dropdown.open').forEach(d => {
        d.classList.remove('open');
        const btn = d.querySelector('.dropdown-toggle-btn');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      });
      if (window.innerWidth <= 991 && navLinksList) {
        navLinksList.classList.remove('open');
        const icon = navToggleBtn ? navToggleBtn.querySelector('.material-symbols-outlined') : null;
        if (icon) icon.textContent = 'menu';
      }
    });
  });

  // 1e. Close mobile nav and open dropdowns when clicking outside header
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

  // 5. Sticky Android App Button & Play Store Notification Modal
  const appStickyBtn = document.getElementById('kpstaAppStickyBtn');
  const appModal = document.getElementById('kpstaAppModal');
  const appModalClose = document.getElementById('kpstaAppModalClose');
  const appModalDismiss = document.getElementById('kpstaAppModalDismiss');
  const appBubble = document.getElementById('kpstaAppBubble');
  const appBubbleClose = document.getElementById('kpstaAppBubbleClose');
  const btnPlaystoreDownload = document.getElementById('btnPlaystoreDownload');

  function openAppModal() {
    if (!appModal) return;
    appModal.classList.add('active');
    appModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (appBubble) {
      appBubble.classList.add('hidden');
    }
  }

  function closeAppModal() {
    if (!appModal) return;
    appModal.classList.remove('active');
    appModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (appStickyBtn) {
    appStickyBtn.addEventListener('click', (e) => {
      e.preventDefault();
      openAppModal();
    });
  }

  if (appBubble) {
    // Show after 2 seconds if not previously dismissed in this session
    const isDismissed = sessionStorage.getItem('kpsta_app_prompt_dismissed');
    if (!isDismissed) {
      setTimeout(() => {
        if (!appModal || !appModal.classList.contains('active')) {
          appBubble.classList.add('visible');
        }
      }, 2000);
    }

    appBubble.addEventListener('click', (e) => {
      // If clicked on close button, don't open modal
      if (e.target.closest('#kpstaAppBubbleClose')) {
        return;
      }
      openAppModal();
    });
  }

  if (appBubbleClose) {
    appBubbleClose.addEventListener('click', (e) => {
      e.stopPropagation();
      e.preventDefault();
      if (appBubble) {
        appBubble.classList.remove('visible');
        appBubble.classList.add('hidden');
      }
      sessionStorage.setItem('kpsta_app_prompt_dismissed', 'true');
    });
  }

  if (appModalClose) {
    appModalClose.addEventListener('click', () => {
      closeAppModal();
      sessionStorage.setItem('kpsta_app_prompt_dismissed', 'true');
    });
  }

  if (appModalDismiss) {
    appModalDismiss.addEventListener('click', () => {
      closeAppModal();
      sessionStorage.setItem('kpsta_app_prompt_dismissed', 'true');
    });
  }

  if (appModal) {
    appModal.addEventListener('click', (e) => {
      if (e.target === appModal) {
        closeAppModal();
        sessionStorage.setItem('kpsta_app_prompt_dismissed', 'true');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && appModal.classList.contains('active')) {
        closeAppModal();
        sessionStorage.setItem('kpsta_app_prompt_dismissed', 'true');
      }
    });
  }

  if (btnPlaystoreDownload) {
    btnPlaystoreDownload.addEventListener('click', () => {
      sessionStorage.setItem('kpsta_app_prompt_dismissed', 'true');
      setTimeout(closeAppModal, 400);
    });
  }
});
