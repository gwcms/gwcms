ALTER TABLE `gw_order_group`
	ADD COLUMN `invoice_date` date NULL AFTER `pay_time`,
	ADD COLUMN `due_date` date NULL AFTER `invoice_date`;
