/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : grdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-07-20 17:49:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gr_bonus_point
-- ----------------------------
DROP TABLE IF EXISTS `gr_bonus_point`;
CREATE TABLE `gr_bonus_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `credit_type` int(11) NOT NULL COMMENT '學分專案id',
  `bonus_point` int(11) NOT NULL COMMENT '積分分數',
  `rec_date` date DEFAULT NULL COMMENT '記錄日期',
  `expiry_date` date DEFAULT NULL COMMENT '過期日期',
  `req_id` int(11) NOT NULL COMMENT '學分申請id',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='積分記錄表';

-- ----------------------------
-- Table structure for gr_credit_point
-- ----------------------------
DROP TABLE IF EXISTS `gr_credit_point`;
CREATE TABLE `gr_credit_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `credit_req_id` int(11) NOT NULL COMMENT '學分申請id',
  `credit_type` int(11) NOT NULL COMMENT '學分專案id',
  `credit_point` int(11) NOT NULL COMMENT '學分分數',
  `rec_date` date DEFAULT NULL COMMENT '記錄日期',
  `expiry_date` date DEFAULT NULL COMMENT '失效日期',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='學分記錄表';

-- ----------------------------
-- Table structure for gr_credit_point_ex
-- ----------------------------
DROP TABLE IF EXISTS `gr_credit_point_ex`;
CREATE TABLE `gr_credit_point_ex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `point_id` int(11) NOT NULL COMMENT '學分記錄表id',
  `long_type` int(2) NOT NULL DEFAULT '1',
  `year` int(11) NOT NULL COMMENT '年限',
  `start_num` int(11) NOT NULL COMMENT '開始分數',
  `end_num` int(11) DEFAULT NULL COMMENT '結束分數',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='學分年度扣減記錄表';

-- ----------------------------
-- Table structure for gr_credit_request
-- ----------------------------
DROP TABLE IF EXISTS `gr_credit_request`;
CREATE TABLE `gr_credit_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `credit_type` int(11) NOT NULL COMMENT '學分專案id',
  `credit_point` int(11) NOT NULL COMMENT '學分分數',
  `apply_date` date DEFAULT NULL COMMENT '申請日期',
  `audit_date` date DEFAULT NULL COMMENT '審核日期',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕原因',
  `images_url` varchar(255) DEFAULT NULL COMMENT '圖片地址',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='學分申請表';

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
  `validity` int(11) NOT NULL DEFAULT '1' COMMENT '有效期 1:1年有效期  5:5年有效期',
  `z_index` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='學分配置表';

-- ----------------------------
-- Table structure for gr_gift_request
-- ----------------------------
DROP TABLE IF EXISTS `gr_gift_request`;
CREATE TABLE `gr_gift_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `gift_type` int(11) NOT NULL COMMENT '兌換專案id',
  `bonus_point` int(11) NOT NULL COMMENT '單個兌換需要的積分',
  `apply_num` int(11) NOT NULL DEFAULT '1' COMMENT '申請數量',
  `apply_date` date DEFAULT NULL COMMENT '申請日期',
  `audit_date` date DEFAULT NULL COMMENT '審核日期',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕原因',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='積分兌換申請表';

-- ----------------------------
-- Table structure for gr_gift_type
-- ----------------------------
DROP TABLE IF EXISTS `gr_gift_type`;
CREATE TABLE `gr_gift_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gift_name` varchar(255) NOT NULL COMMENT '兌換名稱',
  `bonus_point` int(11) NOT NULL DEFAULT '0' COMMENT '積分數值',
  `imges_url` text COMMENT '兌換物品的圖片',
  `inventory` int(255) NOT NULL DEFAULT '0' COMMENT '庫存',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='積分兌換配置表';

-- ----------------------------
-- Table structure for gr_prize
-- ----------------------------
DROP TABLE IF EXISTS `gr_prize`;
CREATE TABLE `gr_prize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `prize_type` int(11) NOT NULL COMMENT '獎項id',
  `prize_point` int(11) NOT NULL COMMENT '獎項扣減學分',
  `award_date` date DEFAULT NULL COMMENT '獲得日期',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='獎金記錄表';

-- ----------------------------
-- Table structure for gr_prize_request
-- ----------------------------
DROP TABLE IF EXISTS `gr_prize_request`;
CREATE TABLE `gr_prize_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `prize_type` int(11) NOT NULL COMMENT '獎金專案id',
  `prize_point` int(11) NOT NULL COMMENT '獎金扣減學分',
  `apply_date` date DEFAULT NULL COMMENT '申請日期',
  `audit_date` date DEFAULT NULL COMMENT '審核日期',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕原因',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='獎金申請表';

-- ----------------------------
-- Table structure for gr_prize_type
-- ----------------------------
DROP TABLE IF EXISTS `gr_prize_type`;
CREATE TABLE `gr_prize_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prize_name` varchar(255) NOT NULL COMMENT '獎項名稱',
  `prize_point` int(11) DEFAULT NULL COMMENT '獎項扣除學分',
  `remark` text COMMENT '備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='獎項類別';

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

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
