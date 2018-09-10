/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : grdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-09-10 10:39:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gr_prize_type
-- ----------------------------
DROP TABLE IF EXISTS `gr_prize_type`;
CREATE TABLE `gr_prize_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prize_name` varchar(255) NOT NULL COMMENT '獎項名稱',
  `prize_point` int(11) DEFAULT NULL COMMENT '獎項扣除學分',
  `min_point` int(11) DEFAULT '0' COMMENT '申請時的最小學分',
  `tries_limit` int(2) DEFAULT '0' COMMENT '0:無次數限制  1：有次數限制',
  `limit_number` int(11) DEFAULT '0' COMMENT '次數限制',
  `remark` text COMMENT '備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='獎項類別';
