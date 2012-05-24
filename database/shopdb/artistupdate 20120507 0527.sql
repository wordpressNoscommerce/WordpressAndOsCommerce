-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.51b-community-nt


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema shopdb
--

CREATE DATABASE IF NOT EXISTS shopdb;
USE shopdb;

--
-- Definition of table `artistupdate`
--

DROP TABLE IF EXISTS `artistupdate`;
CREATE TABLE `artistupdate` (
  `manufacturers_id` int(11) NOT NULL default '0',
  `manufacturers_name` varchar(32) NOT NULL,
  `manufacturers_label` varchar(32) NOT NULL,
  `manufacturers_image` varchar(64) default NULL,
  `manufacturers_image_med` varchar(64) default NULL,
  `manufacturers_press_image` varchar(64) default NULL,
  `date_added` datetime default NULL,
  `last_modified` datetime default NULL,
  `dummy` varchar(1) default NULL,
  `artist_set` varchar(1) default NULL,
  PRIMARY KEY  (`manufacturers_id`),
  KEY `IDX_MANUFACTURERS_NAME` (`manufacturers_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `artistupdate`
--

/*!40000 ALTER TABLE `artistupdate` DISABLE KEYS */;
INSERT INTO `artistupdate` (`manufacturers_id`,`manufacturers_name`,`manufacturers_label`,`manufacturers_image`,`manufacturers_image_med`,`manufacturers_press_image`,`date_added`,`last_modified`,`dummy`,`artist_set`) VALUES 
 (10,'Apparat','32|999','apparat.jpg','apparat_medium.jpg','apparat_press.jpg','2010-11-07 23:16:00','2010-07-09 00:33:00','','M'),
 (12,'Anders Ilar','32|999','anders%20037_kl.jpg','rnm_170_anders.jpg','anders%20037.jpg','2019-11-07 15:30:00','2019-08-09 14:21:00','','A'),
 (14,'Judith Juillerat','32|999','judith_5474a_kl.jpg','rnm_170_Judith.Juillerat.jpg','_00A5474a.jpg','2019-11-07 15:32:00','2010-11-09 16:09:00','','A'),
 (16,'Das Bierbeben','32','dasbier_kl2009.jpg','','DB2009Bild5.JPG','2019-11-07 15:34:00','2030-04-09 12:31:00','','M'),
 (20,'Gwem','32','gwem2008_kl.jpg','','gwem2008_xx.jpg','2019-11-07 15:38:00','2015-08-08 15:51:00','','A'),
 (22,'Jerry Abstract','33|32','JAbstract_2006-2_kl.jpg','','JAbstract_2006-2.jpg','2019-11-07 15:40:00','2015-08-08 16:05:00','','A'),
 (24,'Magnum 38','33|32|999','magnum38_kl.jpg','rnm_170_magnum.jpg','magnum38_tokyo_finger','2019-11-07 15:40:00','2019-08-09 12:13:00','','M'),
 (26,'Nanospeed','32','Nanospeed_kl.jpg','','NANOSP~2.JPG','2019-11-07 15:41:00','2022-07-08 12:56:00','','A'),
 (28,'Phon.o','32|999','PHONO_KL.JPG','rnm_170_phono.jpg','phono_treppe01b.jpg','2019-11-07 15:41:00','2019-08-09 13:37:00','','M'),
 (30,'Rechenzentrum','32','rechenzentrum_kl.jpg','','moroder-dwnld.jpg','2019-11-07 15:42:00','2015-08-08 17:26:00','','A'),
 (33,'Holz','33|32','','','','2019-11-07 15:43:00','2017-08-08 22:26:00','','A'),
 (69,'Static Lounge','','','','','2019-11-07 15:56:00','2027-11-07 16:18:00','','A'),
 (74,'Daniel Meteo','22|32|999','danielmeteo.jpg','rnm_170_danielm.jpg','daniel_meteo_gross.jpg','2019-11-07 15:58:00','2027-10-09 16:17:00','','M'),
 (77,'Wuzi Khan','','','','','2019-11-07 15:59:00','2027-11-07 16:33:00','','A'),
 (79,'Ferdinand Fehlers','','','','','2019-11-07 15:59:00','2027-11-07 15:39:00','','A'),
 (82,'Submission','','','','','2019-11-07 16:01:00','2027-11-07 16:24:00','','A'),
 (86,'Lee Anderson','32','lee_and_kl.jpg','','peter%20fuert.jpg','2027-11-07 15:48:00','2015-08-08 14:48:00','','A'),
 (89,'Various Artists','','','','','2029-11-07 16:06:00','0000-00-00 00:00:00','','-'),
 (215,'Transforma & O.S.T.','32','Transforma-OST_kl.jpg','','Transforma-OST_(300dpi).jpg','2003-12-07 12:33:00','2015-08-08 17:30:00','','M'),
 (178,'Lab Generator','','','','','2003-12-07 12:33:00','0000-00-00 00:00:00','','A'),
 (239,'Warren Suicide','32','WS2_kl.jpg','','WS_RGB.JPG','2020-05-08 20:13:00','2015-08-08 17:33:00','','M'),
 (280,'Enduser','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (240,'Radio Birdman','','','','','2028-05-08 13:29:00','0000-00-00 00:00:00','','A'),
 (242,'Elliott Whitmore','','','','','2028-05-08 13:33:00','0000-00-00 00:00:00','','-'),
 (257,'Housemeister','91','','','','2005-06-08 13:36:00','0000-00-00 00:00:00','','-'),
 (246,'Justice','','','','','2028-05-08 13:36:00','0000-00-00 00:00:00','','-'),
 (248,'Wechsel Garland','','','','','2029-05-08 10:36:00','0000-00-00 00:00:00','','-'),
 (250,'DeWalta & Aquatic','','','','','2029-05-08 10:36:00','0000-00-00 00:00:00','','-'),
 (252,'Max Rouen','','','','','2029-05-08 10:36:00','0000-00-00 00:00:00','','-'),
 (254,'Pluramon','','','','','2029-05-08 10:36:00','0000-00-00 00:00:00','','A'),
 (256,'MÃ¤rz','','','','','2029-05-08 10:36:00','0000-00-00 00:00:00','','-'),
 (259,'Bus feat MC Soom-T','','','','','2009-06-08 18:04:00','0000-00-00 00:00:00','','g'),
 (261,'Khan','','','','','2002-07-08 13:15:00','0000-00-00 00:00:00','','-'),
 (263,'Zero Cash & Khan','','','','','2002-07-08 14:25:00','0000-00-00 00:00:00','','-'),
 (265,'Bohren & der Club of Gore','','','','','2017-07-08 22:23:00','0000-00-00 00:00:00','','-'),
 (282,'Cakebuilder','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (283,'Enduser/Larvae','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (277,'Chevron','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (284,'Scorn','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (286,'Mothboy','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (288,'AZ-Rotator','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (290,'Bong-Ra','','','','','2005-09-08 08:05:00','0000-00-00 00:00:00','','-'),
 (292,'CLP','32|999','clp_hoch_kl.jpg','clp_hochsprung_med.jpg','clp_hochsprung01.jpg','2011-09-08 14:14:00','2027-10-09 16:14:00','','A'),
 (294,'Felix','22','','','','2017-10-08 11:36:00','0000-00-00 00:00:00','','-'),
 (296,'Funckarma','','','','','2011-11-08 12:27:00','0000-00-00 00:00:00','','-'),
 (298,'DJ Flush','32','flush.gif','','','2004-12-08 17:05:00','2019-12-08 00:00:00','','A'),
 (300,'MGR','','','','','2029-01-09 13:42:00','0000-00-00 00:00:00','','-'),
 (302,'Long Distance Calling','','','','','2029-01-09 13:56:00','0000-00-00 00:00:00','','-'),
 (304,'Breandan Davey','93','','','','2026-02-09 14:07:00','0000-00-00 00:00:00','','-'),
 (306,'Bicoma','','','','','2026-02-09 16:36:00','0000-00-00 00:00:00','','-'),
 (308,'Motor','32','MOTOR-004_kl.jpg','','MOTOR-004%20by%20Timothy%20Saccenti.jpg','2003-04-09 12:34:00','2021-04-09 15:48:00','','M'),
 (310,'Math Head','','','','','2016-06-09 13:22:00','0000-00-00 00:00:00','','-'),
 (312,'Cardopusher','','','','','2016-06-09 13:23:00','0000-00-00 00:00:00','','-'),
 (314,'O.S.T.','999','o_s_t_kl.jpg','rnm_170_ost.jpg','chris_02.jpg','2017-09-09 13:39:00','2028-10-09 14:11:00','','g'),
 (316,'Anaphie','999','artist_anaphie_kl.jpg','rnm_170_anaphie.jpg','Andi_1_300.jpg','2013-10-09 13:25:00','2004-01-10 14:53:00','','-'),
 (318,'Khan Of Finland','94','','','','2026-10-09 16:48:00','0000-00-00 00:00:00','','-'),
 (320,'Kadrage','140','','','','2023-11-09 15:33:00','0000-00-00 00:00:00','','-'),
 (322,'Xenia Beliayeva','32|999','artist_xenia_kl.jpg','rnm_170_xenia.jpg','xenia_MG_5589_gr.JPG','2023-02-10 17:56:00','2012-04-10 11:41:00','','M'),
 (324,'Luciano','','','','','2011-03-10 16:18:00','0000-00-00 00:00:00','','-'),
 (326,'Never My Queen','235|999','artist_nevermyqueen_kl.jpg','rnm_170_nevermyqueen.jpg','NeverMyQueen_PressPix.jpg','2004-05-10 18:08:00','2012-04-11 14:19:00','','-'),
 (328,'Soul Center','142|32|999','artist_soulcenter_kl.jpg','rnm_170_soulcenter.jpg','Soulcenter.jpg','2001-07-10 16:23:00','2006-07-10 13:54:00','','M'),
 (330,'Post Industrial Boys','','','','','2001-07-10 18:34:00','0000-00-00 00:00:00','','-'),
 (332,'Ester Brinkmann','','','','','2001-07-10 18:34:00','0000-00-00 00:00:00','','-'),
 (340,'Plagia / Body Combat','','','','','2024-08-10 23:06:00','0000-00-00 00:00:00','','-'),
 (345,'Takeo Toyama','87','','','','2007-09-10 15:00:00','0000-00-00 00:00:00','','-'),
 (342,'AndrÃ¨s GarcÃ¬a','80','','','','2007-09-10 14:13:00','0000-00-00 00:00:00','','-'),
 (346,'Wells / Schneider/ Whitehead / M','87','','','','2007-09-10 15:11:00','2003-02-11 16:13:00','','-'),
 (348,'Toog','87','','','','2015-09-10 15:50:00','0000-00-00 00:00:00','','-'),
 (350,'Frank Bretschneider','32|999','bretschneider_kl.jpg','rnm_170_bretschneider.jpg','frank_bretschneider.jpg','2012-11-10 16:35:00','0000-00-00 00:00:00','','M'),
 (352,'Sorry Entertainers','32|999','artist_the-sorry-entertainers_kl.jpg','rnm_170_the-sorry-entertainers.jpg','the_sorry_entertainer.jpg','2001-12-10 16:57:00','2001-12-10 17:42:00','','M'),
 (354,'Matta','125','','','','2014-12-10 15:19:00','0000-00-00 00:00:00','','-'),
 (356,'Karsten Pflum','125','','','','2014-12-10 15:37:00','0000-00-00 00:00:00','','-'),
 (358,'Black Lung','125','','','','2014-12-10 16:08:00','0000-00-00 00:00:00','','-'),
 (360,'Lars & Gunnar Hemmerling','127','','','','2026-01-11 14:53:00','0000-00-00 00:00:00','','-'),
 (362,'Mark Boombastik & E. D. L.','32|999','artist_boombastik-lopez_kl.jpg','rnm_170_boombastk-lopez.jpg','M.Boombastik_E.Lopez_PressePix.jpg','2003-05-11 14:22:00','2014-06-11 16:43:00','','M'),
 (364,'Stig Inge','236','','','','2024-08-11 16:03:00','0000-00-00 00:00:00','','-'),
 (366,'Medic & Garoni','236','','','','2024-08-11 16:04:00','0000-00-00 00:00:00','','-');
/*!40000 ALTER TABLE `artistupdate` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
