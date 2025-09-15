ALTER TABLE `ifp`
ADD `base_entry` INT NULL AFTER `id`,
ADD `baseline_num` INT NULL AFTER `base_entry`,
ADD `item_code` VARCHAR(255) NULL AFTER `baseline_num`,
ADD `item_desc` VARCHAR(255) NULL AFTER `item_code`,
ADD `qty` FLOAT NULL AFTER `item_desc`,
ADD `uom` VARCHAR(255) NULL AFTER `qty`,
ADD `user_id` BIGINT UNSIGNED NULL AFTER `uom`;
