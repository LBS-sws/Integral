/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-03-05 15:08:23
*/
-- ----------------------------
-- Table structure for gr_prize_type
-- ----------------------------
ALTER TABLE gr_prize_type ADD COLUMN leave_limit int(2) NOT NULL DEFAULT 0 COMMENT '等級限制 0:無限制  1：有限制' AFTER limit_number;
ALTER TABLE gr_prize_type ADD COLUMN leave_number int(10) NOT NULL DEFAULT 0 COMMENT '等級級別' AFTER leave_limit;
