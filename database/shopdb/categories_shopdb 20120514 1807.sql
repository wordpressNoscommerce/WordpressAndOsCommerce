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
-- Definition of table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `categories_id` int(11) NOT NULL auto_increment,
  `categories_image` varchar(64) default NULL,
  `parent_id` int(11) NOT NULL default '0',
  `sort_order` int(3) default NULL,
  `date_added` datetime default NULL,
  `last_modified` datetime default NULL,
  PRIMARY KEY  (`categories_id`),
  KEY `idx_categories_parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=245 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`categories_id`,`categories_image`,`parent_id`,`sort_order`,`date_added`,`last_modified`) VALUES 
 (22,'meteologo.pdf',0,0,'2007-11-11 00:05:03','2008-08-04 15:46:42'),
 (33,'',0,0,'2007-12-03 00:23:29','2008-08-04 15:45:30'),
 (32,'shitlogo.jpg',0,3,'2007-12-03 00:09:30','2008-08-13 17:12:57'),
 (96,'',0,0,'2008-07-17 22:22:34',NULL),
 (94,'',0,0,'2008-07-02 13:14:41',NULL),
 (93,'',0,0,'2008-06-20 13:37:38',NULL),
 (92,'',0,0,'2008-06-09 17:37:24',NULL),
 (91,'',0,0,'2008-06-05 13:34:40',NULL),
 (89,'',0,0,'2008-05-28 13:24:53',NULL),
 (86,'',0,0,'2008-02-18 14:07:58',NULL),
 (87,'',0,0,'2008-05-27 14:57:34',NULL),
 (88,'',0,0,'2008-05-27 14:58:39',NULL),
 (80,'',0,0,'2008-01-31 14:40:46',NULL),
 (79,'',0,0,'2008-01-31 14:39:43',NULL),
 (78,'',0,0,'2008-01-31 14:38:44',NULL),
 (77,'',0,0,'2008-01-28 23:00:58',NULL),
 (76,'',0,0,'2008-01-28 22:59:50',NULL),
 (136,'',0,0,'2008-11-10 14:51:08',NULL),
 (125,'',0,0,'2008-09-05 08:02:03',NULL),
 (127,'',0,0,'2008-10-22 15:05:40',NULL),
 (137,'',0,0,'2008-11-13 16:34:30',NULL),
 (118,'',0,0,'2008-07-17 22:39:52',NULL),
 (117,'',0,0,'2008-07-17 22:38:43',NULL),
 (140,'',0,0,'2009-01-22 13:52:48',NULL),
 (141,'',0,0,'2009-01-27 15:14:23',NULL),
 (142,'',0,0,'2010-03-09 14:18:44',NULL),
 (236,'',0,0,'2011-08-24 15:01:22',NULL),
 (235,'',0,0,'2011-03-29 15:26:13',NULL),
 (234,'',0,0,'2011-02-17 16:35:49',NULL),
 (233,'',0,0,'2010-11-11 17:05:41',NULL),
 (237,'',0,0,'2011-09-23 14:42:24',NULL),
 (238,'',0,0,'2011-10-20 13:13:44',NULL),
 (239,'',0,0,'2011-11-17 15:49:21',NULL),
 (240,'',0,0,'2012-01-10 17:19:52',NULL),
 (241,'',0,0,'2012-04-12 13:44:18',NULL),
 (242,'',0,0,'2012-05-02 15:58:36',NULL),
 (243,'',0,1000,'2012-05-14 17:30:37',NULL),
 (244,'',0,1001,'2012-05-14 17:31:14',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;


--
-- Definition of table `categories_description`
--

DROP TABLE IF EXISTS `categories_description`;
CREATE TABLE `categories_description` (
  `categories_id` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '1',
  `categories_name` varchar(32) NOT NULL,
  `categories_description` text,
  `categories_htc_title_tag` varchar(80) default NULL,
  `categories_htc_desc_tag` longtext,
  `categories_htc_keywords_tag` longtext,
  `categories_htc_description` longtext,
  PRIMARY KEY  (`categories_id`,`language_id`),
  KEY `idx_categories_name` (`categories_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories_description`
--

/*!40000 ALTER TABLE `categories_description` DISABLE KEYS */;
INSERT INTO `categories_description` (`categories_id`,`language_id`,`categories_name`,`categories_description`,`categories_htc_title_tag`,`categories_htc_desc_tag`,`categories_htc_keywords_tag`,`categories_htc_description`) VALUES 
 (32,2,'Shitkatapult','Shitkatapult... why not?<br /><br />10 years of the greatest business in the world (= no money, much stress) can do us no harm!!<br />Shitkatapult and its side arms Musick, Meteosound and the publishing company Random Noize Musick have released over 100 records and what can we say, they are all great. We could not achieve world domination, as originally planned, but at least we can offer a very special brand of music and our understanding of running a good record label: we do what we like!!! Whether it&rsquo;s cutting edge electronica, raw tech-rock, electronic composing, dubdope beats or fragile ambient epics.<br />&nbsp;<br />What else? Shitkatapult is always a decision: we do not play easy to get, we do not hype the hype.<br />But if you like us, you stay with us, as the whole appearance from production, titles, cover artworks and performances is a rich program for special minds. <br />Even if the music industry as a whole leaks and sinks, we will continue to introduce new artists and new musical ideas to the world through our imprints Shitkatapult, Musick and also Meteosound which has just got another kiss of life with a mainly digital release series of abstract downbeat and soultech releases for the future. <br />So, order some T-shirts at Shopkatapult.com and don&rsquo;t watch too much TV. There are better things to do!<br />&nbsp;<br />For your records: Shitkatapult was founded in 1997 by Marco Haas, also known as T.Raumschmiere, and some of his friends. After moving to Berlin, Sascha Ring aka Apparat soon joined in and Daniel Meteo completed the new owner trio.<br />Since 2007, Shitkatapult has been functioning under the roof of Random Noize Musick GmbH which includes all the three labels and the publishing company. Sascha Ring got sick of label office work and, after leaving the company, he now concentrates on his musical work. Our friend Florian Schmieg took Sascha\'s part and we are all happy now! <br />Please visit our new websites which will be &quot;on&quot; soon. 2008 &ndash; the year of contact.<br />&nbsp;<br />Thanks sirs and madams,<br />Your surely mad ants from Berlin. <br /><br />','Shitkatapult','Shitkatapult','Shitkatapult Shopkatapult t.raumschmiere apparat musick meteosound shopkatapult','10 years of the greatest\nbusiness in the world (= no\nmoney, much stress) can do us\nno harm!!\nShitkatapult and its side arms\nMusick, Meteosound and the\npublishing company Random\nNoize Musick have released\nover 100 records and what can\nwe say, they are all great. We\ncould not achieve world\ndomination, as originally\nplanned, but at least we can\noffer a very special brand of\nmusic and our understanding of\nrunning a good record label:\nwe do what we like!!! Whether\nit’s cutting edge electronica,\nraw tech-rock, electronic\ncomposing, dubdope beats or\nfragile ambient epics.'),
 (22,2,'Meteosound','METEOSOUND<br />was founded in 2001 by DANIEL METEO as&nbsp; BERLIN dub electronica label. Marketed by SELECTED CUTS/ECHO BEACH (a.o. Selected Cuts from Blood and Fire...) and released several albums by UK dub producer THE ROOTSMAN and lounged a compilation series that featured unique modern dub productions. <br />In 2006 METEOSOUND became part of RANDOM NOIZE MUSICK (SHITKATAPULT, MUSICK, METEOSOUND, RANDOM NOIZE PUBLISHING). Distributed by MDM the Berlin Label output changed into dub influenced electronic music production of the time, as its series soul dub city or diverse albums and vinyl eps featuring artists like METEO/THIEL, ECHO DEPTH FINDERS, FELIX, AMERIKA RUBY, DANIEL METEO and many more. in 2007 the label started an own digital only release series called DEMAND and continues releasing downbeat, abstract, dubby productions of tomorrows electronic music scene. <br /><br />','Meteosound','Meteosound','Meteosound Shitkatapult Musick Shopkatapult Daniel Meteo Apparat',''),
 (33,2,'Musick to play in the club','MUSICK TO PLAY IN THE CLUB<br />was founded by the SHITKATAPULT crew in 2004 in order to build a new platform for clubmusic releases, dj stuff, raw techno and rave productions. MUSICK so far presented a great line up of 12&quot; vinyl releases by artists like SHRUBBN!!, DJ FLUSH, JERRY ABSTRACT, PETER GRUMMICH, MIKE FUZZ and DAVE TARRIDA, HAKAN LIDBO, T.RAUMSCHMIERE and many many more. Straight forward club releases coming with simple label packaging and fresh energy. <br />In 2007 the first two albums appeared (by MAGNUM 38 and HAKAN LIDBO), as well as the MUSICK dj mix compilation by DJ FLUSH in 2006. <br />At the same time the mothership label SHITKATAPULT started to concentrate more and more on artist albums, songwriting electronica and general label work far away from strictly genre schemes. <br /><br />','Musick to play in the club','Musick to play in the club','Musick to play in the club',''),
 (78,2,'Angora Steel',NULL,'Angora Steel releases','Angora Steel releases','Angora Steel','Our releases on Angora Steel'),
 (77,2,'Hefty',NULL,'Hefty Releases','Hefty Releases','Hefty,T.Raumschmiere','T.Raumschmieres releases on \nHefty'),
 (76,2,'Novamute',NULL,'Novamute Releases','Novamute Releases','Novamute,T.Raumschmiere','Releases of T. Raumschmiere \non Novamute'),
 (79,2,'Kompakt',NULL,'Kompakt releases','Kompakt releases','Kompakt, Shitkatapult','Shitkatapult releases on \nKompakt'),
 (80,2,'Kalkpets',NULL,'Kalkpets releases','Kalkpets releases','Kalkpets, Shitkatapult','Shitkatapult releases on \nKalkpets'),
 (86,2,'Sender',NULL,'Releases on Sender','Releases on Sender','Releases,Sender,T.Raumschmiere','Our Releases on Sender'),
 (87,2,'Karaokekalk','','Karaoke Kalk - Berlin - Indie / Electronica / House','Karaoke Kalk Songs, Videos, Downloads','Musik, Musik hochladen, Musik runterladen, Musik hören, Musik bewerten, Bands, Videos, Video','Karaoke Kalk Songs, Videos, \nDownloads'),
 (88,2,'Echochord','','Echochord releases','Echochord releases to buy on Shopkatapult','Echochord','Echochord releases to buy on \nShopkatapult'),
 (89,2,'Bendertainment','','Bendertainment','Bendertainment','Bendertainment',''),
 (91,2,'Allyoucanbeat','','Allyoucanbeat','Allyoucanbeat','Allyoucanbeat',''),
 (92,2,'Scape','','Scape Records @ Shopkatapult','Scape','Scape',''),
 (93,2,'Eintakt','','Eintakt','Eintakt','Eintakt',''),
 (94,2,'I\'m Single','','I\'m Single','I\'m Single','I\'m Single',''),
 (96,2,'Louisville','<p><strong>Louisville Records</strong> (gegr&uuml;ndet Sommer <a title=\"2004\" href=\"index.php/2004\"><u><font color=\"#0000ff\">2004</font></u></a>) besteht aus zwei Leuten, die in einer Hinterhof Remise in Rheinsberger Stra&szlig;e <a title=\"Berlin\" href=\"index.php/Berlin\"><u><font color=\"#0000ff\">Berlin</font></u></a> sitzen. <a title=\"Yvonne Franken\" href=\"index.php?title=Yvonne_Franken&amp;action=edit\"><u><font color=\"#0000ff\">Yvonne Franken</font></u></a> war vorher Promoterin bei <a title=\"Universal\" href=\"index.php/Universal\"><u><font color=\"#0000ff\">Universal</font></u></a> und <a title=\"Patrick Wagner\" href=\"index.php/Patrick_Wagner\"><u><font color=\"#0000ff\">Patrick Wagner</font></u></a> war Mitbegr&uuml;nder von <a title=\"Kitty-Yo\" href=\"index.php?title=Kitty-Yo&amp;action=edit\"><u><font color=\"#0000ff\">Kitty-Yo</font></u></a> und A&amp;R bei <a title=\"Motor\" href=\"index.php/Motor\"><u><font color=\"#0000ff\">Motor</font></u></a> Music. Das Unternehmen Louisville Records besitzt seit 2006 einen Subverlag bei der <a title=\"EMI\" href=\"index.php/EMI\"><u><font color=\"#0000ff\">EMI</font></u></a> mit den Namen <a title=\"Louisville Publishing\" href=\"index.php?title=Louisville_Publishing&amp;action=edit\"><u><font color=\"#0000ff\">Louisville Publishing</font></u></a> .</p>','Louisville records releases','Releases of Louisville Records at Shopkatapult','Louisville records, releases, shopkatapult','Releases of Louisville Records \nat Shopkatapult'),
 (118,2,'Weiser','','Weiser Releases at Shopkatapult','Weiser Releases at Shopkatapult','Weiser, Label,Releases,Shopkatapult','Weiser Releases at \nShopkatapult'),
 (117,2,'Wonder','','Wonder Releases at Shopkatapult','Wonder Releases at Shopkatapult','Wonder, Label, Releases','Wonder Releases at \nShopkatapult'),
 (125,2,'Ad Noiseam','Ad Noiseam is an experimental electronic music independent record label, based in Berlin, Germany. Ad Noiseam was founded in April of 2001 by Nicolas Chevreux and has released music by artists such as Tarmvred, Somatic Responses, Antigen Shift, Exillon, Cdatakill, Iszoloscope, Detritus, Mothboy, Enduser, Bong-Ra, Shitmat, Knifehandchop, D&auml;lek, Ra and Line 47.','Ad Noiseam releases on Shopkatapult','Ad Noiseam is an experimental electronic music independent record label, based in Berlin, Germany. Ad Noiseam was founded in April of 2001 by Nicolas Chevreux and has released music by artists such as Tarmvred, Somatic Responses, Antigen Shift, Exillon, Cdatakill, Iszoloscope, Detritus, Mothboy, Enduser, Bong-Ra, Shitmat, Knifehandchop, Dälek, Ra and Line 47.','Ad Noiseam,experimental,electronic music,independent,record label,Berlin,Germany,Nicolas Chevreux,Tarmvred,Somatic Responses,Antigen Shift,Exillon,Cdatakill,Iszoloscope,Detritus,Mothboy,Enduser,Bong-Ra,Shitmat,Knifehandchop,Dälek,Ra,Line 47','Ad Noiseam is an \nexperimental electronic music \nindependent record label, \nbased in Berlin, Germany. Ad \nNoiseam was founded in April \nof 2001 by Nicolas Chevreux \nand has released music by \nartists such as Tarmvred, \nSomatic Responses, Antigen \nShift, Exillon, Cdatakill, \nIszoloscope, Detritus, \nMothboy, Enduser, Bong-Ra, \nShitmat, Knifehandchop, \nDälek, Ra and Line 47.'),
 (127,2,'Dock','','Dock','Dock','Dock','Dock'),
 (136,2,'Detroit Underground','','Detroit Underground','Detroit Underground','Detroit Underground',''),
 (137,2,'Fume','','Fume','Fume','Fume',''),
 (140,2,'Viva Hate Records','','Viva Hate Records','Viva Hate Records','Viva Hate Records',''),
 (141,2,'Discograph','','Discograph','Discograph','Discograph',''),
 (142,2,'Max.Ernst','','Max.Ernst','Max.Ernst','Max.Ernst',''),
 (236,2,'ZCKR Records','','ZCKR Records','ZCKR Records','ZCKR Records',''),
 (235,2,'Cupcake Records','','Cupcake Records','Cupcake Records','Cupcake Records',''),
 (234,2,'Army Of The Universe','','Army Of The Universe','Army Of The Universe','Army Of The Universe',''),
 (233,2,'!K7','','!K7','!K7','!K7',''),
 (237,2,'Mute','','Mute','Mute','Mute',''),
 (238,2,'Notic Nastic','','Notic Nastic','Notic Nastic','Notic Nastic',''),
 (239,2,'Killekill','','Killekill','Killekill','Killekill',''),
 (240,2,'Thrill Jockey Records','','Thrill Jockey Records','Thrill Jockey Records','Thrill Jockey Records',''),
 (241,2,'Tokuma Japan Communications','','Tokuma Japan Communications','Tokuma Japan Communications','Tokuma Japan Communications',''),
 (242,2,'Form & Function','','Form & Function','Form & Function','Form & Function',''),
 (243,2,'Main','Main shitkatapult artists','Main','Main','Main',''),
 (244,2,'Alumni','shitkatapult Alumni Artists','Alumni','Alumni','Alumni','');
/*!40000 ALTER TABLE `categories_description` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
