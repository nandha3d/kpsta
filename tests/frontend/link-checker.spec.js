const { test, expect } = require('@playwright/test');

test.describe('Frontend Link Checker', () => {
  test('Crawl main pages and check for dead links and PHP errors', async ({ page }) => {
    const visitedUrls = new Set();
    const urlsToVisit = ['/'];
    const baseUrl = 'http://kpsta.test'; // Ensure this matches your playwright config

    while (urlsToVisit.length > 0) {
      const currentUrlPath = urlsToVisit.pop();
      const fullUrl = new URL(currentUrlPath, baseUrl).toString();

      if (visitedUrls.has(fullUrl)) continue;
      visitedUrls.add(fullUrl);

      console.log(`Checking: ${fullUrl}`);
      const response = await page.goto(fullUrl, { waitUntil: 'domcontentloaded' });
      
      // Assert 200 OK
      expect(response.status(), `Page ${fullUrl} returned status ${response.status()}`).toBe(200);

      // Check for PHP Errors
      const bodyText = await page.innerText('body');
      expect(bodyText).not.toContain('A PHP Error was encountered');
      expect(bodyText).not.toContain('Database Error');

      // Extract more internal links
      const hrefs = await page.$$eval('a', links => links.map(a => a.getAttribute('href')));
      
      for (let href of hrefs) {
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:')) continue;
        
        try {
          const urlObj = new URL(href, baseUrl);
          // Only crawl internal links and ignore admin routes or downloads
          if (urlObj.origin === baseUrl && !urlObj.pathname.startsWith('/admin') && !urlObj.pathname.startsWith('/public') && !urlObj.pathname.startsWith('/uploads')) {
            const cleanPath = urlObj.pathname + urlObj.search;
            if (!visitedUrls.has(urlObj.toString()) && !urlsToVisit.includes(cleanPath)) {
              urlsToVisit.push(cleanPath);
            }
          }
        } catch (e) {
          // Ignore invalid URL structures
        }
      }
    }
  });
});
