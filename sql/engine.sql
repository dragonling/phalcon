SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `scrapy` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `scrapy`;

DROP TABLE IF EXISTS `eva_blog_archives`;
CREATE TABLE IF NOT EXISTS `eva_blog_archives` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postId` int(10) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `archivedAt` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`postId`),
  KEY `post_id_2` (`postId`)
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
  `imageId` int(10) DEFAULT NULL,
  `image` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

DROP TABLE IF EXISTS `eva_blog_categories_posts`;
CREATE TABLE IF NOT EXISTS `eva_blog_categories_posts` (
  `categoryId` int(11) NOT NULL,
  `postId` int(11) NOT NULL,
  PRIMARY KEY (`categoryId`,`postId`)
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
  `userId` int(10) DEFAULT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updatedAt` int(10) DEFAULT NULL,
  `editorId` int(10) DEFAULT NULL,
  `editor_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentStatus` enum('open','closed','authority') COLLATE utf8_unicode_ci DEFAULT 'open',
  `commentType` varchar(15) COLLATE utf8_unicode_ci DEFAULT 'local',
  `commentCount` int(10) DEFAULT '0',
  `count` bigint(20) DEFAULT '0',
  `imageId` int(10) DEFAULT NULL,
  `image` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `summary` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sourceName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sourceUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `createdAt` (`createdAt`),
  KEY `slug_2` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6850 ;

DROP TABLE IF EXISTS `eva_blog_tags`;
CREATE TABLE IF NOT EXISTS `eva_blog_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tagName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `parentId` int(10) DEFAULT '0',
  `rootId` int(10) DEFAULT '0',
  `sortOrder` int(10) DEFAULT '0',
  `count` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14767 ;

DROP TABLE IF EXISTS `eva_blog_tags_posts`;
CREATE TABLE IF NOT EXISTS `eva_blog_tags_posts` (
  `tagId` int(10) NOT NULL,
  `postId` int(10) NOT NULL,
  PRIMARY KEY (`tagId`,`postId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_blog_texts`;
CREATE TABLE IF NOT EXISTS `eva_blog_texts` (
  `postId` int(20) NOT NULL,
  `metaKeywords` text COLLATE utf8_unicode_ci,
  `metaDescription` text COLLATE utf8_unicode_ci,
  `toc` text COLLATE utf8_unicode_ci,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`postId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_comment_comments`;
CREATE TABLE IF NOT EXISTS `eva_comment_comments` (
  `id` int(11) NOT NULL,
  `threadId` int(11) NOT NULL,
  `status` enum('approved','pending','spam','deleted') NOT NULL DEFAULT 'pending',
  `sourceCode` varchar(30) DEFAULT NULL,
  `content` text NOT NULL,
  `parentId` int(10) DEFAULT NULL,
  `parentPath` varchar(200) DEFAULT NULL,
  `depth` tinyint(4) DEFAULT NULL,
  `userId` int(10) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `userSite` varchar(255) DEFAULT NULL,
  `userType` varchar(50) DEFAULT NULL,
  `sourceName` varchar(50) DEFAULT NULL,
  `sourceUrl` varchar(255) DEFAULT NULL,
  `createdAt` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `threadId` (`threadId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `eva_comment_threads`;
