-- phpMyAdmin SQL Dump
-- version 4.0.1
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Skapad: 28 okt 2016 kl 23:18
-- Serverversion: 5.1.52
-- PHP-version: 5.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `admin_devzida`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_blogs`
--

CREATE TABLE IF NOT EXISTS `zida_blogs` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_user_id` int(11) NOT NULL,
  `blog_name` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blog_url` varchar(100) NOT NULL,
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY `blog_url` (`blog_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_blogs_permissions`
--

CREATE TABLE IF NOT EXISTS `zida_blogs_permissions` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `p_role` int(11) NOT NULL,
  `p_type` varchar(10) NOT NULL,
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2118 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_blogs_posts`
--

CREATE TABLE IF NOT EXISTS `zida_blogs_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `post_headline` varchar(255) NOT NULL,
  `post_content` text NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_url` varchar(255) NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_comments`
--

CREATE TABLE IF NOT EXISTS `zida_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `comment_name` varchar(50) NOT NULL,
  `comment_message` text NOT NULL,
  `comment_email` varchar(50) NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_url` text NOT NULL,
  `comment_reply` int(11) NOT NULL,
  `comment_ip` varchar(16) NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_images`
--

CREATE TABLE IF NOT EXISTS `zida_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_real_path` varchar(255) NOT NULL,
  `image_size` int(11) NOT NULL,
  `image_checksum` varchar(32) NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_layouts`
--

CREATE TABLE IF NOT EXISTS `zida_layouts` (
  `layout_id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `layout_name` varchar(100) NOT NULL,
  `layout_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator_user_id` int(11) NOT NULL,
  PRIMARY KEY (`layout_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_pages`
--

CREATE TABLE IF NOT EXISTS `zida_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_owner` int(11) NOT NULL,
  `page_parent_id` int(11) NOT NULL,
  `page_layout` int(11) NOT NULL,
  `page_url` varchar(255) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_projects`
--

CREATE TABLE IF NOT EXISTS `zida_projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_name` varchar(255) NOT NULL,
  `project_url` varchar(255) NOT NULL,
  PRIMARY KEY (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_release`
--

CREATE TABLE IF NOT EXISTS `zida_release` (
  `release_id` int(11) NOT NULL AUTO_INCREMENT,
  `release_mail` varchar(50) NOT NULL,
  `release_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`release_id`),
  UNIQUE KEY `release_mail` (`release_mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_users`
--

CREATE TABLE IF NOT EXISTS `zida_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recruit` varchar(40) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unik` (`username`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_users_auth_fb`
--

CREATE TABLE IF NOT EXISTS `zida_users_auth_fb` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fb_uid` bigint(20) NOT NULL,
  PRIMARY KEY (`auth_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_users_forgot`
--

CREATE TABLE IF NOT EXISTS `zida_users_forgot` (
  `forgot_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `forgot_key` varchar(32) NOT NULL DEFAULT '',
  `forgot_ip` varchar(16) NOT NULL DEFAULT '',
  `forgot_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`forgot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `zida_users_log`
--

CREATE TABLE IF NOT EXISTS `zida_users_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_ip` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
