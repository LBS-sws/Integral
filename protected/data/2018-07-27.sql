/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : grdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-07-27 16:14:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gr_credit_type
-- ----------------------------
DROP TABLE IF EXISTS `gr_credit_type`;
CREATE TABLE `gr_credit_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `credit_code` varchar(255) DEFAULT NULL,
  `credit_name` varchar(255) NOT NULL COMMENT '積分名稱',
  `credit_point` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `category` int(2) NOT NULL DEFAULT '1' COMMENT '積分類型： 1：德  2：智 3：體 4：群 5：美',
  `rule` text COMMENT '得分条件',
  `year_sw` int(2) NOT NULL DEFAULT '0' COMMENT '0:無年限限制  1：有年限限制',
  `year_max` int(11) DEFAULT '0' COMMENT '每年限制申請次數',
  `validity` int(11) NOT NULL DEFAULT '5' COMMENT '有效期 1:1年有效期  5:5年有效期',
  `z_index` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='學分配置表';
