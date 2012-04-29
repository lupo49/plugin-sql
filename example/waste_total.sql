SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `waste_total` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '#db_NoDisplay',
  `year` enum('1960','1970','1980','1990','2000','2005','2007','2008','2009','2010') NOT NULL COMMENT '#db_Filter ',
  `type` enum('generated','recovered','discarded') DEFAULT NULL COMMENT '#db_Filter ',
  `paper` mediumint(8) unsigned DEFAULT NULL,
  `glass` mediumint(8) unsigned DEFAULT NULL,
  `ferrous_metals` mediumint(8) unsigned DEFAULT NULL,
  `aluminum` mediumint(8) unsigned DEFAULT NULL,
  `other_non_ferrous_metals` mediumint(8) unsigned DEFAULT NULL,
  `plastic` mediumint(8) unsigned DEFAULT NULL,
  `rubber_leather` mediumint(8) unsigned DEFAULT NULL,
  `textiles` mediumint(8) unsigned DEFAULT NULL,
  `wood` mediumint(8) unsigned DEFAULT NULL,
  `other` mediumint(8) unsigned DEFAULT NULL,
  `food` mediumint(8) unsigned DEFAULT NULL,
  `yard` mediumint(8) unsigned DEFAULT NULL,
  `misc_inorganic` mediumint(8) unsigned DEFAULT NULL,
  `total` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

INSERT INTO `waste_total` (`id`, `year`, `type`, `paper`, `glass`, `ferrous_metals`, `aluminum`, `other_non_ferrous_metals`, `plastic`, `rubber_leather`, `textiles`, `wood`, `other`, `food`, `yard`, `misc_inorganic`, `total`) VALUES
(1, '1960', 'generated', 29990, 6720, 10300, 340, 180, 390, 1840, 1760, 3030, 70, 12200, 20000, 1300, 88120),
(2, '1970', 'generated', 44310, 12740, 12360, 800, 670, 2900, 2970, 2040, 3720, 770, 12800, 23200, 1780, 121060),
(3, '1980', 'generated', 55160, 15130, 12620, 1730, 1160, 6830, 4200, 2530, 7010, 2520, 13000, 27500, 2250, 151640),
(4, '1990', 'generated', 72730, 13100, 12640, 2810, 1100, 17130, 5790, 5810, 12210, 3190, 23860, 35000, 2900, 208270),
(5, '2000', 'generated', 87740, 12770, 14150, 3190, 1600, 25530, 6670, 9480, 13570, 4000, 29810, 30530, 3500, 242540),
(6, '2005', 'generated', 84840, 12540, 15210, 3330, 1860, 29250, 7290, 11510, 14790, 4290, 31990, 32070, 3690, 252660),
(7, '2007', 'generated', 82530, 12520, 15940, 3360, 1890, 30740, 7500, 12170, 15190, 4550, 32610, 32630, 3750, 255380),
(8, '2008', 'generated', 77420, 12150, 15960, 3410, 1960, 30070, 7590, 12710, 15400, 4670, 33340, 32900, 3780, 251360),
(9, '2009', 'generated', 68430, 11780, 15940, 3440, 1970, 29830, 7630, 13020, 15590, 4710, 34290, 33200, 3820, 243650),
(10, '2010', 'generated', 71310, 11530, 16900, 3410, 2100, 31040, 7780, 13120, 15880, 4790, 34760, 33400, 3840, 249860),
(11, '1960', 'recovered', 5080, 100, 50, 0, 0, 0, 330, 50, 0, 0, 0, 0, 0, 5610),
(12, '1970', 'recovered', 6770, 160, 150, 10, 320, 0, 250, 60, 0, 300, 0, 0, 0, 8020),
(13, '1980', 'recovered', 11740, 750, 370, 310, 540, 20, 130, 160, 0, 500, 0, 0, 0, 14520),
(14, '1990', 'recovered', 20230, 2630, 2230, 1010, 730, 370, 370, 660, 130, 680, 0, 4200, 0, 33240),
(15, '2000', 'recovered', 37560, 2880, 4680, 860, 1060, 1480, 820, 1320, 1370, 980, 680, 15770, 0, 69460),
(16, '2005', 'recovered', 41960, 2590, 5030, 690, 1280, 1780, 1090, 1840, 1830, 1210, 690, 19860, 0, 79850),
(17, '2007', 'recovered', 44480, 2880, 5280, 730, 1300, 2110, 1140, 1920, 2020, 1240, 810, 20900, 0, 84810),
(18, '2008', 'recovered', 42940, 2810, 5300, 720, 1360, 2140, 1130, 1910, 2110, 1300, 800, 21300, 0, 83820),
(19, '2009', 'recovered', 42500, 3000, 5270, 690, 1370, 2140, 1140, 1910, 2200, 1310, 850, 19900, 0, 82280),
(20, '2010', 'recovered', 44570, 3130, 5710, 680, 1480, 2550, 1170, 1970, 2300, 1410, 970, 19200, 0, 85140),
(21, '1960', 'discarded', 24910, 6620, 10250, 340, 180, 390, 1510, 1710, 3030, 70, 12200, 20000, 1300, 82510),
(22, '1970', 'discarded', 37540, 12580, 12210, 790, 350, 2900, 2720, 1980, 3720, 470, 12800, 23200, 1780, 113040),
(23, '1980', 'discarded', 43420, 14380, 12250, 1420, 620, 6810, 4070, 2370, 7010, 2020, 13000, 27500, 2250, 137120),
(24, '1990', 'discarded', 52500, 10470, 10410, 1800, 370, 16760, 5420, 5150, 12080, 2510, 23860, 30800, 2900, 175030),
(25, '2000', 'discarded', 50180, 9890, 9470, 2330, 540, 24050, 5850, 8160, 12200, 3020, 29130, 14760, 3500, 173080),
(26, '2005', 'discarded', 42880, 9950, 10180, 2640, 580, 27470, 6200, 9670, 12960, 3080, 31300, 12210, 3690, 172810),
(27, '2007', 'discarded', 38050, 9640, 10660, 2630, 590, 28630, 6360, 10250, 13170, 3310, 31800, 11730, 3750, 170570),
(28, '2008', 'discarded', 34480, 9340, 10660, 2690, 600, 27930, 6460, 10800, 13290, 3370, 32540, 11600, 3780, 167540),
(29, '2009', 'discarded', 25930, 8780, 10670, 2750, 600, 27690, 6490, 11110, 13390, 3400, 33440, 13300, 3820, 161370),
(30, '2010', 'discarded', 26740, 8400, 11190, 2730, 620, 28490, 6610, 11150, 13580, 3380, 33790, 14200, 3840, 164720);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
