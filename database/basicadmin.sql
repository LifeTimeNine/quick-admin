/*
 Navicat MySQL Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : basicadmin

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 09/03/2022 22:09:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for system_action_log
-- ----------------------------
DROP TABLE IF EXISTS `system_action_log`;
CREATE TABLE `system_action_log`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `suid` int(10) UNSIGNED NOT NULL COMMENT '系统用户ID',
  `node` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '访问节点',
  `request_time` datetime(0) NOT NULL COMMENT '请求参数',
  `request_param` json NOT NULL COMMENT '请求参数',
  `request_ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '请求IP',
  `response_code` int(5) UNSIGNED NOT NULL COMMENT '响应状态码',
  `response_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '响应内容',
  `run_time` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '运行时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `suid`(`suid`) USING BTREE,
  INDEX `node`(`node`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 136 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统操作记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for system_config
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config`  (
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '键',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '值',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置名称',
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_config
-- ----------------------------
INSERT INTO `system_config` VALUES ('system_name', 'BasicAdmin', 1, '系统名称');

-- ----------------------------
-- Table structure for system_error_log
-- ----------------------------
DROP TABLE IF EXISTS `system_error_log`;
CREATE TABLE `system_error_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `hash` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '哈希值',
  `app_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '应用名称',
  `path_info` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '访问地址',
  `access_ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '访问IP',
  `request_param` json NOT NULL COMMENT '请求参数',
  `request_time` datetime(0) NOT NULL COMMENT '请求时间',
  `error_code` int(10) UNSIGNED NOT NULL COMMENT '异常码',
  `error_message` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '异常消息',
  `error_file` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '异常文件',
  `error_line` int(10) UNSIGNED NOT NULL COMMENT '异常行数',
  `error_trace` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '异常跟踪',
  `happen_time` datetime(0) NOT NULL COMMENT '第一次发生的时间',
  `last_happen_time` datetime(0) NOT NULL COMMENT '最后一次发生的时间',
  `happen_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '累计发生次数',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `resolve_suid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '处理用户ID',
  `resolve_time` datetime(0) NULL DEFAULT NULL COMMENT '处理时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `hash`(`hash`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统异记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for system_menu
-- ----------------------------
DROP TABLE IF EXISTS `system_menu`;
CREATE TABLE `system_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重',
  `pid` int(10) UNSIGNED NOT NULL COMMENT '父级ID',
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `icon` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图标',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '页面地址',
  `node` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限节点',
  `params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '参数',
  `create_time` datetime(0) NOT NULL COMMENT '创建时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '软删除标记',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统菜单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_menu
-- ----------------------------
INSERT INTO `system_menu` VALUES (1, 1, 0, '系统管理', 'el-icon-s-tools', '#', NULL, '', '2022-02-20 17:06:43', 1, NULL);
INSERT INTO `system_menu` VALUES (2, 8, 1, '系统用户', 'el-icon-user', '/system/user', 'systemuser/list', NULL, '2022-02-20 17:09:06', 1, NULL);
INSERT INTO `system_menu` VALUES (3, 9, 1, '系统角色', 'el-icon-s-custom', '/system/role', 'systemrole/list', NULL, '2022-02-22 13:02:04', 1, NULL);
INSERT INTO `system_menu` VALUES (4, 10, 1, '系统菜单', 'nested', '/system/menu', 'systemmenu/list', '', '2022-02-22 16:41:19', 1, NULL);
INSERT INTO `system_menu` VALUES (5, 0, 1, '操作日志', 'el-icon-tickets', '/system/actionlog', 'systemactionlog/list', NULL, '2022-02-26 14:43:36', 1, NULL);
INSERT INTO `system_menu` VALUES (6, 0, 1, '异常日志', 'el-icon-warning-outline', '/system/errorlog', 'systemerrorlog/list', NULL, '2022-02-26 19:22:58', 1, NULL);
INSERT INTO `system_menu` VALUES (7, 0, 1, '系统配置', 'el-icon-setting', '/system/config', 'systemconfig/list', NULL, '2022-02-28 14:14:21', 1, NULL);
INSERT INTO `system_menu` VALUES (8, 0, 1, '系统任务', 'task', '/system/task', 'systemtask/list', NULL, '2022-03-07 22:01:13', 1, NULL);

-- ----------------------------
-- Table structure for system_role
-- ----------------------------
DROP TABLE IF EXISTS `system_role`;
CREATE TABLE `system_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名称',
  `desc` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `create_suid` int(10) UNSIGNED NOT NULL COMMENT '创建用户ID',
  `create_time` datetime(0) NOT NULL COMMENT '创建时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '软删除标记',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_role
-- ----------------------------
INSERT INTO `system_role` VALUES (1, '超级管理员', NULL, 0, '2022-02-20 14:15:30', 1, NULL);

-- ----------------------------
-- Table structure for system_role_node
-- ----------------------------
DROP TABLE IF EXISTS `system_role_node`;
CREATE TABLE `system_role_node`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `srid` int(10) UNSIGNED NOT NULL COMMENT '系统角色ID',
  `node` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '权限节点',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 78 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统角色权限节点表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for system_task
-- ----------------------------
DROP TABLE IF EXISTS `system_task`;
CREATE TABLE `system_task`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务名称',
  `command` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务指令',
  `params` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务参数',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '任务类型',
  `crontab` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '定时任务参数',
  `create_time` datetime(0) NOT NULL COMMENT '创建时间',
  `exec_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '执行状态 （1等待中，2执行中）',
  `last_exec_time` datetime(0) NULL DEFAULT NULL COMMENT '最后执行时间',
  `last_exec_result` tinyint(1) UNSIGNED NULL DEFAULT NULL COMMENT '最后一次执行结果',
  `exec_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '执行次数',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE COMMENT 'title'
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统任务表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for system_task_log
-- ----------------------------
DROP TABLE IF EXISTS `system_task_log`;
CREATE TABLE `system_task_log`  (
  `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `stid` int(10) UNSIGNED NOT NULL COMMENT '系统任务ID',
  `pid` int(10) NOT NULL COMMENT '执行的进程ID',
  `exec_time` datetime(0) NOT NULL COMMENT '执行时间',
  `run_time` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '运行时间',
  `output` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '输出内容',
  `result` tinyint(1) UNSIGNED NOT NULL COMMENT '执行结果',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stid`(`stid`) USING BTREE COMMENT 'stid'
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统任务日志' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for system_user
-- ----------------------------
DROP TABLE IF EXISTS `system_user`;
CREATE TABLE `system_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `desc` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `last_login_time` datetime(0) NULL DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '最后登录IP',
  `login_num` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录次数',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '软删除标记',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_user
-- ----------------------------
INSERT INTO `system_user` VALUES (1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', NULL, '超级管理员', '这是一个超级管理员账户', '2022-02-20 14:15:29', 1, '2022-03-09 19:20:02', 3232246728, 64, NULL);

-- ----------------------------
-- Table structure for system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `system_user_role`;
CREATE TABLE `system_user_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `suid` int(10) UNSIGNED NOT NULL COMMENT '系统用户ID',
  `srid` int(10) UNSIGNED NOT NULL COMMENT '系统角色ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统用户角色表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
