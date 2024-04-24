-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Apr 24, 2024 alle 10:32
-- Versione del server: 8.0.36-0ubuntu0.22.04.1
-- Versione PHP: 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `design_palette`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `likes`
--

CREATE TABLE `likes` (
  `id_like` int NOT NULL,
  `id_utente` int NOT NULL DEFAULT '0',
  `id_palette` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dump dei dati per la tabella `likes`
--

INSERT INTO `likes` (`id_like`, `id_utente`, `id_palette`) VALUES
(6, 10, 12),
(7, 10, 10),
(9, 10, 7),
(11, 11, 4),
(13, 11, 16),
(14, 11, 5),
(16, 11, 6),
(19, 10, 20),
(20, 12, 4),
(21, 12, 5),
(22, 12, 6),
(23, 12, 9),
(24, 12, 3),
(25, 12, 8),
(26, 12, 12),
(27, 12, 13),
(28, 12, 14),
(30, 10, 27),
(34, 10, 16),
(37, 11, 21),
(38, 11, 20),
(39, 11, 3),
(40, 11, 22),
(42, 10, 19),
(43, 10, 28),
(45, 10, 33),
(46, 11, 14),
(47, 11, 12),
(48, 11, 11),
(49, 11, 7),
(50, 11, 17),
(51, 11, 23),
(52, 11, 30),
(53, 11, 32),
(54, 11, 33),
(55, 11, 36),
(56, 11, 34),
(57, 11, 28),
(58, 13, 9),
(59, 13, 11),
(60, 13, 3),
(61, 13, 4),
(62, 13, 5),
(63, 13, 8),
(64, 13, 33),
(65, 13, 32),
(66, 13, 6),
(67, 13, 10),
(68, 13, 27),
(69, 13, 12),
(70, 13, 13),
(71, 13, 14),
(72, 13, 16),
(73, 13, 7),
(74, 13, 15),
(75, 13, 17),
(76, 13, 22),
(77, 13, 21),
(78, 13, 20),
(79, 13, 19),
(80, 12, 7),
(81, 14, 37),
(82, 14, 5),
(83, 10, 22),
(84, 10, 18),
(138, 15, 15),
(139, 15, 17),
(145, 15, 5),
(181, 10, 6),
(183, 10, 8),
(184, 11, 13),
(187, 10, 14),
(193, 10, 46),
(194, 11, 46),
(195, 11, 31),
(196, 12, 31),
(200, 10, 4),
(202, 18, 46),
(203, 18, 5),
(204, 18, 13),
(205, 18, 4),
(206, 18, 8),
(207, 18, 53),
(215, 10, 57),
(216, 19, 31),
(217, 27, 57),
(218, 27, 31),
(219, 34, 5),
(220, 34, 9),
(221, 35, 62),
(222, 34, 57),
(223, 34, 49),
(227, 34, 42),
(228, 34, 30),
(229, 34, 47),
(231, 34, 52),
(232, 34, 59),
(235, 37, 58),
(236, 37, 26),
(240, 10, 5),
(241, 10, 13),
(246, 10, 24),
(272, 11, 65),
(274, 41, 67),
(275, 41, 68),
(291, 26, 67),
(295, 10, 90),
(296, 10, 91),
(297, 10, 61),
(311, 10, 71),
(330, 10, 65),
(335, 10, 31),
(338, 10, 97),
(339, 10, 96),
(342, 42, 68),
(343, 10, 99),
(344, 42, 91),
(345, 42, 99),
(346, 48, 67),
(347, 48, 68),
(348, 42, 100),
(349, 49, 65),
(369, 10, 67),
(374, 42, 93),
(375, 42, 71),
(376, 10, 3),
(377, 55, 67),
(378, 55, 62),
(380, 42, 90),
(382, 56, 133),
(383, 10, 68),
(384, 42, 67);

-- --------------------------------------------------------

--
-- Struttura della tabella `palettes`
--

CREATE TABLE `palettes` (
  `id_palette` int NOT NULL,
  `color1` varchar(32) NOT NULL,
  `color2` varchar(32) NOT NULL,
  `color3` varchar(32) NOT NULL,
  `color4` varchar(32) NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `creating_user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dump dei dati per la tabella `palettes`
--

INSERT INTO `palettes` (`id_palette`, `color1`, `color2`, `color3`, `color4`, `likes`, `creating_user_id`) VALUES
(9, '#24a8f0', '#606e71', '#585459', '#F7EEDD', 3, 10),
(26, '#7390a0', '#b1cace', '#9db4b3', '#bdb7ad', 1, 10),
(31, '#db6300', '#e2d540', '#dee2ac', '#f3f7de', 9, 10),
(37, '#8eb3c7', '#c0d9dd', '#b7d7d7', '#e9e3d8', 1, 14),
(42, '#98b0bd', '#b6c6c9', '#c1e1e1', '#c6b89f', 1, 15),
(53, '#db0000', '#43e240', '#e2acac', '#def7ee', 1, 18),
(57, '#008DDA', '#6133e4', '#d38301', '#f3ec4e', 3, 10),
(58, '#008DDA', '#41C9E2', '#ACE2E1', '#F7EEDD', 1, 19),
(59, '#008DDA', '#41C9E2', '#ACE2E1', '#F7EEDD', 1, 27),
(61, '#008DDA', '#7AA2E3', '#41C9E2', '#ACE2E1', 1, 34),
(62, '#ffab01', '#f5ec00', '#fefb41', '#fff994', 7, 35),
(65, '#5bbcff', '#fffab7', '#ffd1e3', '#7ea1ff', 25, 11),
(66, '#1c1678', '#8576ff', '#7bc9ff', '#a3ffd6', 13, 11),
(67, '#fa7070', '#fefded', '#c6ebc5', '#a1c398', 21, 10),
(68, '#401f71', '#824d74', '#be7b72', '#fdaf7b', 18, 10),
(69, '#7e62a7', '#d651b3', '#ce9992', '#efc8af', 7, 10),
(71, '#86469c', '#bc7fdc', '#fb9ad1', '#ffcdea', 12, 10),
(72, '#9e1dc9', '#af50e2', '#ec55ab', '#f495cc', 5, 10),
(84, '#738791', '#879da1', '#808e8e', '#d7d5d0', 0, 41),
(86, '#008DDA', '#41C9E2', '#ACE2E1', '#F7EEDD', 0, 10),
(89, '#d9edbf', '#ff9800', '#2c7865', '#90d26d', 9, 10),
(90, '#c5ee91', '#ecbe79', '#59b19b', '#b4e59a', 2, 10),
(91, '#c5ee91', '#93e1aa', '#59b19b', '#718e81', 2, 10),
(92, '#52d6fc', '#93e3fd', '#cbf0ff', '#f9fdff', 0, 10),
(93, '#1f2544', '#474f7a', '#81689d', '#ffd0ec', 14, 10),
(94, '#1635d0', '#091977', '#7c6a90', '#e0b8d0', 6, 10),
(95, '#df826c', '#f8ffd2', '#d0f288', '#8adab2', 10, 10),
(96, '#e1806b', '#b5bf73', '#c8d7a8', '#c6f0db', 1, 10),
(97, '#edfd00', '#f59900', '#d38301', '#a96800', 1, 10),
(99, '#263e0f', '#4e7a27', '#76bb40', '#cde8b5', 2, 10),
(100, '#5c0701', '#b51a00', '#ff6250', '#ff8c82', 1, 42),
(125, '#008DDA', '#41C9E2', '#ACE2E1', '#F7EEDD', 0, 55),
(126, '#008DDA', '#41C9E2', '#ACE2E1', '#F7EEDD', 0, 55);

-- --------------------------------------------------------

--
-- Struttura della tabella `save_palettes`
--

CREATE TABLE `save_palettes` (
  `id_save` int NOT NULL,
  `id_utente` int NOT NULL DEFAULT '0',
  `id_palette` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `save_palettes`
--

INSERT INTO `save_palettes` (`id_save`, `id_utente`, `id_palette`) VALUES
(35, 11, 65),
(39, 11, 67),
(40, 41, 65),
(42, 41, 85),
(43, 41, 67),
(46, 10, 72),
(52, 10, 65),
(58, 10, 71),
(59, 10, 31),
(61, 11, 68),
(62, 26, 65),
(65, 49, 67),
(69, 10, 67),
(73, 42, 65),
(76, 55, 67),
(77, 55, 62),
(78, 25, 67),
(80, 56, 65),
(81, 10, 68);

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id_utente` int NOT NULL,
  `email` varchar(52) NOT NULL,
  `username` varchar(52) NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `auth` varchar(32) NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id_utente`, `email`, `username`, `password`, `auth`) VALUES
(10, 'teo@gmail.com', 'teo', 'e827aa1ed78e96a113182dce12143f9f', 'normal'),
(11, 'matteo@gmail.com', 'matteo', '150be5b860e60a7fc7c7d9b9815e93d1', 'normal'),
(12, 'test@gmail.com', 'test', '098f6bcd4621d373cade4e832627b4f6', 'normal'),
(13, 'test1@gmail.com', 'test1', '5a105e8b9d40e1329780d62ea2265d8a', 'normal'),
(14, 'test2@gmail.com', 'test2', 'ad0234829205b9033196ba818f7a872b', 'normal'),
(15, 'giorgio@gmail.com', 'giorgio', '16cdae1dc8f5ccc69c51eea2851bff68', 'normal'),
(16, 'teo1@gmail.com', 'teo1', '2a94e031ea050049971ecc2c67768460', 'normal'),
(18, 'bubugagabubuga@gmail.com', 'matteo2', '6e6bc4e49dd477ebc98ef4046c067b5f', 'normal'),
(19, 'flavio@gmail.com', 'flavio', 'f76405ac130dac085b2a6249073b213b', 'normal'),
(20, 'pagani@gmail.com', 'pagani', '8f655f7f60b0f8b1c28bc7ac7b0c2436', 'normal'),
(21, 'pagani1@gmail.com', 'pagani1', '5cab01ab5534dabfa56fba97a318c22b', 'normal'),
(22, 'pagani2@gmail.com', 'pagani2', 'e2d2b7c8b825527f8c3e284f20020c8e', 'normal'),
(23, 'pagani3@gmail.com', 'pagani3', '468e9e87e57381959056f24e98d2aaee', 'normal'),
(24, 'paganii@gmail.com', 'paganii', '0ff239af42be1af9aeac5e2900baaa66', 'normal'),
(25, 'ema@gmail.com', 'ema', '93bdb73b49e88b5ce23da0509da1b8ac', 'normal'),
(26, 'testtest@gmail.com', 'testtest', '05a671c66aefea124cc08b76ea6d30bb', 'normal'),
(27, 'testtesttesttest@gmail.com', 'testtesttesttest', '3dd0cd797a7399b56c470612887108eb', 'normal'),
(28, 'ema1@gmail.com', 'ema1', '4738c733ba7eac5686f56a994be718f5', 'normal'),
(29, 'ema2@gmail.com', 'ema2', '9e8a6d2c1a0627eeaabd656c029d5a05', 'normal'),
(30, 'ema3@gmail.com', 'ema3', 'ba0e163ea2b612478f8a2a141e5006c0', 'normal'),
(32, 'teoteo@gmail.com', 'teoteo', 'bdb15bdf959d3db4183806280b93cd9d', 'normal'),
(33, 'lalala@gmail.com', 'lalala', '9aa6e5f2256c17d2d430b100032b997c', 'normal'),
(34, 'vienna66@gmail.com', 'vienna66', '899d4093d77b388f6d1441e92de1a62f', 'normal'),
(35, 'tito@gmail.com', 'tito', '35056cf3019b02c1b7c4cbcfec9d39f0', 'normal'),
(37, 'Cammellaro.88@gmail.com', 'Crespi', '202cb962ac59075b964b07152d234b70', 'normal'),
(38, 'teogaga@gmail.c', 'teogaga', '6d64af3593bec40a2dd5344693bb8130', 'normal'),
(39, 'teogaga2@d.d', 'teogaga2', '04d60fb26ef797d0428ed04c467a99f3', 'normal'),
(40, 'Tt@gmail.com', 'Tt', 'accc9105df5383111407fd5b41255e23', 'normal'),
(41, 'dani@gmail.com', 'dani', '55b7e8b895d047537e672250dd781555', 'normal'),
(42, 'matteo.carrara.teo@gmail.com', 'matteohcf', NULL, 'google'),
(45, 'armi.espo@gmail.com', 'armando esposito', NULL, 'google'),
(46, 'arm@gmail.com', 'arm', 'f926b3e222d7afee57071b2256839701', 'normal'),
(47, 'coter.thomas@eduabf.eu', 'THOMAS COTER', NULL, 'google'),
(48, 'matteohcf@gmail.com', 'matteohcf L', NULL, 'google'),
(49, 'giacomo.maffeis43@gmail.com', 'Giacomo Maffeis', NULL, 'google'),
(50, 'rachiddaim3@gmail.com', 'rachid daim', NULL, 'google'),
(51, 'prof@gmail.com', 'prof', 'd450c5dbcc10db0749277efc32f15f9f', 'normal'),
(52, 'dao@gmail.com', 'dao', 'f0719ea8e993ccca9ffca5334b96f546', 'normal'),
(53, 'giorgio.pagani2003@gmail.com', 'Giorgio Pagani', NULL, 'google'),
(54, 'prof1@gmail.com', 'prof1', '4f5fdb3de5aa701eae2961743a00c01c', 'normal'),
(55, 'danielecr09@gmail.com', 'Crespi', NULL, 'google'),
(56, 'riccardosilva2002@gmail.com', 'riccardosilva', '38f83da38d48857867acdeb5a489db05', 'normal');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id_like`);

--
-- Indici per le tabelle `palettes`
--
ALTER TABLE `palettes`
  ADD PRIMARY KEY (`id_palette`);

--
-- Indici per le tabelle `save_palettes`
--
ALTER TABLE `save_palettes`
  ADD PRIMARY KEY (`id_save`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id_utente`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `likes`
--
ALTER TABLE `likes`
  MODIFY `id_like` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=385;

--
-- AUTO_INCREMENT per la tabella `palettes`
--
ALTER TABLE `palettes`
  MODIFY `id_palette` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT per la tabella `save_palettes`
--
ALTER TABLE `save_palettes`
  MODIFY `id_save` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id_utente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
