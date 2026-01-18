-- SisfoKK Sentani Initial Database Setup
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create additional user for application
CREATE USER IF NOT EXISTS 'sisfokk_app'@'%' IDENTIFIED BY 'sisfokk_app_2024';
GRANT ALL PRIVILEGES ON sisfokk_sentani_db.* TO 'sisfokk_app'@'%';
FLUSH PRIVILEGES;

SET FOREIGN_KEY_CHECKS = 1;
