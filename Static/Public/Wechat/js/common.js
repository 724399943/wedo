Date.prototype.pattern=function(fmt) {
    var o = {
        "M+" : this.getMonth()+1, //月份
        "d+" : this.getDate(), //日
        "h+" : this.getHours()%12 == 0 ? 12 : this.getHours()%12, //小时
        "H+" : this.getHours(), //小时
        "m+" : this.getMinutes(), //分
        "s+" : this.getSeconds(), //秒
        "q+" : Math.floor((this.getMonth()+3)/3), //季度
        "S" : this.getMilliseconds() //毫秒
    };
    var week = {
        "0" : "/u65e5",
        "1" : "/u4e00",
        "2" : "/u4e8c",
        "3" : "/u4e09",
        "4" : "/u56db",
        "5" : "/u4e94",
        "6" : "/u516d"
    };
    if(/(y+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    if(/(E+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[this.getDay()+""]);
    }
    for(var k in o){
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}
Date.prototype.format = function(format) {
    var o = {
        "M+" : this.getMonth()+1, //month 
        "d+" : this.getDate(), //day 
        "h+" : this.getHours(), //hour 
        "m+" : this.getMinutes(), //minute 
        "s+" : this.getSeconds(), //second 
        "q+" : Math.floor((this.getMonth()+3)/3), //quarter 
        "S" : this.getMilliseconds() //millisecond 
    } 
    if(/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    } 
    for(var k in o) {
        if(new RegExp("("+ k +")").test(format)) { 
            format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length)); 
        } 
    } 
    return format;
}

/*判断电话格式*/
function isTelephone (telephone) {
    var telReg = !!telephone.match(/^(0|86|17951)?(13[0-9]|15[012356789]|17[6780]|18[0-9]|14[57])[0-9]{8}$/);
    //如果手机号码不能通过验证
    return telReg;
}

/*判断邮箱格式*/
function isEmail (email) {
    var emailReg = !!email.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/);
    //如果邮箱不能通过验证
    return emailReg;
}

/*倒计时*/
function countTime(endtime, obj ,callback ,callback2) {         
   var timer = setInterval(function() {     
       if(endtime >= 0) {
            var day = Math.floor(endtime/60/60/24),
                hour = Math.floor(endtime/60/60%24),
                minutes = Math.floor(endtime/60%60),
                seconds = Math.floor(endtime%60);
             if(hour<10) {
                hour = "0"+hour;
             }
             if(minutes<10) {
                minutes = "0"+minutes;
             }
             if(seconds<10) {
                seconds = "0"+seconds;
             }                  
             callback(day,hour,minutes,seconds,obj);     
             -- endtime;
        } else {        
            clearInterval( timer );
            callback2(obj); 
        }        
    }, 1000);     
}

/*计算年龄*/
function calcAge(birthday) {
    var r = birthday.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/);
    if(r == null)return false;
    var d = new Date(r[1], parseInt(r[3]) - 1, r[4]);     
    if (d.getFullYear() == r[1] && (d.getMonth() + 1) == r[3] && d.getDate() == r[4]) {   
        var Y = new Date().getFullYear();
        return (Y - r[1]);
        // console.log("年龄   =   " + (Y - r[1]) + "   周岁");   
    } else {
        alert("输入的日期格式错误！");   
    }
}

function timer() {
    var value = Number($('#Jverify').text());
    if (value > 1) {
        document.getElementById("Jverify").innerHTML = value-1;
        //$('#Jverify').text(value - 1);
    } else {
        document.getElementById("Jverify").innerHTML = '重新获取';
        //$('#Jverify').text('重新获取');
        getVerifySign = true;
        return false;
    }
    window.setTimeout("timer()", 1000);
}


