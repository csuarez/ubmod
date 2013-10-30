SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateStorageAggregateByAll`()
BEGIN
  TRUNCATE `agg_storage_by_all`;

  INSERT INTO `agg_storage_by_all` (
    `dim_date_id`,
    `dim_user_id`,
    `dim_group_id`,
    `dim_tags_id`,
    `fact_storage_count`,
    `space_used_sum`,
    `space_used_max`,
    `space_available_sum`,
    `space_available_max`,
    `space_quota_sum`,
    `space_quota_max`,
    `inodes_used_sum`,
    `inodes_used_max`,
    `inodes_available_sum`,
    `inodes_available_max`,
    `inodes_quota_sum`,
    `inodes_quota_max` 
  )
  SELECT
    `fact_storage`.`dim_date_id`,
    `fact_storage`.`dim_user_id`,
    `fact_storage`.`dim_group_id`,
    `fact_storage`.`dim_tags_id`,
    COUNT(*),
    SUM(`space_used`),
    MAX(`space_used`),
    SUM(`space_available`),
    MAX(`space_available`),
    SUM(`space_quota`),
    MAX(`space_quota`),
    SUM(`inodes_used`),
    MAX(`inodes_used`),
    SUM(`inodes_available`),
    MAX(`inodes_available`),
    SUM(`inodes_quota`),
    MAX(`inodes_quota`)
  FROM `fact_storage`
  GROUP BY
    `fact_storage`.`dim_date_id`,
    `fact_storage`.`dim_user_id`,
    `fact_storage`.`dim_group_id`,
    `fact_storage`.`dim_tags_id`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateStorageAggregateByTimespan`()
BEGIN
  TRUNCATE `agg_storage_by_timespan`;

  INSERT INTO `agg_storage_by_timespan` (
    `dim_timespan_id`,
    `dim_user_id`,
    `dim_group_id`,
    `dim_tags_id`,
    `fact_storage_count`,
    `space_used_sum`,
    `space_used_max`,
    `space_available_sum`,
    `space_available_max`,
    `space_quota_sum`,
    `space_quota_max`,
    `inodes_used_sum`,
    `inodes_used_max`,
    `inodes_available_sum`,
    `inodes_available_max`,
    `inodes_quota_sum`,
    `inodes_quota_max`
  )
  SELECT
    `dim_timespan`.`dim_timespan_id`,
    `fact_storage`.`dim_user_id`,
    `fact_storage`.`dim_group_id`,
    `fact_storage`.`dim_tags_id`,
    COUNT(*),
    SUM(`space_used`),
    MAX(`space_used`),
    SUM(`space_available`),
    MAX(`space_available`),
    SUM(`space_quota`),
    MAX(`space_quota`),
    SUM(`inodes_used`),
    MAX(`inodes_used`),
    SUM(`inodes_available`),
    MAX(`inodes_available`),
    SUM(`inodes_quota`),
    MAX(`inodes_quota`)
  FROM `fact_storage`
  JOIN `dim_date` ON `fact_storage`.`dim_date_id` = `dim_date`.`dim_date_id`
  JOIN `dim_timespan` ON
        `dim_date`.`month`         = `dim_timespan`.`month`
    AND `dim_date`.`year`          = `dim_timespan`.`year`
    AND `dim_date`.`last_7_days`   = `dim_timespan`.`last_7_days`
    AND `dim_date`.`last_30_days`  = `dim_timespan`.`last_30_days`
    AND `dim_date`.`last_90_days`  = `dim_timespan`.`last_90_days`
    AND `dim_date`.`last_365_days` = `dim_timespan`.`last_365_days`
  GROUP BY
    `dim_timespan`.`dim_timespan_id`,
    `fact_storage`.`dim_user_id`,
    `fact_storage`.`dim_group_id`,
    `fact_storage`.`dim_tags_id`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateStorageAggregates`()
BEGIN
  CALL UpdateStorageAggregateByAll();
  CALL UpdateStorageAggregateByTimespan();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateStorageFacts`()
BEGIN
  TRUNCATE `fact_storage`;

  INSERT INTO `fact_storage` (
  `dim_date_id`,
  `dim_user_id`,
  `dim_group_id`,
  `dim_tags_id`,
  `inodes_used`,
  `inodes_available`,
  `inodes_quota`,
  `space_used`,
  `space_available`,
  `space_quota`              
  )
  SELECT
    `dim_date`.`dim_date_id`,
    `dim_user`.`dim_user_id`,
    `dim_group`.`dim_group_id`,
    `dim_tags`.`dim_tags_id`,
    `storage_event`.`inodes_used`,
    `storage_event`.`inodes_available`,
    `storage_event`.`inodes_quota`,
    `storage_event`.`space_used`,
    `storage_event`.`space_available`,
    `storage_event`.`space_quota`
  FROM `storage_event`
  JOIN `dim_date`    ON `storage_event`.`date_key` = `dim_date`.`date`
  JOIN `dim_user`    ON `storage_event`.`user`     = `dim_user`.`name`
  JOIN `dim_tags`    ON `storage_event`.`tags`     = `dim_tags`.`tags`
  JOIN `dim_group`   ON `storage_event`.`group`    = `dim_group`.`name`;
