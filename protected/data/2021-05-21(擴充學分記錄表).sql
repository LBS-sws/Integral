/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-05-21 09:08:23
*/
-- ----------------------------
-- Table structure for gr_credit_point
-- ----------------------------
ALTER TABLE gr_credit_point ADD COLUMN prize_id_list  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '學分扣除的id 逗號分割' AFTER city;
ALTER TABLE gr_credit_point ADD COLUMN prize_json  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '學分扣除明細' AFTER prize_id_list;
