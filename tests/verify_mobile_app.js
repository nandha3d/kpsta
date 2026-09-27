const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

async function testMobileApp() {
  console.log('Starting Playwright Verification for KPSTA Public App...');
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1400, height: 960 }
  });
  const page = await context.newPage();

  const filePath = 'file:///' + path.resolve(__dirname, '..', 'mobile-preview.html').replace(/\\/g, '/');
  console.log('Navigating to:', filePath);
  await page.goto(filePath, { waitUntil: 'load' });

  // 1. Check Showcase Header
  const title = await page.textContent('.showcase-title-area h1');
  console.log('Showcase Title:', title);
  if (!title.includes('KPSTA Public App')) throw new Error('Showcase title mismatch');

  const badge = await page.textContent('.showcase-badge');
  console.log('Showcase Badge:', badge.trim());

  // 2. Check Hero Section in Phone 1 (Member App)
  const heroTitle = await page.textContent('#colPhoneMember .hero-kpsta-title');
  console.log('Hero Title (Phone 1):', heroTitle);
  const heroMotto = await page.textContent('#colPhoneMember .hero-unite-pill');
  console.log('Hero Motto Pill (Phone 1):', heroMotto);
  if (!heroMotto.includes('UNITE FOR QUALITY EDUCATION')) throw new Error('Hero motto missing');

  // 3. Check Phone 2 (Admin App)
  const ci4Badge = await page.textContent('#colPhoneAdmin .admin-ci4-badge');
  console.log('CI4 Live Badge (Phone 2):', ci4Badge.trim());
  const sparkQueueTitle = await page.textContent('#colPhoneAdmin .spark-title-text');
  console.log('SPARK Queue Title (Phone 2):', sparkQueueTitle.trim());

  // Test 1-Tap SPARK Approval on Phone 2
  await page.evaluate(() => {
    const btn = document.querySelector('#sparkItem-1 .btn-spark-approve');
    if (btn) btn.click();
  });
  const sparkCount = await page.textContent('#sparkPendingCount');
  console.log('Updated Pending Approvals count:', sparkCount.trim());

  // Reset scroll on both phone viewports to show hero section and top dashboard
  await page.evaluate(() => {
    document.querySelectorAll('.app-viewport').forEach(vp => vp.scrollTop = 0);
  });

  // Screenshot 1: Dual Phone Showcase View (Matching Image 2)
  await page.screenshot({ path: 'test_dual_showcase.png' });
  console.log('Saved test_dual_showcase.png');
  await page.screenshot({ path: 'test_showcase_main.png' });



  // 3. Test Pay Calculator Modal
  console.log('Testing Pay Calculator...');
  await page.click('[data-action="open-calculator"]');
  await page.waitForSelector('#modalCalculator.active');
  
  // Verify default Net Salary
  const net1 = await page.textContent('#slipNet');
  console.log('Initial Net Salary:', net1);

  // Change Basic Pay to 55200 (HSST)
  await page.fill('#calcBasicPay', '55200');
  const net2 = await page.textContent('#slipNet');
  console.log('Updated Net Salary (HSST):', net2);
  if (net1 === net2) throw new Error('Calculator did not update net salary dynamically');

  await page.screenshot({ path: 'test_calculator_modal.png' });
  console.log('Saved test_calculator_modal.png');
  await page.click('#modalCalculator .btn-close-modal');

  // 4. Test Digital Member ID Modal
  console.log('Testing Digital Member ID Card...');
  await page.click('[data-action="open-digital-id"]');
  await page.waitForSelector('#modalDigitalId.active');
  const memberName = await page.textContent('#modalDigitalId .id-field-val');
  console.log('Digital ID Member Name:', memberName);
  await page.screenshot({ path: 'test_digital_id_modal.png' });
  console.log('Saved test_digital_id_modal.png');
  await page.click('#modalDigitalId .btn-close-modal');

  // 5. Test Admin Portal & Live Flash News Update
  console.log('Testing Admin Management Suite...');
  await page.click('[data-action="open-admin"]');
  await page.waitForSelector('#modalAdminSuite.active');
  
  // Type new flash news
  const testNews = 'NEW TEST FLASH NEWS: ' + Date.now();
  await page.fill('#adminNewsInput', testNews);
  await page.click('#btnAdminPublishNews');
  
  // Verify ticker content updated
  const tickerText = await page.textContent('#flashTickerContent');
  console.log('Updated Ticker includes new test news:', tickerText.includes(testNews));
  if (!tickerText.includes(testNews)) throw new Error('Flash news ticker was not updated');

  // Test PEN Verification in Admin
  await page.fill('#adminPenSearch', '849201');
  await page.click('#btnAdminVerifyPEN');
  const penResult = await page.textContent('#adminPenResult');
  console.log('PEN Verification Result:', penResult.replace(/\s+/g, ' ').trim());
  if (!penResult.includes('Verified Active KPSTA Member')) throw new Error('PEN verification failed');

  await page.screenshot({ path: 'test_admin_modal.png' });
  console.log('Saved test_admin_modal.png');
  await page.click('#modalAdminSuite .btn-close-modal');

  // 6. Test Orders & Circulars Search & Filter
  console.log('Testing Circulars Repository...');
  await page.click('[data-action="open-circulars"]');
  await page.waitForSelector('#modalCirculars.active');
  await page.fill('#circularSearchInput', '18%');
  const filteredCount = await page.locator('#circularsListContainer .circular-row-item').count();
  console.log('Circulars matching "18%":', filteredCount);
  if (filteredCount === 0) throw new Error('Circular search failed');

  await page.screenshot({ path: 'test_circulars_modal.png' });
  console.log('Saved test_circulars_modal.png');
  await page.click('#modalCirculars .btn-close-modal');

  // 7. Test Accessibility Font Sizing
  console.log('Testing Accessibility Font Sizing for 35+ Teachers...');
  await page.click('[data-action="open-drawer"]');
  await page.waitForSelector('#drawerOverlay.active');
  await page.click('.btn-font-scale[data-scale="xlarge"]');
  const isScaled = await page.evaluate(() => document.documentElement.classList.contains('font-scale-xl'));
  console.log('Font scale class applied:', isScaled);
  if (!isScaled) throw new Error('Font scale class not applied');

  await page.screenshot({ path: 'test_drawer_menu.png' });
  console.log('Saved test_drawer_menu.png');
  await page.click('#drawerOverlay');

  // 8. Test Standalone app.html
  console.log('Testing standalone app.html on mobile viewport...');
  const appPage = await context.newPage({
    viewport: { width: 390, height: 844 }, // iPhone 14 / modern Android
    userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15'
  });
  const appFilePath = 'file:///' + path.resolve(__dirname, '..', 'app.html').replace(/\\/g, '/');
  await appPage.goto(appFilePath, { waitUntil: 'load' });
  await appPage.screenshot({ path: 'test_standalone_mobile.png' });
  console.log('Saved test_standalone_mobile.png');

  await browser.close();
  console.log('✅ ALL VERIFICATION TESTS PASSED SUCCESSFULLY!');
}

testMobileApp().catch(err => {
  console.error('❌ Test failed:', err);
  process.exit(1);
});
