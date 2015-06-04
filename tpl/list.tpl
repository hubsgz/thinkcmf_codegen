<admintpl file="header" />
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">__{$business_name}__管理</a></li>
			<li><a href="{:U('add')}" target="_self">添加/修改__{$business_name}__</a></li>
		</ul>
		<form class="J_ajaxForm" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						__{$thead}__
					</tr>
				</thead>
				<foreach name="list" item="vo">
					<tr>
						__{$tlist}__
					</tr>
				</foreach>
			</table>
			
			<div class="pagination">{$page}</div>
		</form>
	</div>
	<script src="__ROOT__/statics/js/common.js"></script>
</body>
</html>