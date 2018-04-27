/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : integraldev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-04-27 17:59:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gr_integral_add
-- ----------------------------
DROP TABLE IF EXISTS `gr_integral_add`;
CREATE TABLE `gr_integral_add` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integral_name` varchar(255) NOT NULL COMMENT '積分名稱',
  `integral_num` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `integral_type` int(2) NOT NULL DEFAULT '1' COMMENT '積分類型： 1：德  2：智 3：體 4：群 5：美',
  `s_remark` text COMMENT '積分要求',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='積分配置表';
