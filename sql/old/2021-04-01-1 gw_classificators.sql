CREATE TABLE `gw_classificators` (
  `id` int NOT NULL,
  `type` INT NOT NULL,
  `title` varchar(100) NOT NULL,
  `aka` varchar(100) NOT NULL COMMENT 'also known as',
  `count` INT NOT NULL,
  `active` tinyint NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `gw_classificators`
  ADD PRIMARY KEY (`id`);

  --
ALTER TABLE `gw_classificators`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
  
  
  ALTER TABLE `gw_classificators` ADD INDEX(`type`);
  
  


CREATE TABLE `gw_classificator_types` (
  `id` int NOT NULL,
  `key` varchar(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `aka` varchar(100) NOT NULL COMMENT 'also known as',
  `count` INT NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `gw_classificator_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `gw_classificator_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;  
