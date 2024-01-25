-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2024 at 01:39 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kurierex`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokument`
--

CREATE TABLE `dokument` (
  `id` int(11) NOT NULL,
  `zamowienieID` int(11) NOT NULL,
  `pdf_file` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokument`
--

INSERT INTO `dokument` (`id`, `zamowienieID`, `pdf_file`) VALUES
(11, 14, 'uploads/Deklaracja-Celna-CN23.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `klient`
--

CREATE TABLE `klient` (
  `id` int(11) NOT NULL,
  `imie` varchar(250) NOT NULL,
  `nazwisko` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `ulica` varchar(250) NOT NULL,
  `nrDomu` int(11) NOT NULL,
  `nrMieszkania` int(11) DEFAULT NULL,
  `miasto` varchar(250) NOT NULL,
  `kodPocztowy` varchar(250) NOT NULL,
  `nrTelefonu` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klient`
--

INSERT INTO `klient` (`id`, `imie`, `nazwisko`, `email`, `ulica`, `nrDomu`, `nrMieszkania`, `miasto`, `kodPocztowy`, `nrTelefonu`) VALUES
(1, 'Nadawca', 'Nawca', 'nadawnie@gmail.com', 'kwiatowa', 7, 3, 'Olsztyn', '10-004', '333444555'),
(2, 'Odbiorca', 'Biorca', 'odbieranie@gmail.com', 'betonowa', 5, 1, 'Warszawa', '00-010', '777888999');

-- --------------------------------------------------------

--
-- Table structure for table `pojazd`
--

CREATE TABLE `pojazd` (
  `pojazdID` int(11) NOT NULL,
  `typPojazdu` char(255) DEFAULT NULL,
  `rokProdukcji` int(11) DEFAULT NULL,
  `Pojemnosc` float DEFAULT NULL,
  `numerRejestracyjny` varchar(255) DEFAULT NULL,
  `stanTechniczny` text DEFAULT NULL,
  `dataOstatniegoPrzegladu` date DEFAULT NULL,
  `dostepny` tinyint(1) DEFAULT NULL,
  `kierowcaID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pojazd`
--

INSERT INTO `pojazd` (`pojazdID`, `typPojazdu`, `rokProdukcji`, `Pojemnosc`, `numerRejestracyjny`, `stanTechniczny`, `dataOstatniegoPrzegladu`, `dostepny`, `kierowcaID`) VALUES
(1, 'rower', 1990, 100, 'ABC123', '0', '2024-01-26', 1, 1),
(2, 'rower', 1990, 100, 'ABC123', '0', '2024-01-26', 1, 1),
(3, 'ocochodzi', 1990, 1005, 'dbc', '0', '2024-01-26', 1, 2),
(4, 'ocochodzi', 1990, 1005, 'dbc', '0', '2024-01-26', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pracownik`
--

CREATE TABLE `pracownik` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) NOT NULL,
  `nazwisko` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nrTelefonu` varchar(50) NOT NULL,
  `typPracownika` enum('Kurier','Logistyk','Technik','Sortownik','AdminZ') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pracownik`
--

INSERT INTO `pracownik` (`id`, `imie`, `nazwisko`, `email`, `nrTelefonu`, `typPracownika`) VALUES
(1, 'Stefan', 'Dycha', 'stefo@mail.com', '786231908', 'Kurier'),
(2, 'Damian', 'Damazy', 'damazy@wp.pl', '656787900', 'Kurier'),
(3, 'techno', 'auciak', 'auta@gmail.com', '231543256', 'Technik');

-- --------------------------------------------------------

--
-- Table structure for table `przesylka`
--

CREATE TABLE `przesylka` (
  `id` int(11) NOT NULL,
  `zamowienieID` int(11) NOT NULL,
  `opis` text NOT NULL,
  `koszt` float DEFAULT 0,
  `waga` float NOT NULL DEFAULT 0,
  `kurierID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `przesylka`
--

INSERT INTO `przesylka` (`id`, `zamowienieID`, `opis`, `koszt`, `waga`, `kurierID`) VALUES
(7, 10, 'perfumy', 0, 0, NULL),
(10, 14, 'sztaba złota', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `trasa`
--

CREATE TABLE `trasa` (
  `trasaID` int(11) NOT NULL,
  `kurierID` int(11) DEFAULT NULL,
  `dlugoscTrasy` float DEFAULT NULL,
  `czasDostawy` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trasa`
--

INSERT INTO `trasa` (`trasaID`, `kurierID`, `dlugoscTrasy`, `czasDostawy`) VALUES
(1, 1, 100, '01:20:00'),
(2, 1, 1005, '01:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `zamowienie`
--

CREATE TABLE `zamowienie` (
  `id` int(11) NOT NULL,
  `nadawcaID` int(11) NOT NULL,
  `odbiorcaID` int(11) NOT NULL,
  `dataNadania` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('Oczekujące na zatwierdzenie','Oczekujące na odbiór przez kuriera','Odebrana','Na sortowni','Realizowanie dostarczenia','Dostarczona','Anulowana','Za granicą','Oczekujące na odbiór od zagranicznej firmy') NOT NULL,
  `trasaID` int(11) DEFAULT NULL,
  `CzyZagraniczna` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienie`
--

INSERT INTO `zamowienie` (`id`, `nadawcaID`, `odbiorcaID`, `dataNadania`, `status`, `trasaID`, `CzyZagraniczna`) VALUES
(10, 1, 2, '2024-01-23', 'Oczekujące na odbiór przez kuriera', NULL, 0),
(14, 1, 2, '2024-01-23', 'Za granicą', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokument`
--
ALTER TABLE `dokument`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zamowienieID` (`zamowienieID`);

--
-- Indexes for table `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pojazd`
--
ALTER TABLE `pojazd`
  ADD PRIMARY KEY (`pojazdID`),
  ADD KEY `kierowcaID` (`kierowcaID`);

--
-- Indexes for table `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `przesylka`
--
ALTER TABLE `przesylka`
  ADD PRIMARY KEY (`id`),
  ADD KEY `przesylka_ibfk_1` (`zamowienieID`),
  ADD KEY `kurierID` (`kurierID`);

--
-- Indexes for table `trasa`
--
ALTER TABLE `trasa`
  ADD PRIMARY KEY (`trasaID`),
  ADD KEY `kurierID` (`kurierID`);

--
-- Indexes for table `zamowienie`
--
ALTER TABLE `zamowienie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nadawcaID` (`nadawcaID`),
  ADD KEY `odbiorcaID` (`odbiorcaID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokument`
--
ALTER TABLE `dokument`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `klient`
--
ALTER TABLE `klient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pojazd`
--
ALTER TABLE `pojazd`
  MODIFY `pojazdID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `przesylka`
--
ALTER TABLE `przesylka`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `trasa`
--
ALTER TABLE `trasa`
  MODIFY `trasaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `zamowienie`
--
ALTER TABLE `zamowienie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dokument`
--
ALTER TABLE `dokument`
  ADD CONSTRAINT `dokument_ibfk_1` FOREIGN KEY (`zamowienieID`) REFERENCES `zamowienie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pojazd`
--
ALTER TABLE `pojazd`
  ADD CONSTRAINT `pojazd_ibfk_1` FOREIGN KEY (`kierowcaID`) REFERENCES `pracownik` (`id`);

--
-- Constraints for table `przesylka`
--
ALTER TABLE `przesylka`
  ADD CONSTRAINT `przesylka_ibfk_1` FOREIGN KEY (`zamowienieID`) REFERENCES `zamowienie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `przesylka_ibfk_2` FOREIGN KEY (`kurierID`) REFERENCES `pracownik` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `trasa`
--
ALTER TABLE `trasa`
  ADD CONSTRAINT `trasa_ibfk_1` FOREIGN KEY (`kurierID`) REFERENCES `pracownik` (`id`);

--
-- Constraints for table `zamowienie`
--
ALTER TABLE `zamowienie`
  ADD CONSTRAINT `zamowienie_ibfk_1` FOREIGN KEY (`nadawcaID`) REFERENCES `klient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `zamowienie_ibfk_2` FOREIGN KEY (`odbiorcaID`) REFERENCES `klient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
