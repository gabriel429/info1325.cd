-- Création de la table partenaires
CREATE TABLE IF NOT EXISTS `partenaires` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) DEFAULT '#',
  `image` VARCHAR(255) DEFAULT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `position` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exemples
INSERT INTO `partenaires` (`name`,`url`,`image`,`active`) VALUES
('Partenaire A','#','partenaire1325.png',1),
('Partenaire B','#','partenaire13252.png',1);
