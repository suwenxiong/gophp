gophp 是一个用php根据设置好的布局和变量，生成静态网站的工具

#1.安装

配置nginx,php 环境
下载所有的文件，然后执行
chmod 777 -R gophp/*

#2.目录说明

cache 是编译面板文件缓存目录

config 配置文件目录

layout 布局文件目录

views 是模板文件目录

web   是编译目录,程序会自动把views目录下模板文件编译到web目录下

index.php 核心文件

#3.基本使用方法

在views目录下创建index.html

在浏览器执行 127.0.0.1/gophp/?r=index

会编译生成web/index.html,并且在当前窗口可以浏览到网页内容

在浏览器执行127.0.0.1/gophp/

会刷新所有的模板文件


#4.布局文件调用

先在layout目录创建header.html

在模板写入  {layout header},可以加载此公共头部


#5.配置文件使用方法

在config/common.php 数组添加自己想要的数据

for example:
    $config = array(
        'title'=>'web',
        'weburl'=>'http://test.com',
    );

在模板{$config[title]}

配置文件还可以请求url获取数据，例如

$config = [
    'title' => 'hello world',
    'info' => get('https://www.baidu.com')
];

有两种请求方式，一种是get,一种是post

get函数使用非常简单，参数就是url

post函数第一个参数是url字符串,第二个参数是请求参数数组，例如 'info'=>post('http://www.baidu.com', ['s'=>'kk'])

#6.模板自定义变量方法

在每个模板开始，可以加入---  ---,符号里面的内容会被解析

格式如下：
---
man=jay
title=haha
---
会产生两个变量，可以在模板调用{$man},{$title}
Notice:---前不能有任何内容


#7.模板引擎
现有的模板引擎只能解析

{layout html}

{$var}

如果你熟悉php，完全可以使用php

<?php if($i==0){?>

<div>hello</div>

<?php }?>

