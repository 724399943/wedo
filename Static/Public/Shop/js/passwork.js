  $(function () {
      $.passcon=function(pass_input,con1,con2,con3){
            $(pass_input).keyup(function () {
                var strongRegex = new RegExp("^(?=.{8,})(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
                var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
                var enoughRegex = new RegExp("(?=.{6,}).*", "g");
                if (false == enoughRegex.test($(this).val())) {
                  
                    $(con1).removeClass('on').siblings().removeClass('on');
                    //('小于六位的时候'); //密码小于六位的时候，密码强度图片都为灰色
                }
                 else if (strongRegex.test($(this).val())) {
                 
                     $(con3).addClass('on').siblings().removeClass('on')
                                      
                    //('强!');  //密码为八位及以上并且字母数字特殊字符三项都包括
                }
                else if (mediumRegex.test($(this).val())) {
                  
                     $(con1).removeClass('on');
                     $(con2).addClass('on');
                     $(con3).removeClass('on');
                    //$('#passstrength').html('中!');  //密码为七位及以上并且字母、数字、特殊字符三项中有两项，强度是中等
                }
                 else {
               
                     $(con1).addClass('on').siblings().removeClass('on');
                    //$('#passstrength').html('弱!');   //如果密码为6为及以下，就算字母、数字、特殊字符三项都包括，强度也是弱的
                }
                return true;
            });
            
      };
      

});
    