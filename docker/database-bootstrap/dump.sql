-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 26. Mai 2023 um 14:48
-- Server-Version: 10.1.38-MariaDB-0+deb9u1
-- PHP-Version: 7.0.33-0+deb9u7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `zeltlager`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `essenmarken`
--

CREATE TABLE `essenmarken` (
  `essenmarke_id` int(11) NOT NULL,
  `kostenstelle_id` int(11) NOT NULL,
  `typ_id` int(11) NOT NULL,
  `time_created` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `mahlzeit_id` int(11) NOT NULL,
  `wertigkeit` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `historie`
--

CREATE TABLE `historie` (
  `historie_id` int(11) NOT NULL,
  `typ` enum('ausweis','essenmarke') NOT NULL,
  `ausweis_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  `payload` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `jugendfeuerwehr`
--

CREATE TABLE `jugendfeuerwehr` (
  `jf_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `zeltdorf_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kasse`
--

CREATE TABLE `kasse` (
  `kasse_id` int(11) NOT NULL,
  `mac` varchar(64) NOT NULL,
  `battstate` double NOT NULL,
  `time_last_seen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kostenstellen`
--

CREATE TABLE `kostenstellen` (
  `kostenstelle_id` int(11) NOT NULL,
  `bezeichnung` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mahlzeiten`
--

CREATE TABLE `mahlzeiten` (
  `mahlzeit_id` int(11) NOT NULL,
  `time_from` int(11) NOT NULL,
  `time_till` int(11) NOT NULL,
  `typ_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `set_name` varchar(50) NOT NULL,
  `set_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`set_name`, `set_value`) VALUES
('', ''),
('aktuelles_zeltdorf_id', '6'),
('check_korrekt_zeltdorf', '0'),
('dashboard_update_intervall', ''),
('essenmarke_date_string', 'd.m.Y H:i'),
('essenmarke_eula_text', 'Diese Essenmarke gilt für eine Mahlzeit!'),
('ip_escpos_drucker', '172.18.6.238'),
('organisation', 'Kreis-Jugendfeuerwehr Landkreis Diepholz'),
('ort', 'Barver'),
('unlock_all_zeltdoerfer', '1562947488'),
('unlock_time', '5'),
('veranstaltung', 'Zeltlager'),
('zeltlager_jahr', '2019');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `teilnehmer`
--

CREATE TABLE `teilnehmer` (
  `teilnehmer_id` int(11) NOT NULL,
  `vorname` varchar(70) NOT NULL,
  `nachname` varchar(70) NOT NULL,
  `jf_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `teilnehmer_mahlzeit`
--

CREATE TABLE `teilnehmer_mahlzeit` (
  `teilnehmer_mahlzeit_id` int(11) NOT NULL,
  `teilnehmer_id` int(11) NOT NULL,
  `mahlzeit_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `kasse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `typ`
--

CREATE TABLE `typ` (
  `typ_id` int(11) NOT NULL,
  `bezeichnung` varchar(50) NOT NULL,
  `preis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `typ`
--

INSERT INTO `typ` (`typ_id`, `bezeichnung`, `preis`) VALUES
(1, 'Frühstück', 2),
(2, 'Mittagessen', 3),
(3, 'Abendbrot', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(55) NOT NULL,
  `password` varchar(64) NOT NULL,
  `time_created` int(11) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zeltdorf`
--

CREATE TABLE `zeltdorf` (
  `zeltdorf_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `barcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `essenmarken`
--
ALTER TABLE `essenmarken`
  ADD PRIMARY KEY (`essenmarke_id`);

--
-- Indizes für die Tabelle `historie`
--
ALTER TABLE `historie`
  ADD PRIMARY KEY (`historie_id`);

--
-- Indizes für die Tabelle `jugendfeuerwehr`
--
ALTER TABLE `jugendfeuerwehr`
  ADD PRIMARY KEY (`jf_id`);

--
-- Indizes für die Tabelle `kasse`
--
ALTER TABLE `kasse`
  ADD PRIMARY KEY (`kasse_id`),
  ADD UNIQUE KEY `mac` (`mac`);

--
-- Indizes für die Tabelle `kostenstellen`
--
ALTER TABLE `kostenstellen`
  ADD PRIMARY KEY (`kostenstelle_id`);

--
-- Indizes für die Tabelle `mahlzeiten`
--
ALTER TABLE `mahlzeiten`
  ADD PRIMARY KEY (`mahlzeit_id`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD UNIQUE KEY `set_name` (`set_name`);

--
-- Indizes für die Tabelle `teilnehmer`
--
ALTER TABLE `teilnehmer`
  ADD UNIQUE KEY `teilnehmer_id` (`teilnehmer_id`),
  ADD KEY `teilnehmer_id_2` (`teilnehmer_id`);

--
-- Indizes für die Tabelle `teilnehmer_mahlzeit`
--
ALTER TABLE `teilnehmer_mahlzeit`
  ADD PRIMARY KEY (`teilnehmer_mahlzeit_id`),
  ADD KEY `teilnehmer_id` (`teilnehmer_id`,`mahlzeit_id`);

--
-- Indizes für die Tabelle `typ`
--
ALTER TABLE `typ`
  ADD PRIMARY KEY (`typ_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indizes für die Tabelle `zeltdorf`
--
ALTER TABLE `zeltdorf`
  ADD PRIMARY KEY (`zeltdorf_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `essenmarken`
--
ALTER TABLE `essenmarken`
  MODIFY `essenmarke_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4673;
--
-- AUTO_INCREMENT für Tabelle `historie`
--
ALTER TABLE `historie`
  MODIFY `historie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43209;
--
-- AUTO_INCREMENT für Tabelle `jugendfeuerwehr`
--
ALTER TABLE `jugendfeuerwehr`
  MODIFY `jf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;
--
-- AUTO_INCREMENT für Tabelle `kasse`
--
ALTER TABLE `kasse`
  MODIFY `kasse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `kostenstellen`
--
ALTER TABLE `kostenstellen`
  MODIFY `kostenstelle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `mahlzeiten`
--
ALTER TABLE `mahlzeiten`
  MODIFY `mahlzeit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT für Tabelle `teilnehmer_mahlzeit`
--
ALTER TABLE `teilnehmer_mahlzeit`
  MODIFY `teilnehmer_mahlzeit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38651;
--
-- AUTO_INCREMENT für Tabelle `typ`
--
ALTER TABLE `typ`
  MODIFY `typ_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `zeltdorf`
--
ALTER TABLE `zeltdorf`
  MODIFY `zeltdorf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
