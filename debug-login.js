const { chromium } = require('@playwright/test');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  await page.goto('http://kpsta.test/admin');
  
  await page.fill('input[name="username"]', 'admin@admin.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');

  await page.waitForTimeout(3000);
  
  const currentUrl = page.url();
  console.log('Current URL after login:', currentUrl);
  
  const content = await page.content();
  if (content.includes('login-error-msg') || content.includes('invalid') || content.includes('incorrect')) {
      console.log('Found error message on page.');
      // Print the error text
      const errorText = await page.evaluate(() => {
          const el = document.querySelector('.login-error-msg');
          return el ? el.innerText : 'No explicit error element found, but error keyword matched.';
      });
      console.log('Error text:', errorText);
  } else {
      console.log('No obvious error message found.');
  }

  await browser.close();
})();
