import { test, expect } from '@playwright/test';

test.describe('mobile menu — small viewport (iPhone SE, 375x667)', () => {
    test.use({ viewport: { width: 375, height: 667 }, isMobile: true, hasTouch: true });

    test('every nav link, including the CTA, can be scrolled fully into view', async ({ page }) => {
        await page.goto('/');
        await page.getByRole('button', { name: 'Open menu' }).click();

        const dialog = page.getByRole('dialog', { name: 'Site menu' });
        await expect(dialog).toBeVisible();

        const links = dialog.getByRole('navigation').getByRole('link');
        const count = await links.count();
        expect(count).toBeGreaterThan(0);

        for (let i = 0; i < count; i++) {
            const link = links.nth(i);
            await link.scrollIntoViewIfNeeded();
            await expect(link).toBeInViewport({ ratio: 1 });
        }
    });
});

test.describe('mobile menu — open/close behavior', () => {
    test.use({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });

    test('opens via the hamburger button and toggles aria state', async ({ page }) => {
        await page.goto('/');
        const trigger = page.locator('[aria-controls="mobile-menu"]');

        await expect(trigger).toHaveAttribute('aria-expanded', 'false');
        await expect(trigger).toHaveAccessibleName('Open menu');
        await trigger.click();

        const dialog = page.getByRole('dialog', { name: 'Site menu' });
        await expect(dialog).toBeVisible();
        await expect(trigger).toHaveAttribute('aria-expanded', 'true');
        await expect(trigger).toHaveAccessibleName('Close menu');
    });

    test('closes via the close button and returns focus to the trigger', async ({ page }) => {
        await page.goto('/');
        const trigger = page.getByRole('button', { name: 'Open menu' });
        await trigger.click();

        const dialog = page.getByRole('dialog', { name: 'Site menu' });
        await dialog.getByRole('button', { name: 'Close menu' }).click();

        await expect(dialog).toBeHidden();
        await expect(trigger).toBeFocused();
    });

    test('closes via the Escape key and restores focus', async ({ page }) => {
        await page.goto('/');
        const trigger = page.getByRole('button', { name: 'Open menu' });
        await trigger.click();

        const dialog = page.getByRole('dialog', { name: 'Site menu' });
        await expect(dialog).toBeVisible();
        await page.keyboard.press('Escape');

        await expect(dialog).toBeHidden();
        await expect(trigger).toBeFocused();
    });

    test('closes and navigates when a nav link is clicked', async ({ page }) => {
        await page.goto('/');
        await page.getByRole('button', { name: 'Open menu' }).click();

        const dialog = page.getByRole('dialog', { name: 'Site menu' });
        await dialog.getByRole('link', { name: /Work/ }).click();

        await expect(page).toHaveURL(/\/work$/);
        await expect(dialog).toBeHidden();
    });

    test('covers the full viewport even when the page was already scrolled before opening', async ({ page }) => {
        await page.goto('/');
        await page.mouse.wheel(0, 900);
        await expect(page.locator('html')).not.toHaveCSS('overflow', 'hidden');

        await page.getByRole('button', { name: 'Open menu' }).click();
        const dialog = page.getByRole('dialog', { name: 'Site menu' });
        await expect(dialog).toBeVisible();

        const box = await dialog.boundingBox();
        const viewport = page.viewportSize();
        expect(box.height).toBeGreaterThanOrEqual(viewport.height - 1);

        // The topmost element at every viewport corner/center must be the dialog
        // itself (or a descendant) — background-page content must not paint through.
        const points = [
            [4, 4], [viewport.width - 4, 4],
            [4, viewport.height - 4], [viewport.width - 4, viewport.height - 4],
            [viewport.width / 2, viewport.height / 2],
        ];
        for (const [x, y] of points) {
            const coveredByDialog = await page.evaluate(([x, y]) => {
                const top = document.elementFromPoint(x, y);
                return !!top?.closest('#mobile-menu');
            }, [x, y]);
            expect(coveredByDialog).toBe(true);
        }
    });

    test('locks background scroll while open and restores it after closing', async ({ page }) => {
        await page.goto('/');
        const html = page.locator('html');

        await expect(html).not.toHaveCSS('overflow', 'hidden');

        await page.getByRole('button', { name: 'Open menu' }).click();
        await expect(page.getByRole('dialog', { name: 'Site menu' })).toBeVisible();
        await expect(html).toHaveCSS('overflow', 'hidden');

        await page.keyboard.press('Escape');
        await expect(page.getByRole('dialog', { name: 'Site menu' })).toBeHidden();
        await expect(html).not.toHaveCSS('overflow', 'hidden');
    });
});
