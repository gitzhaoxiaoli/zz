//自定义核心库，基于jquery核心库和jquery UI 扩展库
//常用事件： 导出excl事件，删除事件，清空搜索表事件，选择地区事件，弹出菜单事件，全选事件，表单验证事件
////////////行为事件配置////////////////
//申请单元：同委托人，生产者，生产企业
; (function($) { //两件事情： 读取企业信息,更新到界面
    $.fn.getEpInfo = function(options) {
        var option = {
            work_code: false,
            eid: false,
            url: '?m=product&c=contract&a=getEpInfo&',
        }
        var obj = $.extend({},
        option, options);
		//企业类型
        var epType = $(this).attr('epType');

        if (obj.work_code) {
            obj.url = obj.url + 'work_code=' + obj.work_code;
        };
        if (obj.eid) {
            obj.url = obj.url + 'eid=' + obj.eid;
        }
        //当前合同项目
        $curr_item = $(this).parent().parent().parent().parent().parent();

       // 
        $.getJSON(obj.url,
        function(json) { 
            if (json.state == 'no') {
                //根据app区分内网还是外网
                $action = '?m=product&m=contract&a=edit&se=20&type=' + $domian;
				
				if(getUrl('app')=='usr'){
					 
					return false;
				}else{
					$(this).val(""); //清空组织机构代码	
					
				}
				
                $('<div >您好<br> 您申请的组织信息在系统中不存在，请先登记组织基本信息再登记产品信息</div>').dialog({
                    title: '提示信息',

                    buttons: {
                        '取消': function() {
                            $(this).dialog('close');
                        },
                        '登记': function() {
                            $(this).dialog('close');
                            $('<iframe border="0" id="iframe-dialog" frameborder="no" src="' + $action + '" />').dialog({
                                title: '新增企业信息',
                                 width: 850,
                                height: 400,
                                position: ['top', 'right'],
                                buttons: {
                                    '我已确认企业信息登记完整': function() {
										
										
                                        $(this).dialog('close');
                                     }
                                },
                                open: function() {
                                    var btn = $('.ui-dialog-buttonpane').find('button:contains("我已确认企业信息登记完整")');
                                    btn.removeClass('ui-state-default').addClass('ui-state-default-highlight');
                                    $('.ui-dialog-buttonpane').css('align', 'center');
                                 }
                            }).width(780).height(400);

                        }
                    },
                    open: function() {
                        var btn = $('.ui-dialog-buttonpane').find('button:contains("登记")');
                        btn.removeClass('ui-state-default').addClass('ui-state-default-highlight');
                    }
                })

                return false;

            }

            $.each(json,
            function(k, v) {
                $curr_item.find('.' + epType).find('.' + k).val(v);

            });
            //同生产者
			//alert('.last' + epType);
            $('.last' + epType).attr('eid', json.eid)
        });

    }
})(jQuery)

//清空目标表中的数据：在列表也中使用-查询数据库
jQuery.clearForm = function(obj_form) {
    $('#clearForm').click(function() {
        $(obj_form).find(':input').each(function() {
            switch (this.type) {
            case "text":
            case "select-one":
                $(this).val('');
                break;
            }
        });
    })
}

//导出统计列表：列表页里面导出过滤后的数据
jQuery.export_xls = function() {
    $('#export-xls-btn').click(function() {
        var form = $('#search-form');
        form.append($('<input type="hidden" name="export" value="1" />'));
        form.submit();
        form.find('input[name=export]').remove();
    });
}

//日期处理-加载日期控件-添加或者编辑页面使用
jQuery.supu_date = function() {
    $("input.input-date").datepicker();
}

//获取地址栏的值  namg  app  返回值：app
function getUrl(name) {
    // alert(name);
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);

    if (r != null) return unescape(r[2]);
    return null;
}

