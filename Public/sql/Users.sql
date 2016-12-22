/****************************************************
## Copyright (C) 2015 Infoservis
##
## Author: 			Infoservis Group
## Date:				2015-03-11
## Web: 				<http://>
##	
## ##################################################
## # Tabulka - Users																#
## ##################################################
## 
## Vytvorenie mozne v mysql prikazom:
##  mysql> source adresa_k_priecinku/Users.sql
##
****************************************************/
CREATE TABLE IF NOT EXISTS Users
(
	Id INT AUTO_INCREMENT NOT NULL,
	Email VARCHAR(255),
	Username VARCHAR(255),
	Passwordname VARCHAR(255),
	Codevalidation VARCHAR(255),
	Logon TIMESTAMP DEFAULT NOW(),
	Validation ENUM('valid', 'invalid') DEFAULT 'invalid',
	Privileges ENUM('admin', 'user') DEFAULT 'user',
	PRIMARY KEY(Id)
)ENGINE = INNODB;
