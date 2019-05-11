--
-- Table structure and data for table `admin_resources`
--

CREATE TABLE `admin_resources`
(
    `id`         char(36)            NOT NULL,
    `identifier` varchar(160)        NOT NULL,
    `created_at` timestamp           NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted`    tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `identifier` (`identifier`),
    KEY `admin_resource_deleted_index` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `admin_resources` (`id`, `identifier`)
VALUES (UUID(), 'adminresources'),
       (UUID(), 'users'),
       (UUID(), 'usergroups'),
       (UUID(), 'usergroups_adminresources'),
       (UUID(), 'userapikeys');

--
-- Table structure and data for table `casbin_rule`
--

CREATE TABLE `casbin_rule`
(
    `id`    int(11)      NOT NULL AUTO_INCREMENT,
    `ptype` varchar(255) NOT NULL,
    `v0`    varchar(255) DEFAULT NULL,
    `v1`    varchar(255) DEFAULT NULL,
    `v2`    varchar(255) DEFAULT NULL,
    `v3`    varchar(255) DEFAULT NULL,
    `v4`    varchar(255) DEFAULT NULL,
    `v5`    varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Table structure and data for table `login_attempts`
--

CREATE TABLE `login_attempts`
(
    `id`         char(36)    NOT NULL,
    `ip_hash`    char(32)    NOT NULL,
    `username`   varchar(64) NOT NULL,
    `ip_address` varchar(32)          DEFAULT NULL,
    `created_at` timestamp   NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `ip_hash` (`ip_hash`),
    KEY `username` (`username`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Table structure and data for table `user_groups`
--

CREATE TABLE `user_groups`
(
    `id`         char(36)            NOT NULL,
    `identifier` varchar(160)        NOT NULL,
    `name`       varchar(128)        NOT NULL,
    `created_at` timestamp           NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted`    tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `identifier` (`identifier`),
    UNIQUE KEY `uniq_name` (`name`),
    KEY `users_deleted_index` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `user_groups` (`id`, `identifier`, `name`)
VALUES (UUID(), 'admin', 'Admin');

--
-- Table structure and data for table `user_groups_admin_resources`
--

CREATE TABLE `user_groups_admin_resources`
(
    `id`                char(36)  NOT NULL,
    `user_group_id`     char(36)  NOT NULL,
    `admin_resource_id` char(36)  NOT NULL,
    `created_at`        timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`        timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_group_id` (`user_group_id`),
    KEY `admin_resource_id` (`admin_resource_id`),
    CONSTRAINT `ugar_ibfk_1` FOREIGN KEY (`admin_resource_id`) REFERENCES `admin_resources` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ugar_ibfk_2` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE NO ACTION
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Table structure and data for table `user_languages`
--

CREATE TABLE `user_languages`
(
    `id`         char(36)            NOT NULL,
    `identifier` varchar(8)          NOT NULL,
    `name`       varchar(128)        NOT NULL,
    `deleted`    tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `identifier` (`identifier`),
    KEY `user_languages_deleted_index` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `user_languages` (`id`, `identifier`, `name`)
VALUES (UUID(), 'en', 'English'),
       (UUID(), 'hu', 'Hungarian');

--
-- Table structure and data for table `users`
--

CREATE TABLE `users`
(
    `id`                  char(36)            NOT NULL,
    `email`               varchar(127)        NOT NULL,
    `username`            varchar(64)         NOT NULL DEFAULT '',
    `password`            text                NOT NULL,
    `user_language_id`    char(36)            NOT NULL,
    `can_login`           tinyint(1) unsigned NOT NULL DEFAULT 1,
    `is_gravatar_allowed` tinyint(1) unsigned NOT NULL DEFAULT 1,
    `created_at`          timestamp           NOT NULL DEFAULT current_timestamp(),
    `updated_at`          timestamp           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted`             tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_username` (`username`),
    UNIQUE KEY `uniq_email` (`email`),
    KEY `users_deleted_index` (`deleted`),
    KEY `user_language_id` (`user_language_id`),
    CONSTRAINT `user_language_id` FOREIGN KEY (`user_language_id`) REFERENCES `user_languages` (`id`) ON DELETE NO ACTION
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Table structure and data for table `users`
--

CREATE TABLE `users_user_groups`
(
    `id`            char(36)            NOT NULL,
    `user_id`       char(36)            NOT NULL,
    `user_group_id` char(36)            NOT NULL,
    `created_at`    timestamp           NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted`       tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `user_group_id` (`user_group_id`),
    KEY `user_group_deleted_index` (`deleted`),
    CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_group_id` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE NO ACTION
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Table structure and data for table `user_api_keys`
--

CREATE TABLE `user_api_keys`
(
    `id`          char(36)            NOT NULL,
    `user_id`     char(36)            NOT NULL,
    `description` mediumtext          NOT NULL,
    `created_at`  timestamp           NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted`     tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `users_deleted_index` (`deleted`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `user_api_keys_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Table structure and data for table `user_api_keys_admin_resources`
--

CREATE TABLE `user_api_keys_admin_resources`
(
    `id`                char(36)  NOT NULL,
    `user_api_key_id`   char(36)  NOT NULL,
    `admin_resource_id` char(36)  NOT NULL,
    `created_at`        timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`        timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_api_key_id` (`user_api_key_id`),
    KEY `admin_resource_id` (`admin_resource_id`),
    CONSTRAINT `uakar_ibfk_1` FOREIGN KEY (`admin_resource_id`) REFERENCES `admin_resources` (`id`) ON DELETE CASCADE,
    CONSTRAINT `uakar_ibfk_2` FOREIGN KEY (`user_api_key_id`) REFERENCES `user_api_keys` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- Provide admins access to all admin resources
INSERT IGNORE INTO `user_groups_admin_resources` (`id`, `user_group_id`, `admin_resource_id`)
SELECT UUID(), user_groups.id AS user_group_id, admin_resources.id AS admin_resource_id
FROM user_groups
         INNER JOIN admin_resources ON 1
WHERE user_groups.identifier = 'admin';
