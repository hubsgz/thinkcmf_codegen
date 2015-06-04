<?php
/**
 *  针对单表生成业务管理代码 (增删改查)
 *  限用于 thinkcmf
 */
$config = require dirname(__FILE__) . '/config.php';
$config['classname'] = tfname($config['table_name']);
$config['columns'] = getcolumns($config);
prikey($config);

main($config);

function main($cfg)
{
	//模板文件
	$tpl = array();
	$tpl['model'] = dirname(__FILE__) . '/tpl/__Model.class.php';
	$tpl['controller'] =  dirname(__FILE__) . '/tpl/__Controller.class.php';
	$tpl['listtpl'] = dirname(__FILE__) . '/tpl/list.tpl';
	$tpl['addtpl'] = dirname(__FILE__) . '/tpl/add.tpl';
	$project_rootpath = $cfg['project_rootpath'];

	//判断目标模块是否存在
	$project_module_tpl_dir = sprintf('%s/tpl_admin/simpleboot/%s', $project_rootpath, $cfg['module_name']);
	if (!is_dir($project_module_tpl_dir)) {
		error('wrong module_name');
	}
	$project_module_app_dir = sprintf('%s/application/%s', $project_rootpath, $cfg['module_name']);
	if (!is_dir($project_module_app_dir)) {
		error('wrong module_name');
	}

	//判断目标文件是否存在
	$c_controller = sprintf(
		'%s/Controller/Admin%sController.class.php', 
		$project_module_app_dir,
		$cfg['classname']
		);
	if (file_exists($c_controller)) {
		error($c_controller.'已存在');
	}
	$c_model = sprintf(
		'%s/application/Common/Model/%sModel.class.php', 
		$project_rootpath,
		$cfg['classname']
		);
	if (file_exists($c_model)) {
		debug($c_model.'已存在');
		unset($c_model);
	} 
	$c_tpl_dir = sprintf(
		'%s/Admin%s', 
		$project_module_tpl_dir,
		$cfg['classname']
		);
	if (is_dir($c_tpl_dir)) {
		error($c_tpl_dir.'已存在');
	}

	//创建模板目录
	mkdir($c_tpl_dir);
	debug('success create ' . $c_tpl_dir);

	//生成模板文件
	file_put_contents($c_tpl_dir . '/index.html', listtpl_content($tpl,$cfg));
	debug('success create ' . $c_tpl_dir . '/index.html');
	file_put_contents($c_tpl_dir . '/add.html', addtpl_content($tpl,$cfg));
	debug('success create ' . $c_tpl_dir . '/add.html');

	//生成模型文件
	if (isset($c_model)) {
		file_put_contents($c_model, model_content($tpl,$cfg));
		debug('success create ' . $c_model);
	}

	//生成控制器文件
	file_put_contents($c_controller, controller_content($tpl,$cfg));
	debug('success create ' . $c_controller);

	//输出该问地址
	debug(sprintf('Manage Url: /index.php?g=%s&m=Admin%s&a=index', $cfg['module_name'], $cfg['classname']));
}

/**
 *	控制器代码内容
 */
function controller_content($tpl, $cfg)
{
	$f = $tpl['controller'];
	$con = file_get_contents($f);
	$tran = tran($cfg);
	return strtr($con, $tran);
}
/**
 *	模型代码内容
 */
function model_content($tpl, $cfg)
{
	$f = $tpl['model'];
	$con = file_get_contents($f);
	$tran = tran($cfg);
	return strtr($con, $tran);
}
/**
 *	添加修改模板内容
 */
function addtpl_content($tpl, $cfg)
{
	$f = $tpl['addtpl'];
	$con = file_get_contents($f);
	$titems = '';
	foreach ($cfg['columns'] as $v) {
		$fname = $v[0];
		if ($fname == $cfg['prikey']) {
			continue;
		}
		if ($v[1] == 'text') {
			$titems .= <<<EOF
			<div class="control-group">
	            <label class="control-label">$fname:</label>
	            <div class="controls">
	                <textarea name="$fname" id="$fname" class="inputtext" style="height:100px;width:300px;">{\$item['$fname']}</textarea>
	            </div>
	        </div>
EOF;
		}
		$titems .= <<<EOF
			<div class="control-group">
	            <label class="control-label">$fname:</label>
	            <div class="controls">
	                <input type="text" name="$fname" value="{\$item['$fname']}" class="input" style="width:500px;" ></input>
	            </div>
	        </div> \n 
EOF;
	}
	$cfg['titems'] = $titems;

	$tran = tran($cfg, '__{', '}__');
	return strtr($con, $tran);
}
/**
 *	列表模板内容
 */
function listtpl_content($tpl, $cfg)
{
	$f = $tpl['listtpl'];
	$con = file_get_contents($f);
	$prikey = $cfg['prikey'];

	$thead = '';
	$tlist = '';
	foreach ($cfg['columns'] as $v) {
		$fname = $v[0];
		$thead .= "<th>$fname</th>\n";
		$tlist .= '<td>{$vo.'.$fname.'}</td>'."\n";
	}
	$thead .= "<th>操作</th>";
	$tlist .= <<<EOF
	<td>
	<a href="{:U('add',array('$prikey'=>\$vo['$prikey']))}">修改</a> |
	<a href="{:U('delete',array('$prikey'=>\$vo['$prikey']))}" class="J_ajax_del" >删除</a>
	</td>
EOF;
	$cfg['thead'] = $thead;
	$cfg['tlist'] = $tlist;

	$tran = tran($cfg, '__{', '}__');
	return strtr($con, $tran);
}

/**
 *	获取字段
 */
function getcolumns($cfg)
{
	$table = $cfg['table_name'];
	$db_host = $cfg['db_conn'][0];
	$db_user = $cfg['db_conn'][1];
	$db_pwd = $cfg['db_conn'][2];
	$db_database = $cfg['db_conn'][3];
	$mysql = new mysqli($db_host, $db_user, $db_pwd, $db_database);
	$query = $mysql->query("show columns from sp_$table;");
	return $query->fetch_all();
}

/**
 *  转换成驼峰类名
 */
function tfname($name)
{
	$tmp = explode('_', $name);
	if (count($tmp) == 1) {
		return $name;
	}
	$str = '';
	foreach ($tmp as $v) {
		$str .= ucfirst($v);
	}
	return $str;
}
/**
 *  获取主键
 */
function prikey(&$cfg)
{
	foreach ($cfg['columns'] as $v) {
		if ($v[3] == 'PRI') {
			$cfg['prikey'] = $v[0];
			return '';
		}
	}
	error('prikey not found');
}
/**
 *  替换数组
 */
function tran($arr, $pre='{', $end="}") 
{
	$re = array();
	foreach ($arr as $k=>$v) {
		$re[$pre.'$'.$k.$end] = $v;
	}
	return $re;
}
/**
 *  debug输出
 */
function debug($str)
{
	echo '[debug] ' . $str . "\n";
}
/**
 *  error输出
 */
function error($str)
{
	echo '[error] ' . $str . "\n";
	exit;
}