-- ============================================================
-- Run this ONCE on a database that was created BEFORE this change.
-- (A new database made from init.sql already has all of this.)
-- ============================================================

-- 1. New column: suggested % for each category
ALTER TABLE transaction_categories
    ADD COLUMN suggested_percent DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER type;

-- 2. Add the plan categories that do not exist yet
INSERT INTO transaction_categories (household_id, name, type)
SELECT NULL, x.name, x.type FROM (
    SELECT 'Grocery' AS name, 'expense' AS type UNION ALL
    SELECT 'Transportation', 'expense' UNION ALL
    SELECT 'Clothing', 'expense'       UNION ALL
    SELECT 'Tuition', 'expense'        UNION ALL
    SELECT 'Rent', 'expense'           UNION ALL
    SELECT 'Loan', 'expense'           UNION ALL
    SELECT 'Medicine', 'expense'       UNION ALL
    SELECT 'Other', 'expense'          UNION ALL
    SELECT 'Additional Income', 'income'
) x
WHERE NOT EXISTS (
    SELECT 1 FROM transaction_categories c
    WHERE c.household_id IS NULL AND c.name = x.name AND c.type = x.type
);

-- 3. The suggested percentages (they add up to 100)
UPDATE transaction_categories SET suggested_percent = 30 WHERE household_id IS NULL AND type = 'expense' AND name = 'Grocery';
UPDATE transaction_categories SET suggested_percent = 10 WHERE household_id IS NULL AND type = 'expense' AND name = 'Transportation';
UPDATE transaction_categories SET suggested_percent = 5  WHERE household_id IS NULL AND type = 'expense' AND name = 'Clothing';
UPDATE transaction_categories SET suggested_percent = 10 WHERE household_id IS NULL AND type = 'expense' AND name = 'Tuition';
UPDATE transaction_categories SET suggested_percent = 25 WHERE household_id IS NULL AND type = 'expense' AND name = 'Rent';
UPDATE transaction_categories SET suggested_percent = 10 WHERE household_id IS NULL AND type = 'expense' AND name = 'Loan';
UPDATE transaction_categories SET suggested_percent = 5  WHERE household_id IS NULL AND type = 'expense' AND name = 'Medicine';
UPDATE transaction_categories SET suggested_percent = 5  WHERE household_id IS NULL AND type = 'expense' AND name = 'Other';
