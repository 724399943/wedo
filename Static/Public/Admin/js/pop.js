/**
 * 
 * @authors xu (you@example.org)
 * @date    2016-09-19 11:16:02
 * @version $Id$
 */

function popwin(title,html){
	popclose();
	var objhtml = '<div class="pop_win"><div class="title">'+title+'<span class="popclose"></span></div><div class="popmain">'+html+'</div></div>';
	$('body').append(objhtml);
	var pop = $('.pop_win')
	var w = pop.width(),h = pop.height();
	pop.css({
		'margin-left': -w/2,
		'margin-top': -h/2
	});
}
$(document).on('click','.popclose',function(){
	$('.pop_win').remove();
})
//关闭弹窗
function popclose(){
  $('.pop_win').remove();
}
// 用法
// popwin('标题内容',主题html内容);