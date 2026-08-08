async function adminLogin(page) {
    await page.goto('/admin');
    // CodeIgniter Aauth login page
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Wait for the dashboard to load to confirm login
    await page.waitForURL('**/admin/home*');
}

module.exports = { adminLogin };
