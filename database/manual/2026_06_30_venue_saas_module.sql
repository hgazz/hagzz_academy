USE hagzz;

DROP PROCEDURE IF EXISTS add_business_type;
DELIMITER //
CREATE PROCEDURE add_business_type()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='academies' AND column_name='business_type') THEN
        ALTER TABLE academies ADD COLUMN business_type VARCHAR(20) NOT NULL DEFAULT 'academy' AFTER role, ADD INDEX academies_business_type_index (business_type);
    END IF;
END//
DELIMITER ;
CALL add_business_type();
DROP PROCEDURE IF EXISTS add_business_type;

CREATE TABLE IF NOT EXISTS saas_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, code VARCHAR(255) NOT NULL UNIQUE, name JSON NOT NULL,
 monthly_price DECIMAL(10,2) NOT NULL DEFAULT 0, annual_price DECIMAL(10,2) NOT NULL DEFAULT 0,
 max_venues INT UNSIGNED NOT NULL DEFAULT 1, max_spaces INT UNSIGNED NOT NULL DEFAULT 1, max_staff INT UNSIGNED NOT NULL DEFAULT 1,
 features JSON NULL, active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE IF NOT EXISTS tenant_subscriptions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, academy_id BIGINT UNSIGNED NOT NULL, saas_plan_id BIGINT UNSIGNED NULL,
 billing_cycle VARCHAR(20) NOT NULL DEFAULT 'monthly', status VARCHAR(20) NOT NULL DEFAULT 'active', custom_price DECIMAL(10,2) NULL,
 starts_at DATE NOT NULL, ends_at DATE NULL, trial_ends_at DATE NULL, auto_renew TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX tenant_subscription_status_index (academy_id,status),
 CONSTRAINT tenant_subscriptions_academy_fk FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
 CONSTRAINT tenant_subscriptions_plan_fk FOREIGN KEY (saas_plan_id) REFERENCES saas_plans(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS venues (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, academy_id BIGINT UNSIGNED NOT NULL, name JSON NOT NULL, phone VARCHAR(255) NULL,
 address VARCHAR(255) NOT NULL, timezone VARCHAR(60) NOT NULL DEFAULT 'Africa/Cairo', currency VARCHAR(3) NOT NULL DEFAULT 'EGP',
 active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX venues_academy_active_index (academy_id,active),
 CONSTRAINT venues_academy_fk FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS venue_spaces (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, venue_id BIGINT UNSIGNED NOT NULL, sport_id BIGINT UNSIGNED NULL,
 name JSON NOT NULL, description JSON NULL, space_type VARCHAR(40) NOT NULL DEFAULT 'court', capacity INT UNSIGNED NULL,
 slot_minutes INT UNSIGNED NOT NULL DEFAULT 60, hourly_price DECIMAL(10,2) NOT NULL, opens_at TIME NOT NULL DEFAULT '08:00:00',
 closes_at TIME NOT NULL DEFAULT '23:00:00', active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
 INDEX venue_spaces_active_index (venue_id,active), CONSTRAINT venue_spaces_venue_fk FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE,
 CONSTRAINT venue_spaces_sport_fk FOREIGN KEY (sport_id) REFERENCES sports(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS venue_customers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, academy_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NULL,
 name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
 UNIQUE KEY venue_customers_phone_unique (academy_id,phone), CONSTRAINT venue_customers_academy_fk FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
 CONSTRAINT venue_customers_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS venue_bookings (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, academy_id BIGINT UNSIGNED NOT NULL, venue_space_id BIGINT UNSIGNED NOT NULL,
 venue_customer_id BIGINT UNSIGNED NOT NULL, reference VARCHAR(40) NOT NULL UNIQUE, booking_type VARCHAR(30) NOT NULL DEFAULT 'individual',
 title VARCHAR(255) NULL, starts_at DATETIME NOT NULL, ends_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'confirmed',
 source VARCHAR(20) NOT NULL DEFAULT 'dashboard', total_amount DECIMAL(10,2) NOT NULL, paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
 payment_method VARCHAR(40) NOT NULL DEFAULT 'cash', payment_method_other VARCHAR(255) NULL, notes TEXT NULL,
 created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX venue_bookings_academy_start_index (academy_id,starts_at),
 INDEX venue_bookings_space_period_index (venue_space_id,starts_at,ends_at),
 CONSTRAINT venue_bookings_academy_fk FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
 CONSTRAINT venue_bookings_space_fk FOREIGN KEY (venue_space_id) REFERENCES venue_spaces(id) ON DELETE CASCADE,
 CONSTRAINT venue_bookings_customer_fk FOREIGN KEY (venue_customer_id) REFERENCES venue_customers(id) ON DELETE RESTRICT
);
