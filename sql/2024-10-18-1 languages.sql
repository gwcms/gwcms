-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 18, 2024 at 07:31 PM
-- Server version: 8.0.39-0ubuntu0.22.04.1
-- PHP Version: 8.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_data_languages`
--

CREATE TABLE `gw_data_languages` (
  `id` int UNSIGNED NOT NULL,
  `sys` tinyint NOT NULL,
  `name` varchar(145) NOT NULL,
  `native_name` varchar(145) NOT NULL,
  `iso639_1` varchar(45) NOT NULL,
  `trcode` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'gwcms translation code & flag & country',
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `popularity` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `gw_data_languages`
--

INSERT INTO `gw_data_languages` (`id`, `sys`, `name`, `native_name`, `iso639_1`, `trcode`, `insert_time`, `update_time`, `popularity`) VALUES
(1, 0, 'Abkhazian', 'Аҧсуа', 'ab', 'ab', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(2, 0, 'Afar', 'Afaraf', 'aa', 'aa', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(3, 0, 'Afrikaans', 'Afrikaans', 'af', 'af', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 24),
(4, 0, 'Akan', 'Akan', 'ak', 'ak', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(5, 0, 'Albanian', 'Shqip', 'sq', 'sq', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 43),
(6, 0, 'Amharic', 'አማርኛ', 'am', 'am', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(7, 0, 'Arabic', 'العربية', 'ar', 'ar', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 83),
(8, 0, 'Aragonese', 'Aragonés', 'an', 'an', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 34),
(9, 0, 'Assamese', 'অসমীয়া', 'as', 'as', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(10, 0, 'Armenian', 'Հայերեն', 'hy', 'hy', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18),
(11, 0, 'Avaric', 'Aвар мацӀ', 'av', 'av', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(12, 0, 'Avestan', 'Avesta', 'ae', 'ae', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(13, 0, 'Aymara', 'Aymar aru', 'ay', 'ay', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(14, 0, 'Azerbaijani', 'Azərbaycan dili', 'az', 'az', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 66),
(15, 0, 'Bashkir', 'башҡорт теле', 'ba', 'ba', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(16, 0, 'Bambara', 'Bamanankan', 'bm', 'bm', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(17, 0, 'Basque', 'Euskara', 'eu', 'eu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 70),
(18, 0, 'Belarusian', 'Беларуская', 'be', 'be', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 42),
(19, 0, 'Bengali', 'বাংলা', 'bn', 'bn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 30),
(20, 0, 'Bihari', 'भोजपुरी', 'bh', 'bh', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(21, 0, 'Bislama', 'Bislama', 'bi', 'bi', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(22, 0, 'Bosnian', 'Bosanski jezik', 'bs', 'bs', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 37),
(23, 0, 'Breton', 'Brezhoneg', 'br', 'br', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 46),
(24, 0, 'Bulgarian', 'български език', 'bg', 'bg', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 75),
(25, 0, 'Burmese', 'ဗမာစာ', 'my', 'my', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 2),
(26, 0, 'Catalan; Valencian', 'Català', 'ca', 'ca', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 95),
(27, 0, 'Chamorro', 'Chamoru', 'ch', 'ch', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(28, 0, 'Chechen', 'нохчийн мотт', 'ce', 'ce', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(29, 0, 'Chichewa', 'Chinyanja', 'ny', 'ny', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(30, 0, 'Chinese', '中文 (Zhōngwén), 汉语, 漢語', 'zh', 'zh', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 96),
(31, 0, 'Chuvash', 'чӑваш чӗлхи', 'cv', 'cv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 9),
(32, 0, 'Cornish', 'Kernewek', 'kw', 'kw', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(33, 0, 'Corsican', 'Corsu', 'co', 'co', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(34, 0, 'Cree', 'ᓀᐦᐃᔭᐍᐏᐣ', 'cr', 'cr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(35, 0, 'Croatian', 'hrvatski', 'hr', 'hr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 69),
(36, 0, 'Czech', 'Česky', 'cs', 'cs', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 90),
(37, 0, 'Danish', 'Dansk', 'da', 'da', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 82),
(38, 0, 'Maldivian', 'ދިވެހި', 'dv', 'dv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(39, 0, 'Dzongkha', 'རྫོང་ཁ', 'dz', 'dz', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(40, 0, 'English', 'English', 'en', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 107),
(41, 0, 'Esperanto', 'Esperanto', 'eo', 'eo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 81),
(42, 0, 'Estonian', 'Eesti', 'et', 'et', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 67),
(43, 0, 'Ewe', 'Eʋegbe', 'ee', 'ee', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(44, 0, 'Faroese', 'Føroyskt', 'fo', 'fo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(45, 0, 'Fijian', 'Vosa Vakaviti', 'fj', 'fj', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(46, 0, 'Finnish', 'Suomi', 'fi', 'fi', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 92),
(47, 0, 'French', 'Français', 'fr', 'fr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 105),
(48, 0, 'Fula', 'Pular', 'ff', 'ff', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(49, 0, 'Galician', 'Galego', 'gl', 'gl', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 65),
(50, 0, 'German', 'Deutsch', 'de', 'de', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 106),
(51, 0, 'Greek', 'Ελληνικά', 'el', 'el', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 60),
(52, 0, 'Guaraní', 'Avañe\'ẽ', 'gn', 'gn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(53, 0, 'Gujarati', 'ગુજરાતી', 'gu', 'gu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 27),
(54, 0, 'Haitian', 'Kreyòl ayisyen', 'ht', 'ht', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 55),
(55, 0, 'Hausa', 'Hausa, هَوُسَ', 'ha', 'ha', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(56, 0, 'Hebrew', 'עברית', 'he', 'he', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 76),
(57, 0, 'Herero', 'Otjiherero', 'hz', 'hz', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(58, 0, 'Hindi', 'हिन्दी, हिंदी\"', 'hi', 'hi', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 68),
(59, 0, 'Hiri Motu', 'Hiri Motu', 'ho', 'ho', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(60, 0, 'Hungarian', 'Magyar', 'hu', 'hu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 89),
(61, 0, 'Indonesian', 'Bahasa Indonesia', 'id', 'id', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 87),
(62, 0, 'Irish', 'Gaeilge', 'ga', 'ga', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 10),
(63, 0, 'Igbo', 'Igbo', 'ig', 'ig', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(64, 0, 'Inupiaq', 'Iñupiaq', 'ik', 'ik', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(65, 0, 'Ido', 'Ido', 'io', 'io', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 29),
(66, 0, 'Icelandic', 'Íslenska', 'is', 'is', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 38),
(67, 0, 'Italian', 'Italiano', 'it', 'it', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 103),
(68, 0, 'Inuktitut', 'ᐃᓄᒃᑎᑐᑦ', 'iu', 'iu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(69, 0, 'Japanese', '日本語', 'ja', 'ja', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 99),
(70, 0, 'Javanese', 'Basa Jawa', 'jv', 'jv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 44),
(71, 0, 'Georgian', 'ქართული', 'ka', 'ka', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 53),
(72, 0, 'Kongo', 'KiKongo', 'kg', 'kg', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(73, 0, 'Kazakh', 'Қазақ тілі', 'kk', 'kk', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 72),
(74, 0, 'Central Khmer', 'ភាសាខ្មែរ', 'km', 'km', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(75, 0, 'Kannada', 'ಕನ್ನಡ', 'kn', 'kn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(76, 0, 'Korean', '韓國語', 'ko', 'ko', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 88),
(77, 0, 'Kanuri', 'Kanuri', 'kr', 'kr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(78, 0, 'Kashmiri', '\"कश्मीरी, كشميري‎\"', 'ks', 'ks', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(79, 0, 'Kurdish', 'Kurdî', 'ku', 'ku', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 20),
(80, 0, 'Komi', 'Kоми кыв', 'kv', 'kv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(81, 0, 'Kirghiz', 'Kыргыз тили', 'ky', 'ky', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(82, 0, 'Latin', 'Latine', 'la', 'la', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 58),
(83, 0, 'Luxembourgish', 'Lëtzebuergesch', 'lb', 'lb', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 39),
(84, 0, 'Luganda', 'Luganda', 'lg', 'lg', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(85, 0, 'Lingala', 'Lingála', 'ln', 'ln', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(86, 0, 'Lao', 'ພາສາລາວ', 'lo', 'lo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(87, 0, 'Lithuanian', 'Lietuvių', 'lt', 'lt', '0000-00-00 00:00:00', '2015-11-06 15:50:38', 200),
(88, 0, 'Luba-Katanga', 'Luba-Katanga', 'lu', 'lu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(89, 0, 'Latvian', 'Latviešu', 'lv', 'lv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 45),
(90, 0, 'Malagasy', 'Malagasy fiteny', 'mg', 'mg', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 35),
(91, 0, 'Marshallese', 'Kajin M̧ajeļ', 'mh', 'mh', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(92, 0, 'Manx', 'Gaelg, Gailck', 'gv', 'gv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(93, 0, 'Māori', 'Te reo Māori', 'mi', 'mi', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(94, 0, 'Macedonian', 'македонски јазик', 'mk', 'mk', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 54),
(95, 0, 'Malayalam', 'മലയാളം', 'ml', 'ml', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 26),
(96, 0, 'Mongolian', 'Монгол', 'mn', 'mn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(97, 0, 'Marathi', 'मराठी', 'mr', 'mr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 41),
(98, 0, 'Malay', 'Bahasa Melayu, بهاس ملايو‎', 'ms', 'ms', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 77),
(99, 0, 'Maltese', 'Malti', 'mt', 'mt', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(100, 0, 'Nauru', 'Ekakairũ Naoero', 'na', 'na', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(101, 0, 'North Ndebele', 'IsiNdebele', 'nd', 'nd', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(102, 0, 'Nepali', 'नेपाली', 'ne', 'ne', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 15),
(103, 0, 'Ndonga', 'Owambo', 'ng', 'ng', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(104, 0, 'Dutch', 'Nederlands', 'nl', 'nl', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 104),
(105, 0, 'Norwegian', 'Norsk', 'no', 'no', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 93),
(106, 0, 'South Ndebele', 'IsiNdebele', 'nr', 'nr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(107, 0, 'Navajo', 'Diné bizaad', 'nv', 'nv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(108, 0, 'Oromo', 'Afaan Oromoo', 'om', 'om', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(109, 0, 'Oriya', 'ଓଡ଼ିଆ', 'or', 'or', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(110, 0, 'Ossetian', 'Ирон æвзаг', 'os', 'os', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(111, 0, 'Punjabi', '\"ਪੰਜਾਬੀ', 'pa', 'pa', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(112, 0, 'Pāli', 'पाऴि', 'pi', 'pi', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(113, 0, 'Persian', 'فارسی', 'fa', 'fa', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 84),
(114, 0, 'Polish', 'Polski', 'pl', 'pl', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 101),
(115, 0, 'Portuguese', 'Português', 'pt', 'pt', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 98),
(116, 0, 'Quechua', 'Kichwa', 'qu', 'qu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 17),
(117, 0, 'Kirundi', 'Rundi', 'rn', 'rn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(118, 0, 'Romanian', 'Română', 'ro', 'ro', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 85),
(119, 0, 'Russian', 'Русский', 'ru', 'ru', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 100),
(120, 0, 'Kinyarwanda', 'Ikinyarwanda', 'rw', 'rw', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(121, 0, 'Sanskrit', 'संस्कृतम्', 'sa', 'sa', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(122, 0, 'Sardinian', 'Sardu', 'sc', 'sc', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(123, 0, 'Sindhi', '\"सिन्धी, سنڌي، سندھی‎\"', 'sd', 'sd', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(124, 0, 'Northern Sami', 'Davvisámegiella', 'se', 'se', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(125, 0, 'Samoan', 'gagana fa\'a Samoa', 'sm', 'sm', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(126, 0, 'Sango', 'yângâ tî sängö', 'sg', 'sg', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(127, 0, 'Serbian', 'Cрпски', 'sr', 'sr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 80),
(128, 0, 'Gaelic', 'Gàidhlig', 'gd', 'gd', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(129, 0, 'Shona', 'chiShona', 'sn', 'sn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(130, 0, 'Sinhala', 'සිංහල', 'si', 'si', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(131, 0, 'Slovak', 'Slovenčina', 'sk', 'sk', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 78),
(132, 0, 'Slovene', 'Slovenščina', 'sl', 'sl', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 74),
(133, 0, 'Somali', 'Soomaaliga', 'so', 'so', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(134, 0, 'Southern Sotho', 'Sesotho', 'st', 'st', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(135, 0, 'Sundanese', 'Basa Sunda', 'su', 'su', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 16),
(136, 0, 'Swahili', 'Kiswahili', 'sw', 'sw', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 28),
(137, 0, 'Swati', 'SiSwati', 'ss', 'ss', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(138, 0, 'Swedish', 'Svenska', 'sv', 'sv', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 97),
(139, 0, 'Tamil', 'தமிழ்', 'ta', 'ta', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 48),
(140, 0, 'Telugu', 'తెలుగు', 'te', 'te', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 51),
(141, 0, 'Tajik', 'Toğikī', 'tg', 'tg', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(142, 0, 'Thai', 'ไทย', 'th', 'th', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 62),
(143, 0, 'Tigrinya', 'ትግርኛ', 'ti', 'ti', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(144, 0, 'Tibetan', 'བོད་ཡིག', 'bo', 'bo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(145, 0, 'Turkmen', 'Türkmen', 'tk', 'tk', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(146, 0, 'Tagalog', 'Wikang Tagalog', 'tl', 'tl', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 56),
(147, 0, 'Tswana', 'Setswana', 'tn', 'tn', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(148, 0, 'Tonga', 'Faka Tonga', 'to', 'to', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(149, 0, 'Turkish', 'Türkçe', 'tr', 'tr', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 86),
(150, 0, 'Tsonga', 'Xitsonga', 'ts', 'ts', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(151, 0, 'Tatar', 'Tatarça', 'tt', 'tt', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 12),
(152, 0, 'Twi', 'Twi', 'tw', 'tw', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(153, 0, 'Tahitian', 'Reo Mā`ohi', 'ty', 'ty', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(154, 0, 'Uighur', 'Uyƣurqə', 'ug', 'ug', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(155, 0, 'Ukrainian', 'Українська', 'uk', 'ua', '0000-00-00 00:00:00', '2024-10-18 19:25:57', 94),
(156, 0, 'Urdu', 'اردو', 'ur', 'ur', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 21),
(157, 0, 'Uzbek', 'O\'zbek', 'uz', 'uz', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(158, 0, 'Venda', 'Tshivenḓa', 've', 've', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(159, 0, 'Vietnamese', 'Tiếng Việt', 'vi', 'vi', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 91),
(160, 0, 'Volapük', 'Volapük', 'vo', 'vo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 73),
(161, 0, 'Walloon', 'Walon', 'wa', 'wa', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 7),
(162, 0, 'Welsh', 'Cymraeg', 'cy', 'cy', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 40),
(163, 0, 'Wolof', 'Wollof', 'wo', 'wo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(164, 0, 'Xhosa', 'IsiXhosa', 'xh', 'xh', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(165, 0, 'Yoruba', 'Yorùbá', 'yo', 'yo', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 36),
(166, 0, 'Zhuang', 'Saw cuengh', 'za', 'za', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(167, 0, 'Zulu', 'IsiZulu', 'zu', 'zu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_data_languages`
--
ALTER TABLE `gw_data_languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sys` (`sys`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_data_languages`
--
ALTER TABLE `gw_data_languages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
