ALTER TABLE `shop_user_wishlist` ADD `type` TINYINT NOT NULL DEFAULT '1' COMMENT '1 - shop_products, 2 - subscriber_group' AFTER `id`;