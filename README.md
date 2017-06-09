# dbbackup

# 安装

### 若PHP和MYSQL未加入环境变量，请运行"添加环境变量.bat"
### 修改config.php内的相关配置，为了区分不同站点，$webSite请务必填写(若重复，会导致无法上传)
### 运行"安装.bat"，会自动在计划任务列表添加名为DBbackup任务，每天1点执行一次。

# 测试

### 进入计划任务列表，手动运行DBbackup任务，观察程序运行结果

# 注意

### 文件可以放在C盘外的任何位置(C盘未测试)，路径中不能有空格，即是说上级目录名中不能有空格。

## 错误路径举例

### D:/Program Files/dbbackup
### D:/My Test/dbbackup