/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.7.24-0ubuntu0.16.04.1 : Database - card
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`card` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `card`;

/*Table structure for table `card` */

DROP TABLE IF EXISTS `card`;

CREATE TABLE `card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_no` varchar(25) DEFAULT NULL COMMENT '卡号',
  `master_id` char(30) DEFAULT '' COMMENT '卡主人openid',
  `user_id` char(30) DEFAULT '' COMMENT '使用者openid',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `desc` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_no` (`card_no`),
  KEY `mopenid` (`master_id`),
  KEY `uopenid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

/*Data for the table `card` */

insert  into `card`(`id`,`card_no`,`master_id`,`user_id`,`address`,`desc`,`create_time`,`update_time`) values (1,'6222020302043810720','osYlX6Dc2xg-UN3H5S9TQGXYKjk4','osYlX6I2R5ga4gflVEfff6j-yO70','西青区灵泉北里','备注',1551517481,NULL),(2,'4512893968894106','osYlX6Dc2xg-UN3H5S9TQGXYKjk4','osYlX6I2R5ga4gflVEfff6j-yO70','西青区华鼎智地','都不行',1551517507,NULL),(3,'12354678','osYlX6Dc5jEyfJ4gm1Br6iDR1apY','osYlX6Dc5jEyfJ4gm1Br6iDR1apY','啦啦啦啦啦','哈哈哈哈哈哈',1551517630,NULL),(4,'12345678','osYlX6Dc5jEyfJ4gm1Br6iDR1apY','osYlX6Dc5jEyfJ4gm1Br6iDR1apY','啊啊啊啊','啦啦啦啦',1551517690,NULL),(5,'6939749268803343','osYlX6Dc2xg-UN3H5S9TQGXYKjk4','osYlX6Dc2xg-UN3H5S9TQGXYKjk4','深圳市宝安区福永街道塘尾十一区','我在幼儿园附近',1551538463,NULL),(6,'14446256667','osYlX6Mqg-v_px4gHd_-JLOxhH80','osYlX6Mqg-v_px4gHd_-JLOxhH80','大概','在家里面',1551583399,NULL),(7,'44636785334','osYlX6Mqg-v_px4gHd_-JLOxhH80','osYlX6Mqg-v_px4gHd_-JLOxhH80','电炖锅','晚点',1551583418,NULL),(8,'23336467','osYlX6Mqg-v_px4gHd_-JLOxhH80','osYlX6LyM53fxRphsZIij2Ic_CdQ','豆腐花','我在这里',1551583440,NULL),(11,'12345','osYlX6I2R5ga4gflVEfff6j-yO70','osYlX6I2R5ga4gflVEfff6j-yO70','tao','有',1551593922,NULL),(12,'2261','osYlX6IDR_zhc2kZ5qiRU9qFDVaA','osYlX6IDR_zhc2kZ5qiRU9qFDVaA','501','81号',1551596874,NULL),(13,'1212','osYlX6IDR_zhc2kZ5qiRU9qFDVaA','osYlX6IDR_zhc2kZ5qiRU9qFDVaA','109','中铁',1551751273,NULL),(14,'111383874','osYlX6LyM53fxRphsZIij2Ic_CdQ','osYlX6LyM53fxRphsZIij2Ic_CdQ','广东广州','me m o',1551753128,NULL);

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` char(30) DEFAULT '',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `tel` char(11) DEFAULT '' COMMENT '手机号',
  `default_card` varchar(25) DEFAULT '' COMMENT '默认卡号',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `status` tinyint(4) DEFAULT '1' COMMENT '0禁用1正常',
  `create_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `OPENID` (`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Data for the table `user` */

insert  into `user`(`id`,`openid`,`nickname`,`tel`,`default_card`,`avatar`,`status`,`create_time`) values (1,'osYlX6Dc2xg-UN3H5S9TQGXYKjk4','姜海蕤','','6939749268803343','http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTILDN0XpzO5w6o0iclfUMECSBBibK6mMe11k2l7Ih6dFp4jDEiaia8nibcPmY2gxL0PNY4okaXyHcYcXcw/132',1,1551517459),(2,'osYlX6Dc5jEyfJ4gm1Br6iDR1apY','Viki?','','12345678','http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJgrrRgJibVl5bOFc8hRsgGWKOWUibuMAEzdVxmAQnW0TWgsyCPOM5ibb4GadthXE9LuuGYEuAHtdG1g/132',1,1551517605),(3,'osYlX6Mqg-v_px4gHd_-JLOxhH80','龙','','44636785334','http://thirdwx.qlogo.cn/mmopen/vi_32/xhelibpeGsEMFrfs80ibKr3OIKS1fiaNib9RhKIBwjGyO2R1KVfwibib7fAU2gyAGWibHXD7fyXeGway8yXpy41O1HeGg/132',1,1551583376),(4,'osYlX6LyM53fxRphsZIij2Ic_CdQ','龙2','','23336467','',1,1551583906),(5,'osYlX6IkK-H8vALCHR2F9HvBJ0eI','Ellipsis','','','http://thirdwx.qlogo.cn/mmopen/vi_32/iaYjQwbjuMRHAdkThTD6LZsTG24nZvBWZ8PPDPscpXZqwvcxaNPckR8YxPxVQWP6XicMibdsByKCcEKFNs41qNhfw/132',1,1551592973),(6,'osYlX6I2R5ga4gflVEfff6j-yO70','不吃猫的鱼?','','6222020302043810720','http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLnqkKB19GZr4MhaHlulsI91MAURbrLQAsttJQWB0bTNuD2b8HkKULFZGNgXLjh4c12mkmGS8RoMw/132',1,1551593900),(7,'osYlX6IDR_zhc2kZ5qiRU9qFDVaA','黑眼圈','','2261','http://thirdwx.qlogo.cn/mmopen/vi_32/IpOXCnCf9J5EGt935J8ntFXvQ31hNemEhiaFCKdFALIP6OctBF37vhsQhriceI4w3EvWiakn6XXWKu16IDR8csqlg/132',1,1551596498),(8,'osYlX6PlsbnsOoNBBjb_bHX4_tY0','Kit','','','http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eoUE42LFYrKDDWxGUf6J1ZIvKaluRdHOkzxU4LviakbasXqYroARNfUCJtqsa5eSznbLcObY5LXHsQ/132',1,1551596997);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
