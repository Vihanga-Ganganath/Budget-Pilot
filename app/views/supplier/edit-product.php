<?php
$product = $data['product'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Product - Budget Pilot</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet"
          href="<?php echo URLROOT; ?>/css/supplier/supplier.css?v=<?php echo @filemtime(APPROOT . '/../public/css/supplier/supplier.css'); ?>">
</head>

<body class="sp">

<div class="shell">

<?php $spActive = 'catalog'; $spActiveLink = true; require APPROOT . '/views/supplier/_sidebar.php'; ?>

  <div class="main">
<?php require APPROOT . '/views/supplier/_appbar.php'; ?>

  <!-- Main Content -->
  <main>

        <div class="card" style="max-width:900px; margin:40px auto;">

            <h2 class="section-title">Edit Product</h2>

            <p style="margin-bottom:25px;">
                Update the product information below.
            </p>


            <form method="POST"
                enctype="multipart/form-data"
                action="<?php echo URLROOT; ?>/supplier/editProduct/<?php echo $product['id']; ?>">

                <!-- Product Name -->
                <div style="margin-bottom:18px;">
                    <label>Product Name *</label>

                    <input
                        class="input"
                        type="text"
                        name="name"
                        required
                        value="<?php echo htmlspecialchars($product['name']); ?>"
                        style="width:100%;">
                </div>


                <!-- Category -->
                 <!-- Variant Details -->
<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px; margin-bottom:18px;">

    <div>
        <label>Variant *</label>
        <input
            class="input"
            type="text"
            name="variant_name"
            required
            value="<?= htmlspecialchars($product['variant_name'] ?? '') ?>"
            style="width:100%;">
    </div>

    <div>
        <label>Price (USD $) *</label>
        <input
            class="input"
            type="number"
            name="price"
            step="0.01"
            min="0"
            required
            value="<?= htmlspecialchars($product['price'] ?? '') ?>"
            style="width:100%;">
    </div>

    <div>
        <label>Stock Quantity *</label>
        <input
            class="input"
            type="number"
            name="stock_quantity"
            min="0"
            required
            value="<?= htmlspecialchars($product['stock_quantity'] ?? '') ?>"
            style="width:100%;">
    </div>

</div>
                <div style="margin-bottom:18px;">
                    <label>Category *</label>

                    <select
                        class="input"
                        name="category_id"
                        required
                        style="width:100%;">

                        <option value="">Select Category</option>

                        <option value="1"
                            <?php echo $product['category_id'] == 1 ? 'selected' : ''; ?>>
                            Detergents &amp; Cleaning
                        </option>

                        <option value="2"
                            <?php echo $product['category_id'] == 2 ? 'selected' : ''; ?>>
                            Personal Care
                        </option>

                        <option value="3"
                            <?php echo $product['category_id'] == 3 ? 'selected' : ''; ?>>
                            Packaged Food
                        </option>

                        <option value="4"
                            <?php echo $product['category_id'] == 4 ? 'selected' : ''; ?>>
                            Cooking Essentials
                        </option>

                        <option value="5"
                            <?php echo $product['category_id'] == 5 ? 'selected' : ''; ?>>
                            Beverages
                        </option>

                        <option value="6"
                            <?php echo $product['category_id'] == 6 ? 'selected' : ''; ?>>
                            Snacks &amp; Spreads
                        </option>

                        <option value="7"
                            <?php echo $product['category_id'] == 7 ? 'selected' : ''; ?>>
                            Household Consumables
                        </option>

                    </select>
                </div>

                <div style="margin-bottom:18px;">
    <label>Product Image</label><br>

    <?php if (!empty($product['image_url'])): ?>
        <div style="margin:8px 0 10px 0;">
            <p style="margin:0 0 5px 0; font-size:13px; color:#666;">
                Current Image
            </p>

            <img
                src="<?= URLROOT . '/' . htmlspecialchars($product['image_url']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
                style="
                    width:90px;
                    height:90px;
                    object-fit:cover;
                    border-radius:8px;
                    border:1px solid #ddd;
                "
            >
        </div>
    <?php endif; ?>

    <input
        class="input"
        type="file"
        name="product_image"
        accept="image/jpeg,image/png,image/webp"
        style="width:100%;"
    >

    <small style="color:#777;">
        Leave this empty to keep the current image. JPG, PNG or WEBP.
    </small>
</div>
                <!-- Description -->
                <div style="margin-bottom:18px;">
                    <label>Description</label>

                    <textarea
                        class="input"
                        name="description"
                        rows="3"
                        style="width:100%;"><?php
                        echo htmlspecialchars($product['description'] ?? '');
                    ?></textarea>
                </div>


                <!-- Ingredients -->
                <div style="margin-bottom:18px;">
                    <label>Ingredients</label>

                    <textarea
                        class="input"
                        name="ingredients"
                        rows="3"
                        style="width:100%;"><?php
                        echo htmlspecialchars($product['ingredients'] ?? '');
                    ?></textarea>
                </div>


                <!-- Nutritional Value -->
                <div style="margin-bottom:18px;">
                    <label>Nutritional Value</label>

                    <textarea
                        class="input"
                        name="nutritional_value"
                        rows="3"
                        style="width:100%;"><?php
                        echo htmlspecialchars($product['nutritional_value'] ?? '');
                    ?></textarea>
                </div>


                <!-- Document URL -->
                <div style="margin-bottom:18px;">
                    <label>Document URL</label>

                    <input
                        class="input"
                        type="text"
                        name="document_url"
                        value="<?php echo htmlspecialchars($product['document_url'] ?? ''); ?>"
                        style="width:100%;">
                </div>


                <!-- Status -->
                <div style="margin-bottom:25px;">
                    <label>Status</label>

                    <select
                        class="input"
                        name="status"
                        style="width:100%;">

                        <option value="Active"
                            <?php echo $product['status'] === 'Active' ? 'selected' : ''; ?>>
                            Active
                        </option>

                        <option value="Inactive"
                            <?php echo $product['status'] === 'Inactive' ? 'selected' : ''; ?>>
                            Inactive
                        </option>

                        <option value="Discontinued"
                            <?php echo $product['status'] === 'Discontinued' ? 'selected' : ''; ?>>
                            Discontinued
                        </option>

                        <option value="Coming Soon"
                            <?php echo $product['status'] === 'Coming Soon' ? 'selected' : ''; ?>>
                            Coming Soon
                        </option>

                    </select>
                </div>


                <!-- Buttons -->
                <div style="display:flex; gap:12px;">

                    <button class="btn" type="submit">
                        Save Changes
                    </button>

                    <a class="btn"
                       href="<?php echo URLROOT; ?>/supplier/catalog">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </main>
  </div>
</div>

<script src="<?php echo URLROOT; ?>/js/customer-js/nav.js"></script>
</body>
</html>