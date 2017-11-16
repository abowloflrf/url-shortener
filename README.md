# URL Shortener 简单短链接实现
使用Slim Framework，php+mysql实现。算法很简单，即使用数据库中的自增id转化为62进制作为短链接，因此永远不会有发生碰撞的情况，且容量可以为无限大没有限制，仅4为域名就可以达到储存62^4=14776336g个域名。为了时初始的短链接不会过于简单，因此mysql中的自增id起始为300000。

输入域名必须带有协议名，支持`(http|ftp)s`

## 使用
**导入数据库表**
```sql
CREATE TABLE `url` (
	`key` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`url_short` VARCHAR(6) NOT NULL DEFAULT '0' COLLATE 'utf8_bin',
	`url_full` TEXT NULL COLLATE 'utf8_bin',
	`click` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`key`),
	UNIQUE INDEX `url_short` (`url_short`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB
AUTO_INCREMENT=300000
;
```
**运行项目**
```shell
$ composer install
$ composer start
> php -S localhost:8080 -t public public/index.php
```
## 预览
![](preview-1.png)

![](preview-2.png)