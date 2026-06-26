USE hagzz;

DROP PROCEDURE IF EXISTS add_hagzz_payment_method_fields;

DELIMITER //
CREATE PROCEDURE add_hagzz_payment_method_fields()
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'invoices'
          AND column_name = 'payment_method'
    ) THEN
        ALTER TABLE invoices
            ADD COLUMN payment_method VARCHAR(40) NULL;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'invoices'
          AND column_name = 'payment_method_other'
    ) THEN
        ALTER TABLE invoices
            ADD COLUMN payment_method_other VARCHAR(255) NULL;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'academy_student_payments'
          AND column_name = 'method_other'
    ) THEN
        ALTER TABLE academy_student_payments
            ADD COLUMN method_other VARCHAR(255) NULL;
    END IF;

    ALTER TABLE academy_student_payments
        MODIFY method ENUM('cash','instapay','fawry','app_online','bank_transfer','card','online','other') NOT NULL DEFAULT 'cash';
END//
DELIMITER ;

CALL add_hagzz_payment_method_fields();

DROP PROCEDURE IF EXISTS add_hagzz_payment_method_fields;
