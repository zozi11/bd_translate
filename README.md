# bd_translate by zozi11

【作者】zozi11

【时间】2022/10/18

【说明】

这是一款基于百度API的翻译接口，利用JS+PHP实现网页一键切换语言选择。采用本地缓存机制索引翻译结果，体验优秀。




【亮点】

采用缓存机制，只有初次打开的时候调用远程翻译API，后续自动存储进数据库，本地读取，不需要每次经过翻译耗时，所有用户再次切换都是秒开，体验优秀




【文件结构】

index.html 演示页面，其中包含一个下拉选择翻译语言的关键select

translate_list.sql SQL数据表创建命令，直接在MYSQL数据库中执行

pdoclass.php 数据库链接类，只需要修改该页面中的MYSQL数据库链接参数即可

ajax_translate.php 核心翻译功能，用于为js提供翻译接口，调用本地数据或调用百度API，其中含有百度API的ID和KEY，需要修改成你自己的

js/translate.js 核心前端JS，里面有两个函数，一个是单个标签翻译，另一个是支持多标签处理。具体调用方式参考index.html




【测试方式】

1 将文件上传到网站目录

2 在MySQL中导入sql文件

3 修改pdoclass.php中的数据库链接参数

4 修改ajax_translate.php 的百度翻译API接口参数

访问index.html，查看演示结果

