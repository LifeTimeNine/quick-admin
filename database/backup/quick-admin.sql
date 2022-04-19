-- MySQL dump 10.13  Distrib 5.7.37, for Linux (x86_64)
--
-- Host: 39.107.104.29    Database: quick-admin
-- ------------------------------------------------------
-- Server version	5.7.27-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `system_action_log`
--

DROP TABLE IF EXISTS `system_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_action_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `suid` int(10) unsigned NOT NULL COMMENT '系统用户ID',
  `node` varchar(100) NOT NULL COMMENT '访问节点',
  `request_time` datetime NOT NULL COMMENT '请求参数',
  `request_param` json NOT NULL COMMENT '请求参数',
  `request_ip` varchar(32) NOT NULL COMMENT '请求IP',
  `response_code` int(5) unsigned NOT NULL COMMENT '响应状态码',
  `response_content` text NOT NULL COMMENT '响应内容',
  `run_time` varchar(32) NOT NULL COMMENT '运行时间',
  PRIMARY KEY (`id`),
  KEY `suid` (`suid`),
  KEY `node` (`node`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COMMENT='系统操作记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_config`
--

DROP TABLE IF EXISTS `system_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL COMMENT '键',
  `value` text NOT NULL COMMENT '值',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `name` varchar(200) NOT NULL COMMENT '配置名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_config`
--

