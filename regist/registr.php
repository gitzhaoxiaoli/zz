<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>企业申请</title>
<link href="../theme/css/style.css" type="text/css" rel="stylesheet" />
<link href="../theme/css/jquery-ui.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../theme/js/jquery.js"></script>
<script type="text/javascript" src="../theme/js/jquery-ui.js"></script>
<script type="text/javascript" src="../theme/js/region.js"></script>
<script type="text/javascript" src="../theme/js/validator.js"></script>
<script type="text/javascript">
$(function() { 
var tooltips = $( "[title]" ).tooltip({position:{my:"left bottom+37", at:"left bottom"}});
	$( "#enterprise-tab" ).tabs({
		collapsible: false
	});
	
	
	
$('button.submit-btn').click(function(){
		if( !Validator.Validate( $('#entry-form')[0], 2 ) ) return;
		if($("#passwd").val() != $("#rpasswd").val()){
			alert("两次密码不一致！！");
			return false;
			
			}
		username = $("#username").val();
		work_code = $("#work-code").val();
		ep_name = $("#ep-name").val();
		$.get("check_username.php?username="+username+"&work_code="+work_code+"&ep_name="+ep_name,function(data){
			if(data == '1'){
				alert("用户名已存在！！");
				$("#username").focus();
				return false;
				
			}else if(data == '2'){
				alert("组织机构代码存在！！");
				$("#work-code").focus();
				return false;
				
			}else if(data == '3'){
				alert("企业名称存在存在！！");
				$("#ep-name").focus();
				return false;
				
				}else{
					
					$("#entry-form").submit();
					
					}
				
			
			})
		
	});
var region = Region.init('../data/cache/region.json');
window.region = region;	
$('#select-region').click( region_dlg );
//$("#username").live("blur",check_username);
});

function region_dlg(){
	$('#select-region-dialog').dialog({
		title	: '选择行政区',
		width	: 500,
		height	: 150,
		modal: true,
		resizable: false,
		autoResize: true,
		buttons	:{
			'取消'	: function(){
				 $(this).dialog("close");
			},
			'确定'	: function(){
				var r = false;
				var country = parseInt($('#statecode').val());
				
				var code_city = $('#city-select').val();
				var code_district = $('#district-select').val();
				
				var code = $('#district-select').val();
				if( !code )
					code = $('#city-select').val();

				//if( (code && code.substring(4,6) != '00') || 156 != country ){
				if( (DistrictNum==0 && code_city) || (DistrictNum!=0 && code_district) ){
					var text = $('#province-select').find('option:selected').text() + $('#city-select').find('option:selected').text() + $('#district-select').find('option:selected').text();
					text = text.replace('市辖区','');
					text = text.replace('--县','');
					text = text.replace('请选择市','');
					text = text.replace('请选择区/县','');
					$('#meta_areaaddr').val(text);
					$('#areacode').val(code);
					$(this).dialog('close');
				} else {
					alert( '请选择行政区！' );
				}
			}
		},
		open	: function(){
			var btn = $('.ui-dialog-buttonpane').find('button:contains("确定")');
			btn.removeClass('ui-state-default').addClass('ui-state-default-highlight');
		}
	});
}

	
//检测组织名称是否重复
function check_ep_name() {
	name = $('#ep-name').val();
	var r = 1;
	//@HBJ 2013-09-26 修复修改时不检测的问题
	if( name != '<?=$ep_name;?>' && name != '' ){
		$.post(
			'?m=ajax&a=check_name',
			{ep_name:name},
			function(result) {
				var state = parseInt( result );
				if( state > 0 ) {
					alert('组织名称系统中已存在');
					r = 0;
				}
			}
		);
	}
	return r;
}

function check_username(){
	username = $("#username").val();
	if(!username){
		alert("用户名不能为空！！");
		return false;
		}
	$.get("check_username.php?username="+username,function(data){
		if(data == '1'){
			alert("用户名已存在！！");
			$("#username").focus();
			return false;
			
			}
			
		
		})
	}
</script>
</head>

<body>
<div id="select-region-dialog" class="tal" style="display:none;">
  <p style="margin-bottom:8px;">请选择行政区(到区/县)，完成后点确定。</p>
  <p> <span id="prov-span">
    <select name="province" id="province-select" style="width:130px;">
      <option value="">选择省</option>
    </select>
    </span> <span id="city-span">
    <select name="city" id="city-select" style="width:160px;">
      <option value="">请选择市</option>
    </select>
    </span> <span id="dist-span">
    <select name="district" id="district-select" style="width:150px;">
      <option value="">请选择区/县</option>
    </select>
    </span> </p>
