-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:8889
-- Tiempo de generación: 13-08-2015 a las 20:41:21
-- Versión del servidor: 5.5.38
-- Versión de PHP: 5.6.2

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `craigslist_tool`
--
DROP DATABASE IF EXISTS `craigslist_tool`;
CREATE DATABASE IF NOT EXISTS `craigslist_tool` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `craigslist_tool`;

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `geodist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `geodist`(IN `mylat` DOUBLE, IN `mylon` DOUBLE, IN `dist` INT)
begin

declare lon1 float;
declare lon2 float;
declare lat1 float;
declare lat2 float;

set lon1 = mylon-dist/abs(cos(radians(mylat))*69);
set lon2 = mylon+dist/abs(cos(radians(mylat))*69);
set lat1 = mylat-(dist/69);
set lat2 = mylat+(dist/69);

-- calculate lon and lat for the rectangle

-- run the query
SELECT tbl_area.name, tbl_area.lat, tbl_area.lng, getdistance(mylat, mylon, tbl_area.lat, tbl_area.lng) AS minDistance

FROM tbl_area

WHERE tbl_area.lng BETWEEN lon1 AND lon2 AND tbl_area.lat BETWEEN lat1 AND lat2

HAVING minDistance < dist 

ORDER BY minDistance;

end$$

--
-- Funciones
--
DROP FUNCTION IF EXISTS `getdistance`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `getdistance`(`mylat` DOUBLE, `mylon` DOUBLE, `geo_lat` DOUBLE, `geo_lng` DOUBLE) RETURNS double
    NO SQL
return 1.60934 * (3956 * 2 * ASIN(SQRT( POWER(SIN((mylat - abs(geo_lat)) * pi()/180 / 2), 2) + COS(mylat * pi()/180) * COS(abs(geo_lat) * pi()/180) * POWER(SIN((mylon - geo_lng) * pi()/180 / 2), 2) )))$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_area`
--

