-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Gegenereerd op: 09 jan 2018 om 22:34
-- Serverversie: 10.1.19-MariaDB
-- PHP-versie: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `happietaria`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gebruiker`
--

CREATE TABLE `gebruiker` (
  `gebruiker_id` int(11) NOT NULL,
  `vereniging_id` int(11) NOT NULL,
  `voornaam` varchar(20) NOT NULL,
  `achternaam` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `wachtwoord` varchar(250) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `telefoon` varchar(13) NOT NULL,
  `rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `gebruiker`
--

INSERT INTO `gebruiker` (`gebruiker_id`, `vereniging_id`, `voornaam`, `achternaam`, `email`, `wachtwoord`, `salt`, `telefoon`, `rol`) VALUES
(1, 1, 'Admin', 'Account', 'admin@happietaria.nl', 'd74b103b40e5d6cd803493ac9fa19659b2821698b03d8bcf4a113d3fc6ddc8878283d92ab8bcdfd0fd39f128b7fda9c519e9dc8601313c66c2e7cabbd72ce8c7', 'SVfcfr8Esp', '0612345678', 3),
(2, 1, 'Test', 'Gebruiker', 'auke.v.o@hotmail.com', '', '', '06-12345678', 1),
(3, 3, 'Dirk', 'Geuzebroek', 'test@@test', '', '', '0612345678', 1),
(9, 1, 'Auke', 'van Oostenbrugge', 'auke.v.o@hotmail.com', '', '', '+316 46519735', 1),
(14, 7, 'Auke', 'van Oostenbrugge', 'auke.v.o@hotmail.com', '', '', '0646519735', 2),
(15, 7, 'Dirk', 'Kuyt', 'd.kuyt@gmail.com', '', '', '06 12 32 34 5', 2),
(16, 3, 'Harry', 'haasjes', 'd.kuyt@gmail.com', '', '', '06 12 32 34 5', 2),
(17, 1, 'Auke', 'van Oostenbrugge', 'auke.v.o@hotmail.com', '', '', '0646519735', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `inschrijving`
--

CREATE TABLE `inschrijving` (
  `inschrijving_id` int(11) NOT NULL,
  `gebruiker_id` int(11) NOT NULL,
  `beschikbaar` varchar(20) NOT NULL,
  `opmerking` varchar(300) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `contact_gehad` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `inschrijving`
--

INSERT INTO `inschrijving` (`inschrijving_id`, `gebruiker_id`, `beschikbaar`, `opmerking`, `timestamp`, `active`, `contact_gehad`) VALUES
(5, 14, 'wo,vr,za', 'Test', '2017-12-31 11:25:48', 0, 0),
(6, 15, 'ma,do,vr,zo', 'Test acc', '2017-12-31 11:58:33', 0, 0),
(7, 16, 'ma,do,vr,zo', 'Test acc', '2017-12-31 11:58:56', 1, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `inschrijving_vaardigheden`
--

CREATE TABLE `inschrijving_vaardigheden` (
  `inschrijving_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `inschrijving_vaardigheden`
--

INSERT INTO `inschrijving_vaardigheden` (`inschrijving_id`, `skill_id`) VALUES
(5, 1),
(5, 6),
(5, 2),
(5, 7),
(6, 1),
(6, 6),
(6, 8),
(6, 7),
(7, 4),
(7, 6),
(7, 2),
(7, 7);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `reservering`
--

CREATE TABLE `reservering` (
  `reservering_id` int(11) NOT NULL,
  `gebruiker_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `shift_id` int(11) NOT NULL,
  `personen` int(11) NOT NULL,
  `tafel_nummer` varchar(10) NOT NULL,
  `geaccepteerd` int(11) NOT NULL COMMENT '0 = nog niet, 1 = ja, 2 = nee',
  `status_id` int(11) NOT NULL,
  `opmerking` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `reservering`
--

INSERT INTO `reservering` (`reservering_id`, `gebruiker_id`, `datum`, `shift_id`, `personen`, `tafel_nummer`, `geaccepteerd`, `status_id`, `opmerking`) VALUES
(1, 17, '2018-01-17', 2, 7, '', 1, 2, 'dit is een test');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `shift`
--

CREATE TABLE `shift` (
  `shift_id` int(11) NOT NULL,
  `opmerking` varchar(40) NOT NULL,
  `begin_tijd` time NOT NULL,
  `eind_tijd` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `shift`
--

INSERT INTO `shift` (`shift_id`, `opmerking`, `begin_tijd`, `eind_tijd`) VALUES
(1, 'Eerste shift', '17:30:00', '19:00:00'),
(2, 'Tweede shift', '20:00:00', '22:00:00');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `naam` varchar(40) NOT NULL,
  `locked` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `status`
--

INSERT INTO `status` (`status_id`, `naam`, `locked`) VALUES
(1, 'Nieuw', 1),
(2, 'beoordeeld', 1),
(3, 'Geweigerd', 1),
(4, 'Gearchiveerd', 1),
(9, 'In behandeling', 0),
(10, 'Mail gestuurd', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vaardigheden`
--

CREATE TABLE `vaardigheden` (
  `skill_id` int(11) NOT NULL,
  `vaardigheid` varchar(30) NOT NULL,
  `type` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `vaardigheden`
--

INSERT INTO `vaardigheden` (`skill_id`, `vaardigheid`, `type`) VALUES
(1, 'Bediening', 1),
(2, 'BHV Diploma', 2),
(3, 'Afwas', 1),
(4, 'Keuken', 1),
(5, 'Tapbevoegdheid', 2),
(6, 'Promotie', 1),
(7, 'EHBO Diploma', 2),
(8, 'Schoonmaak', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vereniging`
--

CREATE TABLE `vereniging` (
  `vereniging_id` int(11) NOT NULL,
  `naam` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `vereniging`
--

INSERT INTO `vereniging` (`vereniging_id`, `naam`) VALUES
(1, 'Geen vereniging'),
(2, 'Ichthus'),
(3, 'Gumbo Millenium'),
(4, 'Boreas'),
(5, 'ZHTC'),
(6, 'Absens Carens'),
(7, 'Navigators');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `gebruiker`
--
ALTER TABLE `gebruiker`
  ADD PRIMARY KEY (`gebruiker_id`);

--
-- Indexen voor tabel `inschrijving`
--
ALTER TABLE `inschrijving`
  ADD PRIMARY KEY (`inschrijving_id`);

--
-- Indexen voor tabel `reservering`
--
ALTER TABLE `reservering`
  ADD PRIMARY KEY (`reservering_id`);

--
-- Indexen voor tabel `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexen voor tabel `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexen voor tabel `vaardigheden`
--
ALTER TABLE `vaardigheden`
  ADD PRIMARY KEY (`skill_id`);

--
-- Indexen voor tabel `vereniging`
--
ALTER TABLE `vereniging`
  ADD PRIMARY KEY (`vereniging_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `gebruiker`
--
ALTER TABLE `gebruiker`
  MODIFY `gebruiker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT voor een tabel `inschrijving`
--
ALTER TABLE `inschrijving`
  MODIFY `inschrijving_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT voor een tabel `reservering`
--
ALTER TABLE `reservering`
  MODIFY `reservering_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT voor een tabel `shift`
--
ALTER TABLE `shift`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT voor een tabel `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT voor een tabel `vaardigheden`
--
ALTER TABLE `vaardigheden`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT voor een tabel `vereniging`
--
ALTER TABLE `vereniging`
  MODIFY `vereniging_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
