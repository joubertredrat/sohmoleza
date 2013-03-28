-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2013 at 04:01 PM
-- Server version: 5.5.29
-- PHP Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `code`
--

-- --------------------------------------------------------

--
-- Table structure for table `aluno`
--

CREATE TABLE IF NOT EXISTS `aluno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `idade` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `zurango`
--

CREATE TABLE IF NOT EXISTS `zurango` (
  `id_zurango` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Chave identificadora da profissão no banco',
  `fk_grupo` int(11) NOT NULL COMMENT 'Eu faço ligação com tabela grupos',
  `fk_relacao` int(11) DEFAULT NULL COMMENT 'Eu faço ligação com a tabela relações',
  `fk_cidade` int(11) NOT NULL COMMENT 'Haha, eu sou uma cidade.',
  `varchar` varchar(50) NOT NULL COMMENT 'Eu sou um varchar com 50 caracteres',
  `text` text NOT NULL COMMENT 'Eu sou um text com texto',
  `char` char(5) DEFAULT NULL COMMENT 'Eu sou um char com 5 caracteres',
  `inteiro` int(8) DEFAULT NULL COMMENT 'Sou um inteiro com 8 posições',
  `biginteiro` bigint(20) NOT NULL,
  `flutuante` float(10,2) NOT NULL COMMENT 'Float aos 10 só eu',
  `dobro` double NOT NULL COMMENT 'Duplamente flutuante somente comigo',
  `eh_ou_nao_eh` tinyint(1) NOT NULL COMMENT 'True ou false, eis a questão',
  `unix` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Na era unix time eu que mando',
  `data_e_hora` datetime NOT NULL COMMENT 'Funciona com data ou hora',
  `data` date DEFAULT NULL COMMENT 'Unicamente data',
  `horario` time NOT NULL COMMENT 'Eu sou o senhor hora',
  `ano` year(4) NOT NULL COMMENT 'Passa ano e eu não mudo',
  PRIMARY KEY (`id_zurango`),
  UNIQUE KEY `fk_grupo` (`fk_grupo`),
  KEY `fk_relacao` (`fk_relacao`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Listagem em nível superior dos tipos de zurangos presentes' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
