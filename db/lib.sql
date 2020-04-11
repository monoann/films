-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Час створення: Квт 05 2020 р., 15:22
-- Версія сервера: 5.5.50
-- Версія PHP: 5.3.29

CREATE DATABASE `lib`;

use `lib`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `lib`
--

-- --------------------------------------------------------

--
-- Структура таблиці `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id_cat` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `category`
--

INSERT INTO `category` (`id_cat`, `name`) VALUES
(1, 'Фантастика'),
(2, 'Романи'),
(3, 'Хай-Тэк'),
(4, 'new'),
(5, 'нова');

-- --------------------------------------------------------

--
-- Структура таблиці `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL,
  `country` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `countries`
--

INSERT INTO `countries` (`id`, `country`) VALUES
(1, 'Україна'),
(2, 'США'),
(3, 'Британія');

-- --------------------------------------------------------

--
-- Структура таблиці `films`
--

CREATE TABLE IF NOT EXISTS `films` (
  `id_film` int(11) NOT NULL,
  `nameb` varchar(30) NOT NULL,
  `author` varchar(30) NOT NULL,
  `actor` text NOT NULL,
  `country_id` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `opus` varchar(400) NOT NULL,
  `img` varchar(200) NOT NULL,
  `file` varchar(200) NOT NULL,
  `trailer` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `films`
--

INSERT INTO `films` (`id_film`, `nameb`, `author`, `actor`, `country_id`, `id_category`, `opus`, `img`, `file`, `trailer`) VALUES
(34, 'йуйцу', 'йуйцу', '', 0, 1, 'йцуйцуйуц', 'resource/filmslogo/images.jpg', 'resource/films/videoplayback.mp4', 'resource/trailers/videoplayback.mp4'),
(35, 'gfgh', 'fgh', '', 0, 2, 'fghfgh', 'resource/filmslogo/images.jpg', 'resource/films/videoplayback.mp4', 'resource/trailers/videoplayback.mp4'),
(36, 'hfhfgh', 'fghgh', '', 0, 2, 'fghfghfghbnbn', 'resource/filmslogo/kino.png', 'resource/films/videoplayback.mp4', 'resource/trailers/videoplayback.mp4'),
(37, 'asd', 'asd', '', 0, 4, 'sfsdfsdf\r\nsdf\r\nsdf\r\nsdf\r\nsdf\r\ns\r\n\r\nsdsdfsdfsdfsdf\r\nsdfsdfwer wrwerwer werwer', 'resource/filmslogo/guest.jpg', 'resource/films/videoplayback.mp4', 'resource/trailers/videoplayback.mp4'),
(38, 'ффвфві', 'кенкен', 'олдолд', 2, 2, 'іваіваіва\r\nіва', 'resource/filmslogo/images.jpg', 'resource/films/videoplayback.mp4', 'resource/trailers/videoplayback.mp4');

-- --------------------------------------------------------

--
-- Структура таблиці `ratio`
--

CREATE TABLE IF NOT EXISTS `ratio` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `ratio` enum('1','2','3','4','5') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL,
  `login` varchar(10) NOT NULL,
  `password` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `reg_date` date NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `group` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id_user`, `login`, `password`, `email`, `reg_date`, `avatar`, `group`) VALUES
(0, 'guest', '', 'guest@guest.ua', '0000-00-00', '0', 0),
(2, 'admin', '123456', 'ddd@live.com', '2018-06-10', '0', 2),
(4, 'user', '123456', 'dwd3w@lol.com', '2018-06-20', 'resource/avatar/0.jpg', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `votes` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `votes`
--

INSERT INTO `votes` (`id`, `film_id`, `user_id`, `votes`, `date`) VALUES
(12, 36, 4, 'asda', '2020-03-29 13:10:16'),
(13, 36, 4, 'ads лрлрло ljklkjlkj lkjljlj kjlkjlj  ljk lkj lkj lkj lj lkj lk jlk jlk jl jl kj lkjljljlkjlkjljlkjljljlj ooiuouoiuouo fdgdgdgdgdgd ''l'''';l''l''l''''l wqeqewqeqewqeq czczczczczc klj lkj lj lj lkj lj lj lj ljl kjl jl jl jl kjlk jl jlk jl j klj klj lj', '2020-03-29 13:12:56'),
(14, 34, 0, 'фів', '2020-03-29 13:21:15'),
(15, 36, 0, 'asdasdasd asd asd as  zc zxc zxc ', '2020-03-29 13:39:44'),
(16, 35, 0, 'asdasdasdasdasd\r\nasd\r\nasdas\r\ndasdasdasdasdasd\r\ncbcvbcvbcb p pipipipi llk jljlk rtretetre ewqeqweq \r\nzxczxzxc 565464\r\nadadadasdeertert\r\nsfsdfsdfsf lkjljlj yuiuyiuy 776575 sasd\r\nxasadads', '2020-03-29 13:42:50'),
(17, 36, 4, 'adsasda ads a ads a a', '2020-03-29 15:34:44'),
(18, 34, 2, 'dadadsad ad a ad adas ', '2020-03-29 15:35:36'),
(19, 37, 2, 'qweqwe', '2020-03-29 19:29:00');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_cat`),
  ADD KEY `id_cat` (`id_cat`);

--
-- Індекси таблиці `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id_film`),
  ADD KEY `id_category` (`id_category`);

--
-- Індекси таблиці `ratio`
--
ALTER TABLE `ratio`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Індекси таблиці `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `category`
--
ALTER TABLE `category`
  MODIFY `id_cat` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблиці `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблиці `films`
--
ALTER TABLE `films`
  MODIFY `id_film` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT для таблиці `ratio`
--
ALTER TABLE `ratio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблиці `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `films`
--
ALTER TABLE `films`
  ADD CONSTRAINT `films_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_cat`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
