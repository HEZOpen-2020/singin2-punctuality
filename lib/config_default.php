
<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

require(DIR . 'internal_config/config.php');

date_default_timezone_set('Asia/Shanghai');

function _C($key) {
	$cu = _CU();
	if(isset($cu[$key])) {
		return $cu[$key];
	} else {
		return [
			// 数据库文件
			'db_path' => "C:/Users/Administrator/AppData/SmartBoard/localData.db",
			'db_password' => '19260817',
			
			// 进程名
			'proc_name' => 'SmartBoard.exe',
			'proc_path' => 'C:/Program Files (x86)/SmartBoard/SmartBoard.exe',
			
			// 系统配置
			'system_locale' => 'GBK', // 编码
			'csrf_bypass' => false, // 绕过 CSRF 攻击检测，调试用

			// 教室打卡界面选项
			'classroom_allow_revoke' => false, // 允许教室打卡撤销
			'classroom_filter_force_consistency' => true, // 状态变更时保持筛选列表不变，防止误操作
			
			// 晨跑第一打卡机信息（入点，同时用于人员同步）
			'hz2zrun_host1' => 'http://192.168.1.180:8083', // 必须有协议标记，末尾不要有斜杠。
			'hz2zrun_name1' => 'runhand23',
			'hz2zrun_type1' => 1, // 0=出, 1=进, 2=未识别
			// 晨跑第二打卡机信息（出点）
			'hz2zrun_host2' => 'http://192.168.1.180:8083',
			'hz2zrun_name2' => 'runhand24',
			'hz2zrun_type2' => 0,
			// 晨跑考勤人员数量
			'hz2zrun_fixed_count' => 10, // 每个活跃班级要求固定成员的数量
			'hz2zrun_temp_count' => 8, // 每个未排除班级抽取随机成员的数量
			// 晨跑考勤时间（用于生成刷卡时间，格式为当天 00:00 之后的秒数）
			'hz2zrun_start' => 6 * 3600 + 23 * 60, // 晨跑考勤开始时间
			'hz2zrun_end' => 6 * 3600 + 44 * 60, // 晨跑考勤结束时间
			'hz2zrun_check_rate' => 0.00, // 生成的考勤记录中补签的概率（若已知补签系统有问题请设置为 0）

			// 传统艺能设置
			'tradition_allow_teacher' => false, // 允许查看教职工部门
			'tradition_allow_other' => true, // 允许选择其他部门
			'tradition_here' => '00000000-0000-0000-0000-000000000000', // 默认班级ID，你应该知道的
			'tradition_nexts' => 5, // 显示“接下来”的数量（并列的人员一定一起显示，因此实际可能会大于此数）
			'tradition_allow_show_all' => false, // 允许查看全部
		][$key];
	}
}

function _C_public_values() {
	return [
		'classroom_allow_revoke',
		'classroom_filter_force_consistency',
		'hz2zrun_name1',
		'hz2zrun_name2',
		'tradition_allow_teacher',
		'tradition_allow_other',
		'tradition_here',
		'tradition_nexts',
		'tradition_allow_show_all',
	];
}