CREATE TABLE IF NOT EXISTS `eva_comment_threads` (
  `id` int(11) NOT NULL,
  `threadKey` varchar(255) NOT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `isCommentAble` varchar(45) DEFAULT NULL,
  `numComments` varchar(45) DEFAULT NULL,
  `lastCommentAt` int(10) DEFAULT NULL,
  `channel` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `threadKey` (`threadKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `eva_file_files`;
CREATE TABLE IF NOT EXISTS `eva_file_files` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('deleted','draft','published','pending') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'published',
  `storageAdapter` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'local',
  `isImage` tinyint(1) NOT NULL DEFAULT '0',
  `fileName` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fileExtension` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `originalName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filePath` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `fileHash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fileSize` bigint(20) DEFAULT NULL,
  `mimeType` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `imageWidth` smallint(5) DEFAULT NULL,
  `imageHeight` smallint(5) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortOrder` int(10) DEFAULT NULL,
  `userId` int(10) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdAt` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

DROP TABLE IF EXISTS `eva_oauth_accesstokens`;
CREATE TABLE IF NOT EXISTS `eva_oauth_accesstokens` (
  `adapterKey` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(520) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
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
  `userId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`adapterKey`,`version`,`remoteUserId`),
  KEY `user_id` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_techanalysis_moving_averages`;
CREATE TABLE IF NOT EXISTS `eva_techanalysis_moving_averages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period` enum('mn','1d','5h','1h','30m','15m','5m','1m') COLLATE utf8_unicode_ci DEFAULT NULL,
  `maPeriod` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `maSimpleValue` float DEFAULT NULL,
  `maSimpleAction` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `maExponentialValue` float DEFAULT NULL,
  `maExponentialAction` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol-period-ma_period` (`symbol`,`period`,`maPeriod`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=955 ;

DROP TABLE IF EXISTS `eva_techanalysis_pivot_points`;
CREATE TABLE IF NOT EXISTS `eva_techanalysis_pivot_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period` enum('mn','1d','5h','1h','30m','15m','5m','1m') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s3` float DEFAULT NULL,
  `s2` float DEFAULT NULL,
  `s1` float DEFAULT NULL,
  `pivotPoints` float DEFAULT NULL,
  `r1` float DEFAULT NULL,
  `r2` float DEFAULT NULL,
  `r3` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol-period-name` (`symbol`,`period`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=796 ;

DROP TABLE IF EXISTS `eva_techanalysis_quotes`;
CREATE TABLE IF NOT EXISTS `eva_techanalysis_quotes` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('forex','commodity','indice','stock','etf','bond','cfdindice','cfdcommodity','cfdbond','cfdstock','cfdforex') COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period1m` text COLLATE utf8_unicode_ci,
  `period5m` text COLLATE utf8_unicode_ci,
  `period15m` text COLLATE utf8_unicode_ci,
  `period30m` text COLLATE utf8_unicode_ci,
  `period1h` text COLLATE utf8_unicode_ci,
  `period5h` text COLLATE utf8_unicode_ci,
  `period1d` text COLLATE utf8_unicode_ci,
  `periodmn` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol` (`symbol`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=133 ;

DROP TABLE IF EXISTS `eva_techanalysis_summaries`;
CREATE TABLE IF NOT EXISTS `eva_techanalysis_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period` enum('mn','1d','5h','1h','30m','15m','5m','1m') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buy` int(11) DEFAULT NULL,
  `sell` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol-period-name` (`symbol`,`period`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=319 ;

DROP TABLE IF EXISTS `eva_techanalysis_technical_indicators`;
CREATE TABLE IF NOT EXISTS `eva_techanalysis_technical_indicators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period` enum('mn','1d','5h','1h','30m','15m','5m','1m') COLLATE utf8_unicode_ci DEFAULT NULL,
  `tiSymbol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tiValue` float DEFAULT NULL,
  `tiAction` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol-period-ti_symbol` (`symbol`,`period`,`tiSymbol`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1909 ;

DROP TABLE IF EXISTS `eva_user_apikeys`;
CREATE TABLE IF NOT EXISTS `eva_user_apikeys` (
  `userId` int(10) NOT NULL,
  `apikey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_user_profiles`;
CREATE TABLE IF NOT EXISTS `eva_user_profiles` (
  `userId` int(10) NOT NULL,
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
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_user_tokens`;
CREATE TABLE IF NOT EXISTS `eva_user_tokens` (
  `sessionId` varchar(40) COLLATE utf8_bin NOT NULL,
  `token` varchar(32) COLLATE utf8_bin NOT NULL,
  `userHash` varchar(32) COLLATE utf8_bin NOT NULL,
  `userId` int(10) NOT NULL,
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
  `avatarId` int(10) DEFAULT '0',
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
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
