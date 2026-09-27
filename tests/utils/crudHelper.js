async function openAddModal(page) {
    await page.click('a[data-target="#modal"], button[data-target="#modal"], .page-header a.btn:has-text("New"), .page-header button:has-text("Add")');
    await page.waitForSelector('#modal.in, #modal.show, #modal:visible', { state: 'visible' });
    await page.waitForTimeout(500);
}

async function submitForm(page, modalSelector = '#modal') {
    // Fill any missing required date/publish/etc fields safely
    await page.evaluate((selector) => {
        const modal = document.querySelector(selector) || document.querySelector('#modal');
        if (!modal) return;
        const form = modal.querySelector('form');
        if (form) {
            form.setAttribute('novalidate', 'true');
            form.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));
        }
        
        // Datepicker fallback if present and empty
        const dateInput = modal.querySelector('input.datepicker, input[name="date"]');
        if (dateInput && !dateInput.value) {
            if (window.$ && $(dateInput).data('datepicker')) {
                $(dateInput).datepicker('setDate', '01-01-2027');
            } else {
                dateInput.value = '01-01-2027';
                dateInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        // Publish radio check
        const radios = modal.querySelectorAll('input[type="radio"][value="1"]');
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

    // Wait for AJAX saving
    const [response] = await Promise.all([
        page.waitForResponse(response => 
            response.url().includes('/add') || 
            response.url().includes('/edit') || 
            response.url().includes('/save') || 
            response.url().includes('/update') || 
            response.url().includes('flash_news') ||
            response.url().includes('order-circular')
        ),
        page.click(`${modalSelector} button[type="submit"]`)
    ]);
    
    // Wait for modal to hide or table to update
    await page.waitForTimeout(1000);
}

async function verifyInTable(page, text) {
    // Search for the text to handle pagination, if a search box is available
    const searchBox = page.locator('input[name="search"]');
    if (await searchBox.count() > 0 && await searchBox.isVisible()) {
        await searchBox.fill(text);
        await searchBox.dispatchEvent('keyup');
        const searchBtn = page.locator('button[name="search"]');
        if (await searchBtn.count() > 0 && await searchBtn.isVisible()) {
            await searchBtn.click();
        }
        await page.waitForTimeout(1000); // Wait for ajax load
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
    const row = page.locator(`tbody tr:has-text("${rowText}")`).first();
    await row.locator('.btn-edit, a.edit, a:has-text("Edit"), button:has-text("Edit")').first().click();
    await page.waitForSelector('#modal.in, #modal.show, #modal:visible', { state: 'visible' });
    await page.waitForTimeout(500);
}

async function deleteItem(page, rowText) {
    const row = page.locator(`tbody tr:has-text("${rowText}")`).first();
    await row.locator('.btn-delete, a.delete, a:has-text("Delete"), button:has-text("Delete")').first().click();
    
    // Wait for delete confirmation modal and click confirm
    await page.waitForSelector('#delete:visible, .confirmation-modal:visible', { state: 'visible' });
    const [response] = await Promise.all([
        page.waitForResponse(response => response.url().includes('/delete')),
        page.locator('#delete button.delete, #delete button.btn-danger, .confirmation-modal button.delete').first().click()
    ]);
    
    await page.waitForTimeout(1000); // Wait for row to be removed
}

module.exports = { openAddModal, submitForm, verifyInTable, editItem, deleteItem };
