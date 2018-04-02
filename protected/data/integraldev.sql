/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : integraldev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-04-02 09:36:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gr_integral
-- ----------------------------
DROP TABLE IF EXISTS `gr_integral`;
CREATE TABLE `gr_integral` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `alg_con` int(11) NOT NULL DEFAULT '0' COMMENT '0：add    1：cut',
  `set_id` varchar(255) DEFAULT NULL COMMENT '積分配置id或兌換id',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `apply_num` int(11) NOT NULL DEFAULT '1' COMMENT '申請數量',
  `images_url` text COMMENT '圖片地址',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕備註',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='員工積分表';

-- ----------------------------
-- Table structure for gr_integral_add
-- ----------------------------
DROP TABLE IF EXISTS `gr_integral_add`;
CREATE TABLE `gr_integral_add` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integral_name` varchar(255) NOT NULL COMMENT '積分名稱',
  `integral_num` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='積分配置表';

-- ----------------------------
-- Table structure for gr_integral_cut
-- ----------------------------
DROP TABLE IF EXISTS `gr_integral_cut`;
CREATE TABLE `gr_integral_cut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integral_name` varchar(255) NOT NULL COMMENT '兌換名稱',
  `integral_num` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值(負數）',
  `imges_url` text COMMENT '兌換物品的圖片',
  `inventory` int(255) NOT NULL DEFAULT '0' COMMENT '庫存',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='積分兌換表';
