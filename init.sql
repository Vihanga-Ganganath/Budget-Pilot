CREATE TABLE households (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- e.g., "The Smith Family"
    created_by_user_id INT, -- Tracks who created the household and manages it
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL, -- Allows subcategories
    name VARCHAR(150) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE CASCADE
);


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT, 
    role ENUM('customer', 'supplier', 'admin') DEFAULT 'customer',
    household_role ENUM('head', 'member') DEFAULT 'head', 
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL, 
    password_hash VARCHAR(255) NOT NULL, 
    phone_number VARCHAR(20), 
    nic VARCHAR(20),          
    date_of_birth DATE, 
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
    account_status ENUM('active', 'locked', 'suspended', 'pending') DEFAULT 'active', -- 'pending' = awaiting admin approval (supplier registrations)
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP NULL,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE SET NULL,
    
    -- THIS IS THE logic FOR MULTI-PORTAL:
    -- Jane can have 'jane@email.com' as an 'admin' AND 'jane@email.com' as a 'customer'
    UNIQUE KEY unique_email_per_role (email, role) 
);


CREATE TABLE transaction_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NULL, -- NULL means it's a global system category (e.g., 'Groceries', 'Transport')
    name VARCHAR(100) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    icon VARCHAR(50), -- For UI display (e.g., a material icon name)
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE
);


CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    loan_name VARCHAR(150) NOT NULL, -- e.g., "Car Lease", "Home Mortgage"
    principal_amount DECIMAL(12, 2) NOT NULL, -- The original borrowed amount
    interest_rate DECIMAL(5, 2) NOT NULL, -- Annual interest rate percentage (e.g., 10.50)
    duration_months INT NOT NULL,
    remaining_balance DECIMAL(12, 2) NOT NULL, -- Starts equal to principal, drops as transactions are logged
    start_date DATE NOT NULL,
    status ENUM('active', 'paid_off') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE
);


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL, -- Links to users table (Brand/Supplier)
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    ingredients TEXT, -- "Update Product Specifications"
    nutritional_value TEXT,
    document_url VARCHAR(255), -- "Upload Product Documents" (Certifications, Manuals)
    status ENUM('Active', 'Inactive', 'Discontinued', 'Coming Soon') DEFAULT 'Active',
    admin_verification ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending', -- "Admin Verify Supplier Products"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE RESTRICT
);


CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    profile_picture_url VARCHAR(255),
    address TEXT,
    preferred_language VARCHAR(50) DEFAULT 'English',
    monthly_income DECIMAL(10, 2) DEFAULT 0.00,
    preferred_currency VARCHAR(10) DEFAULT 'LKR',
    budget_preferences TEXT,
    financial_goals TEXT,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE brands_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL, -- The supplier who registered it
    company_name VARCHAR(150) NOT NULL,
    logo_url VARCHAR(255),
    business_information TEXT,
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending', -- For Admin approval
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE login_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    device_info VARCHAR(255), -- User Agent string (Browser/OS)
    login_status ENUM('success', 'failed', 'locked_out') NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    preference_type ENUM('favorite_brand', 'dietary_habit', 'category_interest') NOT NULL,
    preference_value VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE notification_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    budget_alerts BOOLEAN DEFAULT TRUE,
    promotions BOOLEAN DEFAULT TRUE,
    family_updates BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL, -- The budget limit set by the user
    budget_month DATE NOT NULL, -- Stored as 'YYYY-MM-01' to represent the month
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES transaction_categories(id) ON DELETE CASCADE,
    
    -- Ensure a household only has one budget per category per month
    UNIQUE KEY unique_monthly_budget (household_id, category_id, budget_month) 
);


CREATE TABLE savings_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    target_amount DECIMAL(10, 2) NOT NULL,
    current_amount DECIMAL(10, 2) DEFAULT 0.00,
    target_date DATE,
    status ENUM('active', 'achieved', 'abandoned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE
);


CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    user_id INT NOT NULL, -- The specific person who made the entry
    category_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    transaction_date DATE NOT NULL,
    description VARCHAR(255),
    notes TEXT,
    location VARCHAR(255), -- As requested in spec
    receipt_image_url VARCHAR(255), -- "Store bills as images"
    is_cash_spending BOOLEAN DEFAULT FALSE, -- "Record Cash Spending"
    loan_id INT NULL, -- NULL unless this transaction is a payment towards a specific loan
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES transaction_categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE SET NULL
);