/*分页*/
//pageIndex 为当前页  pageCount为总页数 type为当前tab ，部分tab传0；
// function setCommentPage(pageIndex, pageCount, type) {
//     var temp="";
//     var pageType="";
//     if(type ==0 ){
//         pageType="";
//     }else{
//         pageType = type;
//     }
//     //如果页数超过1时候显示分页
//     if (pageCount > 1){
//         if(pageIndex!=1){
//             //如果不是第一页的时候显示上一页
//             var k=pageIndex-1;
//             temp +='<a id="Jpageprev" class="pageButton prev" ><em></em></a>';
//         }else{
//             //是第一页的时候隐藏上一页
//             temp +='';
//         }
//         for (i = 1; i <= pageCount;i++)
//         {
//             if (i == pageIndex)//当前页
//             {
//                 temp +="<a id='comment"+ i+ "' class='pageButton cur Jclick' href='javascript:;'>";
//                 temp +=i+"</a>";
//             }
//             else
//             {
//                 if(pageIndex-i>=4&&i!=1)  //只显示当前页前三个页码
//                 {
//                     temp+="<a class='pageButton'>...</a>";
//                     i=pageIndex-3;//将页码跳到没有省略的页码
//                 }
//                 else
//                 {
//                     if(i>=pageIndex+3&&i!=pageCount)  //只显示当前页的后两个页码
//                     {
//                         if(pageIndex == pageCount-4){
//                             temp +="<a id='comment"+ (pageCount-1) +"' class='pageButton Jclick' page='"+ (pageCount-1) +"' data-type='"+ pageType +"' data-done='0' href='javascript:;'>";
//                             temp +=(pageCount-1)+"</a>";
//                         }else{
//                             temp+="<a class='pageButton'>...</a>";
//                         }
//                         i=pageCount;  //将页码跳到最后一页
//                     }
//                     temp +="<a id='comment"+ i +"' class='pageButton Jclick' page='"+ i +"' data-type='"+ pageType +"' data-done='0' href='javascript:;'>";
//                     temp +=i+"</a>";
//                 }
//             }
//         }
//         if(pageIndex!=pageCount)
//         {
//             //不是最后一页的时候
//             var k=pageIndex+1;
//             temp +='<a id="Jpageprev" class="pageButton next" ><em></em></a>';
//         }else{
//             //最后一页的时候
//             temp +='';
//         }
//     }
//     if( pageCount==1 ){
//         //temp +="<a id='comment"+ 1+ "' class='pageButton cur Jclick' href='javascript:;'>" + 1 + "</a>";
//     }
//     document.getElementById('pageBox'+pageType).innerHTML=temp;
// }

function createPageTags(pageIndex, pageCount, type) {
    var temp="";
    var pageType=type ;
    pageCount = Number(pageCount);
    pageIndex = Number(pageIndex);
    if ( pageCount > 1 ) {
        if(type == 0){
            pageType = "";
        }else{
            pageType = type;
        }
        //如果页数超过1时候显示分页
        if (pageCount > 1){
            if(pageIndex!=1){
                //如果不是第一页的时候显示上一页
                var k=pageIndex-1;
                temp +='<a href="javascript:;" class="prev" page="'+pageIndex+'" type="'+pageType+'">prev page</a>';
            }else{
                //是第一页的时候隐藏上一页
                temp +='';
            }
            for (i = 1; i <= pageCount;i++)
            {
                if (i == pageIndex)//当前页
                {
                    temp +='<a page="'+i+'" class="page cur" href="javascript:;" type="'+pageType+'">';
                    temp +=i+"</a>";
                }
                else
                {
                    if(pageIndex-i>=4&&i!=1)  //只显示当前页前三个页码
                    {
                        temp+="<b>...</b>";
                        i=pageIndex-3;//将页码跳到没有省略的页码
                    }
                    else
                    {
                        if(i>=pageIndex+3&&i!=pageCount)  //只显示当前页的后两个页码
                        {
                            if(pageIndex == pageCount-4){
                                temp +="<a class='page' page='"+ (pageCount-1) +"' pageNumber='"+pageCount+"' href='javascript:;' type='"+pageType+"'>";
                                temp +=(pageCount-1)+"</a>";
                            }else{
                                temp+="<b>...</b>";
                            }
                            i=pageCount;  //将页码跳到最后一页
                        }
                        temp +="<a class='page' page='"+ i +"' pageNumber='"+pageCount+"' href='javascript:;' type='"+pageType+"'>";
                        temp +=i+"</a>";
                    }
                }
            }
            if(pageIndex!=pageCount)
            {
                //不是最后一页的时候
                var k=pageIndex+1;
                temp +='<a href="javascript:;" class="next" page="'+pageIndex+'" type="'+pageType+'">next page</a>';
            }else{
                //最后一页的时候
                temp +='';
            }
            // temp +='<span class="txt">共' + pageCount +'页，到第</span> <input type="text" name="p" class="text pageNumber J-limit" />&nbsp;&nbsp;页 <input type="button" value="确定"  class="btn Jpagebtn" type="'+pageType+'"></div>';
        }
        if( pageCount==1 ){
            temp +="<a class='page cur' href='javascript:;'>" + 1 + "</a>";
        }   
        document.getElementById('pageBox').innerHTML = temp;
    }
}

/**
 * [listenPageEvent 监听页码事件]
 * @author kofu <418382595@qq.com>
 * @copyright Copyright (c)     2017          Xcrozz (http://www.xcrozz.com)
 * @param     {[type]}      execute [description]
 * @return    {[type]}              [description]
 */
function listenPageEvent(execute) {
    $('.page').click(function(){
        var page = $(this).attr('page');
        execute(page);
    });
    $('.Jpagebtn').click(function(){
        var page = $('input[name="p"]').val();
        if ( !page ) {
            alert('请输入页码');
            return;
        }
        execute(page);
    });
    $('.prev').click(function(){
        var page = parseInt($(this).attr('page'));
        page -= 1;
        execute(page);
    });
    $('.next').click(function(){
        var page = parseInt($(this).attr('page'));
        page += 1;
        execute(page);
    });
}
