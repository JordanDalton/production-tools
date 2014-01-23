/*
SQLyog Community v9.02 
MySQL - 5.5.10 : Database - lgitemsts
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `users` */

insert  into `users`(`USERNAME`,`FIRST_NAME`,`LAST_NAME`,`PASSWORD`,`ACTIVE`,`PASSWORD_TEMP`,`PASSWORD_EXPIRED`,`EMAIL_ADDRESS`,`SALT`) values ('bfay','Bob','Fay','303ed68862e8fbe8b8bafe3fe28bb046732b021b','1','0','0','',0),('jdoe','Juan','Doe','6938c2f62d78a8142d778d7baac2a5aee0fbc330','1','0','0','jorgedoe@letsgel.com',10431207533154583),('jmorgan','Joseph','Morgan','787d559439cfd927780996d2c78f635acca40c37','0','1','0','joseph.morgan@letsgel.com',0),('jtest','Jason','Test','25149dab325312d3c5c7f9eedeab0bf4e85acc8f','1','1','0','jason.test@letsgel.com',61839759112549612),('rhenson','Rick','Henson','3a07bdbefdb8bc01f81ea6f9f1ba18ec6cdc879d','1','0','0','rickh@letsgel.com',17686168634379001),('test','test','test','234a49aa30bdfa514e2c98633b7f3c38383c91cf','0','1','0','',62458810816324006);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
