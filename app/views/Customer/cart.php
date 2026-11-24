<?php $pageCss = 'customer-css/cart'; require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">

<?php
    $cartSubtotal = (float)($data['subtotal'] ?? 0);
    $groceryBudget = (float)($data['grocery_budget'] ?? 150);
    $spentGrocery = (float)($data['spent_grocery'] ?? 0);
    $remainingBudget = (float)($data['remaining_budget'] ?? ($groceryBudget - $spentGrocery));
    $isExceeded = !empty($data['is_budget_exceeded']);
    $budgetPercent = (int)($data['budget_percentage'] ?? 0);
    $items = $data['cart_items'] ?? [];
    $totalCount = (int)($data['total_items'] ?? count($items));
?>

<?php if (!empty($data['flash_message'])): ?>
    <div class="cart-flash <?= $data['flash_type'] === 'error' ? 'error' : 'success' ?>">
        <?= htmlspecialchars($data['flash_message']) ?>
    </div>
<?php endif; ?>

<header class="content-header cart-content-header">
    <div class="header-left cart-header-title">
        <h2 class="page-title text-navy">Shopping List</h2>
        <p class="page-subtitle">Review your groceries and their impact on your budget.</p>
    </div>
    
    <!-- Dynamic Budget Progress (Top Right) -->
    <div class="dashboard-card cart-budget-tracker <?= $isExceeded ? 'is-over' : '' ?>">
        <div class="cart-tracker-top">
            <div>
                <span class="cart-tracker-label">Grocery Budget Status</span>
                <strong class="cart-tracker-val">$<?= number_format($cartSubtotal, 2) ?></strong> 
                <span class="cart-tracker-budget">/ $<?= number_format($groceryBudget, 2) ?></span>
            </div>
            <?php if ($isExceeded): ?>
                <strong class="cart-tracker-status is-over">Over Budget (<?= $budgetPercent ?>%)</strong>
            <?php else: ?>
                <strong class="cart-tracker-status is-ok">Healthy (<?= $budgetPercent ?>%)</strong>
            <?php endif; ?>
        </div>
        <div class="cart-progress-track">
            <div class="cart-progress-fill <?= $isExceeded ? 'is-over' : 'is-ok' ?>" style="width: <?= min(100, $budgetPercent) ?>%;"></div>
        </div>
        <div class="cart-tracker-hint <?= $isExceeded ? 'is-over' : '' ?>">
            <?php if ($isExceeded): ?>
                Warning: You have exceeded your grocery budget by $<?= number_format(abs($remainingBudget), 2) ?>!
            <?php else: ?>
                $<?= number_format($remainingBudget, 2) ?> remaining in this month's grocery allocation
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="cart-grid-layout">
    <!-- LEFT: Itemized List -->
    <div>
        <section class="dashboard-card cart-items-card">
            <div class="cart-items-head">
                <h3 class="cart-items-title">Itemized List (<?= $totalCount ?> <?= $totalCount === 1 ? 'item' : 'items' ?>)</h3>
                <?php if (!empty($items)): ?>
                    <a href="<?= URLROOT ?>/cart/clear" onclick="return confirm('Are you sure you want to clear your cart?');" class="cart-clear-link">Clear All</a>
                <?php endif; ?>
            </div>
            
            <div>
                <?php if (empty($items)): ?>
                    <div class="cart-empty-box">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="cart-empty-icon"><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
                        <h4 class="cart-empty-title">Your cart is empty</h4>
                        <p class="cart-empty-desc">Explore our catalog to find fresh groceries and compare brand prices.</p>
                        <a href="<?= URLROOT ?>/customer/grocery" class="btn cart-btn-browse">Browse Grocery Catalog</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div class="cart-item-row">
                            <div class="cart-item-flex">
                                <div class="cart-item-thumb">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?= URLROOT . '/' . htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                                    <?php else: ?>
                                        <span class="cart-thumb-placeholder">No Image</span>
                                    <?php endif; ?>
                                </div>
                                <div class="cart-item-info">
                                    <span class="cart-brand-tag"><?= htmlspecialchars($item['brand_name'] ?? 'Brand') ?></span>
                                    <h4 class="cart-item-name"><?= htmlspecialchars($item['product_name']) ?></h4>
                                    <strong class="cart-item-price">$<?= number_format((float)$item['price'], 2) ?></strong>
                                    <?php if (!empty($item['unit_price'])): ?>
                                        <span class="cart-unit-price">($<?= number_format((float)$item['unit_price'], 2) ?>/unit)</span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="cart-stepper">
                                    <form method="POST" action="<?= URLROOT ?>/cart/update" class="cart-stepper-form">
                                        <input type="hidden" name="cart_id" value="<?= (int)$item['id'] ?>">
                                        <input type="hidden" name="quantity" value="<?= max(0, (int)$item['quantity'] - 1) ?>">
                                        <button type="submit" class="cart-stepper-btn">-</button>
                                    </form>
                                    <span class="cart-stepper-val">
                                        <?= (int)$item['quantity'] ?>
                                    </span>
                                    <form method="POST" action="<?= URLROOT ?>/cart/update" class="cart-stepper-form">
                                        <input type="hidden" name="cart_id" value="<?= (int)$item['id'] ?>">
                                        <input type="hidden" name="quantity" value="<?= (int)$item['quantity'] + 1 ?>">
                                        <button type="submit" class="cart-stepper-btn">+</button>
                                    </form>
                                </div>

                                <div class="cart-item-total-col">
                                    <strong class="cart-item-subtotal">$<?= number_format((float)$item['item_subtotal'], 2) ?></strong>
                                    <a href="<?= URLROOT ?>/cart/remove/<?= (int)$item['id'] ?>" onclick="return confirm('Remove this item from your cart?');" class="cart-remove-link">Remove</a>
                                </div>
                            </div>

                            <!-- Brand Price Comparison & Smart Swap Section -->
                            <?php 
                                $alternatives = $item['alternatives'] ?? [];
                                $hasCheaper = !empty($item['has_cheaper']);
                                $maxSavings = (float)($item['max_savings'] ?? 0);
                            ?>
                            <?php if (!empty($alternatives)): ?>
                                <details class="cart-compare-details" <?= $hasCheaper ? 'open' : '' ?>>
                                    <summary class="cart-compare-summary <?= $hasCheaper ? 'has-cheaper' : 'no-cheaper' ?>">
                                        <span>
                                            Compare Brands <?= $hasCheaper ? '(Cheaper options available - Save up to $' . number_format($maxSavings, 2) . ')' : '(' . count($alternatives) . ' other options)' ?>
                                        </span>
                                        <span class="cart-compare-arrow">▼</span>
                                    </summary>
                                    <div class="cart-compare-list">
                                        <?php foreach ($alternatives as $alt): ?>
                                            <?php 
                                                $altPrice = (float)$alt['price'];
                                                $altUnitPrice = (float)($alt['unit_price'] > 0 ? $alt['unit_price'] : $altPrice);
                                                $totalSaving = (float)($alt['total_saving'] ?? 0);
                                                $isCheapest = !empty($alt['is_cheapest']);
                                            ?>
                                            <div class="cart-alt-card <?= $isCheapest ? 'is-cheapest' : '' ?>">
                                                <div class="cart-alt-info">
                                                    <div>
                                                        <strong class="cart-alt-name">
                                                            <?= htmlspecialchars($alt['brand'] ?? 'Brand') ?> - <?= htmlspecialchars($alt['name']) ?>
                                                        </strong>
                                                        <div class="cart-alt-pricing">
                                                            <strong class="cart-alt-price">$<?= number_format($altPrice, 2) ?></strong>
                                                            <span>($<?= number_format($altUnitPrice, 2) ?>/unit)</span>
                                                        </div>
                                                    </div>
                                                    <?php if ($isCheapest): ?>
                                                        <span class="cart-badge-cheapest">
                                                            Cheapest
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($totalSaving > 0): ?>
                                                        <span class="cart-badge-savings">
                                                            Save $<?= number_format($totalSaving, 2) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <form method="POST" action="<?= URLROOT ?>/cart/swap" class="cart-swap-form">
                                                    <input type="hidden" name="cart_item_id" value="<?= (int)$item['id'] ?>">
                                                    <input type="hidden" name="new_product_id" value="<?= (int)$alt['id'] ?>">
                                                    <button type="submit" class="btn cart-btn-switch <?= $isCheapest ? 'is-cheapest' : 'not-cheapest' ?>">
                                                        Switch Brand
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- RIGHT: Summary Panel -->
    <div>
        <section class="dashboard-card cart-summary-sidebar">
            <h3 class="cart-summary-title">Order Summary</h3>
            
            <div class="cart-summary-row">
                <span>Cart Subtotal (<?= $totalCount ?> items)</span>
                <strong>$<?= number_format($cartSubtotal, 2) ?></strong>
            </div>
            <div class="cart-summary-row">
                <span>Allocated Grocery Budget</span>
                <strong>$<?= number_format($groceryBudget, 2) ?></strong>
            </div>
            <?php if ($spentGrocery > 0): ?>
                <div class="cart-summary-row spent-earlier">
                    <span>Spent Earlier This Month</span>
                    <span>-$<?= number_format($spentGrocery, 2) ?></span>
                </div>
            <?php endif; ?>
            <div class="cart-summary-row mb-20">
                <span>Remaining Grocery Budget</span>
                <strong class="<?= $remainingBudget < 0 ? 'text-danger' : 'text-success' ?>">
                    $<?= number_format($remainingBudget, 2) ?>
                </strong>
            </div>

            <?php if ($isExceeded): ?>
                <div class="cart-alert-exceeded">
                    Cart total ($<?= number_format($cartSubtotal, 2) ?>) exceeds Remaining Budget ($<?= number_format($remainingBudget, 2) ?>)!
                </div>
            <?php endif; ?>

            <?php if (!empty($data['potential_savings']) && (float)$data['potential_savings'] > 0): ?>
                <div class="cart-savings-banner">
                    <strong>Potential Savings:</strong> Switch to recommended alternatives above to save up to <strong>$<?= number_format((float)$data['potential_savings'], 2) ?></strong>!
                </div>
            <?php endif; ?>
            
            <div class="cart-total-divider">
                <strong class="cart-total-label">Total</strong>
                <strong class="cart-total-value">$<?= number_format($cartSubtotal, 2) ?></strong>
            </div>
            
            <form method="POST" action="<?= URLROOT ?>/cart/checkout">
                <button type="submit" class="btn cart-checkout-btn" <?= empty($items) ? 'disabled' : '' ?>>
                    Deduct From Grocery Budget
                </button>
            </form>
            
        </section>
    </div>
</div>
</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
