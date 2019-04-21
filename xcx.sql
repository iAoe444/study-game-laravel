use xcx;

/*CREATE TABLE IF NOT EXISTS `map`
(
	`user_id` INT AUTO_INCREMENT PRIMARY KEY,
	`open_id` CHAR(28) NOT NULL COMMENT '微信openid',
	`updated_at` INT NOT NULL DEFAULT 0 COMMENT '更新时间', 
    `created_at` INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='映射表';*/

CREATE TABLE IF NOT EXISTS `user`
(
	`open_id` CHAR(28) NOT NULL PRIMARY KEY COMMENT '微信openid',
    `user_name` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '用户名',
    `avatar_url` VARCHAR(200) NOT NULL DEFAULT '0' COMMENT '头像',
    `slogan` VARCHAR(100) NOT NULL DEFAULT '今日的底线永远高于昨日的巅峰' COMMENT '口号',
    `target` VARCHAR(20) NOT NULL DEFAULT '无' COMMENT '目标',
	`gender` TINYINT(1) NOT NULL DEFAULT '未知' COMMENT '性别',
    `province` VARCHAR(10) NOT NULL DEFAULT '广东' COMMENT '用户省份',
    `city` VARCHAR(10) NOT NULL DEFAULT '广州' COMMENT '用户城市',
    `country` VARCHAR(10) NOT NULL DEFAULT '中国' COMMENT '用户国家',
	`updated_at` INT NOT NULL DEFAULT 0 COMMENT '更新时间', 
    `created_at` INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户表';

CREATE TABLE IF NOT EXISTS `user_study`
(
	`open_id` CHAR(28) NOT NULL PRIMARY KEY COMMENT '微信openid',
    `daily_time` INT NOT NULL DEFAULT 0 COMMENT '日学习时长',
    `weekly_time` INT NOT NULL DEFAULT 0 COMMENT '周学习时长',
    `monthly_time` INT NOT NULL DEFAULT 0 COMMENT '月学习时长',
	`study_time` INT NOT NULL DEFAULT 0 COMMENT '学习时长',
	`coin` INT NOT NULL DEFAULT 0 COMMENT '金币',
    `duan_wei` INT NOT NULL DEFAULT 0 COMMENT '段位',
    `complete_task` INT NOT NULL DEFAULT 0 COMMENT '今天完成的任务数量',
	`if_upload` BOOLEAN NOT NULL DEFAULT FALSE COMMENT '用户今天是否上传记录',
	`updated_at` INT NOT NULL DEFAULT 0 COMMENT '更新时间', 
    `created_at` INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户学习表';

CREATE TABLE IF NOT EXISTS `user_note`
(
	`note_id` INT AUTO_INCREMENT PRIMARY KEY,
    `note` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '笔记信息',
    `open_id` CHAR(28) NOT NULL COMMENT '微信openid',
    `updated_at` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
    `created_at` INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户笔记表';

CREATE TABLE IF NOT EXISTS `user_task`
(
	`task_id` INT AUTO_INCREMENT NOT NULL primary KEY COMMENT '任务ID',
    `task_content` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '任务内容',
    `if_complete` INT NOT NULL DEFAULT 0 COMMENT '是否完成',
	`open_id` CHAR(28) NOT NULL COMMENT '微信openid',
	`updated_at` INT NOT NULL DEFAULT 0 COMMENT '更新时间', 
    `created_at` INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户任务表';

CREATE TABLE IF NOT EXISTS `user_complete_tomato`
(
	`complete_id` INT AUTO_INCREMENT NOT NULL PRIMARY KEY COMMENT '完成的ID',
    `task_content` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '任务内容',
    `comment` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '用户备注信息',
    `open_id` CHAR(28) NOT NULL COMMENT '微信openid',
	`start_at` INT NOT NULL DEFAULT 0 COMMENT '开始时间', 
    `end_at` INT NOT NULL DEFAULT 0 COMMENT '结束时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户完成一个番茄详情表';