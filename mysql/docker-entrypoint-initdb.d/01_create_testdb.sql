CREATE DATABASE IF NOT EXISTS `redsoft`;
CREATE DATABASE IF NOT EXISTS `redsoft_test`;

CREATE USER 'redsoft' IDENTIFIED BY 'redsoft';
GRANT ALL ON redsoft.* TO 'redsoft';
GRANT ALL ON redsoft_test.* TO 'redsoft';
