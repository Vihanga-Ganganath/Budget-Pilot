/**
 * Client-Side Shopping Cart Updates, Quantity Adjusters, and Alternative Swaps
 */
document.addEventListener('DOMContentLoaded', () => {
    const weeklyLimit = 300.00;
    const cartWrapper = document.querySelector('.cart-items-wrapper');
    const alertBox = document.getElementById('alertBox');
    const alertExceedLabel = document.getElementById('alertExceedAmount');
    
    // Summary Labels
    const subtotalLabel = document.getElementById('subtotalVal');
    const totalLabel = document.getElementById('totalVal');
    const topSpendTotal = document.getElementById('topSpendTotal');
    const topProgressPct = document.getElementById('topProgressPct');
    const topProgressBar = document.getElementById('topProgressBar');
    
    const summaryOverUnderLabel = document.getElementById('cartOverUnderLabel');
    const summaryOverUnderAmount = document.getElementById('cartOverUnderAmount');
    const summaryBudgetBar = document.getElementById('summaryBudgetBar');
    const summaryBudgetBox = document.getElementById('summaryBudgetBarBox');
    
    const savingsPotentialVal = document.getElementById('savingsPotentialVal');
    const savingsPctLabel = document.getElementById('savingsPctLabel');
    
    // Simulated item weights & counts
    let initialSavings = 54.20;

    // 1. Master Totals Recalculation Engine
    function calculateCartTotals() {
        const cartItems = document.querySelectorAll('.cart-item');
        let subtotal = 0;
        let itemCount = 0;

        cartItems.forEach(item => {
            const price = parseFloat(item.getAttribute('data-price')) || 0;
            const qtyInput = item.querySelector('.qty-input');
            const qty = parseInt(qtyInput.value) || 0;
            
            itemCount += qty;
            const itemTotal = price * qty;
            subtotal += itemTotal;

            // Update item row total price text
            const priceDisplay = item.querySelector('.item-price-val');
            priceDisplay.textContent = `$${itemTotal.toFixed(2)}`;
        });

        // Update header & summary text
        const totalText = `$${subtotal.toFixed(2)}`;
        subtotalLabel.textContent = totalText;
        totalLabel.textContent = totalText;
        topSpendTotal.textContent = totalText;

        // Update badge count
        const badge = document.getElementById('cartBadgeCount');
        if (badge) badge.textContent = itemCount;

        // Compute Budget Percent
        const percent = Math.round((subtotal / weeklyLimit) * 100);
        topProgressPct.textContent = `${percent}%`;

        // Budget checking bounds
        const diff = subtotal - weeklyLimit;

        if (diff > 0) {
            // Over budget
            alertBox.style.display = 'block';
            alertExceedLabel.textContent = `$${diff.toFixed(2)}`;
            
            topProgressPct.className = 'val-pct text-red';
            topProgressBar.className = 'widget-bar-fill bg-red';
            topProgressBar.style.width = '100%';

            summaryOverUnderLabel.textContent = 'Over Budget';
            summaryOverUnderLabel.className = 'status-lbl text-red';
            summaryOverUnderAmount.textContent = `-$${diff.toFixed(2)}`;
            summaryOverUnderAmount.className = 'amount-lbl text-red';
            summaryBudgetBar.className = 'widget-bar-fill bg-red';
            summaryBudgetBar.style.width = '100%';
            
            summaryBudgetBox.querySelector('.indicator-desc').textContent = `You are exceeding grocery limit by $${diff.toFixed(2)} for this week.`;
            totalLabel.className = 'val-summary-bold text-red';
        } else {
            // Under budget
            alertBox.style.display = 'none';
            
            topProgressPct.className = 'val-pct text-green';
            topProgressBar.className = 'widget-bar-fill bg-green';
            topProgressBar.style.width = `${percent}%`;

            const remaining = Math.abs(diff);
            summaryOverUnderLabel.textContent = 'Under Budget';
            summaryOverUnderLabel.className = 'status-lbl text-green';
            summaryOverUnderAmount.textContent = `$${remaining.toFixed(2)}`;
            summaryOverUnderAmount.className = 'amount-lbl text-green';
            summaryBudgetBar.className = 'widget-bar-fill bg-green';
            summaryBudgetBar.style.width = `${percent}%`;

            summaryBudgetBox.querySelector('.indicator-desc').textContent = `You have $${remaining.toFixed(2)} remaining for groceries this week.`;
            totalLabel.className = 'val-summary-bold text-green';
        }

        // Adjust potential savings view
        savingsPotentialVal.textContent = `$${initialSavings.toFixed(2)} this trip`;
        const redPct = Math.round((initialSavings / (subtotal + initialSavings)) * 100) || 0;
        savingsPctLabel.textContent = `${redPct}% reduction in grocery costs`;
    }

    // 2. Attach adjuster listeners to each item
    function initItemAdjusters(item) {
        const btnSub = item.querySelector('.btn-sub');
        const btnAdd = item.querySelector('.btn-add');
        const qtyInput = item.querySelector('.qty-input');
        const btnRemove = item.querySelector('.btn-remove-item');

        btnSub.addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 0;
            if (val > 1) {
                qtyInput.value = val - 1;
                calculateCartTotals();
            } else if (val === 1) {
                // Remove item if decremented to 0
                item.remove();
                calculateCartTotals();
            }
        });

        btnAdd.addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 0;
            qtyInput.value = val + 1;
            calculateCartTotals();
        });

        btnRemove.addEventListener('click', () => {
            item.remove();
            calculateCartTotals();
        });
    }

    const items = document.querySelectorAll('.cart-item');
    items.forEach(initItemAdjusters);

    // 3. Clear All Items
    const btnClearAll = document.getElementById('btnClearAll');
    btnClearAll.addEventListener('click', () => {
        cartWrapper.innerHTML = `<p style="text-align: center; color: var(--text-light); padding: 32px 0; font-weight: 500;">Your shopping cart is empty.</p>`;
        initialSavings = 0;
        calculateCartTotals();
    });

    // 4. Cheaper Alternatives Swapping Logic
    const swapBtns = document.querySelectorAll('.btn-swap-save');
    swapBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const replaceTarget = btn.getAttribute('data-replace');
            const newName = btn.getAttribute('data-name');
            const newPrice = parseFloat(btn.getAttribute('data-price')) || 0;

            const cartItem = document.querySelector(`.cart-item[data-id="${replaceTarget}"]`);
            if (!cartItem) return;

            // Perform visual and attribute updates on cart card
            cartItem.setAttribute('data-price', newPrice);
            cartItem.querySelector('.item-name').textContent = newName;
            
            // Adjust image & labels
            if (replaceTarget === 'steak') {
                cartItem.querySelector('.item-img').src = 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?auto=format&fit=crop&w=150&q=80';
                cartItem.querySelector('.tag-badge').textContent = 'Good Value';
                cartItem.querySelector('.tag-badge').className = 'tag-badge tag-green';
                initialSavings -= 30.50; // Deduct potential savings since already claimed
            } else if (replaceTarget === 'butter') {
                cartItem.querySelector('.item-img').src = 'https://images.unsplash.com/photo-1590080875628-9a8427f6a735?auto=format&fit=crop&w=150&q=80';
                cartItem.querySelector('.tag-badge').textContent = 'Good Value';
                cartItem.querySelector('.tag-badge').className = 'tag-badge tag-green';
                initialSavings -= 8.51;
            }

            // Hide/remove the suggestion row
            btn.closest('.alt-row').remove();

            // Refresh sums
            calculateCartTotals();

            showToast(`Swapped target item for "${newName}"!`);
        });
    });

    // 5. Checkout
    const btnCheckout = document.getElementById('btnCheckout');
    btnCheckout.addEventListener('click', () => {
        showToast("Secure checkout initialized. Redirecting to receipt dashboard...");
        setTimeout(() => {
            location.href = 'dashboard.html';
        }, 1500);
    });

    function showToast(message) {
        const existing = document.getElementById('toastAlert');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'top-toast';
        toast.id = 'toastAlert';
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </span>
                <span class="toast-text">${message}</span>
            </div>
            <button class="toast-close">&times;</button>
        `;
        document.body.appendChild(toast);

        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.remove();
        });

        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s, transform 0.5s';
            toast.style.opacity = '0';
            toast.style.transform = 'translate(-50%, -20px)';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // Initial sum execution
    calculateCartTotals();

    // Load active theme
    const activeTheme = localStorage.getItem('theme');
    if (activeTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
});
