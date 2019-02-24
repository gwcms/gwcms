ALTER TABLE  `tom_order_items` ADD  `finish_time` DATETIME NOT NULL AFTER  `removed` ;

UPDATE tom_order_items oi, tom_orders o SET oi.finish_time=o.finish_time WHERE oi.order_id=o.id AND o.state=30;


# update not updated
# UPDATE tom_order_items oi, tom_orders o SET oi.finish_time=oi.update_time WHERE oi.order_id=o.id AND oi.finish_time < "2001-01-01" AND oi.state=30