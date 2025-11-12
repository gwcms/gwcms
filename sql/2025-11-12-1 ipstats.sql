CREATE TABLE request_ip_stats (
    year  SMALLINT UNSIGNED NOT NULL,
    month TINYINT UNSIGNED NOT NULL,
    day   TINYINT UNSIGNED NOT NULL,
    hour  TINYINT UNSIGNED NOT NULL,
    ip    INT UNSIGNED NOT NULL,
    cnt   INT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (year, month, day, hour, ip)
) ENGINE=InnoDB;


CREATE TABLE request_ip_verify (
  ip INT UNSIGNED NOT NULL PRIMARY KEY,
  state TINYINT NOT NULL DEFAULT 0,        -- -1 = whitelist, 0 = normal, 1 = must verify, 2 = verified
  expires DATETIME DEFAULT NULL,
  country CHAR(2) DEFAULT NULL,
  updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (state),
  INDEX (expires)
) ENGINE=InnoDB;