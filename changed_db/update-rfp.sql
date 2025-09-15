ALTER TABLE `receipt_from_production`
  ADD `base_entry` INT NULL AFTER `id`,
  MODIFY `qty` FLOAT NULL,
  ADD `uom` VARCHAR(255) NULL AFTER `qty`,
  MODIFY `number` INT(10) NULL AFTER `id`,
  MODIFY `is_temp` TINYINT(1) DEFAULT 1 NULL AFTER `id`,
  ADD `user_id` BIGINT UNSIGNED NULL AFTER `uom`;
