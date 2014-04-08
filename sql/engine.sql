SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `scrapy` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `scrapy`;

DROP TABLE IF EXISTS `eva_blog_archives`;
CREATE TABLE IF NOT EXISTS `eva_blog_archives` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` int(10) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `archivedAt` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `post_id_2` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `eva_blog_categories`;
CREATE TABLE IF NOT EXISTS `eva_blog_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `parentId` int(10) DEFAULT '0',
  `rootId` int(10) DEFAULT '0',
  `sortOrder` int(10) DEFAULT '0',
  `createdAt` int(10) DEFAULT NULL,
  `count` int(10) DEFAULT '0',
  `leftId` int(15) DEFAULT '0',
  `rightId` int(15) DEFAULT '0',
  `image_id` int(10) DEFAULT NULL,
  `image` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

DROP TABLE IF EXISTS `eva_blog_categories_posts`;
CREATE TABLE IF NOT EXISTS `eva_blog_categories_posts` (
  `category_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_blog_posts`;
CREATE TABLE IF NOT EXISTS `eva_blog_posts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('deleted','draft','published','pending') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `flag` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visibility` enum('public','private','password') COLLATE utf8_unicode_ci NOT NULL,
  `sourceCode` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'markdown',
  `language` varchar(5) COLLATE utf8_unicode_ci DEFAULT 'en',
  `parentId` int(10) DEFAULT '0',
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sortOrder` int(10) DEFAULT '0',
  `createdAt` int(10) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updatedAt` int(10) DEFAULT NULL,
  `editor_id` int(10) DEFAULT NULL,
  `editor_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentStatus` enum('open','closed','authority') COLLATE utf8_unicode_ci DEFAULT 'open',
  `commentType` varchar(15) COLLATE utf8_unicode_ci DEFAULT 'local',
  `commentCount` int(10) DEFAULT '0',
  `count` bigint(20) DEFAULT '0',
  `image_id` int(10) DEFAULT NULL,
  `image` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `summary` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=185 ;

DROP TABLE IF EXISTS `eva_blog_tags`;
CREATE TABLE IF NOT EXISTS `eva_blog_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tagName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `parentId` int(10) DEFAULT '0',
  `rootId` int(10) DEFAULT '0',
  `sortOrder` int(10) DEFAULT '0',
  `count` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=180 ;

DROP TABLE IF EXISTS `eva_blog_tags_posts`;
CREATE TABLE IF NOT EXISTS `eva_blog_tags_posts` (
  `tag_id` int(10) NOT NULL,
  `post_id` int(10) NOT NULL,
  PRIMARY KEY (`tag_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_blog_texts`;
CREATE TABLE IF NOT EXISTS `eva_blog_texts` (
  `post_id` int(20) NOT NULL,
  `metaKeywords` text COLLATE utf8_unicode_ci,
  `metaDescription` text COLLATE utf8_unicode_ci,
  `toc` text COLLATE utf8_unicode_ci,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_oauth_accesstokens`;
CREATE TABLE IF NOT EXISTS `eva_oauth_accesstokens` (
  `adapterKey` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `version` enum('OAuth1','OAuth2') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OAuth2',
  `tokenStatus` enum('active','expried') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `scope` text COLLATE utf8_unicode_ci,
  `refreshToken` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `refreshedAt` int(10) DEFAULT NULL,
  `expireTime` datetime DEFAULT NULL,
  `remoteToken` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remoteUserId` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `remoteUserName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remoteNickName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remoteEmail` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remoteImageUrl` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remoteExtra` mediumtext COLLATE utf8_unicode_ci,
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`adapterKey`,`version`,`remoteUserId`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_user_profiles`;
CREATE TABLE IF NOT EXISTS `eva_user_profiles` (
  `user_id` int(10) NOT NULL,
  `site` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoDir` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fullName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `relationshipStatus` enum('single','inRelationship','engaged','married','widowed','separated','divorced','other') COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressMore` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `degree` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `industry` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interest` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phoneBusiness` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phoneMobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phoneHome` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signature` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `localIm` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `internalIm` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `otherIm` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updatedAt` int(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_user_tokens`;
CREATE TABLE IF NOT EXISTS `eva_user_tokens` (
  `sessionId` varchar(40) COLLATE utf8_bin NOT NULL,
  `token` varchar(32) COLLATE utf8_bin NOT NULL,
  `userHash` varchar(32) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `refreshAt` int(10) NOT NULL,
  `expiredAt` int(10) NOT NULL,
  PRIMARY KEY (`sessionId`,`token`,`userHash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `eva_user_users`;
CREATE TABLE IF NOT EXISTS `eva_user_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('active','deleted','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `accountType` enum('basic','premium','etc') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'basic',
  `screenName` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstName` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastName` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oldPassword` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar_id` int(10) DEFAULT '0',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'en',
  `emailStatus` enum('active','inactive') COLLATE utf8_unicode_ci DEFAULT 'inactive',
  `emailConfirmedAt` int(10) DEFAULT NULL,
  `createdAt` int(10) DEFAULT NULL,
  `loginAt` int(10) DEFAULT NULL,
  `failedLogins` tinyint(1) DEFAULT '0',
  `loginFailedAt` int(10) DEFAULT NULL,
  `activationHash` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `activedAt` int(10) DEFAULT NULL,
  `passwordResetHash` char(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `passwordResetAt` int(10) DEFAULT NULL,
  `providerType` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEFAULT',
  PRIMARY KEY (`id`),
  KEY `userName` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;
