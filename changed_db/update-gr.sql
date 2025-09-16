ALTER TABLE `goods_receipt`
  MODIFY `po` VARCHAR(50) NULL,
MODIFY `no_series` VARCHAR(50) NULL,
  MODIFY `io` VARCHAR(50) NULL,
  MODIFY `so` VARCHAR(50) NULL,
  MODIFY `no_gi` VARCHAR(50) NULL,
  MODIFY `internal_no` VARCHAR(50) NULL,
  MODIFY `ref_surat_jalan` VARCHAR(50) NULL,
  MODIFY `no_surat_jalan` VARCHAR(50) NULL,
  MODIFY `no_inventory_tf` VARCHAR(50) NULL,
  MODIFY `type_inv_transaction` VARCHAR(50) NULL,
  MODIFY `reason` VARCHAR(255) NULL,
  MODIFY `whse` VARCHAR(50) NULL,
  MODIFY `project_code` VARCHAR(50) NULL,
  MODIFY `acct_code` VARCHAR(50) NULL,
  MODIFY `distr_rule` VARCHAR(50) NULL,
  MODIFY `vendor_code` VARCHAR(50) NULL,
  MODIFY `remarks` VARCHAR(255) NULL,
  MODIFY `updated_at` TIMESTAMP NULL;


ALTER TABLE `goods_receipt`
ADD `item_code` VARCHAR(255) NULL AFTER `vendor_code`,
ADD `item_desc` VARCHAR(255) NULL AFTER `item_code`,
ADD `qty` FLOAT NULL AFTER `item_desc`,
ADD `uom` VARCHAR(255) NULL AFTER `qty`,
ADD `user_id` BIGINT UNSIGNED NULL AFTER `uom`;

