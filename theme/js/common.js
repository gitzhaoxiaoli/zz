function isTEL(str)   //验证电话号码
{
	
	var strTemp="0123456789-()-#*（）";
	 for ( var i=0;i<str.length;i++)
	 {
	  if(strTemp.indexOf(str.charAt(i))==-1){
		 return false;
	  }
	 }
	
	return true;
}

function strlen(str)   //验证字符串长度
{
	var i;
	var len;
	len = 0;
	for (i=0;i<str.length;i++)
	{
	if (str.charCodeAt(i)>255) len+=2; else len++;
	}
	return len;
}

function isFloat(str){   //验证浮点数字
	var number="1234567890.";
	for(var i=0;i<str.length;i++){
		if(number.indexOf(str.charAt(i))==-1){
			return false;
		}
	}
	var arr=str.split(".");
	if(arr.length>2){
		return false;
	}
	return true;
}

function isNumber(str){   //验证是否是数字
	var number_chars="1234567890";
	for(var i=0;i<str.length;i++){
	if(number_chars.indexOf(str.charAt(i))==-1){
			return false;
		}
	}
	
	return true;
}

function isEmail(str){    //验证邮箱名称
 	var i=str.length;
 	var temp = str.indexOf('@');
 	var tempd = str.indexOf('.');
 	if (temp > 1) {
  		if ((i-temp) > 3){
   
    		if ((i-tempd)>0){
    			 return 1;
    		}
   
 		 }
 	}
 	return 0;
 }
 function isNumber2(str){   //验证是否是数字+x
	var number_chars="1234567890xX";
	for(var i=0;i<str.length;i++){
	if(number_chars.indexOf(str.charAt(i))==-1){
			return false;
		}
	}
	
	return true;
}
 function isIdCard(str){   //验证身份证号码

	 	var len=str.length-1;
		if(!isNumber2(str)){
			alert("身份证只能由数字或x组成");
			return false;
		}else{
			if(str.length!=15 && str.length!=18){
				alert("身份证位数不正确");
				return false;
			}else{
				if(str.length==15 && !isNumber(str)){
					alert("15位身份证只能由数字组成");
					return false;
				}else{
					if(str.indexOf("x")!=-1 || str.indexOf("X")!=-1){
						if((str.indexOf("x")!=len)&&(str.indexOf("X")!=len)){
							alert("身份证错误");
							return false;
						}
					}
				}
			}
		}
		
	return true;
}

function isAge(str){    //验证年龄
	if(isNumber(str)){

		if(str>18 && str<70){
			return true;
		}
	}
	return false;
}

function isMore(str,length){
	if(str.length>length){
		return false;
	}
	return true;
}

function isChecked(myobj){     //验证复选框中是否有选择
	if(myobj.length){
		for(var i=0;i<myobj.length;i++){
			if(myobj[i].checked){
				return true;
			}
		}
		myobj[0].focus();
		return false;
	}
	else{
		if(myobj.checked==false){
			myobj.focus();
			return false;
		}
		return true;
	}
}

//检验两个日期的先后
function checkDateEarlier(strStart,strEnd,dif)
{
 //如果有一个输入为空，则通过检验
    if (( strStart == "" ) || ( strEnd == "" ))
        return true;
    var arr1 = strStart.split("-");
    var arr2 = strEnd.split("-");
    var date1 = new Date(arr1[0],parseInt(arr1[1].replace(/^0/,""),10) - 1,arr1[2]);
    var date2 = new Date(arr2[0],parseInt(arr2[1].replace(/^0/,""),10) - 1,arr2[2]);
    if(arr1[1].length == 1)
        arr1[1] = "0" + arr1[1];
    if(arr1[2].length == 1)
        arr1[2] = "0" + arr1[2];
    if(arr2[1].length == 1)
        arr2[1] = "0" + arr2[1];
    if(arr2[2].length == 1)
        arr2[2]="0" + arr2[2];
    var d1 = arr1[0] + arr1[1] + arr1[2];
    var d2 = arr2[0] + arr2[1] + arr2[2];
    var i=parseInt(d2,10)-parseInt(d1,10);
    if(i>=dif)
       return true;
    else
       return false;
}


function isTime(str){      //验证时间
	var number="1234567890:";
	for(var i=0;i<str.length;i++){
		if(number.indexOf(str.charAt(i))==-1){
			return false;
		}
	}
	if(str.indexOf(":")==-1){
		return false;
	}
	if(str.length<3 && str.length>5){
		return false;
	}
	var arg=str.split(":");
	if(arg[0]<0 || arg[0]>24){
		return false;
	}
	if(arg[1]<0 || arg[1]>60){
		return false;
	}
	return true;	
}

function checkTime(hour1,min1,hour2,min2){  //验证订票页面中的进入时间大于出去时间

	if(hour1>hour2){
		return false;
	}
	if(hour1==hour2){
		if(min1>min2){
			return false;
		}
	}
	return true;
}