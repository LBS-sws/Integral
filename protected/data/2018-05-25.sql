/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : integraldev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-05-25 11:10:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gr_act_add
-- ----------------------------
DROP TABLE IF EXISTS `gr_act_add`;
CREATE TABLE `gr_act_add` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '活動名稱',
  `start_time` date NOT NULL,
  `end_time` date NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='學分申請活動';

-- ----------------------------
-- Table structure for gr_act_cut
-- ----------------------------
DROP TABLE IF EXISTS `gr_act_cut`;
CREATE TABLE `gr_act_cut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '活動名稱',
  `start_time` date NOT NULL,
  `end_time` date NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='學分兌換活動';

-- ----------------------------
-- Table structure for gr_gral_add
-- ----------------------------
DROP TABLE IF EXISTS `gr_gral_add`;
CREATE TABLE `gr_gral_add` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '學分活動id',
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `set_id` varchar(255) DEFAULT NULL COMMENT '積分配置id',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `images_url` text COMMENT '圖片地址',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕備註',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL COMMENT '信息錄入的城市(無效字段)',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='員工積分表';

-- ----------------------------
-- Table structure for gr_gral_cut
-- ----------------------------
DROP TABLE IF EXISTS `gr_gral_cut`;
CREATE TABLE `gr_gral_cut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '活動id',
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `set_id` varchar(255) DEFAULT NULL COMMENT '積分配置id',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `apply_num` int(11) NOT NULL DEFAULT '1' COMMENT '申請數量',
  `images_url` text COMMENT '圖片地址',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕備註',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL COMMENT '信息錄入的城市(無效字段)',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='員工積分表';

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
  `year_sw` int(2) NOT NULL DEFAULT '0' COMMENT '0:無年限限制  1：有年限限制',
  `year_num` int(11) DEFAULT '0' COMMENT '每年限制申請次數',
  `validity` int(11) NOT NULL DEFAULT '1' COMMENT '有效期 1:1年有效期  5:5年有效期',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='積分配置表';

-- ----------------------------
-- Table structure for gr_integral_cut
-- ----------------------------
DROP TABLE IF EXISTS `gr_integral_cut`;
CREATE TABLE `gr_integral_cut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integral_name` varchar(255) NOT NULL COMMENT '兌換名稱',
  `integral_num` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `imges_url` text COMMENT '兌換物品的圖片',
  `inventory` int(255) NOT NULL DEFAULT '0' COMMENT '庫存',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='積分兌換表';

-- ----------------------------
-- Table structure for gr_queue
-- ----------------------------
DROP TABLE IF EXISTS `gr_queue`;
CREATE TABLE `gr_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rpt_desc` varchar(250) NOT NULL,
  `req_dt` datetime DEFAULT NULL,
  `fin_dt` datetime DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `status` char(1) NOT NULL,
  `rpt_type` varchar(10) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rpt_content` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gr_queue_param
-- ----------------------------
DROP TABLE IF EXISTS `gr_queue_param`;
CREATE TABLE `gr_queue_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `param_field` varchar(50) NOT NULL,
  `param_value` varchar(500) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gr_queue_user
-- ----------------------------
DROP TABLE IF EXISTS `gr_queue_user`;
CREATE TABLE `gr_queue_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