//提醒 //删除 +警告
jQuery.supu_dialog = function(message, style) {
    if (undefined == style) style = 'notice';

    $('.icon-del').click(function() {
        confirm_dialog(message, $(this).attr('href'), style);
        return false;
    });
}
/*
 * 函数名：message_dialog( message, style )
 * 功  能：弹出消息窗
 * 参  数：message	：提示的消息
 *		   style	：样式图标， 应用：安排功能
 */
function message_dialog(message, style) {
    if (undefined == style) style = 'success';
    $('<div style="margin-bottom:10px;display:none;" class="clearfix"><span id="msg-ico"></span><h6></h6></div>').dialog({
        title: '提示信息',
        width: 240,
        height: 160,
        autoOpen: true,
        resizable: false,
        autoResize: true,
        modal: true,
        overlay: {
            opacity: 0.5,
            background: "black"
        },
        close: function(ev, ui) {
            $(this).remove();
            $('#batch-approval-btn').attr('disabled', true);
        },
        buttons: {
            '确定': function() {
                if ($(this).find('.note-msg').hasClass('msg-ico-success')) window.location.reload();
                $(this).dialog('close');
            }
        },
        open: function() {
            var btn = $('.ui-dialog-buttonpane').find('button:contains("确定")');
            btn.removeClass('ui-state-default').addClass('ui-state-default-highlight');
            $(this).find('#msg-ico').addClass('notice-ico-' + style);
            $(this).find('h6').text(message);
            $(this).find('.ui-widget-content').addClass('notice-' + style);
        }
    });
}

/*
 * 函数名：confirm_dialog( message, action, style )
 * 功  能：弹出询问窗
 * 参  数：message	：提示的消息
 *		   action	：点击确定执行的操作 可以是函数或URL地址 是函数将执行，URL地址则转向
 *		   style	：样式图标 :多用于删除提示
 */
function confirm_dialog(message, action, style) {
    if (undefined == style) style = 'notice';
    $('<div style="display:none;"><p class="tal note-msg" style="padding-left:60px;line-height:1.6em;height:48px;"></p></div>').dialog({
        title: '提示信息',
        width: 360,
        height: 180,
        autoOpen: true,
        resizable: false,
        autoResize: true,
        modal: true,
        overlay: {
            opacity: 0.5,
            background: "black"
        },
        close: function(ev, ui) {
            $(this).remove();
        },
        buttons: {
            '取消': function() {
                $(this).dialog('close');
            },
            '确定': function() {
                if (typeof action == 'string') {
                    window.location.href = action;
                } else if (typeof action == 'function') {
                    action();
                }
                $(this).dialog('close');
            }
        },
        open: function() {
            var btn = $('.ui-dialog-buttonpane').find('button:contains("确定")');
            btn.removeClass('ui-state-default').addClass('ui-state-default-highlight');
            var btn2 = $('.ui-dialog-buttonpane').find('button:contains("取消")');
            btn2.removeClass('ui-state-default').addClass('ui-state-default');

            $(this).find('.note-msg').addClass('msg-ico-' + style).html(message);
        }
    });
}

/*
 * 函数名：iframe_dialog( title, url, width, height )
 * 功  能：子框架窗口
 * 参  数：title	：窗口标题
 *		   url		：指向的页面地址
 *		   i_width	：窗口宽度
 *		   i_height	：窗口高度
 */
function iframe_dialog(i_title, from_url, i_width, i_height) {
    $('<iframe border="0" id="iframe-dialog" frameborder="no" src="' + from_url + '" />').dialog({
        title: i_title,
        autoOpen: true,
        width: i_width,
        height: i_height,
        modal: true,
        resizable: false,
        autoResize: true,
        overlay: {
            opacity: 0.5,
            background: "black"
        },
        close: function(ev, ui) {
            $(this).remove();
        }
    }).width(i_width - 20).height(i_height - 20);

} 
/*
 * 函数名：close_iframe_dialog()
 * 功  能：关闭 子框架 窗口
 */
function close_iframe_dialog() {
    $('#iframe-dialog').dialog('close');
}