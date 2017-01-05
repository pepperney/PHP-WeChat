/*
Navicat MySQL Data Transfer

Source Server         : 本地测试
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : mywechat

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-11-29 19:05:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(255) NOT NULL COMMENT '用户名 ',
  `realname` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `mobile` int(11) DEFAULT NULL COMMENT '手机',
  `qq` int(15) DEFAULT NULL COMMENT 'QQ号',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信openid',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `usertype` int(11) DEFAULT NULL COMMENT '用户类型:0-admin；1-普通用户',
  `userstatus` int(11) DEFAULT '0' COMMENT '用户状态:0-未激活；1-激活',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of t_user
-- ----------------------------
INSERT INTO `t_user` VALUES ('1', 'pepper', 'nulo', 'b3f952d5d9adea6f63bee9d4c6fceeaa', null, null, null, 'oaV_HwNFyHxJWd36hemNbjBms6IA', '蓝格子', 'http://wx.qlogo.cn/mmopen/sTzXrytyAtB13Qia52rPibjTdctDPAfNU765nrANXM8ia1g0ia6EFFnEHsbCuHHYWc4ETAQkdrEtTbToSRbJP9v9xn2RfS2XNQun/0', '1', '1');

-- ----------------------------
-- Table structure for t_wechat
-- ----------------------------
DROP TABLE IF EXISTS `t_wechat`;
CREATE TABLE `t_wechat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) DEFAULT NULL,
  `appid` varchar(255) NOT NULL,
  `appsecret` varchar(255) NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `modify_time` bigint(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of t_wechat
-- ----------------------------
INSERT INTO `t_wechat` VALUES ('1', 'enjoyit', 'wxb8e38e0360a34680', '40f8fce447252a324e9bac84823d9ba0', 'w3CaxMvVgH0A9pUTAyd7jhnTNkJclvkoQ8U6a0CPb3TmWTFPMbiGFy9m44ORoH777Lu-zYc4FuItgpTtrYg9-HCu4dkJPf8brRPQ4ZmnkzgWd-ZL7PRXh8TAkBn4oJRrQMNbAAAMIJ', '1480315081');