END$$

DELIMITER ;

CREATE TABLE IF NOT EXISTS `agg_storage_by_all` (
  `agg_storage_by_all_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dim_date_id` int(10) unsigned NOT NULL,
  `dim_user_id` int(10) unsigned DEFAULT NULL,
  `dim_group_id` int(10) unsigned DEFAULT NULL,
  `dim_tags_id` int(10) unsigned NOT NULL,
  `fact_storage_count` int(10) unsigned NOT NULL,
  `space_used_sum` bigint(20) unsigned NOT NULL,
  `space_used_max` bigint(20) unsigned NOT NULL,
  `space_available_sum` bigint(20) unsigned NOT NULL,
  `space_available_max` bigint(20) unsigned NOT NULL,
  `space_quota_sum` bigint(20) unsigned NOT NULL,
  `space_quota_max` bigint(20) unsigned NOT NULL,
  `inodes_used_sum` bigint(20) unsigned NOT NULL,
  `inodes_used_max` bigint(20) unsigned NOT NULL,
  `inodes_available_sum` bigint(20) unsigned NOT NULL,
  `inodes_available_max` bigint(20) unsigned NOT NULL,
  `inodes_quota_sum` bigint(20) unsigned NOT NULL,
  `inodes_quota_max` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`agg_storage_by_all_id`),
  KEY `dim_date_id` (`dim_date_id`,`dim_user_id`,`dim_group_id`,`dim_tags_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `agg_storage_by_timespan` (
  `agg_storage_by_timespan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dim_timespan_id` int(10) unsigned NOT NULL,
  `dim_user_id` int(10) unsigned DEFAULT NULL,
  `dim_group_id` int(10) unsigned DEFAULT NULL,
  `dim_tags_id` int(10) unsigned NOT NULL,
  `fact_storage_count` int(10) unsigned NOT NULL,
  `space_used_sum` bigint(20) unsigned NOT NULL,
  `space_used_max` bigint(20) unsigned NOT NULL,
  `space_available_sum` bigint(20) unsigned NOT NULL,
  `space_available_max` bigint(20) unsigned NOT NULL,
  `space_quota_sum` bigint(20) unsigned NOT NULL,
  `space_quota_max` bigint(20) unsigned NOT NULL,
  `inodes_used_sum` bigint(20) unsigned NOT NULL,
  `inodes_used_max` bigint(20) unsigned NOT NULL,
  `inodes_available_sum` bigint(20) unsigned NOT NULL,
  `inodes_available_max` bigint(20) unsigned NOT NULL,
  `inodes_quota_sum` bigint(20) unsigned NOT NULL,
  `inodes_quota_max` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`agg_storage_by_timespan_id`),
  KEY `dim_timespan_id` (`dim_timespan_id`,`dim_user_id`,`dim_group_id`,`dim_tags_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `fact_storage` (
  `fact_storage_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dim_date_id` int(10) unsigned NOT NULL,
  `dim_user_id` int(10) unsigned DEFAULT NULL,
  `dim_group_id` int(10) unsigned DEFAULT NULL,
  `dim_tags_id` int(10) unsigned NOT NULL,
  `space_used` bigint(20) unsigned NOT NULL,
  `space_available` bigint(20) unsigned NOT NULL,
  `space_quota` bigint(20) unsigned NOT NULL,
  `inodes_used` bigint(20) unsigned NOT NULL,
  `inodes_available` bigint(20) unsigned NOT NULL,
  `inodes_quota` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`fact_storage_id`),
  KEY `dim_user_id` (`dim_user_id`,`dim_date_id`,`dim_group_id`,`dim_tags_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `storage_event` (
  `storage_event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date_key` date NOT NULL,
  `storage_id` int(10) unsigned NOT NULL,
  `user` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `tags` varchar(255) NOT NULL DEFAULT '[]',
  `project` varchar(255) NOT NULL DEFAULT 'Unknown',
  `inodes_used` bigint(20) unsigned NOT NULL,
  `inodes_available` bigint(20) unsigned NOT NULL,
  `inodes_quota` bigint(20) unsigned NOT NULL,
  `space_used` bigint(20) unsigned NOT NULL,
  `space_available` bigint(20) unsigned NOT NULL,
  `space_quota` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`storage_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