LOCK TABLES `system_config` WRITE;
/*!40000 ALTER TABLE `system_config` DISABLE KEYS */;
INSERT INTO `system_config` VALUES (1,'system_name','QuickAdmin',1,'系统名称'),(2,'test','{\"key1\":\"value\"}',3,'1');
/*!40000 ALTER TABLE `system_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_error_log`
--

DROP TABLE IF EXISTS `system_error_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_error_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `hash` varchar(200) NOT NULL COMMENT '哈希值',
  `app_name` varchar(32) NOT NULL COMMENT '应用名称',
  `path_info` varchar(500) NOT NULL COMMENT '访问地址',
  `access_ip` varchar(32) NOT NULL COMMENT '访问IP',
  `request_param` json NOT NULL COMMENT '请求参数',
  `request_time` datetime NOT NULL COMMENT '请求时间',
  `error_code` int(10) unsigned NOT NULL COMMENT '异常码',
  `error_message` varchar(2000) NOT NULL COMMENT '异常消息',
  `error_file` varchar(500) NOT NULL COMMENT '异常文件',
  `error_line` int(10) unsigned NOT NULL COMMENT '异常行数',
  `error_trace` text NOT NULL COMMENT '异常跟踪',
  `happen_time` datetime NOT NULL COMMENT '第一次发生的时间',
  `last_happen_time` datetime NOT NULL COMMENT '最后一次发生的时间',
  `happen_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计发生次数',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `resolve_suid` int(10) unsigned DEFAULT NULL COMMENT '处理用户ID',
  `resolve_time` datetime DEFAULT NULL COMMENT '处理时间',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='系统异记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `system_menu`
--

DROP TABLE IF EXISTS `system_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重',
  `pid` int(10) unsigned NOT NULL COMMENT '父级ID',
  `title` varchar(64) NOT NULL COMMENT '标题',
  `icon` varchar(128) DEFAULT NULL COMMENT '图标',
  `url` varchar(200) NOT NULL COMMENT '页面地址',
  `node` varchar(200) DEFAULT NULL COMMENT '权限节点',
  `params` varchar(200) DEFAULT NULL COMMENT '参数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除标记',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='系统菜单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_menu`
--

LOCK TABLES `system_menu` WRITE;
/*!40000 ALTER TABLE `system_menu` DISABLE KEYS */;
INSERT INTO `system_menu` VALUES (1,1,0,'system_manage','tools','#',NULL,NULL,'2022-03-14 17:55:24',1,NULL),(2,8,1,'system_user','user-filled','/system/user','systemuser/list',NULL,'2022-03-14 17:55:24',1,NULL),(3,9,1,'system_role','role','/system/role','systemrole/list',NULL,'2022-03-14 17:55:24',1,NULL),(4,10,1,'system_menu','nested','/system/menu','systemmenu/list',NULL,'2022-03-14 17:55:24',1,NULL),(5,0,1,'action_log','tickets','/system/actionlog','systemactionlog/list',NULL,'2022-03-14 17:55:24',1,NULL),(6,0,1,'error_log','warning','/system/errorlog','systemerrorlog/list',NULL,'2022-03-14 17:55:24',1,NULL),(7,0,1,'system_config','setting','/system/config','systemconfig/list',NULL,'2022-03-14 17:55:24',1,NULL),(8,0,1,'system_task','task','/system/task','systemtask/list',NULL,'2022-03-14 17:55:24',1,NULL),(9,0,0,'recycle','recycle','#',NULL,NULL,'2022-03-20 16:09:37',1,NULL),(10,0,9,'system_user','user-filled','/recycle/systemUser','systemuser/recycleList',NULL,'2022-03-20 16:11:49',1,NULL),(11,0,9,'system_menu','nested','/recycle/systemMenu','systemmenu/recycleList',NULL,'2022-03-20 17:01:11',1,NULL),(12,0,9,'system_role','role','/recycle/systemRole','systemrole/recycleList',NULL,'2022-03-20 17:31:46',1,'2022-04-19 09:03:14');
/*!40000 ALTER TABLE `system_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_role`
--

DROP TABLE IF EXISTS `system_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `desc` varchar(200) DEFAULT NULL COMMENT '描述',
  `create_suid` int(10) unsigned NOT NULL COMMENT '创建用户ID',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除标记',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='系统角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_role`
--

LOCK TABLES `system_role` WRITE;
/*!40000 ALTER TABLE `system_role` DISABLE KEYS */;
INSERT INTO `system_role` VALUES (1,'超级管理员',NULL,0,'2022-03-14 17:55:24',1,NULL),(3,'管理员','这是一个管理员',1,'2022-03-18 14:18:52',1,NULL);
/*!40000 ALTER TABLE `system_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_role_node`
--

DROP TABLE IF EXISTS `system_role_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_role_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `srid` int(10) unsigned NOT NULL COMMENT '系统角色ID',
  `node` varchar(100) NOT NULL COMMENT '权限节点',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='系统角色权限节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_role_node`
--

LOCK TABLES `system_role_node` WRITE;
/*!40000 ALTER TABLE `system_role_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_role_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_task`
--

DROP TABLE IF EXISTS `system_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(100) NOT NULL COMMENT '任务名称',
  `command` varchar(1000) NOT NULL COMMENT '任务指令',
  `params` varchar(1000) DEFAULT NULL COMMENT '任务参数',
  `type` int(1) unsigned NOT NULL COMMENT '任务类型',
  `crontab` varchar(200) DEFAULT NULL COMMENT '定时参数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `exec_status` int(1) unsigned NOT NULL DEFAULT '1' COMMENT '执行状态 （1等待中，2执行中）',
  `last_exec_time` datetime DEFAULT NULL COMMENT '最后执行时间',
  `last_exec_result` int(1) unsigned DEFAULT NULL COMMENT '最后一次执行结果',
  `exec_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行次数',
  `status` int(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='系统任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_task_log`
--

DROP TABLE IF EXISTS `system_task_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_task_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `stid` int(10) unsigned NOT NULL COMMENT '系统任务ID',
  `pid` int(10) unsigned NOT NULL COMMENT '执行的进程ID',
  `exec_time` date NOT NULL COMMENT '执行时间',
  `run_time` varchar(15) NOT NULL COMMENT '运行时间',
  `output` longtext COMMENT '输出内容',
  `result` int(1) unsigned NOT NULL COMMENT '执行结果',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='系统任务日志表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `system_user`
--

DROP TABLE IF EXISTS `system_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(64) NOT NULL COMMENT '用户名',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机号',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `name` varchar(32) DEFAULT NULL COMMENT '姓名',
  `desc` varchar(200) DEFAULT NULL COMMENT '描述',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` int(10) unsigned DEFAULT NULL COMMENT '最后登录IP',
  `login_num` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除标记',
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='系统用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_user`
--

LOCK TABLES `system_user` WRITE;
/*!40000 ALTER TABLE `system_user` DISABLE KEYS */;
INSERT INTO `system_user` VALUES (1,'admin','17319707985','2390904403@qq.com','e10adc3949ba59abbe56e057f20f883e','http://ot2.xb-l.com/storage/b5/cfc1209181f9bac0f80b4e9de8160f.gif','超级管理员','这是一个超级管理员账户','2022-03-14 17:55:23',1,'2022-04-19 08:57:58',3232235632,110,NULL);
/*!40000 ALTER TABLE `system_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_user_role`
--

DROP TABLE IF EXISTS `system_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `suid` int(10) unsigned NOT NULL COMMENT '系统用户ID',
  `srid` int(10) unsigned NOT NULL COMMENT '系统角色ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='系统用户角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_user_role`
--

LOCK TABLES `system_user_role` WRITE;
/*!40000 ALTER TABLE `system_user_role` DISABLE KEYS */;
INSERT INTO `system_user_role` VALUES (1,1,1);
/*!40000 ALTER TABLE `system_user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-04-19  9:48:09
