<admintpl file="header"/>
<head/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <ul class="nav nav-tabs">
     <li ><a href="{:U('index')}" >__{$business_name}__管理</a></li>
     <li class="active"><a href="javascript;" target="_self">添加修改__{$business_name}__</a></li>
  </ul>
  <form class="form-horizontal J_ajaxForms" action="{:U('add')}" method="post" id="myform" name="myform" enctype="multipart/form-data">
        <input type="hidden" name="__{$prikey}__" value="{$item.__{$prikey}__}">
        __{$titems}__        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn_submit  J_ajax_submit_btn">提交</button>
            <a class="btn" href="{:U('index')}">返回</a>
        </div>
    </form>
</div>

<script type="text/javascript" src="__ROOT__/statics/js/common.js"></script>
<script type="text/javascript" src="__ROOT__/statics/js/content_addtop.js"></script>
<script type="text/javascript"> 
$(function () {
    var isadd = {:$item[__{$prikey}__] ? 'false' : 'true'};
    $(".J_ajax_close_btn").on('click', function (e) {
        e.preventDefault();
        Wind.use("artDialog", function () {
            art.dialog({
                id: "question",
                icon: "question",
                fixed: true,
                lock: true,
                background: "#CCCCCC",
                opacity: 0,
                content: "您确定需要关闭当前页面嘛？",
                ok:function(){
                    setCookie("refersh_time",1);
                    window.close();
                    return true;
                }
            });
        });
    });
    /////---------------------
     Wind.use('validate', 'ajaxForm', 'artDialog', function () {
            //javascript
            /*
                //编辑器
                editorcontent = new baidu.editor.ui.Editor({initialFrameHeight:300, zIndex:2});
                editorcontent.render('content');
                try{editorcontent.sync();}catch(err){};
                //增加编辑器验证规则
                jQuery.validator.addMethod('editorcontent',function(){
                    try{editorcontent.sync();}catch(err){};
                    return editorcontent.hasContents();
                });
            */
            var form = $('form.J_ajaxForms');
            //ie处理placeholder提交问题
            if ($.browser.msie) {
                form.find('[placeholder]').each(function () {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });
            }
            //表单验证开始
            form.validate({
                //是否在获取焦点时验证
                onfocusout:false,
                //是否在敲击键盘时验证
                onkeyup:false,
                //当鼠标掉级时验证
                onclick: false,
                //验证错误
                showErrors: function (errorMap, errorArr) {
                    //errorMap {'name':'错误信息'}
                    //errorArr [{'message':'错误信息',element:({})}]
                    try{
                        $(errorArr[0].element).focus();
                        art.dialog({
                            id:'error',
                            icon: 'error',
                            lock: true,
                            fixed: true,
                            background:"#CCCCCC",
                            opacity:0,
                            content: errorArr[0].message,
                            cancelVal: '确定',
                            cancel: function(){
                                $(errorArr[0].element).focus();
                            }
                        });
                    }catch(err){
                    }
                },
                //验证规则
                rules: {}, //{'post[title]':{required:1},'post[content]':{editorcontent:true}, 'post[send_time]':{required:1}, 'post[type]':{required:1}, 'post[send_type]':{required:1}},
                //验证未通过提示消息
                messages: {}, //{'post[title]':{required:'请输入标题'},'post[content]':{editorcontent:'内容不能为空'}, 'post[send_time]':'请选择时间'},
                //给未通过验证的元素加效果,闪烁等
                highlight: false,
                //是否在获取焦点时验证
                onfocusout: false,
                //验证通过，提交表单
                submitHandler: function (forms) {
                    $(forms).ajaxSubmit({
                        url: form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                        dataType: 'json',
                        beforeSubmit: function (arr, $form, options) {
                            
                        },
                        success: function (data, statusText, xhr, $form) {
                            if(data.status){
                                setCookie("refersh_time",1);
                                //添加成功
                                Wind.use("artDialog", function () {
                                    art.dialog({
                                        id: "succeed",
                                        icon: "succeed",
                                        fixed: true,
                                        lock: true,
                                        background: "#CCCCCC",
                                        opacity: 0,
                                        content: data.info,
                                        button:[
                                            {
                                                name: isadd ? '继续添加？' : '继续修改?',
                                                callback:function(){
                                                    reloadPage(window);
                                                    return true;
                                                },
                                                focus: true
                                            },{
                                                name: '返回列表',
                                                callback:function(){
                                                    location.href="{:U('index')}";
                                                    return true;
                                                }
                                            }
                                        ]
                                    });
                                });
                            }else{
                                isalert(data.info);
                            }
                        }
                    });
                }
            });
        });
    ////-------------------------
});

</script>

</body>
</html>
