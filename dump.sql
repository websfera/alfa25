-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Počítač: db:3306
-- Vytvořeno: Čtv 21. kvě 2026, 13:14
-- Verze serveru: 8.0.41
-- Verze PHP: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Databáze: `alfa25`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `conversation`
--

CREATE TABLE `conversation` (
                                `uuid` binary(16) NOT NULL,
                                `name` varchar(100) COLLATE utf8mb4_czech_ci DEFAULT NULL,
                                `is_group` tinyint(1) NOT NULL DEFAULT '0',
                                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `conversation_member`
--

CREATE TABLE `conversation_member` (
                                       `conversation_uuid` binary(16) NOT NULL,
                                       `user_uuid` binary(16) NOT NULL,
                                       `joined_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `message`
--

CREATE TABLE `message` (
                           `uuid` binary(16) NOT NULL,
                           `message` text COLLATE utf8mb4_czech_ci NOT NULL,
                           `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           `user_uuid` binary(16) NOT NULL,
                           `conversation_uuid` binary(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
                        `uuid` binary(16) NOT NULL,
                        `username` varchar(32) COLLATE utf8mb4_czech_ci NOT NULL,
                        `email` varchar(64) COLLATE utf8mb4_czech_ci NOT NULL,
                        `password` varchar(64) COLLATE utf8mb4_czech_ci NOT NULL,
                        `phone` int UNSIGNED DEFAULT NULL,
                        `first_name` varchar(16) COLLATE utf8mb4_czech_ci NOT NULL,
                        `last_name` varchar(16) COLLATE utf8mb4_czech_ci DEFAULT NULL,
                        `gender` enum('m','f') COLLATE utf8mb4_czech_ci DEFAULT NULL,
                        `birthdate` date DEFAULT NULL,
                        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`uuid`, `username`, `email`, `password`, `phone`, `first_name`, `last_name`, `gender`, `birthdate`, `created_at`, `updated_at`) VALUES
    (0x019cbf463e8670289c7cf617c5af60cb, 'martin', 'user1@test.cz', '$2y$10$JcVi4R5N2cQR3bQ43sc.dOKAvSx7JbOHTqV1t3Yf025r1sDKDgoiO', 737123456, 'Jan', 'Žižka', 'f', '2000-01-01', '2026-03-05 18:32:57', NULL);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `conversation`
--
ALTER TABLE `conversation`
    ADD PRIMARY KEY (`uuid`);

--
-- Indexy pro tabulku `conversation_member`
--
ALTER TABLE `conversation_member`
    ADD PRIMARY KEY (`conversation_uuid`,`user_uuid`),
  ADD KEY `user_uuid` (`user_uuid`),
  ADD KEY `conversation_uuid` (`conversation_uuid`);

--
-- Indexy pro tabulku `message`
--
ALTER TABLE `message`
    ADD PRIMARY KEY (`uuid`),
  ADD KEY `user_uuid` (`user_uuid`),
  ADD KEY `message_conversation_fk` (`conversation_uuid`);

--
-- Indexy pro tabulku `user`
--
ALTER TABLE `user`
    ADD UNIQUE KEY `idx_uq_uuid` (`uuid`),
    ADD UNIQUE KEY `IDX_UQ_USERNAME` (`username`),
    ADD UNIQUE KEY `idx_uq_email` (`email`);

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `conversation_member`
--
ALTER TABLE `conversation_member`
    ADD CONSTRAINT `conversation_member_ibfk_1` FOREIGN KEY (`conversation_uuid`) REFERENCES `conversation` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversation_member_ibfk_2` FOREIGN KEY (`user_uuid`) REFERENCES `user` (`uuid`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `message`
--
ALTER TABLE `message`
    ADD CONSTRAINT `message_conversation_fk` FOREIGN KEY (`conversation_uuid`) REFERENCES `conversation` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_uuid`) REFERENCES `user` (`uuid`) ON DELETE CASCADE;
COMMIT;
