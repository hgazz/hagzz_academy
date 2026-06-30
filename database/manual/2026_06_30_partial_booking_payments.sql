USE hagzz;

DROP PROCEDURE IF EXISTS add_hagzz_paid_amount;

DELIMITER //
CREATE PROCEDURE add_hagzz_paid_amount()
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'invoices'
          AND column_name = 'paid_amount'
    ) THEN
        ALTER TABLE invoices
            ADD COLUMN paid_amount DECIMAL(10, 2) NULL AFTER amount;
    END IF;
END//
DELIMITER ;

CALL add_hagzz_paid_amount();
DROP PROCEDURE IF EXISTS add_hagzz_paid_amount;
