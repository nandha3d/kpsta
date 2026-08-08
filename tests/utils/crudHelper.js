async function openAddModal(page) {
    await page.click('a[data-target="#modal"], button:has-text("Add"), a.btn:has-text("New")');
    await page.waitForSelector('.modal.in, .modal.show', { state: 'visible' });
}

async function submitForm(page, modalSelector = '.modal.in') {
    // Check if there is a Yes button for publish/is_publish and check the radio directly
    await page.evaluate((selector) => {
        const radios = document.querySelectorAll(`${selector} input[type="radio"][value="1"]`);
        for (const radio of radios) {
            if (radio.name === 'is_publish' || radio.name === 'publish' || radio.name.includes('publish')) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
                if (radio.parentElement && radio.parentElement.classList.contains('btn')) {
                    radio.parentElement.classList.add('active');
                }
            }
        }
    }, modalSelector);

    // Wait for any AJAX saving
    const [response] = await Promise.all([
        page.waitForResponse(response => response.url().includes('/add') || response.url().includes('/edit') || response.url().includes('/save') || response.url().includes('/update') || response.url().includes('flash_news')),
        page.click(`${modalSelector} button[type="submit"]`)
    ]);
    
    // Wait for modal to hide or show error
    await page.waitForTimeout(500); // Give it a moment to animate
    // The modal should close or a success alert should appear.
    // In this app, it usually reloads the table via ajax or shows a PNotify success message
    await page.waitForTimeout(1000); // Wait for animations/DOM updates
}

async function verifyInTable(page, text) {
    // Search for the text to handle pagination, if a search box is available
    const searchBox = page.locator('input[name="search"]');
    if (await searchBox.count() > 0 && await searchBox.isVisible()) {
        await searchBox.fill(text);
        await page.waitForTimeout(500); // Wait for debounce or reload
        // Try pressing enter just in case it's not auto-submit
        await searchBox.press('Enter');
        await page.waitForTimeout(1500); // Wait for ajax load
    } else {
        // Fallback for datatables search
        const dtSearch = page.locator('input[type="search"]');
        if (await dtSearch.count() > 0 && await dtSearch.isVisible()) {
            await dtSearch.fill(text);
            await page.waitForTimeout(1500);
        }
    }
    
    // Assert that the text is visible in the table body
    await page.waitForSelector(`tbody tr:has-text("${text}")`);
}

async function editItem(page, rowText) {
    // Find the row with the specific text, then click the edit button inside it
    const row = page.locator(`tbody tr:has-text("${rowText}")`).first();
    await row.locator('.btn-edit').click();
    await page.waitForSelector('.modal.in, .modal.show', { state: 'visible' });
}

async function deleteItem(page, rowText) {
    const row = page.locator(`tbody tr:has-text("${rowText}")`).first();
    await row.locator('.btn-delete').click();
    
    // Wait for delete confirmation modal and click confirm
    await page.waitForSelector('#delete.in, #delete.show', { state: 'visible' });
    const [response] = await Promise.all([
        page.waitForResponse(response => response.url().includes('/delete')),
        page.click('#delete button.btn-danger:has-text("Delete")')
    ]);
    
    await page.waitForTimeout(1000); // Wait for row to be removed
}

module.exports = { openAddModal, submitForm, verifyInTable, editItem, deleteItem };