</div>
<form method="post" id="entry-form" action="save.php">
  <div id="enterprise-tab" style="margin:0 auto;width:750px;" class="tal">
  <ul>
    <li><a href="#tab-basic">企业申请</a></li> 
  </ul>
    <ul class="main-form" style="padding-top:5px;">
    <li><br/>
      </li>
    
     
      <li>
        <label class="field">用户名：</label>
        <em>*</em><span>
        <input type="text" id="username" dataType="Require" msg="请输入用户名！" name="username" value="" class="input"  title=""  style="width:250px" />
        </span> <span id="check"></span></li>
	<li>
        <label class="field">密码：</label>
        <em>*</em><span>
        <input type="password" id="passwd" dataType="Require" msg="请输入密码！" name="passwd" value="" class="input"  title=""  style="width:250px" />
        </span> </li>
		<li>
        <label class="field">确认密码：</label>
        <em>*</em><span>
        <input type="password" id="rpasswd" dataType="Require" msg="请输入确认密码！" name="rpsswd" value="" class="input"  title=""  style="width:250px" />
        </span> </li>
        <!-- <li>
       <label class="field">组织机构代码</label>
        <em>*</em><span>
        <input type="text" id="work-code" dataType="Require" msg="请输入组织组织代码！"  name="work_code" value="" class="input" style="width:120px" />
        </span> 
        </li>-->
		<li>
        <label class="field">生产企业名称</label>
        <em>*</em><span>
        <input type="text" id="ep-name" dataType="Require" msg="请输入组织名称！" name="ep_name" value="" class="input"  title="填写营业执照上企业名称"  style="width:465px" />
        </span> </li>
       
	  <li>
        <label class="field">联系人</label>
        <em>*</em><span>
        <input type="text" dataType="Require" msg="请输入联系人！" name="person" value="" class="input" style="width:120px" />
        </span>
        <label class="field3">手机</label>
        <em></em><span>
        <input type="text"  msg="请输入联系人手机！" name="person_tel" value="" class="input" style="width:120px" />
        </span>
        
      </li>
      <li>
        <label class="field">电话</label>
        <em>*</em><span>
        <input type="text" dataType="Require" msg="请输入企业联系电话！" name="ep_phone" value="" class="input" style="width:330px" />(请写明区号，例如：010-xxxxxxx)
        </span>
		</li>
	  <li>
        <label class="field">传真</label>
        <em>*</em><span>
        <input type="text" dataType="Require" msg="请输入企业传真！" name="ep_fax" value="" class="input" style="width:120px" />(请写明区号，例如：010-xxxxxxxx)
        </span>
		</li>
	  <li>
        <label class="field">联系邮箱 </label>
        <em></em><span>
        <input type="text" name="person_email" value="" class="input" style="width:120px" />
        </span> </li>
      <!--
      <li>
        <label class="field" style="float:left;">行政区划</label>

        <em >*</em><span>
        <input type="hidden" name="areacode" id="areacode"/>
        <input type="text" readonly="readonly" id="meta_areaaddr" dataType="Require" msg="请选择企业据在行政区!" name="areaaddr" value="" title="组织认证地址所在行政区" style="height:18px;width:305px;border-style:solid;border-width:1px 0 1px 1px;border-color:#ccc;float:left;" />
        <i class="i-select" id="select-region"></i></span>
        
         </li>
      -->
		<li>
        <label class="field">通讯地址</label>
        <em>*</em><span>
        <input type="text" id="cta_addr" dataType="Require" msg="请输入企业通讯地址！" name="cta_addr" value="<?=$cta_addr;?>" class="input" style="width:500px" />
         </span> </li>
         <!--
      <li>
        <label class="field">通讯英文</label>
        <em></em><span>
        <input type="text" id="cta_addr_e" name="cta_addr_e" value="<?=$cta_addr_e;?>" class="input" style="width:368px" />
        </span>
        <label class="field3">邮编</label>
        <em>*</em><span>
        <input type="text" id="cta_addrcode" dataType="Require" msg="请输入企业通讯地址邮编！" name="cta_addrcode" value="<?=$cta_addrcode;?>" class="input" size="6" />
        </span> </li>
  
      <li>
        <label class="field">企业使用认证机构ERP系统操作指南</label>
        <em>*</em><span>
        <a href="./企业使用认证机构ERP系统操作指南.docx"><span style="color:blue"><u>点击下载</u></span></a>
        </span></li>    -->
     </ul>
    <p>
      <center>
        <button class="btn btn-submit submit-btn" type="button">提交申请</button>
      </center>
    </p>
    </form>
    <br/>
  </div>


</div>

</body>
</html>
