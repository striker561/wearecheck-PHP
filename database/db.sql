SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `tbl_album` (
  `id` char(26) NOT NULL,
  `userId` char(26) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_comment` (
  `id` char(26) NOT NULL,
  `postId` char(26) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_photo` (
  `id` char(26) NOT NULL,
  `albumId` char(26) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `thumbnailUrl` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_post` (
  `id` char(26) NOT NULL,
  `userId` char(26) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `tbl_todo` (
  `id` char(26) NOT NULL,
  `userId` char(26) NOT NULL,
  `title` varchar(100) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `tbl_user` (
  `id` char(26) NOT NULL,
  `name` varchar(40) NOT NULL,
  `username` varchar(20) NOT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`address`)),
  `phone` varchar(25) NOT NULL,
  `website` varchar(20) NOT NULL,
  `company` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`company`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `tbl_album`
  ADD PRIMARY KEY (`id`),
  ADD KEY `albumUserFK` (`userId`);

ALTER TABLE `tbl_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postCommentFK` (`postId`);

ALTER TABLE `tbl_photo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photoAlbumFK` (`albumId`);

ALTER TABLE `tbl_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postUserFk` (`userId`);


ALTER TABLE `tbl_todo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `todoUserFK` (`userId`);


ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `tbl_album`
  ADD CONSTRAINT `albumUserFK` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_comment`
  ADD CONSTRAINT `postCommentFK` FOREIGN KEY (`postId`) REFERENCES `tbl_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_photo`
  ADD CONSTRAINT `photoAlbumFK` FOREIGN KEY (`albumId`) REFERENCES `tbl_album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_post`
  ADD CONSTRAINT `postUserFk` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_todo`
  ADD CONSTRAINT `todoUserFK` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
