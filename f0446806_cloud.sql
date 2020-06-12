-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 10.0.0.57
-- Время создания: Июн 12 2020 г., 19:30
-- Версия сервера: 5.7.26-29
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `f0446806_cloud`
--

-- --------------------------------------------------------

--
-- Структура таблицы `directories`
--

CREATE TABLE `directories` (
  `directory_ID` int(128) NOT NULL,
  `directory_name` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_520_ci;

--
-- Дамп данных таблицы `directories`
--

INSERT INTO `directories` (`directory_ID`, `directory_name`) VALUES
(1, 'MoHAX'),
(2, 'MoHAX25');

-- --------------------------------------------------------

--
-- Структура таблицы `shared_files`
--

CREATE TABLE `shared_files` (
  `directory_ID` int(128) NOT NULL,
  `file` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL,
  `token` varchar(256) COLLATE utf8_unicode_520_ci NOT NULL,
  `filename` varchar(256) COLLATE utf8_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_ID` int(64) NOT NULL,
  `username` varchar(256) COLLATE utf8_unicode_520_ci NOT NULL,
  `pass` varchar(256) COLLATE utf8_unicode_520_ci NOT NULL,
  `directory_ID` int(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_520_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_ID`, `username`, `pass`, `directory_ID`) VALUES
(1, 'MoHAX', 'f8ebecba92808d0462759bc80cbf9adf', 1),
(2, 'MoHAX25', 'f8ebecba92808d0462759bc80cbf9adf', 2);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `directories`
--
ALTER TABLE `directories`
  ADD PRIMARY KEY (`directory_ID`);

--
-- Индексы таблицы `shared_files`
--
ALTER TABLE `shared_files`
  ADD UNIQUE KEY `filename` (`filename`),
  ADD KEY `directory_ID` (`directory_ID`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `directory_ID` (`directory_ID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `directories`
--
ALTER TABLE `directories`
  ADD CONSTRAINT `directories_ibfk_1` FOREIGN KEY (`directory_ID`) REFERENCES `users` (`directory_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shared_files`
--
ALTER TABLE `shared_files`
  ADD CONSTRAINT `shared_files_ibfk_1` FOREIGN KEY (`directory_ID`) REFERENCES `directories` (`directory_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
