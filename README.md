QuickAdmin
===============

> 运行环境要求PHP8.0+

基于 ThinkPHP 8.1 二次开发的一个基础后台管理模板

## 安装

~~~
composer create-project lifetime/quick-admin
~~~

## 对应前端模板

从仓库拉取代码
~~~
git clone https://github.com/LifeTimeNine/quick-admin-ts.git
~~~

进入项目目录
~~~
cd quick-admin-ts
~~~

安装依赖
~~~
npm install
~~~

运行
~~~
npm run dev
~~~

## 系统任务
此模块依赖于一个计划任务管理的项目[https://github.com/LifeTimeNine/timer.git](https://github.com/LifeTimeNine/timer.git)

如果要使用此模块，请安装此服务