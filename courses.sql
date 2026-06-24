-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Хост: 172.19.0.2:3306
-- Время создания: Июн 24 2026 г., 18:31
-- Версия сервера: 10.6.27-MariaDB-ubu2204
-- Версия PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `student17_database`
--

-- --------------------------------------------------------

--
-- Структура таблицы `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL,
  `format` varchar(50) DEFAULT 'онлайн',
  `icon` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `price`, `duration`, `max_students`, `format`, `icon`) VALUES
(1, 'Blender 3D: Базовый курс', 'Основы интерфейса Blender, работа с примитивами, базовое моделирование, материалы и освещение. Для новичков.', '12000.00', '4 недели', 10, 'онлайн', '🎨'),
(2, 'Blender 3D: Продвинутое моделирование', 'Сложные техники полигонального моделирования, работа с модификаторами, создание высокодетализированных объектов.', '18000.00', '6 недель', 8, 'онлайн', '🔷'),
(3, 'Blender 3D: Текстурирование и материалы', 'Создание PBR-материалов, UV-развёртка, нодовый редактор, процедурные текстуры и шейдинг.', '15000.00', '5 недель', 8, 'онлайн', '🎯'),
(4, 'Blender 3D: Освещение и рендеринг', 'Настройка Cycles и Eevee, студийное освещение, HDRI-окружения, постобработка в Compositor.', '14000.00', '4 недели', 8, 'онлайн', '💡'),
(5, 'Blender 3D: Анимация и риггинг', 'Создание скелетной анимации, риггинг персонажей, ключевые кадры, график кривых, инверсная кинематика.', '22000.00', '8 недель', 6, 'онлайн', '🦴'),
(6, 'Blender 3D: Скульптинг', 'Цифровая скульптура в Blender, работа с кистями, динамическая топология, ретопология и детализация.', '16000.00', '6 недель', 6, 'онлайн', '🗿'),
(7, 'Blender 3D: Персонажная анимация', 'Создание персонажей с нуля: моделирование, текстурирование, риггинг и анимация походки, эмоций.', '28000.00', '10 недель', 5, 'онлайн', '🧍'),
(8, 'Blender 3D: Архитектурная визуализация', 'Интерьерная и экстерьерная визуализация, работа с чертежами, фотограмметрия, реалистичная архитектура.', '20000.00', '8 недель', 7, 'онлайн', '🏛️');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
