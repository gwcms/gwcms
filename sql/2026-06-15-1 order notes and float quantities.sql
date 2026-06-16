ALTER TABLE `gw_order_group`
	MODIFY COLUMN `adm_message` text NOT NULL;

ALTER TABLE `gw_order_item`
	MODIFY COLUMN `qty` float NOT NULL;