CREATE TABLE shopping_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    created_by_user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL, -- e.g., "Weekly Groceries", "Christmas Party"
    target_date DATE, -- When they plan to go shopping
    status ENUM('active', 'completed', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,               -- supplier who givs the offer
    title VARCHAR(150) NOT NULL,            -- ex: "Christmas Special: 20% Off"
    description TEXT,                       -- description of the offer
    promo_code VARCHAR(50) UNIQUE,          -- (if needed) (ex: XMAS20)
    
    -- Type of Discount
    discount_type ENUM('percentage', 'fixed_amount', 'buy_x_get_y') NOT NULL, 
    discount_value DECIMAL(10, 2),          -- percentage (20%) or cash (Rs. 500)
    
    -- Bundle Offers (Buy X Get Y)
    buy_quantity INT DEFAULT 0,             -- ex: 2 (Buy 2)
    get_quantity INT DEFAULT 0,             -- ex: 1 (Get 1 Free)

    -- the time offer lives
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    
    -- ststus of the offer and created date
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                  -- userwho get the notification
    title VARCHAR(150) NOT NULL,           -- ex: 'Budget Limit Exceeded!'
    message TEXT NOT NULL,                 -- main message (ex: 'You have spent 80% of your grocery budget.')
    type ENUM('budget_alert', 'promotion', 'family_invite', 'system') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,         -- status of read or not read yet
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,                      
    error_type ENUM('database', 'auth', 'transaction', 'system') NOT NULL,
    error_message TEXT NOT NULL,           
    stack_trace TEXT,                      
    occurred_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE search_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    search_keyword VARCHAR(150) NOT NULL,  
    results_found INT DEFAULT 0,           
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variant_name VARCHAR(100) NOT NULL, -- e.g., '400g Pouch', '1kg Tin'
    sku VARCHAR(100) UNIQUE NOT NULL, -- "Generate Product SKU"
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0, -- "Add Inventory Quantity"
    is_hidden BOOLEAN DEFAULT FALSE, -- "Temporarily Hide Products"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);


CREATE TABLE product_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    tag_name VARCHAR(50) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);


CREATE TABLE product_alternatives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,           -- original product
    alternative_product_id INT NOT NULL, -- alternative product
    reason VARCHAR(100),               -- ex 'Cheaper price', 'Better nutrition'
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (alternative_product_id) REFERENCES products(id) ON DELETE CASCADE
);


CREATE TABLE shopping_list_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shopping_list_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_price DECIMAL(10, 2) NOT NULL, -- The price AT THE TIME it was added to the cart
    is_checked BOOLEAN DEFAULT FALSE, -- For crossing items off the list in the store
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shopping_list_id) REFERENCES shopping_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    -- Prevent the same exact product from being added twice to the same list (just update quantity instead)
    UNIQUE KEY unique_product_per_list (shopping_list_id, product_id)
);


CREATE TABLE product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL, -- The customer who bought it
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_review (product_id, user_id) -- One review per user per product
);


CREATE TABLE user_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, product_id)
);


CREATE TABLE product_analytics_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NULL, -- Can be null if viewing anonymously
    action_type ENUM('view', 'add_to_cart', 'remove_from_cart', 'search_click') NOT NULL,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    -- No foreign key constraint strictly required for user_id if we want to keep anonymous analytics
);


CREATE TABLE page_views_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    product_id INT NULL,                   
    page_url VARCHAR(255) NOT NULL,        
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);


CREATE TABLE promotion_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    promotion_id INT NOT NULL,
    product_id INT NOT NULL,                -- if we want we can relate variant_id too think sbout it later
    
    FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    -- To prevent the same item from being saved twice under a single offer
    UNIQUE KEY unique_promo_product (promotion_id, product_id)
);


CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variant_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    image_type ENUM('Front', 'Back', 'Side', 'Other') DEFAULT 'Front',
    
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);


CREATE TABLE inventory_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variant_id INT NOT NULL,
    change_amount INT NOT NULL, -- e.g., +50 (restock) or -2 (sold)
    reason VARCHAR(255), -- e.g., 'New Batch Arrival', 'Customer Purchase'
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- ============================================================
-- MIGRATION: Run this once on an existing database
-- ALTER TABLE users
--   MODIFY COLUMN account_status
--     ENUM('active','locked','suspended','pending') DEFAULT 'active';
-- ============================================================