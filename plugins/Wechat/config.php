<?php
return array (
		'AppID' => array (// 在后台插件配置表单中的键名 ,会是config[text]
				'title' => 'AppID(应用ID):', // 表单的label标题
				'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
				'value' => 'xxxxxxxxxxxxxx',// 表单的默认值
				'tip' => '在开发者中心设置' 
		),
		'AppSecret' => array (// 在后台插件配置表单中的键名 ,会是config[text]
				'title' => 'AppSecret(应用密钥):', // 表单的label标题
				'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
				'value' => 'xxxxxxxxxxxxxx',// 表单的默认值
				'tip' => '在开发者中心设置' 
		),
		'Token' => array (// 在后台插件配置表单中的键名 ,会是config[text]
				'title' => 'Token(令牌):', // 表单的label标题
				'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
				'value' => 'xxxxxxxxxxxxxx',// 表单的默认值
				'tip' => '在开发者中心设置' 
		),
		'EncodingAESKey' => array (// 在后台插件配置表单中的键名 ,会是config[text]
				'title' => 'EncodingAESKey(消息加解密密钥):', // 表单的label标题
				'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
				'value' => 'xxxxxxxxxxxxxx',// 表单的默认值
				'tip' => '在开发者中心设置' 
		),
		'AppType' => array (// 在后台插件配置表单中的键名 ,会是config[select]
				'title' => '公众号类型:',
				'type' => 'select',
				'options' => array (//select 和radio,checkbox的子选项
					'1' => '订阅号',// 值=>显示
					'2' => '服务号'
				),
				'value' => '1',
				'tip' => '这是公众号类型选择' 
		),
        'IsAuth' => array (// 在后台插件配置表单中的键名 ,会是config[select]
            'title' => '是否认证:',
            'type' => 'select',
            'options' => array (//select 和radio,checkbox的子选项
                '0' => '否',// 值=>显示
                '1' => '是'
            ),
            'value' => '0',
            'tip' => '此公众号是否通过认证'
        ),
        'BaiduAk' => array(
            'title' => '百度地图api秘钥:', // 表单的label标题
            'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
            'value' => 'xxxxxxxxxxxxxx',// 表单的默认值
            'tip' => '申请地址:http://lbsyun.baidu.com/apiconsole/key'
        ),
        'Welcome' => array(
            'title' => '用户关注欢迎语:', // 表单的label标题
            'type' => 'textarea',// 表单的类型：text,password,textarea,checkbox,radio,select等
            'value' => 'xxxxxxxxxxxxxx',// 表单的默认值
            'tip' => '用户关注后自动回复的欢迎语'
        ),
	/* 'text' => array (// 在后台插件配置表单中的键名 ,会是config[text]
		'title' => '文本:', // 表单的label标题
		'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
		'value' => 'hello,ThinkCMF!',// 表单的默认值
		'tip' => '这是文本组件的演示' //表单的帮助提示
	),
	'password' => array (// 在后台插件配置表单中的键名 ,会是config[password]
		'title' => '密码:',
		'type' => 'password',
		'value' => '',
		'tip' => '这是密码组件' 
	),
	'select' => array (// 在后台插件配置表单中的键名 ,会是config[select]
		'title' => '下拉列表:',
		'type' => 'select',
		'options' => array (//select 和radio,checkbox的子选项
			'1' => 'ThinkCMFX',// 值=>显示
			'2' => 'ThinkCMF',
			'3' => '跟猫玩糗事',
			'4' => '门户应用' 
		),
		'value' => '1',
		'tip' => '这是下拉列表组件' 
	),
	'checkbox' => array (
		'title' => '多选框',
		'type' => 'checkbox',
		'options' => array (
			'1' => 'genmaowan.com',
			'2' => 'www.thinkcmf.com' 
		),
		'value' => 1,
		'tip' => '这是多选框组件' 
	),
	'radio' => array (
		'title' => '单选框',
		'type' => 'radio',
		'options' => array (
			'1' => 'ThinkCMFX',
			'2' => 'ThinkCMF' 
		),
		'value' => '1',
		'tip' => '这是单选框组件' 
	),
	'textarea' => array (
		'title' => '多行文本',
		'type' => 'textarea',
		'options' => array (
			'1' => 'ThinkCMFX',
			'2' => 'ThinkCMF' 
		),
		'value' => '这里是你要填写的内容',
		'tip' => '这是多行文本组件' 
	)  */
);
					