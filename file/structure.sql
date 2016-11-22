
CREATE TABLE `jaringan` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `kode_member` varchar(10) DEFAULT NULL,
  `sponsor` varchar(10) DEFAULT NULL,
  `upline` varchar(10) DEFAULT NULL,
  `posisi` enum('L','R') NOT NULL DEFAULT 'L'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
