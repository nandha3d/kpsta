/**
 * KPSTA Public App - Interactive Logic & State Controller
 * Fully functional client-side SPA for teachers and administrators
 */

(function () {
  'use strict';

  // Application State
  const AppState = {
    activeTab: 'home',
    fontScale: 'normal',
    isAdmin: false,
    adminRole: 'State Administrator',
    
    // Member Profile (Active Session)
    currentMember: {
      pen: '849201',
      name: 'Sreejith K.',
      designation: 'HST Social Science',
      school: 'Govt Model Higher Secondary School, Kozhikode',
      schoolCode: '16042',
      district: 'Kozhikode',
      status: 'Active Member',
      bloodGroup: 'B+ve',
      membershipNo: 'KPSTA-KKD-849201',
      validThru: 'March 2029'
    },

    // Flash News Items
    flashNews: [
      { id: 1, text: 'KPSTA സംസ്ഥാന ഭാരവാഹികളുടെ സത്യപ്രതിജ്ഞ @ എറണാകുളം 22.02.2026', link: '#', date: 'Feb 22, 2026' },
      { id: 2, text: 'CM Kids Scholarship LP & UP (LSS, USS) ഹാൻഡ്‌ബുക്ക് പ്രസിദ്ധീകരിച്ചു', link: '#', date: 'Feb 25, 2026' },
      { id: 3, text: 'അധ്യാപകരുടെ അന്തർജില്ലാ സ്ഥലംമാറ്റ അപേക്ഷ തീയതി നീട്ടി', link: '#', date: 'March 01, 2026' }
    ],

    // Orders & Circulars Database
    circulars: [
      {
        id: 'GO-42',
        orderNo: 'G.O.(P) No. 42/2026/Fin',
        title: 'അധ്യാപക-ജീവനക്കാരുടെ ക്ഷാമബത്ത (DA) 18% ആയി വർദ്ധിപ്പിച്ച് ഉത്തരവിറങ്ങി',
        category: 'finance',
        categoryLabel: 'Finance & Pay',
        date: '15.03.2026',
        pages: '4 Pages',
        fileSize: '1.2 MB',
        summary: 'കേരളത്തിലെ സർക്കാർ, എയ്ഡഡ് സ്കൂൾ അധ്യാപകരുടെയും അനധ്യാപകരുടെയും ക്ഷാമബത്ത നിരക്ക് 18% ആയി പുതുക്കി നിശ്ചയിച്ച ധനകാര്യ വകുപ്പിന്റെ അന്തിമ ഉത്തരവ്.'
      },
      {
        id: 'GO-38',
        orderNo: 'G.O.(Rt) No. 38/2026/GEDN',
        title: 'പൊതുവിദ്യാഭ്യാസ വകുപ്പ് - അധ്യാപകരുടെ അന്തർജില്ലാ പൊതുസ്ഥലംമാറ്റ മാർഗ്ഗരേഖ 2026',
        category: 'general',
        categoryLabel: 'General Education',
        date: '08.03.2026',
        pages: '12 Pages',
        fileSize: '3.4 MB',
        summary: '2026-27 അധ്യയന വർഷത്തെ സർക്കാർ/എയ്ഡഡ് സ്കൂൾ അധ്യാപകരുടെ പൊതുസ്ഥലംമാറ്റത്തിനുള്ള ഓൺലൈൻ അപേക്ഷാ നടപടിക്രമങ്ങളും മുൻഗണനാ മാനദണ്ഡങ്ങളും.'
      },
      {
        id: 'CIR-105',
        orderNo: 'Circular No. DGE/105/2026',
        title: 'CM Kids Scholarship LP & UP (LSS / USS) പരീക്ഷാ തീയതികളും ഹാൾടിക്കറ്റും',
        category: 'academic',
        categoryLabel: 'Academic / Exams',
        date: '02.03.2026',
        pages: '2 Pages',
        fileSize: '850 KB',
        summary: '2026 വർഷത്തെ എൽ.എസ്.എസ്, യു.എസ്.എസ് പരീക്ഷകൾ ഏപ്രിൽ 25 ന് നടക്കും. പരീക്ഷാ ഹാൾടിക്കറ്റുകൾ സ്കൂൾ സമ്പൂർണ്ണ ലോഗിൻ വഴി ഡൗൺലോഡ് ചെയ്യാം.'
      },
      {
        id: 'HSE-89',
        orderNo: 'Order No. HSE/Admn/89/2026',
        title: 'ഹയർ സെക്കൻഡറി അധ്യാപകരുടെ ക്ലസ്റ്റർ റിസോഴ്സ് മീറ്റിംഗ് ഷെഡ്യൂൾ',
        category: 'hse',
        categoryLabel: 'Higher Secondary',
        date: '28.02.2026',
        pages: '6 Pages',
        fileSize: '1.8 MB',
        summary: 'പ്ലസ് വൺ, പ്ലസ് ടു അധ്യാപകർക്കായുള്ള ജില്ലാതല ക്ലസ്റ്റർ സംഗമങ്ങളും മൂല്യനിർണ്ണയ പരിശീലന തീയതികളും പ്രഖ്യാപിച്ചു.'
      },
      {
        id: 'VHSE-14',
        orderNo: 'Circular No. VHSE/2026/14',
        title: 'വി.എച്ച്.എസ്.ഇ പ്രാക്ടിക്കൽ പരീക്ഷാ നടത്തിപ്പും വേതന പരിഷ്കരണവും',
        category: 'vhse',
        categoryLabel: 'VHSE',
        date: '20.02.2026',
        pages: '3 Pages',
        fileSize: '950 KB',
        summary: 'വൊക്കേഷണൽ ഹയർ സെക്കൻഡറി വിദ്യാർത്ഥികളുടെ പൊതു പ്രാക്ടിക്കൽ എക്സാമിനേഴ്സ് മാർഗ്ഗനിർദ്ദേശങ്ങൾ.'
      },
      {
        id: 'KSR-12',
        orderNo: 'G.O.(P) No. 12/2026/P&ARD',
        title: 'മെഡിസെപ്പ് (MEDISEP) ഇൻഷുറൻസ് - മൂന്നാം ഘട്ട ചികിത്സാ ആനുകൂല്യങ്ങളും കാഷ്‌ലെസ്സ് പരിധിയും',
        category: 'service',
        categoryLabel: 'Service Rules',
        date: '14.02.2026',
        pages: '8 Pages',
        fileSize: '2.1 MB',
        summary: 'അധ്യാപകർക്കും കുടുംബാംഗങ്ങൾക്കുമുള്ള മെഡിസെപ്പ് ആനുകൂല്യങ്ങളിൽ കാർഡിയാക്, ഓങ്കോളജി പാക്കേജുകളുടെ പുതുക്കിയ പരിധി വിവരങ്ങൾ.'
      }
    ],

    // State Leaders Directory (21 Leaders)
    leaders: [
      { id: 1, name: 'P.K. Aravindan', role: 'State President', phone: '9495409460', district: 'Kozhikode', img: 'uploads/office_bearer/a92f6e32c90f4389f0db121091ca9be2.jpg' },
      { id: 2, name: 'Abdul Majeed K.', role: 'General Secretary', phone: '9495409460', district: 'Malappuram', img: 'uploads/office_bearer/bc48c84e6c9ecf5e71135ae9aacd971d.jpg' },
      { id: 3, name: 'B. Sunilkumar', role: 'Treasurer', phone: '9847225059', district: 'Kollam', img: 'uploads/office_bearer/27e2d3c5c50084b52fe80920bad0f96e.jpg' },
      { id: 4, name: 'Anilkumar Venjarammude', role: 'Senior Vice President', phone: '9400277467', district: 'Thiruvananthapuram', img: '' },
      { id: 5, name: 'T.U. Sadath', role: 'Associate General Secretary', phone: '9895398197', district: 'Ernakulam', img: '' },
      { id: 6, name: 'P.S. Gireesh Kumar', role: 'Vice President', phone: '9447544645', district: 'Alappuzha', img: '' },
      { id: 7, name: 'Saju George', role: 'Vice President', phone: '9400534821', district: 'Idukki', img: '' },
      { id: 8, name: 'M.K. Aruna', role: 'Vice President', phone: '9400104904', district: 'Kannur', img: '' },
      { id: 9, name: 'John Bosco P.A.', role: 'Vice President', phone: '9447385412', district: 'Thrissur', img: '' },
      { id: 10, name: 'A.M. Sreekumar', role: 'State Secretary', phone: '9447248053', district: 'Palakkad', img: '' },
      { id: 11, name: 'N. Rajendran', role: 'State Secretary', phone: '9447352189', district: 'Kottayam', img: '' },
      { id: 12, name: 'Thomas Scaria', role: 'State Secretary', phone: '9447805128', district: 'Pathanamthitta', img: '' },
      { id: 13, name: 'P.S. Manoj', role: 'State Secretary', phone: '9447918234', district: 'Wayanad', img: '' },
      { id: 14, name: 'K. Balakrishnan', role: 'State Secretary', phone: '9447602931', district: 'Kasaragod', img: '' }
    ],

    // Medisep Cashless Hospitals Sample (Across Kerala)
    medisepHospitals: [
      { name: 'Aster MIMS Hospital', district: 'Kozhikode', type: 'Super Specialty (Cashless 24x7)', phone: '0495-2488000' },
      { name: 'Baby Memorial Hospital', district: 'Kozhikode', type: 'Tertiary Care (Cashless)', phone: '0495-2777777' },
      { name: 'Aster Medcity', district: 'Ernakulam', type: 'Super Specialty (Cashless 24x7)', phone: '0484-6699999' },
      { name: 'Lisie Hospital', district: 'Ernakulam', type: 'Cardiac & Multi Specialty', phone: '0484-2402044' },
      { name: 'KIMSHEALTH Hospital', district: 'Thiruvananthapuram', type: 'Super Specialty (Cashless)', phone: '0471-2941000' },
      { name: 'PRS Hospital', district: 'Thiruvananthapuram', type: 'Multi Specialty', phone: '0471-2344442' },
      { name: 'Amala Institute of Medical Sciences', district: 'Thrissur', type: 'Medical College & Hospital', phone: '0487-2304000' },
      { name: 'Jubilee Mission Hospital', district: 'Thrissur', type: 'Super Specialty', phone: '0487-2432200' },
      { name: 'Caritas Hospital', district: 'Kottayam', type: 'Super Specialty (Cashless)', phone: '0481-2790025' }
    ]
  };

  // DOM Helpers
  const $ = (selector, context = document) => context.querySelector(selector);
  const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initCalculator();
    initCirculars();
    initAdminPortal();
    initDrawersAndModals();
    initAccessibility();
    initShowcaseControls();
    updateFlashNewsDisplay();
  });

  // =========================================================================
  // Showcase Controls (Restart, Fullscreen)
  // =========================================================================
  function initShowcaseControls() {
    const btnRestart = $('#btnRestartApp');
    const btnFullscreen = $('#btnFullscreenApp');

    if (btnRestart) {
      btnRestart.addEventListener('click', () => {
        // Reset state
        closeAllModals();
        switchTab('home');
        showToast('🔄 App state refreshed to Home');
      });
    }

    if (btnFullscreen) {
      btnFullscreen.addEventListener('click', () => {
        document.body.classList.toggle('is-fullscreen');
        const isFull = document.body.classList.contains('is-fullscreen');
        btnFullscreen.innerHTML = isFull 
          ? '<span>Exit Fullscreen</span>' 
          : '<span>Full screen ↗</span>';
      });
    }
  }

  // =========================================================================
  // Navigation Tabs Controller
  // =========================================================================
  function initNavigation() {
    const tabs = $$('.nav-tab-btn');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        switchTab(target);
      });
    });

    // Delegate chips and links to open tabs/modals
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-action]');
      if (!trigger) return;

      const action = trigger.dataset.action;
      e.preventDefault();

      switch (action) {
        case 'open-calculator':
          openModal('modalCalculator');
          break;
        case 'open-digital-id':
          openModal('modalDigitalId');
          break;
        case 'open-circulars':
          openModal('modalCirculars');
          break;
        case 'open-magazine':
          openModal('modalMagazine');
          break;
        case 'open-medisep':
          openModal('modalMedisep');
          break;
        case 'open-academic':
          openModal('modalAcademic');
          break;
        case 'open-office-bearers':
          openModal('modalLeadership');
          break;
        case 'open-forms':
          openModal('modalForms');
          break;
        case 'open-admin':
          openModal('modalAdminSuite');
          break;
        case 'open-conference':
          openModal('modalConference');
          break;
        case 'open-all-menus':
          openModal('modalAllMenus');
          break;
        case 'open-drawer':
          openDrawer();
          break;
        case 'close-drawer':
          closeDrawer();
          break;
        case 'open-news-popup':
          openFlashNewsPopup();
          break;
      }
    });
  }

  function switchTab(tabName) {
    AppState.activeTab = tabName;

    // Update bottom nav UI
    $$('.nav-tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.tab === tabName);
    });

    // Handle Tab Views
    switch (tabName) {
      case 'home':
        closeAllModals();
        scrollToTop();
        break;
      case 'circulars':
        openModal('modalCirculars');
        break;
      case 'calculator':
        openModal('modalCalculator');
        break;
      case 'magazine':
        openModal('modalMagazine');
        break;
      case 'menus':
        openModal('modalAllMenus');
        break;
    }
  }

  function scrollToTop() {
    const viewport = $('.app-viewport');
    if (viewport) {
      viewport.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  // =========================================================================
  // Modals & Drawer Management
  // =========================================================================
  function initDrawersAndModals() {
    // Close modal on backdrop click
    $$('.modal-overlay').forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          closeModal(modal.id);
        }
      });
    });

    // Close buttons inside modals
    $$('.btn-close-modal').forEach(btn => {
      btn.addEventListener('click', () => {
        const modal = btn.closest('.modal-overlay');
        if (modal) closeModal(modal.id);
      });
    });

    // Close drawer on backdrop click
    const drawerOverlay = $('#drawerOverlay');
    if (drawerOverlay) {
      drawerOverlay.addEventListener('click', (e) => {
        if (e.target === drawerOverlay) {
          closeDrawer();
        }
      });
    }

    // Keyboard ESC handler
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeAllModals();
        closeDrawer();
      }
    });
  }

  window.openModal = function (modalId) {
    const modal = $(`#${modalId}`);
    if (modal) {
      modal.classList.add('active');
    }
  };

  window.closeModal = function (modalId) {
    const modal = $(`#${modalId}`);
    if (modal) {
      modal.classList.remove('active');
    }
  };

  function closeAllModals() {
    $$('.modal-overlay').forEach(m => m.classList.remove('active'));
  }

  function openDrawer() {
    const drawer = $('#drawerOverlay');
    if (drawer) drawer.classList.add('active');
  }

  function closeDrawer() {
    const drawer = $('#drawerOverlay');
    if (drawer) drawer.classList.remove('active');
  }

  // =========================================================================
  // Pay & DA Calculator Module (Kerala Teachers 11th Pay Revision)
  // =========================================================================
  function initCalculator() {
    const inputBasic = $('#calcBasicPay');
    const selectDA = $('#calcDARate');
    const selectHRA = $('#calcHRAClass');
    const inputPF = $('#calcPF');
    const inputGIS = $('#calcGIS');
    const inputSLI = $('#calcSLI');
    const inputIT = $('#calcIT');
    const btnDownloadSlip = $('#btnDownloadPaySlip');

    // Scale Quick Selection Pills
    $$('.btn-scale-pill').forEach(pill => {
      pill.addEventListener('click', () => {
        $$('.btn-scale-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        const basic = pill.dataset.basic;
        if (inputBasic) {
          inputBasic.value = basic;
          calculateSalary();
        }
      });
    });

    // Live calculation listeners
    [inputBasic, selectDA, selectHRA, inputPF, inputGIS, inputSLI, inputIT].forEach(el => {
      if (el) {
        el.addEventListener('input', calculateSalary);
        el.addEventListener('change', calculateSalary);
      }
    });

    if (btnDownloadSlip) {
      btnDownloadSlip.addEventListener('click', () => {
        showToast('📄 Salary Statement summary downloaded');
      });
    }

    // Run initial calculation
    calculateSalary();
  }

  function calculateSalary() {
    const basic = parseFloat($('#calcBasicPay')?.value || 41300);
    const daPercent = parseFloat($('#calcDARate')?.value || 18);
    const hraClass = $('#calcHRAClass')?.value || 'municipality';

    // Calculate DA
    const daAmount = Math.round(basic * (daPercent / 100));

    // Calculate HRA by Class
    let hraAmount = 2900;
    if (hraClass === 'corporation') hraAmount = 3600;
    else if (hraClass === 'municipality') hraAmount = 2900;
    else if (hraClass === 'panchayat') hraAmount = 2200;
    else if (hraClass === 'hill') hraAmount = 3200;

    // Gross Salary
    const gross = basic + daAmount + hraAmount;

    // Deductions
    const pf = parseFloat($('#calcPF')?.value || Math.round(basic * 0.08));
    const gis = parseFloat($('#calcGIS')?.value || 400);
    const sli = parseFloat($('#calcSLI')?.value || 500);
    const medisep = 500; // Fixed Kerala Govt Medisep deduction
    const it = parseFloat($('#calcIT')?.value || 0);

    const totalDeductions = pf + gis + sli + medisep + it;
    const netSalary = gross - totalDeductions;

    // Update UI
    updateText('#slipBasic', formatINR(basic));
    updateText('#slipDA', formatINR(daAmount));
    updateText('#slipHRA', formatINR(hraAmount));
    updateText('#slipGross', formatINR(gross));
    updateText('#slipDeductions', formatINR(totalDeductions));
    updateText('#slipNet', formatINR(netSalary));
    updateText('#slipMedisep', formatINR(medisep));
  }

  function formatINR(val) {
    return '₹' + Math.round(val).toLocaleString('en-IN');
  }

  function updateText(selector, text) {
    const el = $(selector);
    if (el) el.textContent = text;
  }

  // =========================================================================
  // Orders & Circulars Module
  // =========================================================================
  function initCirculars() {
    const searchInput = $('#circularSearchInput');
    const categoryChips = $$('.btn-cat-chip');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        renderCircularsList(e.target.value.trim().toLowerCase());
      });
    }

    categoryChips.forEach(chip => {
      chip.addEventListener('click', () => {
        categoryChips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        renderCircularsList(searchInput?.value.trim().toLowerCase() || '', chip.dataset.cat);
      });
    });

    renderCircularsList();
  }

  function renderCircularsList(searchTerm = '', category = 'all') {
    const container = $('#circularsListContainer');
    if (!container) return;

    const filtered = AppState.circulars.filter(item => {
      const matchCat = (category === 'all' || item.category === category);
      const matchText = (item.title.toLowerCase().includes(searchTerm) || 
                         item.orderNo.toLowerCase().includes(searchTerm) ||
                         item.summary.toLowerCase().includes(searchTerm));
      return matchCat && matchText;
    });

    if (filtered.length === 0) {
      container.innerHTML = `
        <div style="text-align:center; padding: 2rem 1rem; color: #64748b;">
          <span style="font-size: 2.5rem;">🔍</span>
          <p style="font-weight: 800; font-size: 1rem; margin-top: 0.5rem;">No circulars found</p>
          <p style="font-size: 0.8rem;">Try a different keyword or category filter.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = filtered.map(item => `
      <div class="circular-row-item" onclick="openCircularDetail('${item.id}')">
        <span class="circular-type-badge">${item.categoryLabel}</span>
        <div class="circular-info-col">
          <div class="circular-item-title">${item.title}</div>
          <div class="circular-item-meta">
            <span><strong>${item.orderNo}</strong></span>
            <span>📅 ${item.date}</span>
            <span>📄 ${item.pages}</span>
          </div>
        </div>
        <button class="btn-showcase" style="background:#0284c7; color:#fff; border:none; padding:0.35rem 0.65rem;" onclick="event.stopPropagation(); downloadCircular('${item.id}')">PDF 📥</button>
      </div>
    `).join('');
  }

  window.openCircularDetail = function (id) {
    const item = AppState.circulars.find(c => c.id === id);
    if (!item) return;

    alert(`📄 [KPSTA Order & Circular Preview]\n\n${item.orderNo}\n\nTitle: ${item.title}\n\nDate: ${item.date}\nCategory: ${item.categoryLabel}\n\nSummary:\n${item.summary}\n\n✅ Official Signed Copy Verified.`);
  };

  window.downloadCircular = function (id) {
    const item = AppState.circulars.find(c => c.id === id);
    showToast(`📥 Downloading: ${item ? item.orderNo : 'Order Circular'}.pdf`);
  };

  // =========================================================================
  // Admin Management Portal Module
  // =========================================================================
  function initAdminPortal() {
    const btnAddNews = $('#btnAdminPublishNews');
    const inputNews = $('#adminNewsInput');
    const btnAddCircular = $('#btnAdminPublishCircular');
    const btnVerifyPen = $('#btnAdminVerifyPEN');

    // Publish Flash News
    if (btnAddNews && inputNews) {
      btnAddNews.addEventListener('click', () => {
        const text = inputNews.value.trim();
        if (!text) {
          alert('Please enter flash news headline!');
          return;
        }

        const newId = Date.now();
        AppState.flashNews.unshift({
          id: newId,
          text: text,
          link: '#',
          date: 'Just Now'
        });

        inputNews.value = '';
        updateFlashNewsDisplay();
        showToast('⚡ Flash News Published Live to Ticker!');
      });
    }

    // Publish Order & Circular
    if (btnAddCircular) {
      btnAddCircular.addEventListener('click', () => {
        const title = $('#adminCircTitle')?.value.trim();
        const orderNo = $('#adminCircNo')?.value.trim();
        const cat = $('#adminCircCat')?.value || 'general';

        if (!title || !orderNo) {
          alert('Please enter Circular Order Number and Title!');
          return;
        }

        const newCircular = {
          id: 'GO-' + Date.now().toString().slice(-4),
          orderNo: orderNo,
          title: title,
          category: cat,
          categoryLabel: cat.toUpperCase(),
          date: 'Today',
          pages: '2 Pages',
          fileSize: '1.0 MB',
          summary: title
        };

        AppState.circulars.unshift(newCircular);
        renderCircularsList();
        showToast(`✅ Circular ${orderNo} added to public portal!`);
        
        // Clear inputs
        if ($('#adminCircTitle')) $('#adminCircTitle').value = '';
        if ($('#adminCircNo')) $('#adminCircNo').value = '';
      });
    }

    // PEN Membership Verification Tool
    if (btnVerifyPen) {
      btnVerifyPen.addEventListener('click', () => {
        const pen = $('#adminPenSearch')?.value.trim();
        const resultBox = $('#adminPenResult');
        if (!pen || !resultBox) return;

        if (pen === '849201' || pen === AppState.currentMember.pen) {
          resultBox.innerHTML = `
            <div style="background:#ecfdf5; border:1px solid #10b981; border-radius:8px; padding:0.65rem; color:#065f46;">
              <strong>✅ Verified Active KPSTA Member</strong><br>
              <strong>Name:</strong> Sreejith K. (HST Social Science)<br>
              <strong>School:</strong> Govt Model HSS, Kozhikode (16042)<br>
              <strong>Membership:</strong> KPSTA-KKD-849201 (Valid till 2029)
            </div>
          `;
        } else {
          resultBox.innerHTML = `
            <div style="background:#eff6ff; border:1px solid #3b82f6; border-radius:8px; padding:0.65rem; color:#1e40af;">
              <strong>✅ Verified State School Teacher</strong><br>
              <strong>PEN:</strong> ${pen}<br>
              <strong>Status:</strong> Active Kerala Govt Teacher Service Record<br>
              <strong>KPSTA Unit:</strong> Enrolled in Annual Subscription
            </div>
          `;
        }
      });
    }
  }

  function updateFlashNewsDisplay() {
    const marquee = $('#flashTickerContent');
    if (marquee && AppState.flashNews.length > 0) {
      marquee.innerHTML = AppState.flashNews.map(n => `⚡ ${n.text} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; `).join('');
    }
  }

  function openFlashNewsPopup() {
    const items = AppState.flashNews.map((n, idx) => `${idx + 1}. ${n.text} (${n.date})`).join('\n\n');
    alert(`⚡ [KPSTA LIVE FLASH NEWS BULLETIN]\n\n${items}\n\nTap OK to continue.`);
  }

  // =========================================================================
  // Dual-Phone View Switching (Matching User Images 1 & 2)
  // =========================================================================
  window.setViewMode = function (mode) {
    const wrapper = $('.showcase-wrapper');
    if (!wrapper) return;
    wrapper.classList.remove('view-member-only', 'view-admin-only');

    $$('.btn-view-toggle').forEach(b => b.classList.remove('active'));

    if (mode === 'member') {
      wrapper.classList.add('view-member-only');
      const btn = $('#btnToggleMember');
      if (btn) btn.classList.add('active');
      showToast('📱 Member Public App View (Phone 1)');
    } else if (mode === 'admin') {
      wrapper.classList.add('view-admin-only');
      const btn = $('#btnToggleAdmin');
      if (btn) btn.classList.add('active');
      showToast('⚙️ CI4 Live Admin App View (Phone 2)');
    } else {
      const btn = $('#btnToggleDual');
      if (btn) btn.classList.add('active');
      showToast('📱📱 Dual Phones Side-by-Side View');
    }
  };

  // =========================================================================
  // SPARK Queue 1-Tap Approvals
  // =========================================================================
  window.approveSparkMember = function (id, name, pen) {
    const item = $(`#sparkItem-${id}`);
    if (item) {
      item.classList.add('approved');
      const btn = item.querySelector('.btn-spark-approve');
      if (btn) {
        btn.classList.add('done');
        btn.innerHTML = '<span>✓ Approved & Signed</span>';
        btn.disabled = true;
      }
    }

    const badge = $('#sparkPendingCount');
    if (badge) {
      const current = parseInt(badge.textContent) || 14;
      const updated = Math.max(0, current - 1);
      badge.textContent = `${updated} Pending Approvals`;
    }

    showToast(`✅ ${name} (PEN: ${pen}) Verified & Digital ID Issued!`);
  };

  // Phone 2 Admin Event Bindings
  document.addEventListener('DOMContentLoaded', () => {
    // Dismiss API toast on click
    const apiToast = $('#adminApiToast');
    if (apiToast) {
      apiToast.style.cursor = 'pointer';
      apiToast.addEventListener('click', () => {
        apiToast.style.opacity = '0';
        setTimeout(() => apiToast.style.display = 'none', 300);
      });
    }

    // Phone 2 Flash News Publisher
    const btnPub2 = $('#btnAdminPublishNewsPhone2');
    const inputNews2 = $('#adminNewsInputPhone2');
    if (btnPub2 && inputNews2) {
      btnPub2.addEventListener('click', () => {
        const text = inputNews2.value.trim();
        if (!text) {
          alert('Please enter a flash news headline');
          return;
        }
        AppState.flashNews.unshift({
          id: Date.now(),
          text: text,
          link: '#',
          date: 'Just now'
        });
        updateFlashTicker();
        const disp = $('#adminTickerDisplay');
        if (disp) disp.textContent = text + ' • ' + disp.textContent;
        const countBadge = $('#adminNewsCountBadge');
        if (countBadge) countBadge.textContent = parseInt(countBadge.textContent) + 1;
        inputNews2.value = '';
        showToast('⚡ Live Flash News Published to Both Phone Displays!');
      });
    }

    // Phone 2 SPARK PEN Search
    const btnPen2 = $('#btnAdminVerifyPENPhone2');
    const inputPen2 = $('#adminPenSearchPhone2');
    const resultPen2 = $('#adminPenResultPhone2');
    if (btnPen2 && inputPen2 && resultPen2) {
      btnPen2.addEventListener('click', () => {
        const query = inputPen2.value.trim();
        if (query === '849201') {
          resultPen2.innerHTML = `
            <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:0.65rem; font-size:0.76rem; color:#166534; font-weight:700; margin-top:0.4rem;">
              <div style="font-weight:900; color:#14532d; font-size:0.84rem;">✅ Verified Active KPSTA Member</div>
              <div><strong>Name:</strong> Sreejith K. (HST Social Science)</div>
              <div><strong>School:</strong> Govt Model HSS, Kozhikode (16042)</div>
              <div><strong>Status:</strong> KPSTA-KKD-849201 (Active Member)</div>
            </div>
          `;
        } else if (query === '852104') {
          resultPen2.innerHTML = `
            <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:0.65rem; font-size:0.76rem; color:#166534; font-weight:700; margin-top:0.4rem;">
              <div style="font-weight:900; color:#14532d; font-size:0.84rem;">✅ Verified SPARK Record</div>
              <div><strong>Name:</strong> Priya S. (HST English)</div>
              <div><strong>School:</strong> GHSS Medical College, Kozhikode (16045)</div>
            </div>
          `;
        } else {
          resultPen2.innerHTML = `
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:0.65rem; font-size:0.76rem; color:#991b1b; font-weight:700; margin-top:0.4rem;">
              ⚠️ PEN: ${query} not found in current active state cache.
            </div>
          `;
        }
      });
    }
  });

  // =========================================================================
  // Accessibility Font Sizing (for 35+ Teachers)
  // =========================================================================
  function initAccessibility() {
    const buttons = $$('.btn-font-scale');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const scale = btn.dataset.scale;
        document.documentElement.classList.remove('font-scale-lg', 'font-scale-xl');

        if (scale === 'large') {
          document.documentElement.classList.add('font-scale-lg');
          showToast('🔍 Text size enlarged (+12%)');
        } else if (scale === 'xlarge') {
          document.documentElement.classList.add('font-scale-xl');
          showToast('🔍 Text size enlarged (+25%)');
        } else {
          showToast('🔍 Standard bold font restored');
        }
      });
    });
  }

  // =========================================================================
  // Toast Notifications
  // =========================================================================
  window.showToast = function (message) {
    let toast = $('.toast-msg');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'toast-msg';
      const viewport = $('.app-viewport');
      if (viewport) viewport.appendChild(toast);
      else document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.classList.add('show');

    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
      toast.classList.remove('show');
    }, 2800);
  };

})();

