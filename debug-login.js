const { chromium } = require('@playwright/test');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  console.log('Navigating to http://kpsta.test/admin...');
  await page.goto('http://kpsta.test/admin');
  
  await page.fill('input[name="username"]', 'admin');
  await page.fill('input[name="password"]', 'admin@1#456');
  await page.click('button[type="submit"]');

  await page.waitForTimeout(3000);
  
  const currentUrl = page.url();
  console.log('Current URL after login:', currentUrl);
  
  const title = await page.title();
  console.log('Page Title:', title);

  const content = await page.content();
  if (content.includes('login-error-msg') || content.includes('invalid') || content.includes('incorrect') || content.includes('Error')) {
      const errorText = await page.evaluate(() => {
          const el = document.querySelector('.login-error-msg');
          return el ? el.innerText : 'No explicit error element found, check page text';
      });
      console.log('Error found:', errorText);
  } else {
      console.log('SUCCESS! No errors found.');
  }

  await browser.close();
})();