DROP TABLE IF EXISTS `tbl_area`;
CREATE TABLE `tbl_area` (
`id_area` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_area`
--

INSERT INTO `tbl_area` (`id_area`, `name`, `lat`, `lng`) VALUES
(1, 'Phoenix - Central/South Phx - AZ', 33.4372686, -112.0077881),
(2, 'Phoenix - East Valley - AZ', 33.5443977, -111.9272222),
(3, 'Phoenix - Phx North - AZ', 33.4372686, -112.0077881),
(4, 'West Valley - AZ', 34.400014, -110.578623),
(5, 'Vancouver - Vancouver', 49.2827291, -123.1207375),
(6, 'Vancouver - North Shore', 49.3269904, -123.0732801),
(7, 'Vancouver - Burnaby/Newwest', 49.2237214, -122.9428505),
(8, 'Vancouver - Delta/Surrey/Langley', 49.103931, -122.729141),
(9, 'Vancouver - Tricities/Pitt/Maple', 45.6387281, -122.6614861),
(10, 'Vancouver - Richmond', 49.1665898, -123.133569),
(11, 'San Diego - City of San Diego - CA', 32.715738, -117.1610838),
(12, 'San Diego - North San Diego County - CA', 32.715738, -117.1610838),
(13, 'San Diego - East San Diego County - CA', 32.7494952, -117.1100315),
(14, 'San Diego - South San Diego County - CA', 32.715738, -117.1610838),
(15, 'Los Angeles - Westside-Southbay-310 - CA', 33.9172396, -118.4161994),
(16, 'Los Angeles - San Fernando Valley - CA', 34.1825782, -118.4396756),
(17, 'Los Angeles - Central LA 213/323 - CA', 33.7418838, -118.310385),
(18, 'Los Angeles - San Gabriel Valley - CA', 34.0333443, -118.0370113),
(19, 'Los Angeles - Long beach/562 - CA', 33.7737543, -118.1892923),
(20, 'Los Angeles - Antelope Valley - CA', 34.7513712, -118.2522977),
(21, 'Inland Empire - CA', 34.9592083, -116.419389),
(22, 'Orange County - CA', 33.7174708, -117.8311428),
(23, 'Sacramento - CA', 38.5815719, -121.4943996),
(24, 'Denver - CO', 39.7392358, -104.990251),
(25, 'Washington - District of Columbia - DC', 38.9071923, -77.0368707),
(26, 'Washington - Northern Virginia - DC', 38.920855, -76.99679),
(27, 'Washington - Maryland - DC', 38.9071923, -77.0368707),
(28, 'Ft Myers/SW Florida - Lee County - FL', 26.5337051, -81.7553083),
(29, 'Ft Myers/SW Florida - Charlotte County - FL', 26.8786614, -82.212151),
(30, 'Ft Myers/SW Florida - Collier County - FL', 26.12441, -81.77164),
(31, 'Tampa Bay Area - Hernando Co - FL', 28.014226, -82.5648151),
(32, 'Tampa Bay Area - Hillsborough Co - FL', 27.9483921, -82.4549055),
(33, 'Tampa Bay Area - Pasco Co - FL', 28.1934361, -82.6205723),
(34, 'Tampa Bay Area - Pinellas Co - FL', 27.891411, -82.789269),
(35, 'Jacksonville - FL', 30.3321838, -81.655651),
(36, 'Orlando - FL', 28.5383355, -81.3792365),
(37, 'Atlanta - City of Atlanta - GA', 33.7488933, -84.3903931),
(38, 'Atlanta - Otp North - GA', 32.1656221, -82.9000751),
(39, 'Atlanta - Otp East - GA', 32.1656221, -82.9000751),
(40, 'Atlanta - Otp South - GA', 32.1656221, -82.9000751),
(41, 'Atlanta - Otp West - GA', 32.1656221, -82.9000751),
(42, 'Hawaii - Honolulu, Oahu - HI', 21.4389123, -158.0000565),
(43, 'Hawaii - Big Island - HI', 19.5429151, -155.6658568),
(44, 'Hawaii - Maui - HI', 20.7983626, -156.3319253),
(45, 'Hawaii - Kauai - HI', 21.9661076, -159.5737912),
(46, 'Hawaii - Molokai - HI', 21.1443935, -157.0226297),
(47, 'Chicago - City of Chicago- IL', 41.994681, -87.7130582),
(48, 'Chicago - North Chicagoland - IL', 41.909592, -87.7443196),
(49, 'Chicago - West Chicagoland- IL', 41.9236777, -87.8502394),
(50, 'Chicago - South Chicagoland - IL', 41.8568061, -87.6146268),
(51, 'Chicago - Northwest Indiana - IL', 41.8781136, -87.6297982),
(52, 'Chicago - Northwest Suburbs - IL', 41.7686035, -87.8143146),
(53, 'Indianapolis - IN', 39.768403, -86.158068),
(54, 'Baltimore - MD', 39.2903848, -76.6121893),
(55, 'Boston - Boston/Cambridge/Brookline - MA', 42.3284483, -71.0854325),
(56, 'Boston - Northwest/Merrimack - MA', 42.3633101, -71.0606965),
(57, 'Boston - Metro West - MA', 42.2959267, -71.7128471),
(58, 'Boston - North Shore - MA', 42.4088429, -70.9934588),
(59, 'Boston - South Shore - MA', 42.2951319, -71.1224877),
(60, 'Detroit Metro - Macomb County - MI', 42.625575, -83.05149),
(61, 'Detroit Metro - Wayne County - MI', 42.2161722, -83.3553842),
(62, 'Detroit Metro - Oakland County - MI', 42.4899099, -83.1552791),
(63, 'Minneapolis/St. Paul - Hennepin County - MN', 44.8847554, -93.2222846),
(64, 'Minneapolis/St. Paul - Ramsey County - MN', 44.9716692, -93.2806134),
(65, 'Minneapolis/St. Paul - Anoka/Chisago/Isanti - MN', 45.4894008, -93.2476091),
(66, 'Minneapolis/St. Paul - Washington Co/WI - MN', 44.9389111, -93.1177555),
(67, 'Minneapolis/St. Paul - Dakota/Scott - MN', 44.6834072, -93.5407909),
(68, 'Minneapolis/St. Paul - Carver/Sherburne/Wright - MN', 46.6715593, -93.0074403),
(69, 'Kansas City, MO - MO', 39.0997265, -94.5785667),
(70, 'St Louis - MO', 38.6270025, -90.1994042),
(71, 'Las Vegas - NV', 36.1699412, -115.1398296),
(72, 'New Hampshire - NH', 43.1938516, -71.5723953),
(73, 'Central NJ - NJ', 39.829728, -74.9789595),
(74, 'North Jersey - NJ', 40.9594292, -74.221728),
(75, 'South Jersey - NJ', 39.9399353, -74.8440776),
(76, 'Long Island - NY', 40.7891424, -73.1349605),
(77, 'New York City - Manhattan - NY', 40.7830603, -73.9712488),
(78, 'New York City - Brooklyn - NY', 40.6781784, -73.9441579),
(79, 'New York City - Queens - NY', 40.7282239, -73.7948516),
(80, 'New York City - Bronx - NY', 40.8447819, -73.8648268),
(81, 'New York City - Staten Island - NY', 40.5795317, -74.1502007),
(82, 'New York City - New Jersey - NY', 40.7127837, -74.0059413),
(83, 'New York City - Long Ssland - NY', 40.7002522, -73.8080783),
(84, 'New York City - Westchester - NY', 40.7127837, -74.0059413),
(85, 'New York City - Fairfield Co, CT - NY', 41.0504238, -73.5320808),
(86, 'Charlotte - NC', 35.2270869, -80.8431267),
(87, 'Raleigh/Durham/CH - NC', 35.728815, -78.644926),
(88, 'Cincinnati - OH', 39.1031182, -84.5120196),
(89, 'Cleveland - OH', 41.49932, -81.6943605),
(90, 'Columbus, OH - OH', 39.9611755, -82.9987942),
(91, 'Oklahoma City - OK', 35.4675602, -97.5164276),
(92, 'Tulsa - OK', 36.1539816, -95.992775),
(93, 'Portland - Multnomah County - OR', 45.5230622, -122.6764816),
(94, 'Portland - Washington County - OR', 45.5230622, -122.6764816),
(95, 'Portland - Clark/Cowlitz WA - OR', 45.5230622, -122.6764816),
(96, 'Portland - Clackamas County - OR', 45.5230622, -122.6764816),
(97, 'Portland - North Coast - OR', 45.5390719, -122.672282),
(98, 'Portland - Yamhill Co - OR', 45.3098299, -122.936101),
(99, 'Portland - Columbia Gorge - OR', 45.6673206, -122.8674949),
(100, 'Philadelphia - PA', 39.9525839, -75.1652215),
(101, 'Pittsburgh - PA', 40.4406248, -79.9958864),
(102, 'Nashville - TN', 36.1626638, -86.7816016),
(103, 'Hampton Roads - VA', 36.7774935, -76.4471535),
(104, 'Richmond - VA', 37.5407246, -77.4360481),
(105, 'Seattle-tacoma - Seattle - WA', 47.4502499, -122.3088165),
(106, 'Seattle-tacoma - Eastside - WA', 47.2111983, -122.4144869),
(107, 'Seattle-tacoma - Snohomish County - WA', 48.0329979, -121.8339472),
(108, 'Seattle-tacoma - Kitsap/West Puget - WA', 48.7415588, -122.4559598),
(109, 'Seattle-tacoma - Tacoma/Pierce - WA', 47.2521173, -122.4374518),
(110, 'Seattle-tacoma - Olympia/Thurston - WA', 47.0378741, -122.9006951),
(111, 'Seattle-tacoma - South King Co - WA', 47.4502499, -122.3088165),
(112, 'Milwaukee - WI', 43.0389025, -87.9064736),
(113, 'Dallas/Fort Worth - Dallas - TX', 32.761725, -96.8573702),
(114, 'Dallas/Fort Worth - Dort Worth - TX', 32.7378766, -97.2364147),
(115, 'Dallas/Fort Worth - Mid Cities - TX', 32.8672501, -97.0641303),
(116, 'Dallas/Fort Worth - North DFW - TX', 32.8998091, -97.0403352),
(117, 'Dallas/Fort Worth - South DFW - TX', 32.8998091, -97.0403352),
(118, 'Houston - TX', 29.7604267, -95.3698028),
(119, 'San Antonio - TX', 29.4241219, -98.4936282),
(120, 'Austin - TX', 30.267153, -97.7430608),
(121, 'South Florida - FL', 27.6648274, -81.5157535);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_area`
--
ALTER TABLE `tbl_area`
 ADD PRIMARY KEY (`id_area`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_area`
--
ALTER TABLE `tbl_area`
MODIFY `id_area` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=122;SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
