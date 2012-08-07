-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vygenerováno: Stř 30. kvě 2012, 13:45
-- Verze MySQL: 5.5.20
-- Verze PHP: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `kerio_sprava_uzivatelu_a_linek`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `date`
--

CREATE TABLE IF NOT EXISTS `date` (
  `id` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `date`
--

INSERT INTO `date` (`id`, `time`) VALUES
(1, 1338376862),
(2, 1338378075);

-- --------------------------------------------------------

--
-- Struktura tabulky `line`
--

CREATE TABLE IF NOT EXISTS `line` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `number` int(10) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `line`
--

INSERT INTO `line` (`id`, `user_id`, `number`, `description`, `status`) VALUES
(9, 31, 10, 'linka 10', 1),
(10, 29, 11, 'linka', 1),
(11, 21, 12, 'linka 12', 1),
(12, 31, 13, 'erat', 1),
(13, 21, 14, 'linka 14', 1),
(14, 26, 15, 'frolik linka', 1),
(15, 18, 16, 'nakladal', 1),
(16, 0, 17, 'line 17', 0),
(17, 31, 18, 'linka 18', 1),
(18, 29, 19, '', 1),
(19, 13, 20, 'linka 20', 1),
(20, 10, 21, 'linka 21', 1),
(21, 16, 22, 'linka 22', 1),
(22, 24, 23, 'linka 23', 1),
(23, 25, 24, 'linka 24', 1),
(24, 11, 25, '', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `fullname`, `login`, `email`) VALUES
(10, 'Jakub KovÃ¡Å™', 'jkovar', ''),
(11, 'Jakub Å tÄ›pÃ¡nek', 'jstepanek', ''),
(13, 'OndÅ™ej NÄ›mec', 'onemec', ''),
(16, 'Miroslav BlaÅ¥Ã¡k', 'mblatak', ''),
(18, 'Jakub NaklÃ¡dal', 'jnakladal', ''),
(21, 'TomÃ¡Å¡ Plekanec', 'tplekanec', ''),
(24, 'David KrejÄÃ­', 'dkrejci', ''),
(25, 'Petr TenkrÃ¡t', 'ptenkrat', ''),
(26, 'Michael FrolÃ­k', 'mfrolik', ''),
(29, 'AleÅ¡ HemskÃ½', 'ahemsky', 'hemy@hemy.com'),
(31, 'Martin Erat', 'merat', 'mail@nekde.com');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
