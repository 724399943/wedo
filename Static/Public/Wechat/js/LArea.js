window.LArea = (function() {
    var MobileArea = function() {
        this.gearArea;
        this.data;
        this.index = 0;
        this.value = [0, 0, 0];
        this.callback = '';
    }
    MobileArea.prototype = {
        init: function(params) {
            this.params = params;
            this.trigger = document.querySelector(params.trigger);
            if(params.valueTo){
              this.valueTo=document.querySelector(params.valueTo);
            }
            this.keys = params.keys;
            this.type = params.type||1;
            switch (this.type) {
                case 1:
                case 2:
                    break;
                default:
                    throw new Error('错误提示: 没有这种数据源类型');
                    break;
            }
            if (params.callback) {
                this.callback = params.callback;
            }
            this.bindEvent();
        },
        getData: function(callback) {
            var _self = this;
            _self.params.data = LAreaData;
            if (typeof _self.params.data == "object") {
                _self.data = _self.params.data;
                callback();
            } else {
                var xhr = new XMLHttpRequest();
                xhr.open('get', _self.params.data);
                xhr.onload = function(e) {
                    if ((xhr.status >= 200 && xhr.status < 300) || xhr.status === 0) {
                        var responseData = JSON.parse(xhr.responseText);
                        _self.data = responseData.data;
                        if (callback) {
                            callback()
                        };
                    }
                }
                xhr.send();
            }
        },
        bindEvent: function() {
            var _self = this;
            //呼出插件
            function popupArea(e) {
                _self.gearArea = document.createElement("div");
                _self.gearArea.className = "gearArea";
                _self.gearArea.innerHTML = '<div class="area_ctrl slideInUp">' +
                    '<div class="area_btn_box">' +
                    '<div class="area_btn larea_cancel">取消</div>' +
                    '<div class="area_btn larea_finish">确定</div>' +
                    '</div>' +
                    '<div class="area_roll_mask">' +
                    '<div class="area_roll">' +
                    '<div>' +
                    '<div class="gear area_province" data-areatype="area_province"></div>' +
                    '<div class="area_grid">' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear area_city" data-areatype="area_city"></div>' +
                    '<div class="area_grid">' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear area_county" data-areatype="area_county"></div>' +
                    '<div class="area_grid">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                document.getElementById("content").appendChild(_self.gearArea);
                areaCtrlInit();
                var larea_cancel = _self.gearArea.querySelector(".larea_cancel");
                larea_cancel.addEventListener('touchstart', function(e) {
                    _self.close(e);
                });
                var larea_finish = _self.gearArea.querySelector(".larea_finish");
                larea_finish.addEventListener('touchstart', function(e) {
                    _self.finish(e);
                });
                var area_province = _self.gearArea.querySelector(".area_province");
                var area_city = _self.gearArea.querySelector(".area_city");
                var area_county = _self.gearArea.querySelector(".area_county");
                area_province.addEventListener('touchstart', gearTouchStart);
                area_city.addEventListener('touchstart', gearTouchStart);
                area_county.addEventListener('touchstart', gearTouchStart);
                area_province.addEventListener('touchmove', gearTouchMove);
                area_city.addEventListener('touchmove', gearTouchMove);
                area_county.addEventListener('touchmove', gearTouchMove);
                area_province.addEventListener('touchend', gearTouchEnd);
                area_city.addEventListener('touchend', gearTouchEnd);
                area_county.addEventListener('touchend', gearTouchEnd);
            }
            //初始化插件默认值
            function areaCtrlInit() {
                _self.gearArea.querySelector(".area_province").setAttribute("val", _self.value[0]);
                _self.gearArea.querySelector(".area_city").setAttribute("val", _self.value[1]);
                _self.gearArea.querySelector(".area_county").setAttribute("val", _self.value[2]);

                switch (_self.type) {
                    case 1:
                        _self.setGearTooth(_self.data);
                        break;
                    case 2:
                        _self.setGearTooth(_self.data[0]);
                        break;
                }
            }
            //触摸开始
            function gearTouchStart(e) {
                e.preventDefault();
                var target = e.target;
                while (true) {
                    if (!target.classList.contains("gear")) {
                        target = target.parentElement;
                    } else {
                        break
                    }
                }
                clearInterval(target["int_" + target.id]);
                target["old_" + target.id] = e.targetTouches[0].screenY;
                target["o_t_" + target.id] = (new Date()).getTime();
                var top = target.getAttribute('top');
                if (top) {
                    target["o_d_" + target.id] = parseFloat(top.replace(/em/g, ""));
                } else {
                    target["o_d_" + target.id] = 0;
                }
                target.style.webkitTransitionDuration = target.style.transitionDuration = '0ms';
            }
            //手指移动
            function gearTouchMove(e) {
                e.preventDefault();
                var target = e.target;
                while (true) {
                    if (!target.classList.contains("gear")) {
                        target = target.parentElement;
                    } else {
                        break
                    }
                }
                target["new_" + target.id] = e.targetTouches[0].screenY;
                target["n_t_" + target.id] = (new Date()).getTime();
                var f = (target["new_" + target.id] - target["old_" + target.id]) * 30 / window.innerHeight;
                target["pos_" + target.id] = target["o_d_" + target.id] + f;
                target.style["-webkit-transform"] = 'translate3d(0,' + target["pos_" + target.id] + 'em,0)';
                target.setAttribute('top', target["pos_" + target.id] + 'em');
                if(e.targetTouches[0].screenY<1){
                    gearTouchEnd(e);
                };
            }
            //离开屏幕
            function gearTouchEnd(e) {
                e.preventDefault();
                var target = e.target;
                while (true) {
                    if (!target.classList.contains("gear")) {
                        target = target.parentElement;
                    } else {
                        break;
                    }
                }
                var flag = (target["new_" + target.id] - target["old_" + target.id]) / (target["n_t_" + target.id] - target["o_t_" + target.id]);
                if (Math.abs(flag) <= 0.2) {
                    target["spd_" + target.id] = (flag < 0 ? -0.08 : 0.08);
                } else {
                    if (Math.abs(flag) <= 0.5) {
                        target["spd_" + target.id] = (flag < 0 ? -0.16 : 0.16);
                    } else {
                        target["spd_" + target.id] = flag / 2;
                    }
                }
                if (!target["pos_" + target.id]) {
                    target["pos_" + target.id] = 0;
                }
                rollGear(target);
            }
            //缓动效果
            function rollGear(target) {
                var d = 0;
                var stopGear = false;
                function setDuration() {
                    target.style.webkitTransitionDuration = target.style.transitionDuration = '200ms';
                    stopGear = true;
                }
                clearInterval(target["int_" + target.id]);
                target["int_" + target.id] = setInterval(function() {
                    var pos = target["pos_" + target.id];
                    var speed = target["spd_" + target.id] * Math.exp(-0.03 * d);
                    pos += speed;
                    if (Math.abs(speed) > 0.1) {} else {
                        var b = Math.round(pos / 2) * 2;
                        pos = b;
                        setDuration();
                    }
                    if (pos > 0) {
                        pos = 0;
                        setDuration();
                    }
                    var minTop = -(target.dataset.len - 1) * 2;
                    if (pos < minTop) {
                        pos = minTop;
                        setDuration();
                    }
                    if (stopGear) {
                        var gearVal = Math.abs(pos) / 2;
                        setGear(target, gearVal);
                        clearInterval(target["int_" + target.id]);
                    }
                    target["pos_" + target.id] = pos;
                    target.style["-webkit-transform"] = 'translate3d(0,' + pos + 'em,0)';
                    target.setAttribute('top', pos + 'em');
                    d++;
                }, 30);
            }
            //控制插件滚动后停留的值
            function setGear(target, val) {
                val = Math.round(val);
                target.setAttribute("val", val);
                switch (_self.type) {
                    case 1:
                         _self.setGearTooth(_self.data);
                        break;
                    case 2:
                     switch(target.dataset['areatype']){
                         case 'area_province':
                         _self.setGearTooth(_self.data[0]);
                             break;
                         case 'area_city':
                             var ref = target.childNodes[val].getAttribute('ref');
                             var childData=[];
                             var nextData= _self.data[2];
                             for (var i in nextData) {
                                 if(i==ref){
                                    childData = nextData[i];
                                    break;
                                 }
                             };
                        _self.index=2;
                        _self.setGearTooth(childData);
                             break;
                     }
                }
                
            }
            _self.getData(function() {
                _self.trigger.addEventListener('click', popupArea);
            });
        },
        //重置节点个数
        setGearTooth: function(data) {
            var _self = this;
            var item = data || [];
            var l = item.length;
            var gearChild = _self.gearArea.querySelectorAll(".gear");
            var gearVal = gearChild[_self.index].getAttribute('val');
            var maxVal = l - 1;
            if (gearVal > maxVal) {
                gearVal = maxVal;
            }
            gearChild[_self.index].setAttribute('data-len', l);
            if (l > 0) {
                var id = item[gearVal][this.keys['id']];
                var childData;
                switch (_self.type) {
                    case 1:
                    childData = item[gearVal].child
                        break;
                    case 2:
                     var nextData= _self.data[_self.index+1] 
                     for (var i in nextData) {
                         if(i==id){
                            childData = nextData[i];
                            break;
                         }
                     };
                        break;
                }
                var itemStr = "";
                for (var i = 0; i < l; i++) {
                    itemStr += "<div class='tooth' id='" + item[i][this.keys['id']] + "'  ref='" + item[i][this.keys['id']] + "'>" + item[i][this.keys['name']] + "</div>";
                }
                gearChild[_self.index].innerHTML = itemStr;
                gearChild[_self.index].style["-webkit-transform"] = 'translate3d(0,' + (-gearVal * 2) + 'em,0)';
                gearChild[_self.index].setAttribute('top', -gearVal * 2 + 'em');
                gearChild[_self.index].setAttribute('val', gearVal);
                _self.index++;
                if (_self.index > 2) {
                    _self.index = 0;
                    return;
                }
                _self.setGearTooth(childData);
            } else {
                gearChild[_self.index].innerHTML = "<div class='tooth'></div>";
                gearChild[_self.index].setAttribute('val', 0);
                if(_self.index==1){
                    gearChild[2].innerHTML = "<div class='tooth'></div>";
                    gearChild[2].setAttribute('val', 0);
                }
                _self.index = 0;
            }
        },
        finish: function(e) {
            var _self = this;
            var area_province = _self.gearArea.querySelector(".area_province");
            var area_city = _self.gearArea.querySelector(".area_city");
            var area_county = _self.gearArea.querySelector(".area_county");
            var provinceVal = parseInt(area_province.getAttribute("val"));
            var provinceText = area_province.childNodes[provinceVal].textContent;
            var provinceCode = area_province.childNodes[provinceVal].getAttribute('ref');
            var cityVal = parseInt(area_city.getAttribute("val"));
            var cityText = area_city.childNodes[cityVal].textContent;
            var cityCode = area_city.childNodes[cityVal].getAttribute('ref');
            var countyVal = parseInt(area_county.getAttribute("val"));
            var countyText = area_county.childNodes[countyVal].textContent;
            var countyCode = area_county.childNodes[countyVal].getAttribute('ref');
            _self.trigger.value = provinceText + ((cityText)?(' - ' + cityText):(''))+ ((countyText)?(' - ' + countyText):(''));
            _self.value = [provinceVal, cityVal, countyVal];
            if(this.valueTo){
                // this.valueTo.value= provinceCode +((cityCode)?(',' + cityCode):('')) + ((countyCode)?(',' + countyCode):(''));
                this.valueTo.value= provinceVal + ',' + cityVal + ',' + countyVal;
            }
            document.getElementById('province').value = provinceCode;
            document.getElementById('city').value = cityCode;
            document.getElementById('county').value = countyCode;
            _self.callback ? _self.callback() : '';
            _self.close(e);
        },
        close: function(e) {
            e.preventDefault();
            var _self = this;
            var evt = new CustomEvent('input');
            _self.trigger.dispatchEvent(evt);
            document.getElementById("content").removeChild(_self.gearArea);
            _self.gearArea=null;
        }
    }
    return MobileArea;
})()

var region={"2":{"name":"北京"},"3":{"name":"安徽"},"4":{"name":"福建"},"5":{"name":"甘肃"},"6":{"name":"广东"},"7":{"name":"广西"},"8":{"name":"贵州"},"9":{"name":"海南"},"10":{"name":"河北"},"11":{"name":"河南"},"12":{"name":"黑龙江"},"13":{"name":"湖北"},"14":{"name":"湖南"},"15":{"name":"吉林"},"16":{"name":"江苏"},"17":{"name":"江西"},"18":{"name":"辽宁"},"19":{"name":"内蒙古"},"20":{"name":"宁夏"},"21":{"name":"青海"},"22":{"name":"山东"},"23":{"name":"山西"},"24":{"name":"陕西"},"25":{"name":"上海"},"26":{"name":"四川"},"27":{"name":"天津"},"28":{"name":"西藏"},"29":{"name":"新疆"},"30":{"name":"云南"},"31":{"name":"浙江"},"32":{"name":"重庆"},"33":{"name":"香港"},"34":{"name":"澳门"},"35":{"name":"台湾"},"36":{"name":"安庆"},"37":{"name":"蚌埠"},"38":{"name":"巢湖"},"39":{"name":"池州"},"40":{"name":"滁州"},"41":{"name":"阜阳"},"42":{"name":"淮北"},"43":{"name":"淮南"},"44":{"name":"黄山"},"45":{"name":"六安"},"46":{"name":"马鞍山"},"47":{"name":"宿州"},"48":{"name":"铜陵"},"49":{"name":"芜湖"},"50":{"name":"宣城"},"51":{"name":"亳州"},"52":{"name":"北京"},"53":{"name":"福州"},"54":{"name":"龙岩"},"55":{"name":"南平"},"56":{"name":"宁德"},"57":{"name":"莆田"},"58":{"name":"泉州"},"59":{"name":"三明"},"60":{"name":"厦门"},"61":{"name":"漳州"},"62":{"name":"兰州"},"63":{"name":"白银"},"64":{"name":"定西"},"65":{"name":"甘南"},"66":{"name":"嘉峪关"},"67":{"name":"金昌"},"68":{"name":"酒泉"},"69":{"name":"临夏"},"70":{"name":"陇南"},"71":{"name":"平凉"},"72":{"name":"庆阳"},"73":{"name":"天水"},"74":{"name":"武威"},"75":{"name":"张掖"},"76":{"name":"广州"},"77":{"name":"深圳"},"78":{"name":"潮州"},"79":{"name":"东莞"},"80":{"name":"佛山"},"81":{"name":"河源"},"82":{"name":"惠州"},"83":{"name":"江门"},"84":{"name":"揭阳"},"85":{"name":"茂名"},"86":{"name":"梅州"},"87":{"name":"清远"},"88":{"name":"汕头"},"89":{"name":"汕尾"},"90":{"name":"韶关"},"91":{"name":"阳江"},"92":{"name":"云浮"},"93":{"name":"湛江"},"94":{"name":"肇庆"},"95":{"name":"中山"},"96":{"name":"珠海"},"97":{"name":"南宁"},"98":{"name":"桂林"},"99":{"name":"百色"},"100":{"name":"北海"},"101":{"name":"崇左"},"102":{"name":"防城港"},"103":{"name":"贵港"},"104":{"name":"河池"},"105":{"name":"贺州"},"106":{"name":"来宾"},"107":{"name":"柳州"},"108":{"name":"钦州"},"109":{"name":"梧州"},"110":{"name":"玉林"},"111":{"name":"贵阳"},"112":{"name":"安顺"},"113":{"name":"毕节"},"114":{"name":"六盘水"},"115":{"name":"黔东南"},"116":{"name":"黔南"},"117":{"name":"黔西南"},"118":{"name":"铜仁"},"119":{"name":"遵义"},"120":{"name":"海口"},"121":{"name":"三亚"},"122":{"name":"白沙"},"123":{"name":"保亭"},"124":{"name":"昌江"},"125":{"name":"澄迈县"},"126":{"name":"定安县"},"127":{"name":"东方"},"128":{"name":"乐东"},"129":{"name":"临高县"},"130":{"name":"陵水"},"131":{"name":"琼海"},"132":{"name":"琼中"},"133":{"name":"屯昌县"},"134":{"name":"万宁"},"135":{"name":"文昌"},"136":{"name":"五指山"},"137":{"name":"儋州"},"138":{"name":"石家庄"},"139":{"name":"保定"},"140":{"name":"沧州"},"141":{"name":"承德"},"142":{"name":"邯郸"},"143":{"name":"衡水"},"144":{"name":"廊坊"},"145":{"name":"秦皇岛"},"146":{"name":"唐山"},"147":{"name":"邢台"},"148":{"name":"张家口"},"149":{"name":"郑州"},"150":{"name":"洛阳"},"151":{"name":"开封"},"152":{"name":"安阳"},"153":{"name":"鹤壁"},"154":{"name":"济源"},"155":{"name":"焦作"},"156":{"name":"南阳"},"157":{"name":"平顶山"},"158":{"name":"三门峡"},"159":{"name":"商丘"},"160":{"name":"新乡"},"161":{"name":"信阳"},"162":{"name":"许昌"},"163":{"name":"周口"},"164":{"name":"驻马店"},"165":{"name":"漯河"},"166":{"name":"濮阳"},"167":{"name":"哈尔滨"},"168":{"name":"大庆"},"169":{"name":"大兴安岭"},"170":{"name":"鹤岗"},"171":{"name":"黑河"},"172":{"name":"鸡西"},"173":{"name":"佳木斯"},"174":{"name":"牡丹江"},"175":{"name":"七台河"},"176":{"name":"齐齐哈尔"},"177":{"name":"双鸭山"},"178":{"name":"绥化"},"179":{"name":"伊春"},"180":{"name":"武汉"},"181":{"name":"仙桃"},"182":{"name":"鄂州"},"183":{"name":"黄冈"},"184":{"name":"黄石"},"185":{"name":"荆门"},"186":{"name":"荆州"},"187":{"name":"潜江"},"188":{"name":"神农架林区"},"189":{"name":"十堰"},"190":{"name":"随州"},"191":{"name":"天门"},"192":{"name":"咸宁"},"193":{"name":"襄樊"},"194":{"name":"孝感"},"195":{"name":"宜昌"},"196":{"name":"恩施"},"197":{"name":"长沙"},"198":{"name":"张家界"},"199":{"name":"常德"},"200":{"name":"郴州"},"201":{"name":"衡阳"},"202":{"name":"怀化"},"203":{"name":"娄底"},"204":{"name":"邵阳"},"205":{"name":"湘潭"},"206":{"name":"湘西"},"207":{"name":"益阳"},"208":{"name":"永州"},"209":{"name":"岳阳"},"210":{"name":"株洲"},"211":{"name":"长春"},"212":{"name":"吉林"},"213":{"name":"白城"},"214":{"name":"白山"},"215":{"name":"辽源"},"216":{"name":"四平"},"217":{"name":"松原"},"218":{"name":"通化"},"219":{"name":"延边"},"220":{"name":"南京"},"221":{"name":"苏州"},"222":{"name":"无锡"},"223":{"name":"常州"},"224":{"name":"淮安"},"225":{"name":"连云港"},"226":{"name":"南通"},"227":{"name":"宿迁"},"228":{"name":"泰州"},"229":{"name":"徐州"},"230":{"name":"盐城"},"231":{"name":"扬州"},"232":{"name":"镇江"},"233":{"name":"南昌"},"234":{"name":"抚州"},"235":{"name":"赣州"},"236":{"name":"吉安"},"237":{"name":"景德镇"},"238":{"name":"九江"},"239":{"name":"萍乡"},"240":{"name":"上饶"},"241":{"name":"新余"},"242":{"name":"宜春"},"243":{"name":"鹰潭"},"244":{"name":"沈阳"},"245":{"name":"大连"},"246":{"name":"鞍山"},"247":{"name":"本溪"},"248":{"name":"朝阳"},"249":{"name":"丹东"},"250":{"name":"抚顺"},"251":{"name":"阜新"},"252":{"name":"葫芦岛"},"253":{"name":"锦州"},"254":{"name":"辽阳"},"255":{"name":"盘锦"},"256":{"name":"铁岭"},"257":{"name":"营口"},"258":{"name":"呼和浩特"},"259":{"name":"阿拉善盟"},"260":{"name":"巴彦淖尔盟"},"261":{"name":"包头"},"262":{"name":"赤峰"},"263":{"name":"鄂尔多斯"},"264":{"name":"呼伦贝尔"},"265":{"name":"通辽"},"266":{"name":"乌海"},"267":{"name":"乌兰察布市"},"268":{"name":"锡林郭勒盟"},"269":{"name":"兴安盟"},"270":{"name":"银川"},"271":{"name":"固原"},"272":{"name":"石嘴山"},"273":{"name":"吴忠"},"274":{"name":"中卫"},"275":{"name":"西宁"},"276":{"name":"果洛"},"277":{"name":"海北"},"278":{"name":"海东"},"279":{"name":"海南"},"280":{"name":"海西"},"281":{"name":"黄南"},"282":{"name":"玉树"},"283":{"name":"济南"},"284":{"name":"青岛"},"285":{"name":"滨州"},"286":{"name":"德州"},"287":{"name":"东营"},"288":{"name":"菏泽"},"289":{"name":"济宁"},"290":{"name":"莱芜"},"291":{"name":"聊城"},"292":{"name":"临沂"},"293":{"name":"日照"},"294":{"name":"泰安"},"295":{"name":"威海"},"296":{"name":"潍坊"},"297":{"name":"烟台"},"298":{"name":"枣庄"},"299":{"name":"淄博"},"300":{"name":"太原"},"301":{"name":"长治"},"302":{"name":"大同"},"303":{"name":"晋城"},"304":{"name":"晋中"},"305":{"name":"临汾"},"306":{"name":"吕梁"},"307":{"name":"朔州"},"308":{"name":"忻州"},"309":{"name":"阳泉"},"310":{"name":"运城"},"311":{"name":"西安"},"312":{"name":"安康"},"313":{"name":"宝鸡"},"314":{"name":"汉中"},"315":{"name":"商洛"},"316":{"name":"铜川"},"317":{"name":"渭南"},"318":{"name":"咸阳"},"319":{"name":"延安"},"320":{"name":"榆林"},"321":{"name":"上海"},"322":{"name":"成都"},"323":{"name":"绵阳"},"324":{"name":"阿坝"},"325":{"name":"巴中"},"326":{"name":"达州"},"327":{"name":"德阳"},"328":{"name":"甘孜"},"329":{"name":"广安"},"330":{"name":"广元"},"331":{"name":"乐山"},"332":{"name":"凉山"},"333":{"name":"眉山"},"334":{"name":"南充"},"335":{"name":"内江"},"336":{"name":"攀枝花"},"337":{"name":"遂宁"},"338":{"name":"雅安"},"339":{"name":"宜宾"},"340":{"name":"资阳"},"341":{"name":"自贡"},"342":{"name":"泸州"},"343":{"name":"天津"},"344":{"name":"拉萨"},"345":{"name":"阿里"},"346":{"name":"昌都"},"347":{"name":"林芝"},"348":{"name":"那曲"},"349":{"name":"日喀则"},"350":{"name":"山南"},"351":{"name":"乌鲁木齐"},"352":{"name":"阿克苏"},"353":{"name":"阿拉尔"},"354":{"name":"巴音郭楞"},"355":{"name":"博尔塔拉"},"356":{"name":"昌吉"},"357":{"name":"哈密"},"358":{"name":"和田"},"359":{"name":"喀什"},"360":{"name":"克拉玛依"},"361":{"name":"克孜勒苏"},"362":{"name":"石河子"},"363":{"name":"图木舒克"},"364":{"name":"吐鲁番"},"365":{"name":"五家渠"},"366":{"name":"伊犁"},"367":{"name":"昆明"},"368":{"name":"怒江"},"369":{"name":"普洱"},"370":{"name":"丽江"},"371":{"name":"保山"},"372":{"name":"楚雄"},"373":{"name":"大理"},"374":{"name":"德宏"},"375":{"name":"迪庆"},"376":{"name":"红河"},"377":{"name":"临沧"},"378":{"name":"曲靖"},"379":{"name":"文山"},"380":{"name":"西双版纳"},"381":{"name":"玉溪"},"382":{"name":"昭通"},"383":{"name":"杭州"},"384":{"name":"湖州"},"385":{"name":"嘉兴"},"386":{"name":"金华"},"387":{"name":"丽水"},"388":{"name":"宁波"},"389":{"name":"绍兴"},"390":{"name":"台州"},"391":{"name":"温州"},"392":{"name":"舟山"},"393":{"name":"衢州"},"394":{"name":"重庆"},"395":{"name":"香港"},"396":{"name":"澳门"},"397":{"name":"台湾"},"398":{"name":"迎江区"},"399":{"name":"大观区"},"400":{"name":"宜秀区"},"401":{"name":"桐城市"},"402":{"name":"怀宁县"},"403":{"name":"枞阳县"},"404":{"name":"潜山县"},"405":{"name":"太湖县"},"406":{"name":"宿松县"},"407":{"name":"望江县"},"408":{"name":"岳西县"},"409":{"name":"中市区"},"410":{"name":"东市区"},"411":{"name":"西市区"},"412":{"name":"郊区"},"413":{"name":"怀远县"},"414":{"name":"五河县"},"415":{"name":"固镇县"},"416":{"name":"居巢区"},"417":{"name":"庐江县"},"418":{"name":"无为县"},"419":{"name":"含山县"},"420":{"name":"和县"},"421":{"name":"贵池区"},"422":{"name":"东至县"},"423":{"name":"石台县"},"424":{"name":"青阳县"},"425":{"name":"琅琊区"},"426":{"name":"南谯区"},"427":{"name":"天长市"},"428":{"name":"明光市"},"429":{"name":"来安县"},"430":{"name":"全椒县"},"431":{"name":"定远县"},"432":{"name":"凤阳县"},"433":{"name":"蚌山区"},"434":{"name":"龙子湖区"},"435":{"name":"禹会区"},"436":{"name":"淮上区"},"437":{"name":"颍州区"},"438":{"name":"颍东区"},"439":{"name":"颍泉区"},"440":{"name":"界首市"},"441":{"name":"临泉县"},"442":{"name":"太和县"},"443":{"name":"阜南县"},"444":{"name":"颖上县"},"445":{"name":"相山区"},"446":{"name":"杜集区"},"447":{"name":"烈山区"},"448":{"name":"濉溪县"},"449":{"name":"田家庵区"},"450":{"name":"大通区"},"451":{"name":"谢家集区"},"452":{"name":"八公山区"},"453":{"name":"潘集区"},"454":{"name":"凤台县"},"455":{"name":"屯溪区"},"456":{"name":"黄山区"},"457":{"name":"徽州区"},"458":{"name":"歙县"},"459":{"name":"休宁县"},"460":{"name":"黟县"},"461":{"name":"祁门县"},"462":{"name":"金安区"},"463":{"name":"裕安区"},"464":{"name":"寿县"},"465":{"name":"霍邱县"},"466":{"name":"舒城县"},"467":{"name":"金寨县"},"468":{"name":"霍山县"},"469":{"name":"雨山区"},"470":{"name":"花山区"},"471":{"name":"金家庄区"},"472":{"name":"当涂县"},"473":{"name":"埇桥区"},"474":{"name":"砀山县"},"475":{"name":"萧县"},"476":{"name":"灵璧县"},"477":{"name":"泗县"},"478":{"name":"铜官山区"},"479":{"name":"狮子山区"},"480":{"name":"郊区"},"481":{"name":"铜陵县"},"482":{"name":"镜湖区"},"483":{"name":"弋江区"},"484":{"name":"鸠江区"},"485":{"name":"三山区"},"486":{"name":"芜湖县"},"487":{"name":"繁昌县"},"488":{"name":"南陵县"},"489":{"name":"宣州区"},"490":{"name":"宁国市"},"491":{"name":"郎溪县"},"492":{"name":"广德县"},"493":{"name":"泾县"},"494":{"name":"绩溪县"},"495":{"name":"旌德县"},"496":{"name":"涡阳县"},"497":{"name":"蒙城县"},"498":{"name":"利辛县"},"499":{"name":"谯城区"},"500":{"name":"东城区"},"501":{"name":"西城区"},"502":{"name":"海淀区"},"503":{"name":"朝阳区"},"504":{"name":"崇文区"},"505":{"name":"宣武区"},"506":{"name":"丰台区"},"507":{"name":"石景山区"},"508":{"name":"房山区"},"509":{"name":"门头沟区"},"510":{"name":"通州区"},"511":{"name":"顺义区"},"512":{"name":"昌平区"},"513":{"name":"怀柔区"},"514":{"name":"平谷区"},"515":{"name":"大兴区"},"516":{"name":"密云县"},"517":{"name":"延庆县"},"518":{"name":"鼓楼区"},"519":{"name":"台江区"},"520":{"name":"仓山区"},"521":{"name":"马尾区"},"522":{"name":"晋安区"},"523":{"name":"福清市"},"524":{"name":"长乐市"},"525":{"name":"闽侯县"},"526":{"name":"连江县"},"527":{"name":"罗源县"},"528":{"name":"闽清县"},"529":{"name":"永泰县"},"530":{"name":"平潭县"},"531":{"name":"新罗区"},"532":{"name":"漳平市"},"533":{"name":"长汀县"},"534":{"name":"永定县"},"535":{"name":"上杭县"},"536":{"name":"武平县"},"537":{"name":"连城县"},"538":{"name":"延平区"},"539":{"name":"邵武市"},"540":{"name":"武夷山市"},"541":{"name":"建瓯市"},"542":{"name":"建阳市"},"543":{"name":"顺昌县"},"544":{"name":"浦城县"},"545":{"name":"光泽县"},"546":{"name":"松溪县"},"547":{"name":"政和县"},"548":{"name":"蕉城区"},"549":{"name":"福安市"},"550":{"name":"福鼎市"},"551":{"name":"霞浦县"},"552":{"name":"古田县"},"553":{"name":"屏南县"},"554":{"name":"寿宁县"},"555":{"name":"周宁县"},"556":{"name":"柘荣县"},"557":{"name":"城厢区"},"558":{"name":"涵江区"},"559":{"name":"荔城区"},"560":{"name":"秀屿区"},"561":{"name":"仙游县"},"562":{"name":"鲤城区"},"563":{"name":"丰泽区"},"564":{"name":"洛江区"},"565":{"name":"清濛开发区"},"566":{"name":"泉港区"},"567":{"name":"石狮市"},"568":{"name":"晋江市"},"569":{"name":"南安市"},"570":{"name":"惠安县"},"571":{"name":"安溪县"},"572":{"name":"永春县"},"573":{"name":"德化县"},"574":{"name":"金门县"},"575":{"name":"梅列区"},"576":{"name":"三元区"},"577":{"name":"永安市"},"578":{"name":"明溪县"},"579":{"name":"清流县"},"580":{"name":"宁化县"},"581":{"name":"大田县"},"582":{"name":"尤溪县"},"583":{"name":"沙县"},"584":{"name":"将乐县"},"585":{"name":"泰宁县"},"586":{"name":"建宁县"},"587":{"name":"思明区"},"588":{"name":"海沧区"},"589":{"name":"湖里区"},"590":{"name":"集美区"},"591":{"name":"同安区"},"592":{"name":"翔安区"},"593":{"name":"芗城区"},"594":{"name":"龙文区"},"595":{"name":"龙海市"},"596":{"name":"云霄县"},"597":{"name":"漳浦县"},"598":{"name":"诏安县"},"599":{"name":"长泰县"},"600":{"name":"东山县"},"601":{"name":"南靖县"},"602":{"name":"平和县"},"603":{"name":"华安县"},"604":{"name":"皋兰县"},"605":{"name":"城关区"},"606":{"name":"七里河区"},"607":{"name":"西固区"},"608":{"name":"安宁区"},"609":{"name":"红古区"},"610":{"name":"永登县"},"611":{"name":"榆中县"},"612":{"name":"白银区"},"613":{"name":"平川区"},"614":{"name":"会宁县"},"615":{"name":"景泰县"},"616":{"name":"靖远县"},"617":{"name":"临洮县"},"618":{"name":"陇西县"},"619":{"name":"通渭县"},"620":{"name":"渭源县"},"621":{"name":"漳县"},"622":{"name":"岷县"},"623":{"name":"安定区"},"624":{"name":"安定区"},"625":{"name":"合作市"},"626":{"name":"临潭县"},"627":{"name":"卓尼县"},"628":{"name":"舟曲县"},"629":{"name":"迭部县"},"630":{"name":"玛曲县"},"631":{"name":"碌曲县"},"632":{"name":"夏河县"},"633":{"name":"嘉峪关市"},"634":{"name":"金川区"},"635":{"name":"永昌县"},"636":{"name":"肃州区"},"637":{"name":"玉门市"},"638":{"name":"敦煌市"},"639":{"name":"金塔县"},"640":{"name":"瓜州县"},"641":{"name":"肃北"},"642":{"name":"阿克塞"},"643":{"name":"临夏市"},"644":{"name":"临夏县"},"645":{"name":"康乐县"},"646":{"name":"永靖县"},"647":{"name":"广河县"},"648":{"name":"和政县"},"649":{"name":"东乡族自治县"},"650":{"name":"积石山"},"651":{"name":"成县"},"652":{"name":"徽县"},"653":{"name":"康县"},"654":{"name":"礼县"},"655":{"name":"两当县"},"656":{"name":"文县"},"657":{"name":"西和县"},"658":{"name":"宕昌县"},"659":{"name":"武都区"},"660":{"name":"崇信县"},"661":{"name":"华亭县"},"662":{"name":"静宁县"},"663":{"name":"灵台县"},"664":{"name":"崆峒区"},"665":{"name":"庄浪县"},"666":{"name":"泾川县"},"667":{"name":"合水县"},"668":{"name":"华池县"},"669":{"name":"环县"},"670":{"name":"宁县"},"671":{"name":"庆城县"},"672":{"name":"西峰区"},"673":{"name":"镇原县"},"674":{"name":"正宁县"},"675":{"name":"甘谷县"},"676":{"name":"秦安县"},"677":{"name":"清水县"},"678":{"name":"秦州区"},"679":{"name":"麦积区"},"680":{"name":"武山县"},"681":{"name":"张家川"},"682":{"name":"古浪县"},"683":{"name":"民勤县"},"684":{"name":"天祝"},"685":{"name":"凉州区"},"686":{"name":"高台县"},"687":{"name":"临泽县"},"688":{"name":"民乐县"},"689":{"name":"山丹县"},"690":{"name":"肃南"},"691":{"name":"甘州区"},"692":{"name":"从化市"},"693":{"name":"天河区"},"694":{"name":"东山区"},"695":{"name":"白云区"},"696":{"name":"海珠区"},"697":{"name":"荔湾区"},"698":{"name":"越秀区"},"699":{"name":"黄埔区"},"700":{"name":"番禺区"},"701":{"name":"花都区"},"702":{"name":"增城区"},"703":{"name":"从化区"},"704":{"name":"市郊"},"705":{"name":"福田区"},"706":{"name":"罗湖区"},"707":{"name":"南山区"},"708":{"name":"宝安区"},"709":{"name":"龙岗区"},"710":{"name":"盐田区"},"711":{"name":"湘桥区"},"712":{"name":"潮安县"},"713":{"name":"饶平县"},"714":{"name":"南城区"},"715":{"name":"东城区"},"716":{"name":"万江区"},"717":{"name":"莞城区"},"718":{"name":"石龙镇"},"719":{"name":"虎门镇"},"720":{"name":"麻涌镇"},"721":{"name":"道滘镇"},"722":{"name":"石碣镇"},"723":{"name":"沙田镇"},"724":{"name":"望牛墩镇"},"725":{"name":"洪梅镇"},"726":{"name":"茶山镇"},"727":{"name":"寮步镇"},"728":{"name":"大岭山镇"},"729":{"name":"大朗镇"},"730":{"name":"黄江镇"},"731":{"name":"樟木头"},"732":{"name":"凤岗镇"},"733":{"name":"塘厦镇"},"734":{"name":"谢岗镇"},"735":{"name":"厚街镇"},"736":{"name":"清溪镇"},"737":{"name":"常平镇"},"738":{"name":"桥头镇"},"739":{"name":"横沥镇"},"740":{"name":"东坑镇"},"741":{"name":"企石镇"},"742":{"name":"石排镇"},"743":{"name":"长安镇"},"744":{"name":"中堂镇"},"745":{"name":"高埗镇"},"746":{"name":"禅城区"},"747":{"name":"南海区"},"748":{"name":"顺德区"},"749":{"name":"三水区"},"750":{"name":"高明区"},"751":{"name":"东源县"},"752":{"name":"和平县"},"753":{"name":"源城区"},"754":{"name":"连平县"},"755":{"name":"龙川县"},"756":{"name":"紫金县"},"757":{"name":"惠阳区"},"758":{"name":"惠城区"},"759":{"name":"大亚湾"},"760":{"name":"博罗县"},"761":{"name":"惠东县"},"762":{"name":"龙门县"},"763":{"name":"江海区"},"764":{"name":"蓬江区"},"765":{"name":"新会区"},"766":{"name":"台山市"},"767":{"name":"开平市"},"768":{"name":"鹤山市"},"769":{"name":"恩平市"},"770":{"name":"榕城区"},"771":{"name":"普宁市"},"772":{"name":"揭东县"},"773":{"name":"揭西县"},"774":{"name":"惠来县"},"775":{"name":"茂南区"},"776":{"name":"茂港区"},"777":{"name":"高州市"},"778":{"name":"化州市"},"779":{"name":"信宜市"},"780":{"name":"电白县"},"781":{"name":"梅县"},"782":{"name":"梅江区"},"783":{"name":"兴宁市"},"784":{"name":"大埔县"},"785":{"name":"丰顺县"},"786":{"name":"五华县"},"787":{"name":"平远县"},"788":{"name":"蕉岭县"},"789":{"name":"清城区"},"790":{"name":"英德市"},"791":{"name":"连州市"},"792":{"name":"佛冈县"},"793":{"name":"阳山县"},"794":{"name":"清新县"},"795":{"name":"连山"},"796":{"name":"连南"},"797":{"name":"南澳县"},"798":{"name":"潮阳区"},"799":{"name":"澄海区"},"800":{"name":"龙湖区"},"801":{"name":"金平区"},"802":{"name":"濠江区"},"803":{"name":"潮南区"},"804":{"name":"城区"},"805":{"name":"陆丰市"},"806":{"name":"海丰县"},"807":{"name":"陆河县"},"808":{"name":"曲江县"},"809":{"name":"浈江区"},"810":{"name":"武江区"},"811":{"name":"曲江区"},"812":{"name":"乐昌市"},"813":{"name":"南雄市"},"814":{"name":"始兴县"},"815":{"name":"仁化县"},"816":{"name":"翁源县"},"817":{"name":"新丰县"},"818":{"name":"乳源"},"819":{"name":"江城区"},"820":{"name":"阳春市"},"821":{"name":"阳西县"},"822":{"name":"阳东县"},"823":{"name":"云城区"},"824":{"name":"罗定市"},"825":{"name":"新兴县"},"826":{"name":"郁南县"},"827":{"name":"云安县"},"828":{"name":"赤坎区"},"829":{"name":"霞山区"},"830":{"name":"坡头区"},"831":{"name":"麻章区"},"832":{"name":"廉江市"},"833":{"name":"雷州市"},"834":{"name":"吴川市"},"835":{"name":"遂溪县"},"836":{"name":"徐闻县"},"837":{"name":"肇庆市"},"838":{"name":"高要市"},"839":{"name":"四会市"},"840":{"name":"广宁县"},"841":{"name":"怀集县"},"842":{"name":"封开县"},"843":{"name":"德庆县"},"844":{"name":"石岐街道"},"845":{"name":"东区街道"},"846":{"name":"西区街道"},"847":{"name":"环城街道"},"848":{"name":"中山港街道"},"849":{"name":"五桂山街道"},"850":{"name":"香洲区"},"851":{"name":"斗门区"},"852":{"name":"金湾区"},"853":{"name":"邕宁区"},"854":{"name":"青秀区"},"855":{"name":"兴宁区"},"856":{"name":"良庆区"},"857":{"name":"西乡塘区"},"858":{"name":"江南区"},"859":{"name":"武鸣县"},"860":{"name":"隆安县"},"861":{"name":"马山县"},"862":{"name":"上林县"},"863":{"name":"宾阳县"},"864":{"name":"横县"},"865":{"name":"秀峰区"},"866":{"name":"叠彩区"},"867":{"name":"象山区"},"868":{"name":"七星区"},"869":{"name":"雁山区"},"870":{"name":"阳朔县"},"871":{"name":"临桂县"},"872":{"name":"灵川县"},"873":{"name":"全州县"},"874":{"name":"平乐县"},"875":{"name":"兴安县"},"876":{"name":"灌阳县"},"877":{"name":"荔浦县"},"878":{"name":"资源县"},"879":{"name":"永福县"},"880":{"name":"龙胜"},"881":{"name":"恭城"},"882":{"name":"右江区"},"883":{"name":"凌云县"},"884":{"name":"平果县"},"885":{"name":"西林县"},"886":{"name":"乐业县"},"887":{"name":"德保县"},"888":{"name":"田林县"},"889":{"name":"田阳县"},"890":{"name":"靖西县"},"891":{"name":"田东县"},"892":{"name":"那坡县"},"893":{"name":"隆林"},"894":{"name":"海城区"},"895":{"name":"银海区"},"896":{"name":"铁山港区"},"897":{"name":"合浦县"},"898":{"name":"江州区"},"899":{"name":"凭祥市"},"900":{"name":"宁明县"},"901":{"name":"扶绥县"},"902":{"name":"龙州县"},"903":{"name":"大新县"},"904":{"name":"天等县"},"905":{"name":"港口区"},"906":{"name":"防城区"},"907":{"name":"东兴市"},"908":{"name":"上思县"},"909":{"name":"港北区"},"910":{"name":"港南区"},"911":{"name":"覃塘区"},"912":{"name":"桂平市"},"913":{"name":"平南县"},"914":{"name":"金城江区"},"915":{"name":"宜州市"},"916":{"name":"天峨县"},"917":{"name":"凤山县"},"918":{"name":"南丹县"},"919":{"name":"东兰县"},"920":{"name":"都安"},"921":{"name":"罗城"},"922":{"name":"巴马"},"923":{"name":"环江"},"924":{"name":"大化"},"925":{"name":"八步区"},"926":{"name":"钟山县"},"927":{"name":"昭平县"},"928":{"name":"富川"},"929":{"name":"兴宾区"},"930":{"name":"合山市"},"931":{"name":"象州县"},"932":{"name":"武宣县"},"933":{"name":"忻城县"},"934":{"name":"金秀"},"935":{"name":"城中区"},"936":{"name":"鱼峰区"},"937":{"name":"柳北区"},"938":{"name":"柳南区"},"939":{"name":"柳江县"},"940":{"name":"柳城县"},"941":{"name":"鹿寨县"},"942":{"name":"融安县"},"943":{"name":"融水"},"944":{"name":"三江"},"945":{"name":"钦南区"},"946":{"name":"钦北区"},"947":{"name":"灵山县"},"948":{"name":"浦北县"},"949":{"name":"万秀区"},"950":{"name":"蝶山区"},"951":{"name":"长洲区"},"952":{"name":"岑溪市"},"953":{"name":"苍梧县"},"954":{"name":"藤县"},"955":{"name":"蒙山县"},"956":{"name":"玉州区"},"957":{"name":"北流市"},"958":{"name":"容县"},"959":{"name":"陆川县"},"960":{"name":"博白县"},"961":{"name":"兴业县"},"962":{"name":"南明区"},"963":{"name":"云岩区"},"964":{"name":"花溪区"},"965":{"name":"乌当区"},"966":{"name":"白云区"},"967":{"name":"小河区"},"968":{"name":"金阳新区"},"969":{"name":"新天园区"},"970":{"name":"清镇市"},"971":{"name":"开阳县"},"972":{"name":"修文县"},"973":{"name":"息烽县"},"974":{"name":"西秀区"},"975":{"name":"关岭"},"976":{"name":"镇宁"},"977":{"name":"紫云"},"978":{"name":"平坝县"},"979":{"name":"普定县"},"980":{"name":"毕节市"},"981":{"name":"大方县"},"982":{"name":"黔西县"},"983":{"name":"金沙县"},"984":{"name":"织金县"},"985":{"name":"纳雍县"},"986":{"name":"赫章县"},"987":{"name":"威宁"},"988":{"name":"钟山区"},"989":{"name":"六枝特区"},"990":{"name":"水城县"},"991":{"name":"盘县"},"992":{"name":"凯里市"},"993":{"name":"黄平县"},"994":{"name":"施秉县"},"995":{"name":"三穗县"},"996":{"name":"镇远县"},"997":{"name":"岑巩县"},"998":{"name":"天柱县"},"999":{"name":"锦屏县"},"1000":{"name":"剑河县"},"1001":{"name":"台江县"},"1002":{"name":"黎平县"},"1003":{"name":"榕江县"},"1004":{"name":"从江县"},"1005":{"name":"雷山县"},"1006":{"name":"麻江县"},"1007":{"name":"丹寨县"},"1008":{"name":"都匀市"},"1009":{"name":"福泉市"},"1010":{"name":"荔波县"},"1011":{"name":"贵定县"},"1012":{"name":"瓮安县"},"1013":{"name":"独山县"},"1014":{"name":"平塘县"},"1015":{"name":"罗甸县"},"1016":{"name":"长顺县"},"1017":{"name":"龙里县"},"1018":{"name":"惠水县"},"1019":{"name":"三都"},"1020":{"name":"兴义市"},"1021":{"name":"兴仁县"},"1022":{"name":"普安县"},"1023":{"name":"晴隆县"},"1024":{"name":"贞丰县"},"1025":{"name":"望谟县"},"1026":{"name":"册亨县"},"1027":{"name":"安龙县"},"1028":{"name":"铜仁市"},"1029":{"name":"江口县"},"1030":{"name":"石阡县"},"1031":{"name":"思南县"},"1032":{"name":"德江县"},"1033":{"name":"玉屏"},"1034":{"name":"印江"},"1035":{"name":"沿河"},"1036":{"name":"松桃"},"1037":{"name":"万山特区"},"1038":{"name":"红花岗区"},"1039":{"name":"务川县"},"1040":{"name":"道真县"},"1041":{"name":"汇川区"},"1042":{"name":"赤水市"},"1043":{"name":"仁怀市"},"1044":{"name":"遵义县"},"1045":{"name":"桐梓县"},"1046":{"name":"绥阳县"},"1047":{"name":"正安县"},"1048":{"name":"凤冈县"},"1049":{"name":"湄潭县"},"1050":{"name":"余庆县"},"1051":{"name":"习水县"},"1052":{"name":"道真"},"1053":{"name":"务川"},"1054":{"name":"秀英区"},"1055":{"name":"龙华区"},"1056":{"name":"琼山区"},"1057":{"name":"美兰区"},"1058":{"name":"市区"},"1059":{"name":"洋浦开发区"},"1060":{"name":"那大镇"},"1061":{"name":"王五镇"},"1062":{"name":"雅星镇"},"1063":{"name":"大成镇"},"1064":{"name":"中和镇"},"1065":{"name":"峨蔓镇"},"1066":{"name":"南丰镇"},"1067":{"name":"白马井镇"},"1068":{"name":"兰洋镇"},"1069":{"name":"和庆镇"},"1070":{"name":"海头镇"},"1071":{"name":"排浦镇"},"1072":{"name":"东成镇"},"1073":{"name":"光村镇"},"1074":{"name":"木棠镇"},"1075":{"name":"新州镇"},"1076":{"name":"三都镇"},"1077":{"name":"其他"},"1078":{"name":"长安区"},"1079":{"name":"桥东区"},"1080":{"name":"桥西区"},"1081":{"name":"新华区"},"1082":{"name":"裕华区"},"1083":{"name":"井陉矿区"},"1084":{"name":"高新区"},"1085":{"name":"辛集市"},"1086":{"name":"藁城市"},"1087":{"name":"晋州市"},"1088":{"name":"新乐市"},"1089":{"name":"鹿泉市"},"1090":{"name":"井陉县"},"1091":{"name":"正定县"},"1092":{"name":"栾城县"},"1093":{"name":"行唐县"},"1094":{"name":"灵寿县"},"1095":{"name":"高邑县"},"1096":{"name":"深泽县"},"1097":{"name":"赞皇县"},"1098":{"name":"无极县"},"1099":{"name":"平山县"},"1100":{"name":"元氏县"},"1101":{"name":"赵县"},"1102":{"name":"新市区"},"1103":{"name":"南市区"},"1104":{"name":"北市区"},"1105":{"name":"涿州市"},"1106":{"name":"定州市"},"1107":{"name":"安国市"},"1108":{"name":"高碑店市"},"1109":{"name":"满城县"},"1110":{"name":"清苑县"},"1111":{"name":"涞水县"},"1112":{"name":"阜平县"},"1113":{"name":"徐水县"},"1114":{"name":"定兴县"},"1115":{"name":"唐县"},"1116":{"name":"高阳县"},"1117":{"name":"容城县"},"1118":{"name":"涞源县"},"1119":{"name":"望都县"},"1120":{"name":"安新县"},"1121":{"name":"易县"},"1122":{"name":"曲阳县"},"1123":{"name":"蠡县"},"1124":{"name":"顺平县"},"1125":{"name":"博野县"},"1126":{"name":"雄县"},"1127":{"name":"运河区"},"1128":{"name":"新华区"},"1129":{"name":"泊头市"},"1130":{"name":"任丘市"},"1131":{"name":"黄骅市"},"1132":{"name":"河间市"},"1133":{"name":"沧县"},"1134":{"name":"青县"},"1135":{"name":"东光县"},"1136":{"name":"海兴县"},"1137":{"name":"盐山县"},"1138":{"name":"肃宁县"},"1139":{"name":"南皮县"},"1140":{"name":"吴桥县"},"1141":{"name":"献县"},"1142":{"name":"孟村"},"1143":{"name":"双桥区"},"1144":{"name":"双滦区"},"1145":{"name":"鹰手营子矿区"},"1146":{"name":"承德县"},"1147":{"name":"兴隆县"},"1148":{"name":"平泉县"},"1149":{"name":"滦平县"},"1150":{"name":"隆化县"},"1151":{"name":"丰宁"},"1152":{"name":"宽城"},"1153":{"name":"围场"},"1154":{"name":"从台区"},"1155":{"name":"复兴区"},"1156":{"name":"邯山区"},"1157":{"name":"峰峰矿区"},"1158":{"name":"武安市"},"1159":{"name":"邯郸县"},"1160":{"name":"临漳县"},"1161":{"name":"成安县"},"1162":{"name":"大名县"},"1163":{"name":"涉县"},"1164":{"name":"磁县"},"1165":{"name":"肥乡县"},"1166":{"name":"永年县"},"1167":{"name":"邱县"},"1168":{"name":"鸡泽县"},"1169":{"name":"广平县"},"1170":{"name":"馆陶县"},"1171":{"name":"魏县"},"1172":{"name":"曲周县"},"1173":{"name":"桃城区"},"1174":{"name":"冀州市"},"1175":{"name":"深州市"},"1176":{"name":"枣强县"},"1177":{"name":"武邑县"},"1178":{"name":"武强县"},"1179":{"name":"饶阳县"},"1180":{"name":"安平县"},"1181":{"name":"故城县"},"1182":{"name":"景县"},"1183":{"name":"阜城县"},"1184":{"name":"安次区"},"1185":{"name":"广阳区"},"1186":{"name":"霸州市"},"1187":{"name":"三河市"},"1188":{"name":"固安县"},"1189":{"name":"永清县"},"1190":{"name":"香河县"},"1191":{"name":"大城县"},"1192":{"name":"文安县"},"1193":{"name":"大厂"},"1194":{"name":"海港区"},"1195":{"name":"山海关区"},"1196":{"name":"北戴河区"},"1197":{"name":"昌黎县"},"1198":{"name":"抚宁县"},"1199":{"name":"卢龙县"},"1200":{"name":"青龙"},"1201":{"name":"路北区"},"1202":{"name":"路南区"},"1203":{"name":"古冶区"},"1204":{"name":"开平区"},"1205":{"name":"丰南区"},"1206":{"name":"丰润区"},"1207":{"name":"遵化市"},"1208":{"name":"迁安市"},"1209":{"name":"滦县"},"1210":{"name":"滦南县"},"1211":{"name":"乐亭县"},"1212":{"name":"迁西县"},"1213":{"name":"玉田县"},"1214":{"name":"唐海县"},"1215":{"name":"桥东区"},"1216":{"name":"桥西区"},"1217":{"name":"南宫市"},"1218":{"name":"沙河市"},"1219":{"name":"邢台县"},"1220":{"name":"临城县"},"1221":{"name":"内丘县"},"1222":{"name":"柏乡县"},"1223":{"name":"隆尧县"},"1224":{"name":"任县"},"1225":{"name":"南和县"},"1226":{"name":"宁晋县"},"1227":{"name":"巨鹿县"},"1228":{"name":"新河县"},"1229":{"name":"广宗县"},"1230":{"name":"平乡县"},"1231":{"name":"威县"},"1232":{"name":"清河县"},"1233":{"name":"临西县"},"1234":{"name":"桥西区"},"1235":{"name":"桥东区"},"1236":{"name":"宣化区"},"1237":{"name":"下花园区"},"1238":{"name":"宣化县"},"1239":{"name":"张北县"},"1240":{"name":"康保县"},"1241":{"name":"沽源县"},"1242":{"name":"尚义县"},"1243":{"name":"蔚县"},"1244":{"name":"阳原县"},"1245":{"name":"怀安县"},"1246":{"name":"万全县"},"1247":{"name":"怀来县"},"1248":{"name":"涿鹿县"},"1249":{"name":"赤城县"},"1250":{"name":"崇礼县"},"1251":{"name":"金水区"},"1252":{"name":"邙山区"},"1253":{"name":"二七区"},"1254":{"name":"管城区"},"1255":{"name":"中原区"},"1256":{"name":"上街区"},"1257":{"name":"惠济区"},"1258":{"name":"郑东新区"},"1259":{"name":"经济技术开发区"},"1260":{"name":"高新开发区"},"1261":{"name":"出口加工区"},"1262":{"name":"巩义市"},"1263":{"name":"荥阳市"},"1264":{"name":"新密市"},"1265":{"name":"新郑市"},"1266":{"name":"登封市"},"1267":{"name":"中牟县"},"1268":{"name":"西工区"},"1269":{"name":"老城区"},"1270":{"name":"涧西区"},"1271":{"name":"瀍河回族区"},"1272":{"name":"洛龙区"},"1273":{"name":"吉利区"},"1274":{"name":"偃师市"},"1275":{"name":"孟津县"},"1276":{"name":"新安县"},"1277":{"name":"栾川县"},"1278":{"name":"嵩县"},"1279":{"name":"汝阳县"},"1280":{"name":"宜阳县"},"1281":{"name":"洛宁县"},"1282":{"name":"伊川县"},"1283":{"name":"鼓楼区"},"1284":{"name":"龙亭区"},"1285":{"name":"顺河回族区"},"1286":{"name":"金明区"},"1287":{"name":"禹王台区"},"1288":{"name":"杞县"},"1289":{"name":"通许县"},"1290":{"name":"尉氏县"},"1291":{"name":"开封县"},"1292":{"name":"兰考县"},"1293":{"name":"北关区"},"1294":{"name":"文峰区"},"1295":{"name":"殷都区"},"1296":{"name":"龙安区"},"1297":{"name":"林州市"},"1298":{"name":"安阳县"},"1299":{"name":"汤阴县"},"1300":{"name":"滑县"},"1301":{"name":"内黄县"},"1302":{"name":"淇滨区"},"1303":{"name":"山城区"},"1304":{"name":"鹤山区"},"1305":{"name":"浚县"},"1306":{"name":"淇县"},"1307":{"name":"济源市"},"1308":{"name":"解放区"},"1309":{"name":"中站区"},"1310":{"name":"马村区"},"1311":{"name":"山阳区"},"1312":{"name":"沁阳市"},"1313":{"name":"孟州市"},"1314":{"name":"修武县"},"1315":{"name":"博爱县"},"1316":{"name":"武陟县"},"1317":{"name":"温县"},"1318":{"name":"卧龙区"},"1319":{"name":"宛城区"},"1320":{"name":"邓州市"},"1321":{"name":"南召县"},"1322":{"name":"方城县"},"1323":{"name":"西峡县"},"1324":{"name":"镇平县"},"1325":{"name":"内乡县"},"1326":{"name":"淅川县"},"1327":{"name":"社旗县"},"1328":{"name":"唐河县"},"1329":{"name":"新野县"},"1330":{"name":"桐柏县"},"1331":{"name":"新华区"},"1332":{"name":"卫东区"},"1333":{"name":"湛河区"},"1334":{"name":"石龙区"},"1335":{"name":"舞钢市"},"1336":{"name":"汝州市"},"1337":{"name":"宝丰县"},"1338":{"name":"叶县"},"1339":{"name":"鲁山县"},"1340":{"name":"郏县"},"1341":{"name":"湖滨区"},"1342":{"name":"义马市"},"1343":{"name":"灵宝市"},"1344":{"name":"渑池县"},"1345":{"name":"陕县"},"1346":{"name":"卢氏县"},"1347":{"name":"梁园区"},"1348":{"name":"睢阳区"},"1349":{"name":"永城市"},"1350":{"name":"民权县"},"1351":{"name":"睢县"},"1352":{"name":"宁陵县"},"1353":{"name":"虞城县"},"1354":{"name":"柘城县"},"1355":{"name":"夏邑县"},"1356":{"name":"卫滨区"},"1357":{"name":"红旗区"},"1358":{"name":"凤泉区"},"1359":{"name":"牧野区"},"1360":{"name":"卫辉市"},"1361":{"name":"辉县市"},"1362":{"name":"新乡县"},"1363":{"name":"获嘉县"},"1364":{"name":"原阳县"},"1365":{"name":"延津县"},"1366":{"name":"封丘县"},"1367":{"name":"长垣县"},"1368":{"name":"浉河区"},"1369":{"name":"平桥区"},"1370":{"name":"罗山县"},"1371":{"name":"光山县"},"1372":{"name":"新县"},"1373":{"name":"商城县"},"1374":{"name":"固始县"},"1375":{"name":"潢川县"},"1376":{"name":"淮滨县"},"1377":{"name":"息县"},"1378":{"name":"魏都区"},"1379":{"name":"禹州市"},"1380":{"name":"长葛市"},"1381":{"name":"许昌县"},"1382":{"name":"鄢陵县"},"1383":{"name":"襄城县"},"1384":{"name":"川汇区"},"1385":{"name":"项城市"},"1386":{"name":"扶沟县"},"1387":{"name":"西华县"},"1388":{"name":"商水县"},"1389":{"name":"沈丘县"},"1390":{"name":"郸城县"},"1391":{"name":"淮阳县"},"1392":{"name":"太康县"},"1393":{"name":"鹿邑县"},"1394":{"name":"驿城区"},"1395":{"name":"西平县"},"1396":{"name":"上蔡县"},"1397":{"name":"平舆县"},"1398":{"name":"正阳县"},"1399":{"name":"确山县"},"1400":{"name":"泌阳县"},"1401":{"name":"汝南县"},"1402":{"name":"遂平县"},"1403":{"name":"新蔡县"},"1404":{"name":"郾城区"},"1405":{"name":"源汇区"},"1406":{"name":"召陵区"},"1407":{"name":"舞阳县"},"1408":{"name":"临颍县"},"1409":{"name":"华龙区"},"1410":{"name":"清丰县"},"1411":{"name":"南乐县"},"1412":{"name":"范县"},"1413":{"name":"台前县"},"1414":{"name":"濮阳县"},"1415":{"name":"道里区"},"1416":{"name":"南岗区"},"1417":{"name":"动力区"},"1418":{"name":"平房区"},"1419":{"name":"香坊区"},"1420":{"name":"太平区"},"1421":{"name":"道外区"},"1422":{"name":"阿城区"},"1423":{"name":"呼兰区"},"1424":{"name":"松北区"},"1425":{"name":"尚志市"},"1426":{"name":"双城市"},"1427":{"name":"五常市"},"1428":{"name":"方正县"},"1429":{"name":"宾县"},"1430":{"name":"依兰县"},"1431":{"name":"巴彦县"},"1432":{"name":"通河县"},"1433":{"name":"木兰县"},"1434":{"name":"延寿县"},"1435":{"name":"萨尔图区"},"1436":{"name":"红岗区"},"1437":{"name":"龙凤区"},"1438":{"name":"让胡路区"},"1439":{"name":"大同区"},"1440":{"name":"肇州县"},"1441":{"name":"肇源县"},"1442":{"name":"林甸县"},"1443":{"name":"杜尔伯特"},"1444":{"name":"呼玛县"},"1445":{"name":"漠河县"},"1446":{"name":"塔河县"},"1447":{"name":"兴山区"},"1448":{"name":"工农区"},"1449":{"name":"南山区"},"1450":{"name":"兴安区"},"1451":{"name":"向阳区"},"1452":{"name":"东山区"},"1453":{"name":"萝北县"},"1454":{"name":"绥滨县"},"1455":{"name":"爱辉区"},"1456":{"name":"五大连池市"},"1457":{"name":"北安市"},"1458":{"name":"嫩江县"},"1459":{"name":"逊克县"},"1460":{"name":"孙吴县"},"1461":{"name":"鸡冠区"},"1462":{"name":"恒山区"},"1463":{"name":"城子河区"},"1464":{"name":"滴道区"},"1465":{"name":"梨树区"},"1466":{"name":"虎林市"},"1467":{"name":"密山市"},"1468":{"name":"鸡东县"},"1469":{"name":"前进区"},"1470":{"name":"郊区"},"1471":{"name":"向阳区"},"1472":{"name":"东风区"},"1473":{"name":"同江市"},"1474":{"name":"富锦市"},"1475":{"name":"桦南县"},"1476":{"name":"桦川县"},"1477":{"name":"汤原县"},"1478":{"name":"抚远县"},"1479":{"name":"爱民区"},"1480":{"name":"东安区"},"1481":{"name":"阳明区"},"1482":{"name":"西安区"},"1483":{"name":"绥芬河市"},"1484":{"name":"海林市"},"1485":{"name":"宁安市"},"1486":{"name":"穆棱市"},"1487":{"name":"东宁县"},"1488":{"name":"林口县"},"1489":{"name":"桃山区"},"1490":{"name":"新兴区"},"1491":{"name":"茄子河区"},"1492":{"name":"勃利县"},"1493":{"name":"龙沙区"},"1494":{"name":"昂昂溪区"},"1495":{"name":"铁峰区"},"1496":{"name":"建华区"},"1497":{"name":"富拉尔基区"},"1498":{"name":"碾子山区"},"1499":{"name":"梅里斯达斡尔区"},"1500":{"name":"讷河市"},"1501":{"name":"龙江县"},"1502":{"name":"依安县"},"1503":{"name":"泰来县"},"1504":{"name":"甘南县"},"1505":{"name":"富裕县"},"1506":{"name":"克山县"},"1507":{"name":"克东县"},"1508":{"name":"拜泉县"},"1509":{"name":"尖山区"},"1510":{"name":"岭东区"},"1511":{"name":"四方台区"},"1512":{"name":"宝山区"},"1513":{"name":"集贤县"},"1514":{"name":"友谊县"},"1515":{"name":"宝清县"},"1516":{"name":"饶河县"},"1517":{"name":"北林区"},"1518":{"name":"安达市"},"1519":{"name":"肇东市"},"1520":{"name":"海伦市"},"1521":{"name":"望奎县"},"1522":{"name":"兰西县"},"1523":{"name":"青冈县"},"1524":{"name":"庆安县"},"1525":{"name":"明水县"},"1526":{"name":"绥棱县"},"1527":{"name":"伊春区"},"1528":{"name":"带岭区"},"1529":{"name":"南岔区"},"1530":{"name":"金山屯区"},"1531":{"name":"西林区"},"1532":{"name":"美溪区"},"1533":{"name":"乌马河区"},"1534":{"name":"翠峦区"},"1535":{"name":"友好区"},"1536":{"name":"上甘岭区"},"1537":{"name":"五营区"},"1538":{"name":"红星区"},"1539":{"name":"新青区"},"1540":{"name":"汤旺河区"},"1541":{"name":"乌伊岭区"},"1542":{"name":"铁力市"},"1543":{"name":"嘉荫县"},"1544":{"name":"江岸区"},"1545":{"name":"武昌区"},"1546":{"name":"江汉区"},"1547":{"name":"硚口区"},"1548":{"name":"汉阳区"},"1549":{"name":"青山区"},"1550":{"name":"洪山区"},"1551":{"name":"东西湖区"},"1552":{"name":"汉南区"},"1553":{"name":"蔡甸区"},"1554":{"name":"江夏区"},"1555":{"name":"黄陂区"},"1556":{"name":"新洲区"},"1557":{"name":"经济开发区"},"1558":{"name":"仙桃市"},"1559":{"name":"鄂城区"},"1560":{"name":"华容区"},"1561":{"name":"梁子湖区"},"1562":{"name":"黄州区"},"1563":{"name":"麻城市"},"1564":{"name":"武穴市"},"1565":{"name":"团风县"},"1566":{"name":"红安县"},"1567":{"name":"罗田县"},"1568":{"name":"英山县"},"1569":{"name":"浠水县"},"1570":{"name":"蕲春县"},"1571":{"name":"黄梅县"},"1572":{"name":"黄石港区"},"1573":{"name":"西塞山区"},"1574":{"name":"下陆区"},"1575":{"name":"铁山区"},"1576":{"name":"大冶市"},"1577":{"name":"阳新县"},"1578":{"name":"东宝区"},"1579":{"name":"掇刀区"},"1580":{"name":"钟祥市"},"1581":{"name":"京山县"},"1582":{"name":"沙洋县"},"1583":{"name":"沙市区"},"1584":{"name":"荆州区"},"1585":{"name":"石首市"},"1586":{"name":"洪湖市"},"1587":{"name":"松滋市"},"1588":{"name":"公安县"},"1589":{"name":"监利县"},"1590":{"name":"江陵县"},"1591":{"name":"潜江市"},"1592":{"name":"神农架林区"},"1593":{"name":"张湾区"},"1594":{"name":"茅箭区"},"1595":{"name":"丹江口市"},"1596":{"name":"郧县"},"1597":{"name":"郧西县"},"1598":{"name":"竹山县"},"1599":{"name":"竹溪县"},"1600":{"name":"房县"},"1601":{"name":"曾都区"},"1602":{"name":"广水市"},"1603":{"name":"天门市"},"1604":{"name":"咸安区"},"1605":{"name":"赤壁市"},"1606":{"name":"嘉鱼县"},"1607":{"name":"通城县"},"1608":{"name":"崇阳县"},"1609":{"name":"通山县"},"1610":{"name":"襄城区"},"1611":{"name":"樊城区"},"1612":{"name":"襄阳区"},"1613":{"name":"老河口市"},"1614":{"name":"枣阳市"},"1615":{"name":"宜城市"},"1616":{"name":"南漳县"},"1617":{"name":"谷城县"},"1618":{"name":"保康县"},"1619":{"name":"孝南区"},"1620":{"name":"应城市"},"1621":{"name":"安陆市"},"1622":{"name":"汉川市"},"1623":{"name":"孝昌县"},"1624":{"name":"大悟县"},"1625":{"name":"云梦县"},"1626":{"name":"长阳"},"1627":{"name":"五峰"},"1628":{"name":"西陵区"},"1629":{"name":"伍家岗区"},"1630":{"name":"点军区"},"1631":{"name":"猇亭区"},"1632":{"name":"夷陵区"},"1633":{"name":"宜都市"},"1634":{"name":"当阳市"},"1635":{"name":"枝江市"},"1636":{"name":"远安县"},"1637":{"name":"兴山县"},"1638":{"name":"秭归县"},"1639":{"name":"恩施市"},"1640":{"name":"利川市"},"1641":{"name":"建始县"},"1642":{"name":"巴东县"},"1643":{"name":"宣恩县"},"1644":{"name":"咸丰县"},"1645":{"name":"来凤县"},"1646":{"name":"鹤峰县"},"1647":{"name":"岳麓区"},"1648":{"name":"芙蓉区"},"1649":{"name":"天心区"},"1650":{"name":"开福区"},"1651":{"name":"雨花区"},"1652":{"name":"开发区"},"1653":{"name":"浏阳市"},"1654":{"name":"长沙县"},"1655":{"name":"望城县"},"1656":{"name":"宁乡县"},"1657":{"name":"永定区"},"1658":{"name":"武陵源区"},"1659":{"name":"慈利县"},"1660":{"name":"桑植县"},"1661":{"name":"武陵区"},"1662":{"name":"鼎城区"},"1663":{"name":"津市市"},"1664":{"name":"安乡县"},"1665":{"name":"汉寿县"},"1666":{"name":"澧县"},"1667":{"name":"临澧县"},"1668":{"name":"桃源县"},"1669":{"name":"石门县"},"1670":{"name":"北湖区"},"1671":{"name":"苏仙区"},"1672":{"name":"资兴市"},"1673":{"name":"桂阳县"},"1674":{"name":"宜章县"},"1675":{"name":"永兴县"},"1676":{"name":"嘉禾县"},"1677":{"name":"临武县"},"1678":{"name":"汝城县"},"1679":{"name":"桂东县"},"1680":{"name":"安仁县"},"1681":{"name":"雁峰区"},"1682":{"name":"珠晖区"},"1683":{"name":"石鼓区"},"1684":{"name":"蒸湘区"},"1685":{"name":"南岳区"},"1686":{"name":"耒阳市"},"1687":{"name":"常宁市"},"1688":{"name":"衡阳县"},"1689":{"name":"衡南县"},"1690":{"name":"衡山县"},"1691":{"name":"衡东县"},"1692":{"name":"祁东县"},"1693":{"name":"鹤城区"},"1694":{"name":"靖州"},"1695":{"name":"麻阳"},"1696":{"name":"通道"},"1697":{"name":"新晃"},"1698":{"name":"芷江"},"1699":{"name":"沅陵县"},"1700":{"name":"辰溪县"},"1701":{"name":"溆浦县"},"1702":{"name":"中方县"},"1703":{"name":"会同县"},"1704":{"name":"洪江市"},"1705":{"name":"娄星区"},"1706":{"name":"冷水江市"},"1707":{"name":"涟源市"},"1708":{"name":"双峰县"},"1709":{"name":"新化县"},"1710":{"name":"城步"},"1711":{"name":"双清区"},"1712":{"name":"大祥区"},"1713":{"name":"北塔区"},"1714":{"name":"武冈市"},"1715":{"name":"邵东县"},"1716":{"name":"新邵县"},"1717":{"name":"邵阳县"},"1718":{"name":"隆回县"},"1719":{"name":"洞口县"},"1720":{"name":"绥宁县"},"1721":{"name":"新宁县"},"1722":{"name":"岳塘区"},"1723":{"name":"雨湖区"},"1724":{"name":"湘乡市"},"1725":{"name":"韶山市"},"1726":{"name":"湘潭县"},"1727":{"name":"吉首市"},"1728":{"name":"泸溪县"},"1729":{"name":"凤凰县"},"1730":{"name":"花垣县"},"1731":{"name":"保靖县"},"1732":{"name":"古丈县"},"1733":{"name":"永顺县"},"1734":{"name":"龙山县"},"1735":{"name":"赫山区"},"1736":{"name":"资阳区"},"1737":{"name":"沅江市"},"1738":{"name":"南县"},"1739":{"name":"桃江县"},"1740":{"name":"安化县"},"1741":{"name":"江华"},"1742":{"name":"冷水滩区"},"1743":{"name":"零陵区"},"1744":{"name":"祁阳县"},"1745":{"name":"东安县"},"1746":{"name":"双牌县"},"1747":{"name":"道县"},"1748":{"name":"江永县"},"1749":{"name":"宁远县"},"1750":{"name":"蓝山县"},"1751":{"name":"新田县"},"1752":{"name":"岳阳楼区"},"1753":{"name":"君山区"},"1754":{"name":"云溪区"},"1755":{"name":"汨罗市"},"1756":{"name":"临湘市"},"1757":{"name":"岳阳县"},"1758":{"name":"华容县"},"1759":{"name":"湘阴县"},"1760":{"name":"平江县"},"1761":{"name":"天元区"},"1762":{"name":"荷塘区"},"1763":{"name":"芦淞区"},"1764":{"name":"石峰区"},"1765":{"name":"醴陵市"},"1766":{"name":"株洲县"},"1767":{"name":"攸县"},"1768":{"name":"茶陵县"},"1769":{"name":"炎陵县"},"1770":{"name":"朝阳区"},"1771":{"name":"宽城区"},"1772":{"name":"二道区"},"1773":{"name":"南关区"},"1774":{"name":"绿园区"},"1775":{"name":"双阳区"},"1776":{"name":"净月潭开发区"},"1777":{"name":"高新技术开发区"},"1778":{"name":"经济技术开发区"},"1779":{"name":"汽车产业开发区"},"1780":{"name":"德惠市"},"1781":{"name":"九台市"},"1782":{"name":"榆树市"},"1783":{"name":"农安县"},"1784":{"name":"船营区"},"1785":{"name":"昌邑区"},"1786":{"name":"龙潭区"},"1787":{"name":"丰满区"},"1788":{"name":"蛟河市"},"1789":{"name":"桦甸市"},"1790":{"name":"舒兰市"},"1791":{"name":"磐石市"},"1792":{"name":"永吉县"},"1793":{"name":"洮北区"},"1794":{"name":"洮南市"},"1795":{"name":"大安市"},"1796":{"name":"镇赉县"},"1797":{"name":"通榆县"},"1798":{"name":"江源区"},"1799":{"name":"八道江区"},"1800":{"name":"长白"},"1801":{"name":"临江市"},"1802":{"name":"抚松县"},"1803":{"name":"靖宇县"},"1804":{"name":"龙山区"},"1805":{"name":"西安区"},"1806":{"name":"东丰县"},"1807":{"name":"东辽县"},"1808":{"name":"铁西区"},"1809":{"name":"铁东区"},"1810":{"name":"伊通"},"1811":{"name":"公主岭市"},"1812":{"name":"双辽市"},"1813":{"name":"梨树县"},"1814":{"name":"前郭尔罗斯"},"1815":{"name":"宁江区"},"1816":{"name":"长岭县"},"1817":{"name":"乾安县"},"1818":{"name":"扶余县"},"1819":{"name":"东昌区"},"1820":{"name":"二道江区"},"1821":{"name":"梅河口市"},"1822":{"name":"集安市"},"1823":{"name":"通化县"},"1824":{"name":"辉南县"},"1825":{"name":"柳河县"},"1826":{"name":"延吉市"},"1827":{"name":"图们市"},"1828":{"name":"敦化市"},"1829":{"name":"珲春市"},"1830":{"name":"龙井市"},"1831":{"name":"和龙市"},"1832":{"name":"安图县"},"1833":{"name":"汪清县"},"1834":{"name":"玄武区"},"1835":{"name":"鼓楼区"},"1836":{"name":"白下区"},"1837":{"name":"建邺区"},"1838":{"name":"秦淮区"},"1839":{"name":"雨花台区"},"1840":{"name":"下关区"},"1841":{"name":"栖霞区"},"1842":{"name":"浦口区"},"1843":{"name":"江宁区"},"1844":{"name":"六合区"},"1845":{"name":"溧水县"},"1846":{"name":"高淳县"},"1847":{"name":"沧浪区"},"1848":{"name":"金阊区"},"1849":{"name":"平江区"},"1850":{"name":"虎丘区"},"1851":{"name":"吴中区"},"1852":{"name":"相城区"},"1853":{"name":"园区"},"1854":{"name":"新区"},"1855":{"name":"常熟市"},"1856":{"name":"张家港市"},"1857":{"name":"玉山镇"},"1858":{"name":"巴城镇"},"1859":{"name":"周市镇"},"1860":{"name":"陆家镇"},"1861":{"name":"花桥镇"},"1862":{"name":"淀山湖镇"},"1863":{"name":"张浦镇"},"1864":{"name":"周庄镇"},"1865":{"name":"千灯镇"},"1866":{"name":"锦溪镇"},"1867":{"name":"开发区"},"1868":{"name":"吴江市"},"1869":{"name":"太仓市"},"1870":{"name":"崇安区"},"1871":{"name":"北塘区"},"1872":{"name":"南长区"},"1873":{"name":"锡山区"},"1874":{"name":"惠山区"},"1875":{"name":"滨湖区"},"1876":{"name":"新区"},"1877":{"name":"江阴市"},"1878":{"name":"宜兴市"},"1879":{"name":"天宁区"},"1880":{"name":"钟楼区"},"1881":{"name":"戚墅堰区"},"1882":{"name":"郊区"},"1883":{"name":"新北区"},"1884":{"name":"武进区"},"1885":{"name":"溧阳市"},"1886":{"name":"金坛市"},"1887":{"name":"清河区"},"1888":{"name":"清浦区"},"1889":{"name":"楚州区"},"1890":{"name":"淮阴区"},"1891":{"name":"涟水县"},"1892":{"name":"洪泽县"},"1893":{"name":"盱眙县"},"1894":{"name":"金湖县"},"1895":{"name":"新浦区"},"1896":{"name":"连云区"},"1897":{"name":"海州区"},"1898":{"name":"赣榆县"},"1899":{"name":"东海县"},"1900":{"name":"灌云县"},"1901":{"name":"灌南县"},"1902":{"name":"崇川区"},"1903":{"name":"港闸区"},"1904":{"name":"经济开发区"},"1905":{"name":"启东市"},"1906":{"name":"如皋市"},"1907":{"name":"通州市"},"1908":{"name":"海门市"},"1909":{"name":"海安县"},"1910":{"name":"如东县"},"1911":{"name":"宿城区"},"1912":{"name":"宿豫区"},"1913":{"name":"宿豫县"},"1914":{"name":"沭阳县"},"1915":{"name":"泗阳县"},"1916":{"name":"泗洪县"},"1917":{"name":"海陵区"},"1918":{"name":"高港区"},"1919":{"name":"兴化市"},"1920":{"name":"靖江市"},"1921":{"name":"泰兴市"},"1922":{"name":"姜堰市"},"1923":{"name":"云龙区"},"1924":{"name":"鼓楼区"},"1925":{"name":"九里区"},"1926":{"name":"贾汪区"},"1927":{"name":"泉山区"},"1928":{"name":"新沂市"},"1929":{"name":"邳州市"},"1930":{"name":"丰县"},"1931":{"name":"沛县"},"1932":{"name":"铜山县"},"1933":{"name":"睢宁县"},"1934":{"name":"城区"},"1935":{"name":"亭湖区"},"1936":{"name":"盐都区"},"1937":{"name":"盐都县"},"1938":{"name":"东台市"},"1939":{"name":"大丰市"},"1940":{"name":"响水县"},"1941":{"name":"滨海县"},"1942":{"name":"阜宁县"},"1943":{"name":"射阳县"},"1944":{"name":"建湖县"},"1945":{"name":"广陵区"},"1946":{"name":"维扬区"},"1947":{"name":"邗江区"},"1948":{"name":"仪征市"},"1949":{"name":"高邮市"},"1950":{"name":"江都市"},"1951":{"name":"宝应县"},"1952":{"name":"京口区"},"1953":{"name":"润州区"},"1954":{"name":"丹徒区"},"1955":{"name":"丹阳市"},"1956":{"name":"扬中市"},"1957":{"name":"句容市"},"1958":{"name":"东湖区"},"1959":{"name":"西湖区"},"1960":{"name":"青云谱区"},"1961":{"name":"湾里区"},"1962":{"name":"青山湖区"},"1963":{"name":"红谷滩新区"},"1964":{"name":"昌北区"},"1965":{"name":"高新区"},"1966":{"name":"南昌县"},"1967":{"name":"新建县"},"1968":{"name":"安义县"},"1969":{"name":"进贤县"},"1970":{"name":"临川区"},"1971":{"name":"南城县"},"1972":{"name":"黎川县"},"1973":{"name":"南丰县"},"1974":{"name":"崇仁县"},"1975":{"name":"乐安县"},"1976":{"name":"宜黄县"},"1977":{"name":"金溪县"},"1978":{"name":"资溪县"},"1979":{"name":"东乡县"},"1980":{"name":"广昌县"},"1981":{"name":"章贡区"},"1982":{"name":"于都县"},"1983":{"name":"瑞金市"},"1984":{"name":"南康市"},"1985":{"name":"赣县"},"1986":{"name":"信丰县"},"1987":{"name":"大余县"},"1988":{"name":"上犹县"},"1989":{"name":"崇义县"},"1990":{"name":"安远县"},"1991":{"name":"龙南县"},"1992":{"name":"定南县"},"1993":{"name":"全南县"},"1994":{"name":"宁都县"},"1995":{"name":"兴国县"},"1996":{"name":"会昌县"},"1997":{"name":"寻乌县"},"1998":{"name":"石城县"},"1999":{"name":"安福县"},"2000":{"name":"吉州区"},"2001":{"name":"青原区"},"2002":{"name":"井冈山市"},"2003":{"name":"吉安县"},"2004":{"name":"吉水县"},"2005":{"name":"峡江县"},"2006":{"name":"新干县"},"2007":{"name":"永丰县"},"2008":{"name":"泰和县"},"2009":{"name":"遂川县"},"2010":{"name":"万安县"},"2011":{"name":"永新县"},"2012":{"name":"珠山区"},"2013":{"name":"昌江区"},"2014":{"name":"乐平市"},"2015":{"name":"浮梁县"},"2016":{"name":"浔阳区"},"2017":{"name":"庐山区"},"2018":{"name":"瑞昌市"},"2019":{"name":"九江县"},"2020":{"name":"武宁县"},"2021":{"name":"修水县"},"2022":{"name":"永修县"},"2023":{"name":"德安县"},"2024":{"name":"星子县"},"2025":{"name":"都昌县"},"2026":{"name":"湖口县"},"2027":{"name":"彭泽县"},"2028":{"name":"安源区"},"2029":{"name":"湘东区"},"2030":{"name":"莲花县"},"2031":{"name":"芦溪县"},"2032":{"name":"上栗县"},"2033":{"name":"信州区"},"2034":{"name":"德兴市"},"2035":{"name":"上饶县"},"2036":{"name":"广丰县"},"2037":{"name":"玉山县"},"2038":{"name":"铅山县"},"2039":{"name":"横峰县"},"2040":{"name":"弋阳县"},"2041":{"name":"余干县"},"2042":{"name":"波阳县"},"2043":{"name":"万年县"},"2044":{"name":"婺源县"},"2045":{"name":"渝水区"},"2046":{"name":"分宜县"},"2047":{"name":"袁州区"},"2048":{"name":"丰城市"},"2049":{"name":"樟树市"},"2050":{"name":"高安市"},"2051":{"name":"奉新县"},"2052":{"name":"万载县"},"2053":{"name":"上高县"},"2054":{"name":"宜丰县"},"2055":{"name":"靖安县"},"2056":{"name":"铜鼓县"},"2057":{"name":"月湖区"},"2058":{"name":"贵溪市"},"2059":{"name":"余江县"},"2060":{"name":"沈河区"},"2061":{"name":"皇姑区"},"2062":{"name":"和平区"},"2063":{"name":"大东区"},"2064":{"name":"铁西区"},"2065":{"name":"苏家屯区"},"2066":{"name":"东陵区"},"2067":{"name":"沈北新区"},"2068":{"name":"于洪区"},"2069":{"name":"浑南新区"},"2070":{"name":"新民市"},"2071":{"name":"辽中县"},"2072":{"name":"康平县"},"2073":{"name":"法库县"},"2074":{"name":"西岗区"},"2075":{"name":"中山区"},"2076":{"name":"沙河口区"},"2077":{"name":"甘井子区"},"2078":{"name":"旅顺口区"},"2079":{"name":"金州区"},"2080":{"name":"开发区"},"2081":{"name":"瓦房店市"},"2082":{"name":"普兰店市"},"2083":{"name":"庄河市"},"2084":{"name":"长海县"},"2085":{"name":"铁东区"},"2086":{"name":"铁西区"},"2087":{"name":"立山区"},"2088":{"name":"千山区"},"2089":{"name":"岫岩"},"2090":{"name":"海城市"},"2091":{"name":"台安县"},"2092":{"name":"本溪"},"2093":{"name":"平山区"},"2094":{"name":"明山区"},"2095":{"name":"溪湖区"},"2096":{"name":"南芬区"},"2097":{"name":"桓仁"},"2098":{"name":"双塔区"},"2099":{"name":"龙城区"},"2100":{"name":"喀喇沁左翼蒙古族自治县"},"2101":{"name":"北票市"},"2102":{"name":"凌源市"},"2103":{"name":"朝阳县"},"2104":{"name":"建平县"},"2105":{"name":"振兴区"},"2106":{"name":"元宝区"},"2107":{"name":"振安区"},"2108":{"name":"宽甸"},"2109":{"name":"东港市"},"2110":{"name":"凤城市"},"2111":{"name":"顺城区"},"2112":{"name":"新抚区"},"2113":{"name":"东洲区"},"2114":{"name":"望花区"},"2115":{"name":"清原"},"2116":{"name":"新宾"},"2117":{"name":"抚顺县"},"2118":{"name":"阜新"},"2119":{"name":"海州区"},"2120":{"name":"新邱区"},"2121":{"name":"太平区"},"2122":{"name":"清河门区"},"2123":{"name":"细河区"},"2124":{"name":"彰武县"},"2125":{"name":"龙港区"},"2126":{"name":"南票区"},"2127":{"name":"连山区"},"2128":{"name":"兴城市"},"2129":{"name":"绥中县"},"2130":{"name":"建昌县"},"2131":{"name":"太和区"},"2132":{"name":"古塔区"},"2133":{"name":"凌河区"},"2134":{"name":"凌海市"},"2135":{"name":"北镇市"},"2136":{"name":"黑山县"},"2137":{"name":"义县"},"2138":{"name":"白塔区"},"2139":{"name":"文圣区"},"2140":{"name":"宏伟区"},"2141":{"name":"太子河区"},"2142":{"name":"弓长岭区"},"2143":{"name":"灯塔市"},"2144":{"name":"辽阳县"},"2145":{"name":"双台子区"},"2146":{"name":"兴隆台区"},"2147":{"name":"大洼县"},"2148":{"name":"盘山县"},"2149":{"name":"银州区"},"2150":{"name":"清河区"},"2151":{"name":"调兵山市"},"2152":{"name":"开原市"},"2153":{"name":"铁岭县"},"2154":{"name":"西丰县"},"2155":{"name":"昌图县"},"2156":{"name":"站前区"},"2157":{"name":"西市区"},"2158":{"name":"鲅鱼圈区"},"2159":{"name":"老边区"},"2160":{"name":"盖州市"},"2161":{"name":"大石桥市"},"2162":{"name":"回民区"},"2163":{"name":"玉泉区"},"2164":{"name":"新城区"},"2165":{"name":"赛罕区"},"2166":{"name":"清水河县"},"2167":{"name":"土默特左旗"},"2168":{"name":"托克托县"},"2169":{"name":"和林格尔县"},"2170":{"name":"武川县"},"2171":{"name":"阿拉善左旗"},"2172":{"name":"阿拉善右旗"},"2173":{"name":"额济纳旗"},"2174":{"name":"临河区"},"2175":{"name":"五原县"},"2176":{"name":"磴口县"},"2177":{"name":"乌拉特前旗"},"2178":{"name":"乌拉特中旗"},"2179":{"name":"乌拉特后旗"},"2180":{"name":"杭锦后旗"},"2181":{"name":"昆都仑区"},"2182":{"name":"青山区"},"2183":{"name":"东河区"},"2184":{"name":"九原区"},"2185":{"name":"石拐区"},"2186":{"name":"白云矿区"},"2187":{"name":"土默特右旗"},"2188":{"name":"固阳县"},"2189":{"name":"达尔罕茂明安联合旗"},"2190":{"name":"红山区"},"2191":{"name":"元宝山区"},"2192":{"name":"松山区"},"2193":{"name":"阿鲁科尔沁旗"},"2194":{"name":"巴林左旗"},"2195":{"name":"巴林右旗"},"2196":{"name":"林西县"},"2197":{"name":"克什克腾旗"},"2198":{"name":"翁牛特旗"},"2199":{"name":"喀喇沁旗"},"2200":{"name":"宁城县"},"2201":{"name":"敖汉旗"},"2202":{"name":"东胜区"},"2203":{"name":"达拉特旗"},"2204":{"name":"准格尔旗"},"2205":{"name":"鄂托克前旗"},"2206":{"name":"鄂托克旗"},"2207":{"name":"杭锦旗"},"2208":{"name":"乌审旗"},"2209":{"name":"伊金霍洛旗"},"2210":{"name":"海拉尔区"},"2211":{"name":"莫力达瓦"},"2212":{"name":"满洲里市"},"2213":{"name":"牙克石市"},"2214":{"name":"扎兰屯市"},"2215":{"name":"额尔古纳市"},"2216":{"name":"根河市"},"2217":{"name":"阿荣旗"},"2218":{"name":"鄂伦春自治旗"},"2219":{"name":"鄂温克族自治旗"},"2220":{"name":"陈巴尔虎旗"},"2221":{"name":"新巴尔虎左旗"},"2222":{"name":"新巴尔虎右旗"},"2223":{"name":"科尔沁区"},"2224":{"name":"霍林郭勒市"},"2225":{"name":"科尔沁左翼中旗"},"2226":{"name":"科尔沁左翼后旗"},"2227":{"name":"开鲁县"},"2228":{"name":"库伦旗"},"2229":{"name":"奈曼旗"},"2230":{"name":"扎鲁特旗"},"2231":{"name":"海勃湾区"},"2232":{"name":"乌达区"},"2233":{"name":"海南区"},"2234":{"name":"化德县"},"2235":{"name":"集宁区"},"2236":{"name":"丰镇市"},"2237":{"name":"卓资县"},"2238":{"name":"商都县"},"2239":{"name":"兴和县"},"2240":{"name":"凉城县"},"2241":{"name":"察哈尔右翼前旗"},"2242":{"name":"察哈尔右翼中旗"},"2243":{"name":"察哈尔右翼后旗"},"2244":{"name":"四子王旗"},"2245":{"name":"二连浩特市"},"2246":{"name":"锡林浩特市"},"2247":{"name":"阿巴嘎旗"},"2248":{"name":"苏尼特左旗"},"2249":{"name":"苏尼特右旗"},"2250":{"name":"东乌珠穆沁旗"},"2251":{"name":"西乌珠穆沁旗"},"2252":{"name":"太仆寺旗"},"2253":{"name":"镶黄旗"},"2254":{"name":"正镶白旗"},"2255":{"name":"正蓝旗"},"2256":{"name":"多伦县"},"2257":{"name":"乌兰浩特市"},"2258":{"name":"阿尔山市"},"2259":{"name":"科尔沁右翼前旗"},"2260":{"name":"科尔沁右翼中旗"},"2261":{"name":"扎赉特旗"},"2262":{"name":"突泉县"},"2263":{"name":"西夏区"},"2264":{"name":"金凤区"},"2265":{"name":"兴庆区"},"2266":{"name":"灵武市"},"2267":{"name":"永宁县"},"2268":{"name":"贺兰县"},"2269":{"name":"原州区"},"2270":{"name":"海原县"},"2271":{"name":"西吉县"},"2272":{"name":"隆德县"},"2273":{"name":"泾源县"},"2274":{"name":"彭阳县"},"2275":{"name":"惠农县"},"2276":{"name":"大武口区"},"2277":{"name":"惠农区"},"2278":{"name":"陶乐县"},"2279":{"name":"平罗县"},"2280":{"name":"利通区"},"2281":{"name":"中卫县"},"2282":{"name":"青铜峡市"},"2283":{"name":"中宁县"},"2284":{"name":"盐池县"},"2285":{"name":"同心县"},"2286":{"name":"沙坡头区"},"2287":{"name":"海原县"},"2288":{"name":"中宁县"},"2289":{"name":"城中区"},"2290":{"name":"城东区"},"2291":{"name":"城西区"},"2292":{"name":"城北区"},"2293":{"name":"湟中县"},"2294":{"name":"湟源县"},"2295":{"name":"大通"},"2296":{"name":"玛沁县"},"2297":{"name":"班玛县"},"2298":{"name":"甘德县"},"2299":{"name":"达日县"},"2300":{"name":"久治县"},"2301":{"name":"玛多县"},"2302":{"name":"海晏县"},"2303":{"name":"祁连县"},"2304":{"name":"刚察县"},"2305":{"name":"门源"},"2306":{"name":"平安县"},"2307":{"name":"乐都县"},"2308":{"name":"民和"},"2309":{"name":"互助"},"2310":{"name":"化隆"},"2311":{"name":"循化"},"2312":{"name":"共和县"},"2313":{"name":"同德县"},"2314":{"name":"贵德县"},"2315":{"name":"兴海县"},"2316":{"name":"贵南县"},"2317":{"name":"德令哈市"},"2318":{"name":"格尔木市"},"2319":{"name":"乌兰县"},"2320":{"name":"都兰县"},"2321":{"name":"天峻县"},"2322":{"name":"同仁县"},"2323":{"name":"尖扎县"},"2324":{"name":"泽库县"},"2325":{"name":"河南蒙古族自治县"},"2326":{"name":"玉树县"},"2327":{"name":"杂多县"},"2328":{"name":"称多县"},"2329":{"name":"治多县"},"2330":{"name":"囊谦县"},"2331":{"name":"曲麻莱县"},"2332":{"name":"市中区"},"2333":{"name":"历下区"},"2334":{"name":"天桥区"},"2335":{"name":"槐荫区"},"2336":{"name":"历城区"},"2337":{"name":"长清区"},"2338":{"name":"章丘市"},"2339":{"name":"平阴县"},"2340":{"name":"济阳县"},"2341":{"name":"商河县"},"2342":{"name":"市南区"},"2343":{"name":"市北区"},"2344":{"name":"城阳区"},"2345":{"name":"四方区"},"2346":{"name":"李沧区"},"2347":{"name":"黄岛区"},"2348":{"name":"崂山区"},"2349":{"name":"胶州市"},"2350":{"name":"即墨市"},"2351":{"name":"平度市"},"2352":{"name":"胶南市"},"2353":{"name":"莱西市"},"2354":{"name":"滨城区"},"2355":{"name":"惠民县"},"2356":{"name":"阳信县"},"2357":{"name":"无棣县"},"2358":{"name":"沾化县"},"2359":{"name":"博兴县"},"2360":{"name":"邹平县"},"2361":{"name":"德城区"},"2362":{"name":"陵县"},"2363":{"name":"乐陵市"},"2364":{"name":"禹城市"},"2365":{"name":"宁津县"},"2366":{"name":"庆云县"},"2367":{"name":"临邑县"},"2368":{"name":"齐河县"},"2369":{"name":"平原县"},"2370":{"name":"夏津县"},"2371":{"name":"武城县"},"2372":{"name":"东营区"},"2373":{"name":"河口区"},"2374":{"name":"垦利县"},"2375":{"name":"利津县"},"2376":{"name":"广饶县"},"2377":{"name":"牡丹区"},"2378":{"name":"曹县"},"2379":{"name":"单县"},"2380":{"name":"成武县"},"2381":{"name":"巨野县"},"2382":{"name":"郓城县"},"2383":{"name":"鄄城县"},"2384":{"name":"定陶县"},"2385":{"name":"东明县"},"2386":{"name":"市中区"},"2387":{"name":"任城区"},"2388":{"name":"曲阜市"},"2389":{"name":"兖州市"},"2390":{"name":"邹城市"},"2391":{"name":"微山县"},"2392":{"name":"鱼台县"},"2393":{"name":"金乡县"},"2394":{"name":"嘉祥县"},"2395":{"name":"汶上县"},"2396":{"name":"泗水县"},"2397":{"name":"梁山县"},"2398":{"name":"莱城区"},"2399":{"name":"钢城区"},"2400":{"name":"东昌府区"},"2401":{"name":"临清市"},"2402":{"name":"阳谷县"},"2403":{"name":"莘县"},"2404":{"name":"茌平县"},"2405":{"name":"东阿县"},"2406":{"name":"冠县"},"2407":{"name":"高唐县"},"2408":{"name":"兰山区"},"2409":{"name":"罗庄区"},"2410":{"name":"河东区"},"2411":{"name":"沂南县"},"2412":{"name":"郯城县"},"2413":{"name":"沂水县"},"2414":{"name":"苍山县"},"2415":{"name":"费县"},"2416":{"name":"平邑县"},"2417":{"name":"莒南县"},"2418":{"name":"蒙阴县"},"2419":{"name":"临沭县"},"2420":{"name":"东港区"},"2421":{"name":"岚山区"},"2422":{"name":"五莲县"},"2423":{"name":"莒县"},"2424":{"name":"泰山区"},"2425":{"name":"岱岳区"},"2426":{"name":"新泰市"},"2427":{"name":"肥城市"},"2428":{"name":"宁阳县"},"2429":{"name":"东平县"},"2430":{"name":"荣成市"},"2431":{"name":"乳山市"},"2432":{"name":"环翠区"},"2433":{"name":"文登市"},"2434":{"name":"潍城区"},"2435":{"name":"寒亭区"},"2436":{"name":"坊子区"},"2437":{"name":"奎文区"},"2438":{"name":"青州市"},"2439":{"name":"诸城市"},"2440":{"name":"寿光市"},"2441":{"name":"安丘市"},"2442":{"name":"高密市"},"2443":{"name":"昌邑市"},"2444":{"name":"临朐县"},"2445":{"name":"昌乐县"},"2446":{"name":"芝罘区"},"2447":{"name":"福山区"},"2448":{"name":"牟平区"},"2449":{"name":"莱山区"},"2450":{"name":"开发区"},"2451":{"name":"龙口市"},"2452":{"name":"莱阳市"},"2453":{"name":"莱州市"},"2454":{"name":"蓬莱市"},"2455":{"name":"招远市"},"2456":{"name":"栖霞市"},"2457":{"name":"海阳市"},"2458":{"name":"长岛县"},"2459":{"name":"市中区"},"2460":{"name":"山亭区"},"2461":{"name":"峄城区"},"2462":{"name":"台儿庄区"},"2463":{"name":"薛城区"},"2464":{"name":"滕州市"},"2465":{"name":"张店区"},"2466":{"name":"临淄区"},"2467":{"name":"淄川区"},"2468":{"name":"博山区"},"2469":{"name":"周村区"},"2470":{"name":"桓台县"},"2471":{"name":"高青县"},"2472":{"name":"沂源县"},"2473":{"name":"杏花岭区"},"2474":{"name":"小店区"},"2475":{"name":"迎泽区"},"2476":{"name":"尖草坪区"},"2477":{"name":"万柏林区"},"2478":{"name":"晋源区"},"2479":{"name":"高新开发区"},"2480":{"name":"民营经济开发区"},"2481":{"name":"经济技术开发区"},"2482":{"name":"清徐县"},"2483":{"name":"阳曲县"},"2484":{"name":"娄烦县"},"2485":{"name":"古交市"},"2486":{"name":"城区"},"2487":{"name":"郊区"},"2488":{"name":"沁县"},"2489":{"name":"潞城市"},"2490":{"name":"长治县"},"2491":{"name":"襄垣县"},"2492":{"name":"屯留县"},"2493":{"name":"平顺县"},"2494":{"name":"黎城县"},"2495":{"name":"壶关县"},"2496":{"name":"长子县"},"2497":{"name":"武乡县"},"2498":{"name":"沁源县"},"2499":{"name":"城区"},"2500":{"name":"矿区"},"2501":{"name":"南郊区"},"2502":{"name":"新荣区"},"2503":{"name":"阳高县"},"2504":{"name":"天镇县"},"2505":{"name":"广灵县"},"2506":{"name":"灵丘县"},"2507":{"name":"浑源县"},"2508":{"name":"左云县"},"2509":{"name":"大同县"},"2510":{"name":"城区"},"2511":{"name":"高平市"},"2512":{"name":"沁水县"},"2513":{"name":"阳城县"},"2514":{"name":"陵川县"},"2515":{"name":"泽州县"},"2516":{"name":"榆次区"},"2517":{"name":"介休市"},"2518":{"name":"榆社县"},"2519":{"name":"左权县"},"2520":{"name":"和顺县"},"2521":{"name":"昔阳县"},"2522":{"name":"寿阳县"},"2523":{"name":"太谷县"},"2524":{"name":"祁县"},"2525":{"name":"平遥县"},"2526":{"name":"灵石县"},"2527":{"name":"尧都区"},"2528":{"name":"侯马市"},"2529":{"name":"霍州市"},"2530":{"name":"曲沃县"},"2531":{"name":"翼城县"},"2532":{"name":"襄汾县"},"2533":{"name":"洪洞县"},"2534":{"name":"吉县"},"2535":{"name":"安泽县"},"2536":{"name":"浮山县"},"2537":{"name":"古县"},"2538":{"name":"乡宁县"},"2539":{"name":"大宁县"},"2540":{"name":"隰县"},"2541":{"name":"永和县"},"2542":{"name":"蒲县"},"2543":{"name":"汾西县"},"2544":{"name":"离石市"},"2545":{"name":"离石区"},"2546":{"name":"孝义市"},"2547":{"name":"汾阳市"},"2548":{"name":"文水县"},"2549":{"name":"交城县"},"2550":{"name":"兴县"},"2551":{"name":"临县"},"2552":{"name":"柳林县"},"2553":{"name":"石楼县"},"2554":{"name":"岚县"},"2555":{"name":"方山县"},"2556":{"name":"中阳县"},"2557":{"name":"交口县"},"2558":{"name":"朔城区"},"2559":{"name":"平鲁区"},"2560":{"name":"山阴县"},"2561":{"name":"应县"},"2562":{"name":"右玉县"},"2563":{"name":"怀仁县"},"2564":{"name":"忻府区"},"2565":{"name":"原平市"},"2566":{"name":"定襄县"},"2567":{"name":"五台县"},"2568":{"name":"代县"},"2569":{"name":"繁峙县"},"2570":{"name":"宁武县"},"2571":{"name":"静乐县"},"2572":{"name":"神池县"},"2573":{"name":"五寨县"},"2574":{"name":"岢岚县"},"2575":{"name":"河曲县"},"2576":{"name":"保德县"},"2577":{"name":"偏关县"},"2578":{"name":"城区"},"2579":{"name":"矿区"},"2580":{"name":"郊区"},"2581":{"name":"平定县"},"2582":{"name":"盂县"},"2583":{"name":"盐湖区"},"2584":{"name":"永济市"},"2585":{"name":"河津市"},"2586":{"name":"临猗县"},"2587":{"name":"万荣县"},"2588":{"name":"闻喜县"},"2589":{"name":"稷山县"},"2590":{"name":"新绛县"},"2591":{"name":"绛县"},"2592":{"name":"垣曲县"},"2593":{"name":"夏县"},"2594":{"name":"平陆县"},"2595":{"name":"芮城县"},"2596":{"name":"莲湖区"},"2597":{"name":"新城区"},"2598":{"name":"碑林区"},"2599":{"name":"雁塔区"},"2600":{"name":"灞桥区"},"2601":{"name":"未央区"},"2602":{"name":"阎良区"},"2603":{"name":"临潼区"},"2604":{"name":"长安区"},"2605":{"name":"蓝田县"},"2606":{"name":"周至县"},"2607":{"name":"户县"},"2608":{"name":"高陵县"},"2609":{"name":"汉滨区"},"2610":{"name":"汉阴县"},"2611":{"name":"石泉县"},"2612":{"name":"宁陕县"},"2613":{"name":"紫阳县"},"2614":{"name":"岚皋县"},"2615":{"name":"平利县"},"2616":{"name":"镇坪县"},"2617":{"name":"旬阳县"},"2618":{"name":"白河县"},"2619":{"name":"陈仓区"},"2620":{"name":"渭滨区"},"2621":{"name":"金台区"},"2622":{"name":"凤翔县"},"2623":{"name":"岐山县"},"2624":{"name":"扶风县"},"2625":{"name":"眉县"},"2626":{"name":"陇县"},"2627":{"name":"千阳县"},"2628":{"name":"麟游县"},"2629":{"name":"凤县"},"2630":{"name":"太白县"},"2631":{"name":"汉台区"},"2632":{"name":"南郑县"},"2633":{"name":"城固县"},"2634":{"name":"洋县"},"2635":{"name":"西乡县"},"2636":{"name":"勉县"},"2637":{"name":"宁强县"},"2638":{"name":"略阳县"},"2639":{"name":"镇巴县"},"2640":{"name":"留坝县"},"2641":{"name":"佛坪县"},"2642":{"name":"商州区"},"2643":{"name":"洛南县"},"2644":{"name":"丹凤县"},"2645":{"name":"商南县"},"2646":{"name":"山阳县"},"2647":{"name":"镇安县"},"2648":{"name":"柞水县"},"2649":{"name":"耀州区"},"2650":{"name":"王益区"},"2651":{"name":"印台区"},"2652":{"name":"宜君县"},"2653":{"name":"临渭区"},"2654":{"name":"韩城市"},"2655":{"name":"华阴市"},"2656":{"name":"华县"},"2657":{"name":"潼关县"},"2658":{"name":"大荔县"},"2659":{"name":"合阳县"},"2660":{"name":"澄城县"},"2661":{"name":"蒲城县"},"2662":{"name":"白水县"},"2663":{"name":"富平县"},"2664":{"name":"秦都区"},"2665":{"name":"渭城区"},"2666":{"name":"杨陵区"},"2667":{"name":"兴平市"},"2668":{"name":"三原县"},"2669":{"name":"泾阳县"},"2670":{"name":"乾县"},"2671":{"name":"礼泉县"},"2672":{"name":"永寿县"},"2673":{"name":"彬县"},"2674":{"name":"长武县"},"2675":{"name":"旬邑县"},"2676":{"name":"淳化县"},"2677":{"name":"武功县"},"2678":{"name":"吴起县"},"2679":{"name":"宝塔区"},"2680":{"name":"延长县"},"2681":{"name":"延川县"},"2682":{"name":"子长县"},"2683":{"name":"安塞县"},"2684":{"name":"志丹县"},"2685":{"name":"甘泉县"},"2686":{"name":"富县"},"2687":{"name":"洛川县"},"2688":{"name":"宜川县"},"2689":{"name":"黄龙县"},"2690":{"name":"黄陵县"},"2691":{"name":"榆阳区"},"2692":{"name":"神木县"},"2693":{"name":"府谷县"},"2694":{"name":"横山县"},"2695":{"name":"靖边县"},"2696":{"name":"定边县"},"2697":{"name":"绥德县"},"2698":{"name":"米脂县"},"2699":{"name":"佳县"},"2700":{"name":"吴堡县"},"2701":{"name":"清涧县"},"2702":{"name":"子洲县"},"2703":{"name":"长宁区"},"2704":{"name":"闸北区"},"2705":{"name":"闵行区"},"2706":{"name":"徐汇区"},"2707":{"name":"浦东新区"},"2708":{"name":"杨浦区"},"2709":{"name":"普陀区"},"2710":{"name":"静安区"},"2711":{"name":"卢湾区"},"2712":{"name":"虹口区"},"2713":{"name":"黄浦区"},"2714":{"name":"南汇区"},"2715":{"name":"松江区"},"2716":{"name":"嘉定区"},"2717":{"name":"宝山区"},"2718":{"name":"青浦区"},"2719":{"name":"金山区"},"2720":{"name":"奉贤区"},"2721":{"name":"崇明县"},"2722":{"name":"青羊区"},"2723":{"name":"锦江区"},"2724":{"name":"金牛区"},"2725":{"name":"武侯区"},"2726":{"name":"成华区"},"2727":{"name":"龙泉驿区"},"2728":{"name":"青白江区"},"2729":{"name":"新都区"},"2730":{"name":"温江区"},"2731":{"name":"高新区"},"2732":{"name":"高新西区"},"2733":{"name":"都江堰市"},"2734":{"name":"彭州市"},"2735":{"name":"邛崃市"},"2736":{"name":"崇州市"},"2737":{"name":"金堂县"},"2738":{"name":"双流县"},"2739":{"name":"郫县"},"2740":{"name":"大邑县"},"2741":{"name":"蒲江县"},"2742":{"name":"新津县"},"2743":{"name":"都江堰市"},"2744":{"name":"彭州市"},"2745":{"name":"邛崃市"},"2746":{"name":"崇州市"},"2747":{"name":"金堂县"},"2748":{"name":"双流县"},"2749":{"name":"郫县"},"2750":{"name":"大邑县"},"2751":{"name":"蒲江县"},"2752":{"name":"新津县"},"2753":{"name":"涪城区"},"2754":{"name":"游仙区"},"2755":{"name":"江油市"},"2756":{"name":"盐亭县"},"2757":{"name":"三台县"},"2758":{"name":"平武县"},"2759":{"name":"安县"},"2760":{"name":"梓潼县"},"2761":{"name":"北川县"},"2762":{"name":"马尔康县"},"2763":{"name":"汶川县"},"2764":{"name":"理县"},"2765":{"name":"茂县"},"2766":{"name":"松潘县"},"2767":{"name":"九寨沟县"},"2768":{"name":"金川县"},"2769":{"name":"小金县"},"2770":{"name":"黑水县"},"2771":{"name":"壤塘县"},"2772":{"name":"阿坝县"},"2773":{"name":"若尔盖县"},"2774":{"name":"红原县"},"2775":{"name":"巴州区"},"2776":{"name":"通江县"},"2777":{"name":"南江县"},"2778":{"name":"平昌县"},"2779":{"name":"通川区"},"2780":{"name":"万源市"},"2781":{"name":"达县"},"2782":{"name":"宣汉县"},"2783":{"name":"开江县"},"2784":{"name":"大竹县"},"2785":{"name":"渠县"},"2786":{"name":"旌阳区"},"2787":{"name":"广汉市"},"2788":{"name":"什邡市"},"2789":{"name":"绵竹市"},"2790":{"name":"罗江县"},"2791":{"name":"中江县"},"2792":{"name":"康定县"},"2793":{"name":"丹巴县"},"2794":{"name":"泸定县"},"2795":{"name":"炉霍县"},"2796":{"name":"九龙县"},"2797":{"name":"甘孜县"},"2798":{"name":"雅江县"},"2799":{"name":"新龙县"},"2800":{"name":"道孚县"},"2801":{"name":"白玉县"},"2802":{"name":"理塘县"},"2803":{"name":"德格县"},"2804":{"name":"乡城县"},"2805":{"name":"石渠县"},"2806":{"name":"稻城县"},"2807":{"name":"色达县"},"2808":{"name":"巴塘县"},"2809":{"name":"得荣县"},"2810":{"name":"广安区"},"2811":{"name":"华蓥市"},"2812":{"name":"岳池县"},"2813":{"name":"武胜县"},"2814":{"name":"邻水县"},"2815":{"name":"利州区"},"2816":{"name":"元坝区"},"2817":{"name":"朝天区"},"2818":{"name":"旺苍县"},"2819":{"name":"青川县"},"2820":{"name":"剑阁县"},"2821":{"name":"苍溪县"},"2822":{"name":"峨眉山市"},"2823":{"name":"乐山市"},"2824":{"name":"犍为县"},"2825":{"name":"井研县"},"2826":{"name":"夹江县"},"2827":{"name":"沐川县"},"2828":{"name":"峨边"},"2829":{"name":"马边"},"2830":{"name":"西昌市"},"2831":{"name":"盐源县"},"2832":{"name":"德昌县"},"2833":{"name":"会理县"},"2834":{"name":"会东县"},"2835":{"name":"宁南县"},"2836":{"name":"普格县"},"2837":{"name":"布拖县"},"2838":{"name":"金阳县"},"2839":{"name":"昭觉县"},"2840":{"name":"喜德县"},"2841":{"name":"冕宁县"},"2842":{"name":"越西县"},"2843":{"name":"甘洛县"},"2844":{"name":"美姑县"},"2845":{"name":"雷波县"},"2846":{"name":"木里"},"2847":{"name":"东坡区"},"2848":{"name":"仁寿县"},"2849":{"name":"彭山县"},"2850":{"name":"洪雅县"},"2851":{"name":"丹棱县"},"2852":{"name":"青神县"},"2853":{"name":"阆中市"},"2854":{"name":"南部县"},"2855":{"name":"营山县"},"2856":{"name":"蓬安县"},"2857":{"name":"仪陇县"},"2858":{"name":"顺庆区"},"2859":{"name":"高坪区"},"2860":{"name":"嘉陵区"},"2861":{"name":"西充县"},"2862":{"name":"市中区"},"2863":{"name":"东兴区"},"2864":{"name":"威远县"},"2865":{"name":"资中县"},"2866":{"name":"隆昌县"},"2867":{"name":"东  区"},"2868":{"name":"西  区"},"2869":{"name":"仁和区"},"2870":{"name":"米易县"},"2871":{"name":"盐边县"},"2872":{"name":"船山区"},"2873":{"name":"安居区"},"2874":{"name":"蓬溪县"},"2875":{"name":"射洪县"},"2876":{"name":"大英县"},"2877":{"name":"雨城区"},"2878":{"name":"名山县"},"2879":{"name":"荥经县"},"2880":{"name":"汉源县"},"2881":{"name":"石棉县"},"2882":{"name":"天全县"},"2883":{"name":"芦山县"},"2884":{"name":"宝兴县"},"2885":{"name":"翠屏区"},"2886":{"name":"宜宾县"},"2887":{"name":"南溪县"},"2888":{"name":"江安县"},"2889":{"name":"长宁县"},"2890":{"name":"高县"},"2891":{"name":"珙县"},"2892":{"name":"筠连县"},"2893":{"name":"兴文县"},"2894":{"name":"屏山县"},"2895":{"name":"雁江区"},"2896":{"name":"简阳市"},"2897":{"name":"安岳县"},"2898":{"name":"乐至县"},"2899":{"name":"大安区"},"2900":{"name":"自流井区"},"2901":{"name":"贡井区"},"2902":{"name":"沿滩区"},"2903":{"name":"荣县"},"2904":{"name":"富顺县"},"2905":{"name":"江阳区"},"2906":{"name":"纳溪区"},"2907":{"name":"龙马潭区"},"2908":{"name":"泸县"},"2909":{"name":"合江县"},"2910":{"name":"叙永县"},"2911":{"name":"古蔺县"},"2912":{"name":"和平区"},"2913":{"name":"河西区"},"2914":{"name":"南开区"},"2915":{"name":"河北区"},"2916":{"name":"河东区"},"2917":{"name":"红桥区"},"2918":{"name":"东丽区"},"2919":{"name":"津南区"},"2920":{"name":"西青区"},"2921":{"name":"北辰区"},"2922":{"name":"塘沽区"},"2923":{"name":"汉沽区"},"2924":{"name":"大港区"},"2925":{"name":"武清区"},"2926":{"name":"宝坻区"},"2927":{"name":"经济开发区"},"2928":{"name":"宁河县"},"2929":{"name":"静海县"},"2930":{"name":"蓟县"},"2931":{"name":"城关区"},"2932":{"name":"林周县"},"2933":{"name":"当雄县"},"2934":{"name":"尼木县"},"2935":{"name":"曲水县"},"2936":{"name":"堆龙德庆县"},"2937":{"name":"达孜县"},"2938":{"name":"墨竹工卡县"},"2939":{"name":"噶尔县"},"2940":{"name":"普兰县"},"2941":{"name":"札达县"},"2942":{"name":"日土县"},"2943":{"name":"革吉县"},"2944":{"name":"改则县"},"2945":{"name":"措勤县"},"2946":{"name":"昌都县"},"2947":{"name":"江达县"},"2948":{"name":"贡觉县"},"2949":{"name":"类乌齐县"},"2950":{"name":"丁青县"},"2951":{"name":"察雅县"},"2952":{"name":"八宿县"},"2953":{"name":"左贡县"},"2954":{"name":"芒康县"},"2955":{"name":"洛隆县"},"2956":{"name":"边坝县"},"2957":{"name":"林芝县"},"2958":{"name":"工布江达县"},"2959":{"name":"米林县"},"2960":{"name":"墨脱县"},"2961":{"name":"波密县"},"2962":{"name":"察隅县"},"2963":{"name":"朗县"},"2964":{"name":"那曲县"},"2965":{"name":"嘉黎县"},"2966":{"name":"比如县"},"2967":{"name":"聂荣县"},"2968":{"name":"安多县"},"2969":{"name":"申扎县"},"2970":{"name":"索县"},"2971":{"name":"班戈县"},"2972":{"name":"巴青县"},"2973":{"name":"尼玛县"},"2974":{"name":"日喀则市"},"2975":{"name":"南木林县"},"2976":{"name":"江孜县"},"2977":{"name":"定日县"},"2978":{"name":"萨迦县"},"2979":{"name":"拉孜县"},"2980":{"name":"昂仁县"},"2981":{"name":"谢通门县"},"2982":{"name":"白朗县"},"2983":{"name":"仁布县"},"2984":{"name":"康马县"},"2985":{"name":"定结县"},"2986":{"name":"仲巴县"},"2987":{"name":"亚东县"},"2988":{"name":"吉隆县"},"2989":{"name":"聂拉木县"},"2990":{"name":"萨嘎县"},"2991":{"name":"岗巴县"},"2992":{"name":"乃东县"},"2993":{"name":"扎囊县"},"2994":{"name":"贡嘎县"},"2995":{"name":"桑日县"},"2996":{"name":"琼结县"},"2997":{"name":"曲松县"},"2998":{"name":"措美县"},"2999":{"name":"洛扎县"},"3000":{"name":"加查县"},"3001":{"name":"隆子县"},"3002":{"name":"错那县"},"3003":{"name":"浪卡子县"},"3004":{"name":"天山区"},"3005":{"name":"沙依巴克区"},"3006":{"name":"新市区"},"3007":{"name":"水磨沟区"},"3008":{"name":"头屯河区"},"3009":{"name":"达坂城区"},"3010":{"name":"米东区"},"3011":{"name":"乌鲁木齐县"},"3012":{"name":"阿克苏市"},"3013":{"name":"温宿县"},"3014":{"name":"库车县"},"3015":{"name":"沙雅县"},"3016":{"name":"新和县"},"3017":{"name":"拜城县"},"3018":{"name":"乌什县"},"3019":{"name":"阿瓦提县"},"3020":{"name":"柯坪县"},"3021":{"name":"阿拉尔市"},"3022":{"name":"库尔勒市"},"3023":{"name":"轮台县"},"3024":{"name":"尉犁县"},"3025":{"name":"若羌县"},"3026":{"name":"且末县"},"3027":{"name":"焉耆"},"3028":{"name":"和静县"},"3029":{"name":"和硕县"},"3030":{"name":"博湖县"},"3031":{"name":"博乐市"},"3032":{"name":"精河县"},"3033":{"name":"温泉县"},"3034":{"name":"呼图壁县"},"3035":{"name":"米泉市"},"3036":{"name":"昌吉市"},"3037":{"name":"阜康市"},"3038":{"name":"玛纳斯县"},"3039":{"name":"奇台县"},"3040":{"name":"吉木萨尔县"},"3041":{"name":"木垒"},"3042":{"name":"哈密市"},"3043":{"name":"伊吾县"},"3044":{"name":"巴里坤"},"3045":{"name":"和田市"},"3046":{"name":"和田县"},"3047":{"name":"墨玉县"},"3048":{"name":"皮山县"},"3049":{"name":"洛浦县"},"3050":{"name":"策勒县"},"3051":{"name":"于田县"},"3052":{"name":"民丰县"},"3053":{"name":"喀什市"},"3054":{"name":"疏附县"},"3055":{"name":"疏勒县"},"3056":{"name":"英吉沙县"},"3057":{"name":"泽普县"},"3058":{"name":"莎车县"},"3059":{"name":"叶城县"},"3060":{"name":"麦盖提县"},"3061":{"name":"岳普湖县"},"3062":{"name":"伽师县"},"3063":{"name":"巴楚县"},"3064":{"name":"塔什库尔干"},"3065":{"name":"克拉玛依市"},"3066":{"name":"阿图什市"},"3067":{"name":"阿克陶县"},"3068":{"name":"阿合奇县"},"3069":{"name":"乌恰县"},"3070":{"name":"石河子市"},"3071":{"name":"图木舒克市"},"3072":{"name":"吐鲁番市"},"3073":{"name":"鄯善县"},"3074":{"name":"托克逊县"},"3075":{"name":"五家渠市"},"3076":{"name":"阿勒泰市"},"3077":{"name":"布克赛尔"},"3078":{"name":"伊宁市"},"3079":{"name":"布尔津县"},"3080":{"name":"奎屯市"},"3081":{"name":"乌苏市"},"3082":{"name":"额敏县"},"3083":{"name":"富蕴县"},"3084":{"name":"伊宁县"},"3085":{"name":"福海县"},"3086":{"name":"霍城县"},"3087":{"name":"沙湾县"},"3088":{"name":"巩留县"},"3089":{"name":"哈巴河县"},"3090":{"name":"托里县"},"3091":{"name":"青河县"},"3092":{"name":"新源县"},"3093":{"name":"裕民县"},"3094":{"name":"和布克赛尔"},"3095":{"name":"吉木乃县"},"3096":{"name":"昭苏县"},"3097":{"name":"特克斯县"},"3098":{"name":"尼勒克县"},"3099":{"name":"察布查尔"},"3100":{"name":"盘龙区"},"3101":{"name":"五华区"},"3102":{"name":"官渡区"},"3103":{"name":"西山区"},"3104":{"name":"东川区"},"3105":{"name":"安宁市"},"3106":{"name":"呈贡县"},"3107":{"name":"晋宁县"},"3108":{"name":"富民县"},"3109":{"name":"宜良县"},"3110":{"name":"嵩明县"},"3111":{"name":"石林县"},"3112":{"name":"禄劝"},"3113":{"name":"寻甸"},"3114":{"name":"兰坪"},"3115":{"name":"泸水县"},"3116":{"name":"福贡县"},"3117":{"name":"贡山"},"3118":{"name":"宁洱"},"3119":{"name":"思茅区"},"3120":{"name":"墨江"},"3121":{"name":"景东"},"3122":{"name":"景谷"},"3123":{"name":"镇沅"},"3124":{"name":"江城"},"3125":{"name":"孟连"},"3126":{"name":"澜沧"},"3127":{"name":"西盟"},"3128":{"name":"古城区"},"3129":{"name":"宁蒗"},"3130":{"name":"玉龙"},"3131":{"name":"永胜县"},"3132":{"name":"华坪县"},"3133":{"name":"隆阳区"},"3134":{"name":"施甸县"},"3135":{"name":"腾冲县"},"3136":{"name":"龙陵县"},"3137":{"name":"昌宁县"},"3138":{"name":"楚雄市"},"3139":{"name":"双柏县"},"3140":{"name":"牟定县"},"3141":{"name":"南华县"},"3142":{"name":"姚安县"},"3143":{"name":"大姚县"},"3144":{"name":"永仁县"},"3145":{"name":"元谋县"},"3146":{"name":"武定县"},"3147":{"name":"禄丰县"},"3148":{"name":"大理市"},"3149":{"name":"祥云县"},"3150":{"name":"宾川县"},"3151":{"name":"弥渡县"},"3152":{"name":"永平县"},"3153":{"name":"云龙县"},"3154":{"name":"洱源县"},"3155":{"name":"剑川县"},"3156":{"name":"鹤庆县"},"3157":{"name":"漾濞"},"3158":{"name":"南涧"},"3159":{"name":"巍山"},"3160":{"name":"潞西市"},"3161":{"name":"瑞丽市"},"3162":{"name":"梁河县"},"3163":{"name":"盈江县"},"3164":{"name":"陇川县"},"3165":{"name":"香格里拉县"},"3166":{"name":"德钦县"},"3167":{"name":"维西"},"3168":{"name":"泸西县"},"3169":{"name":"蒙自县"},"3170":{"name":"个旧市"},"3171":{"name":"开远市"},"3172":{"name":"绿春县"},"3173":{"name":"建水县"},"3174":{"name":"石屏县"},"3175":{"name":"弥勒县"},"3176":{"name":"元阳县"},"3177":{"name":"红河县"},"3178":{"name":"金平"},"3179":{"name":"河口"},"3180":{"name":"屏边"},"3181":{"name":"临翔区"},"3182":{"name":"凤庆县"},"3183":{"name":"云县"},"3184":{"name":"永德县"},"3185":{"name":"镇康县"},"3186":{"name":"双江"},"3187":{"name":"耿马"},"3188":{"name":"沧源"},"3189":{"name":"麒麟区"},"3190":{"name":"宣威市"},"3191":{"name":"马龙县"},"3192":{"name":"陆良县"},"3193":{"name":"师宗县"},"3194":{"name":"罗平县"},"3195":{"name":"富源县"},"3196":{"name":"会泽县"},"3197":{"name":"沾益县"},"3198":{"name":"文山县"},"3199":{"name":"砚山县"},"3200":{"name":"西畴县"},"3201":{"name":"麻栗坡县"},"3202":{"name":"马关县"},"3203":{"name":"丘北县"},"3204":{"name":"广南县"},"3205":{"name":"富宁县"},"3206":{"name":"景洪市"},"3207":{"name":"勐海县"},"3208":{"name":"勐腊县"},"3209":{"name":"红塔区"},"3210":{"name":"江川县"},"3211":{"name":"澄江县"},"3212":{"name":"通海县"},"3213":{"name":"华宁县"},"3214":{"name":"易门县"},"3215":{"name":"峨山"},"3216":{"name":"新平"},"3217":{"name":"元江"},"3218":{"name":"昭阳区"},"3219":{"name":"鲁甸县"},"3220":{"name":"巧家县"},"3221":{"name":"盐津县"},"3222":{"name":"大关县"},"3223":{"name":"永善县"},"3224":{"name":"绥江县"},"3225":{"name":"镇雄县"},"3226":{"name":"彝良县"},"3227":{"name":"威信县"},"3228":{"name":"水富县"},"3229":{"name":"西湖区"},"3230":{"name":"上城区"},"3231":{"name":"下城区"},"3232":{"name":"拱墅区"},"3233":{"name":"滨江区"},"3234":{"name":"江干区"},"3235":{"name":"萧山区"},"3236":{"name":"余杭区"},"3237":{"name":"市郊"},"3238":{"name":"建德市"},"3239":{"name":"富阳市"},"3240":{"name":"临安市"},"3241":{"name":"桐庐县"},"3242":{"name":"淳安县"},"3243":{"name":"吴兴区"},"3244":{"name":"南浔区"},"3245":{"name":"德清县"},"3246":{"name":"长兴县"},"3247":{"name":"安吉县"},"3248":{"name":"南湖区"},"3249":{"name":"秀洲区"},"3250":{"name":"海宁市"},"3251":{"name":"嘉善县"},"3252":{"name":"平湖市"},"3253":{"name":"桐乡市"},"3254":{"name":"海盐县"},"3255":{"name":"婺城区"},"3256":{"name":"金东区"},"3257":{"name":"兰溪市"},"3258":{"name":"市区"},"3259":{"name":"佛堂镇"},"3260":{"name":"上溪镇"},"3261":{"name":"义亭镇"},"3262":{"name":"大陈镇"},"3263":{"name":"苏溪镇"},"3264":{"name":"赤岸镇"},"3265":{"name":"东阳市"},"3266":{"name":"永康市"},"3267":{"name":"武义县"},"3268":{"name":"浦江县"},"3269":{"name":"磐安县"},"3270":{"name":"莲都区"},"3271":{"name":"龙泉市"},"3272":{"name":"青田县"},"3273":{"name":"缙云县"},"3274":{"name":"遂昌县"},"3275":{"name":"松阳县"},"3276":{"name":"云和县"},"3277":{"name":"庆元县"},"3278":{"name":"景宁"},"3279":{"name":"海曙区"},"3280":{"name":"江东区"},"3281":{"name":"江北区"},"3282":{"name":"镇海区"},"3283":{"name":"北仑区"},"3284":{"name":"鄞州区"},"3285":{"name":"余姚市"},"3286":{"name":"慈溪市"},"3287":{"name":"奉化市"},"3288":{"name":"象山县"},"3289":{"name":"宁海县"},"3290":{"name":"越城区"},"3291":{"name":"上虞市"},"3292":{"name":"嵊州市"},"3293":{"name":"绍兴县"},"3294":{"name":"新昌县"},"3295":{"name":"诸暨市"},"3296":{"name":"椒江区"},"3297":{"name":"黄岩区"},"3298":{"name":"路桥区"},"3299":{"name":"温岭市"},"3300":{"name":"临海市"},"3301":{"name":"玉环县"},"3302":{"name":"三门县"},"3303":{"name":"天台县"},"3304":{"name":"仙居县"},"3305":{"name":"鹿城区"},"3306":{"name":"龙湾区"},"3307":{"name":"瓯海区"},"3308":{"name":"瑞安市"},"3309":{"name":"乐清市"},"3310":{"name":"洞头县"},"3311":{"name":"永嘉县"},"3312":{"name":"平阳县"},"3313":{"name":"苍南县"},"3314":{"name":"文成县"},"3315":{"name":"泰顺县"},"3316":{"name":"定海区"},"3317":{"name":"普陀区"},"3318":{"name":"岱山县"},"3319":{"name":"嵊泗县"},"3320":{"name":"衢州市"},"3321":{"name":"江山市"},"3322":{"name":"常山县"},"3323":{"name":"开化县"},"3324":{"name":"龙游县"},"3325":{"name":"合川区"},"3326":{"name":"江津区"},"3327":{"name":"南川区"},"3328":{"name":"永川区"},"3329":{"name":"南岸区"},"3330":{"name":"渝北区"},"3331":{"name":"万盛区"},"3332":{"name":"大渡口区"},"3333":{"name":"万州区"},"3334":{"name":"北碚区"},"3335":{"name":"沙坪坝区"},"3336":{"name":"巴南区"},"3337":{"name":"涪陵区"},"3338":{"name":"江北区"},"3339":{"name":"九龙坡区"},"3340":{"name":"渝中区"},"3341":{"name":"黔江开发区"},"3342":{"name":"长寿区"},"3343":{"name":"双桥区"},"3344":{"name":"綦江县"},"3345":{"name":"潼南县"},"3346":{"name":"铜梁县"},"3347":{"name":"大足县"},"3348":{"name":"荣昌县"},"3349":{"name":"璧山县"},"3350":{"name":"垫江县"},"3351":{"name":"武隆县"},"3352":{"name":"丰都县"},"3353":{"name":"城口县"},"3354":{"name":"梁平县"},"3355":{"name":"开县"},"3356":{"name":"巫溪县"},"3357":{"name":"巫山县"},"3358":{"name":"奉节县"},"3359":{"name":"云阳县"},"3360":{"name":"忠县"},"3361":{"name":"石柱"},"3362":{"name":"彭水"},"3363":{"name":"酉阳"},"3364":{"name":"秀山"},"3365":{"name":"沙田区"},"3366":{"name":"东区"},"3367":{"name":"观塘区"},"3368":{"name":"黄大仙区"},"3369":{"name":"九龙城区"},"3370":{"name":"屯门区"},"3371":{"name":"葵青区"},"3372":{"name":"元朗区"},"3373":{"name":"深水埗区"},"3374":{"name":"西贡区"},"3375":{"name":"大埔区"},"3376":{"name":"湾仔区"},"3377":{"name":"油尖旺区"},"3378":{"name":"北区"},"3379":{"name":"南区"},"3380":{"name":"荃湾区"},"3381":{"name":"中西区"},"3382":{"name":"离岛区"},"3383":{"name":"澳门"},"3384":{"name":"台北"},"3385":{"name":"高雄"},"3386":{"name":"基隆"},"3387":{"name":"台中"},"3388":{"name":"台南"},"3389":{"name":"新竹"},"3390":{"name":"嘉义"},"3391":{"name":"宜兰县"},"3392":{"name":"桃园县"},"3393":{"name":"苗栗县"},"3394":{"name":"彰化县"},"3395":{"name":"南投县"},"3396":{"name":"云林县"},"3397":{"name":"屏东县"},"3398":{"name":"台东县"},"3399":{"name":"花莲县"},"3400":{"name":"澎湖县"},"3401":{"name":"合肥"},"3402":{"name":"庐阳区"},"3403":{"name":"瑶海区"},"3404":{"name":"蜀山区"},"3405":{"name":"包河区"},"3406":{"name":"长丰县"},"3407":{"name":"肥东县"},"3408":{"name":"肥西县"}};

var LAreaData=[
    {
        "id": "1",
        "name": "Penang",
        "child": [
            {
                "id": "15",
                "name": "Georgetown",
                "child": [
                    {
                        "id": "469",
                        "name": "Malaysia"
                    }
                ]
            },
            {
                "id": "16",
                "name": "Jelutong"
            },
            {
                "id": "17",
                "name": "Gelugor"
            },
            {
                "id": "18",
                "name": "Bayan Lepas"
            },
            {
                "id": "19",
                "name": "Bayan Baru"
            },
            {
                "id": "20",
                "name": "Batu Maung"
            },
            {
                "id": "21",
                "name": "Balik Pulau "
            },
            {
                "id": "22",
                "name": "Paya Terubong"
            },
            {
                "id": "23",
                "name": "Ayer Itam"
            },
            {
                "id": "24",
                "name": "Tanjung Bungah"
            },
            {
                "id": "25",
                "name": "Tanjung Tokong"
            },
            {
                "id": "26",
                "name": "Batu Ferringhi "
            },
            {
                "id": "27",
                "name": "Teluk Kumbar"
            },
            {
                "id": "28",
                "name": "Teluk Bahang"
            },
            {
                "id": "29",
                "name": "Butterworth"
            },
            {
                "id": "30",
                "name": "Penaga"
            },
            {
                "id": "31",
                "name": "Kepala Batas"
            },
            {
                "id": "32",
                "name": "Tasek Gelugor"
            },
            {
                "id": "33",
                "name": "Permatang Pauh"
            },
            {
                "id": "34",
                "name": "Perai"
            },
            {
                "id": "35",
                "name": "Bukit Mertajam"
            },
            {
                "id": "36",
                "name": "Simpang Ampat"
            },
            {
                "id": "37",
                "name": "Sungai Jawi"
            },
            {
                "id": "38",
                "name": "Nibong Tebal"
            },
            {
                "id": "39",
                "name": "Kubang Semang"
            }
        ]
    },
    {
        "id": "2",
        "name": "Johor",
        "child": [
            {
                "id": "40",
                "name": "Nusajaya"
            },
            {
                "id": "41",
                "name": "Johor Bahru"
            },
            {
                "id": "42",
                "name": "Kulai"
            },
            {
                "id": "43",
                "name": "Senai"
            },
            {
                "id": "44",
                "name": "Bandar Tenggara"
            },
            {
                "id": "45",
                "name": "Gugusan Taib Andak"
            },
            {
                "id": "46",
                "name": "Pekan Nenas"
            },
            {
                "id": "47",
                "name": "Gelang Patah"
            },
            {
                "id": "48",
                "name": "Pengerang"
            },
            {
                "id": "49",
                "name": "Pasir Gudang"
            },
            {
                "id": "50",
                "name": "Masai"
            },
            {
                "id": "51",
                "name": "Ulu Tiram"
            },
            {
                "id": "52",
                "name": "Layang-layang"
            },
            {
                "id": "53",
                "name": "Kota Tinggi"
            },
            {
                "id": "54",
                "name": "Ayer Tawar 2"
            },
            {
                "id": "55",
                "name": "Ayer Tawar 3"
            },
            {
                "id": "56",
                "name": "Ayer Tawar 4"
            },
            {
                "id": "57",
                "name": "Ayer Tawar 5"
            },
            {
                "id": "58",
                "name": "Bandar Penawar"
            },
            {
                "id": "59",
                "name": "Pontian"
            },
            {
                "id": "60",
                "name": "Ayer Baloi"
            },
            {
                "id": "61",
                "name": "Benut"
            },
            {
                "id": "62",
                "name": "Kukup"
            },
            {
                "id": "63",
                "name": "Batu Pahat"
            },
            {
                "id": "64",
                "name": "Rengit"
            },
            {
                "id": "65",
                "name": "Senggarang"
            },
            {
                "id": "66",
                "name": "Seri Gading"
            },
            {
                "id": "67",
                "name": "Seri Medan"
            },
            {
                "id": "68",
                "name": "Parit Sulong"
            },
            {
                "id": "69",
                "name": "Semerah"
            },
            {
                "id": "70",
                "name": "Yong Peng"
            },
            {
                "id": "71",
                "name": "Muar"
            },
            {
                "id": "72",
                "name": "Parit Jawa"
            },
            {
                "id": "73",
                "name": "Bukit Pasir"
            },
            {
                "id": "74",
                "name": "Sungai Mati"
            },
            {
                "id": "75",
                "name": "Panchor"
            },
            {
                "id": "76",
                "name": "Pagoh"
            },
            {
                "id": "77",
                "name": "Gerisek"
            },
            {
                "id": "78",
                "name": "Bukit Gambir"
            },
            {
                "id": "79",
                "name": "Tangkak"
            },
            {
                "id": "80",
                "name": "Segamat"
            },
            {
                "id": "81",
                "name": "Batu Anam"
            },
            {
                "id": "82",
                "name": "Jementah"
            },
            {
                "id": "83",
                "name": "Labis"
            },
            {
                "id": "84",
                "name": "Chaah"
            },
            {
                "id": "85",
                "name": "Kluang"
            },
            {
                "id": "86",
                "name": "Ayer Hitam"
            },
            {
                "id": "87",
                "name": "Simpang Rengam"
            },
            {
                "id": "88",
                "name": "Rengam"
            },
            {
                "id": "89",
                "name": "Parit Raja"
            },
            {
                "id": "90",
                "name": "Bekok"
            },
            {
                "id": "91",
                "name": "Paloh"
            },
            {
                "id": "92",
                "name": "Kahang"
            },
            {
                "id": "93",
                "name": "Mersing"
            },
            {
                "id": "94",
                "name": "Endau"
            }
        ]
    },
    {
        "id": "3",
        "name": "Kedah",
        "child": [
            {
                "id": "95",
                "name": "Alor Setar"
            },
            {
                "id": "96",
                "name": "Jitra"
            },
            {
                "id": "97",
                "name": "Changloon"
            },
            {
                "id": "98",
                "name": "Bukit Kayu Hitam"
            },
            {
                "id": "99",
                "name": "Kodiang"
            },
            {
                "id": "100",
                "name": "Ayer Hitam"
            },
            {
                "id": "101",
                "name": "Kepala Batas"
            },
            {
                "id": "102",
                "name": "Kuala Nerang"
            },
            {
                "id": "103",
                "name": "Pokok Sena"
            },
            {
                "id": "104",
                "name": "Pendang"
            },
            {
                "id": "105",
                "name": "Langgar"
            },
            {
                "id": "106",
                "name": "Kuala Kedah"
            },
            {
                "id": "107",
                "name": "Simpang Empat"
            },
            {
                "id": "108",
                "name": "Kota Sarang Semut"
            },
            {
                "id": "109",
                "name": "Yan"
            },
            {
                "id": "110",
                "name": "Langkawi"
            },
            {
                "id": "111",
                "name": "Sungai Petani"
            },
            {
                "id": "112",
                "name": "Bedong"
            },
            {
                "id": "113",
                "name": "Sik"
            },
            {
                "id": "114",
                "name": "Gurun"
            },
            {
                "id": "115",
                "name": "Jeniang"
            },
            {
                "id": "116",
                "name": "Merbok"
            },
            {
                "id": "117",
                "name": "Kota Kuala Muda"
            },
            {
                "id": "118",
                "name": "Kulim"
            },
            {
                "id": "119",
                "name": "Baling"
            },
            {
                "id": "120",
                "name": "Kuala Pegang"
            },
            {
                "id": "121",
                "name": "Kupang"
            },
            {
                "id": "122",
                "name": "Kuala Ketil"
            },
            {
                "id": "123",
                "name": "Padang Serai"
            },
            {
                "id": "124",
                "name": "Lunas"
            },
            {
                "id": "125",
                "name": "Karangan"
            },
            {
                "id": "126",
                "name": "Serdang"
            }
        ]
    },
    {
        "id": "4",
        "name": "Kelantan",
        "child": [
            {
                "id": "127",
                "name": "Kota Bharu"
            },
            {
                "id": "128",
                "name": "Bachok"
            },
            {
                "id": "129",
                "name": "Wakaf Bharu"
            },
            {
                "id": "130",
                "name": "Tumpat"
            },
            {
                "id": "131",
                "name": "Melor"
            },
            {
                "id": "132",
                "name": "Ketereh"
            },
            {
                "id": "133",
                "name": "Kem Desa Pahlawan"
            },
            {
                "id": "134",
                "name": "Pulai Chondong"
            },
            {
                "id": "135",
                "name": "Cherang Ruku"
            },
            {
                "id": "136",
                "name": "Pasir Puteh"
            },
            {
                "id": "137",
                "name": "Selising"
            },
            {
                "id": "138",
                "name": "Pasir Mas"
            },
            {
                "id": "139",
                "name": "Rantau Panjang"
            },
            {
                "id": "140",
                "name": "Tanah Merah"
            },
            {
                "id": "141",
                "name": "Jeli"
            },
            {
                "id": "142",
                "name": "Kuala Balah"
            },
            {
                "id": "143",
                "name": "Ayer Lanas"
            },
            {
                "id": "144",
                "name": "Kuala krai"
            },
            {
                "id": "145",
                "name": "Dabong"
            },
            {
                "id": "146",
                "name": "Gua Musang"
            },
            {
                "id": "147",
                "name": "Temangan"
            },
            {
                "id": "148",
                "name": "Machang"
            }
        ]
    },
    {
        "id": "5",
        "name": "Kuala Lumpur",
        "child": [
            {
                "id": "149",
                "name": "Kuala Lumpur"
            },
            {
                "id": "150",
                "name": "Gombak"
            },
            {
                "id": "151",
                "name": "Setapak"
            },
            {
                "id": "152",
                "name": "Pandan"
            },
            {
                "id": "153",
                "name": "Cheras"
            },
            {
                "id": "154",
                "name": "Bukit Bintang"
            },
            {
                "id": "155",
                "name": "Titiwangsa"
            },
            {
                "id": "156",
                "name": "Setiawangsa"
            },
            {
                "id": "157",
                "name": "Wangsa Maju"
            },
            {
                "id": "158",
                "name": "Batu"
            },
            {
                "id": "159",
                "name": "Kepong"
            },
            {
                "id": "160",
                "name": "Segambut"
            },
            {
                "id": "161",
                "name": "Lembah Pantai"
            },
            {
                "id": "162",
                "name": "Seputeh"
            },
            {
                "id": "163",
                "name": "Bandar Tun Razak"
            }
        ]
    },
    {
        "id": "6",
        "name": "Melaka",
        "child": [
            {
                "id": "164",
                "name": "Melaka"
            },
            {
                "id": "165",
                "name": "Ayer Keroh"
            },
            {
                "id": "166",
                "name": "Durian Tunggal"
            },
            {
                "id": "167",
                "name": "Kem Trendak"
            },
            {
                "id": "168",
                "name": "Sungai Udang"
            },
            {
                "id": "169",
                "name": "Tanjong Kling"
            },
            {
                "id": "170",
                "name": "Jasin"
            },
            {
                "id": "171",
                "name": "Asahan"
            },
            {
                "id": "172",
                "name": "Bemban"
            },
            {
                "id": "173",
                "name": "Merlimau"
            },
            {
                "id": "174",
                "name": "Sungai Rambai"
            },
            {
                "id": "175",
                "name": "Selandar"
            },
            {
                "id": "176",
                "name": "Alor Gajah"
            },
            {
                "id": "177",
                "name": "Lubok China"
            },
            {
                "id": "178",
                "name": "Kuala Sungai Baru"
            },
            {
                "id": "179",
                "name": "Masjid Tanah"
            }
        ]
    },
    {
        "id": "7",
        "name": "Negeri Sembilan",
        "child": [
            {
                "id": "180",
                "name": "Seremban"
            },
            {
                "id": "181",
                "name": "Port Dickson"
            },
            {
                "id": "182",
                "name": "Rantau "
            },
            {
                "id": "183",
                "name": "Si Rusa"
            },
            {
                "id": "184",
                "name": "Linggi"
            },
            {
                "id": "185",
                "name": "Rembau"
            },
            {
                "id": "186",
                "name": "Kota"
            },
            {
                "id": "187",
                "name": "Tanjong Ipoh"
            },
            {
                "id": "188",
                "name": "Kuala Klawang"
            },
            {
                "id": "189",
                "name": "Mantin"
            },
            {
                "id": "190",
                "name": "Bandar Baru Enstek"
            },
            {
                "id": "191",
                "name": "Nilai"
            },
            {
                "id": "192",
                "name": "Labu"
            },
            {
                "id": "193",
                "name": "Kuala Pilah"
            },
            {
                "id": "194",
                "name": "Bahau"
            },
            {
                "id": "195",
                "name": "Bandar Seri Jempol"
            },
            {
                "id": "196",
                "name": "Batu Kikir"
            },
            {
                "id": "197",
                "name": "Simpang Pertang"
            },
            {
                "id": "198",
                "name": "Simpang Durian"
            },
            {
                "id": "199",
                "name": "Tampin"
            },
            {
                "id": "200",
                "name": "Johol"
            },
            {
                "id": "201",
                "name": "Gemencheh"
            },
            {
                "id": "202",
                "name": "Gemas"
            },
            {
                "id": "203",
                "name": "Pusat Bandar Palong"
            },
            {
                "id": "204",
                "name": "Rompin"
            }
        ]
    },
    {
        "id": "8",
        "name": "Pahang",
        "child": [
            {
                "id": "205",
                "name": "Kuantan"
            },
            {
                "id": "206",
                "name": "Bukit Goh"
            },
            {
                "id": "207",
                "name": "Balok"
            },
            {
                "id": "208",
                "name": "Sungai Lembing"
            },
            {
                "id": "209",
                "name": "Gambang"
            },
            {
                "id": "210",
                "name": "Bandar Pusat Jengka"
            },
            {
                "id": "211",
                "name": "Maran"
            },
            {
                "id": "212",
                "name": "Pekan"
            },
            {
                "id": "213",
                "name": "Muadzam Shah"
            },
            {
                "id": "214",
                "name": "Kuala Rompin"
            },
            {
                "id": "215",
                "name": "Bandar Tun Abdul Razak"
            },
            {
                "id": "216",
                "name": "Jerantut"
            },
            {
                "id": "217",
                "name": "Damak"
            },
            {
                "id": "218",
                "name": "Jerantut"
            },
            {
                "id": "219",
                "name": "Padang Tengku"
            },
            {
                "id": "220",
                "name": "Kuala Lipis"
            },
            {
                "id": "221",
                "name": "Benta"
            },
            {
                "id": "222",
                "name": "Dong"
            },
            {
                "id": "223",
                "name": "Raub"
            },
            {
                "id": "224",
                "name": "Sungai Ruan"
            },
            {
                "id": "225",
                "name": "Sungai Koyan"
            },
            {
                "id": "226",
                "name": "Sega"
            },
            {
                "id": "227",
                "name": "Temerloh"
            },
            {
                "id": "228",
                "name": "Kuala Krau"
            },
            {
                "id": "229",
                "name": "Chenor"
            },
            {
                "id": "230",
                "name": "Bandar Bera"
            },
            {
                "id": "231",
                "name": "Triang"
            },
            {
                "id": "232",
                "name": "Kemayan"
            },
            {
                "id": "233",
                "name": "Mentakab"
            },
            {
                "id": "234",
                "name": "Lanchang"
            },
            {
                "id": "235",
                "name": "Karak"
            },
            {
                "id": "236",
                "name": "Bentong"
            },
            {
                "id": "237",
                "name": "Lurah Bilut"
            },
            {
                "id": "238",
                "name": "Tanah Rata"
            },
            {
                "id": "239",
                "name": "Brinchang"
            },
            {
                "id": "240",
                "name": "Ringlet"
            },
            {
                "id": "241",
                "name": "Bukit Fraser"
            },
            {
                "id": "242",
                "name": "Genting Highlands"
            }
        ]
    },
    {
        "id": "9",
        "name": "Perak",
        "child": [
            {
                "id": "243",
                "name": "Ipoh"
            },
            {
                "id": "244",
                "name": "Batu Gajah"
            },
            {
                "id": "245",
                "name": "Sungai Siput"
            },
            {
                "id": "246",
                "name": "Ulu Kinta"
            },
            {
                "id": "247",
                "name": "Chemor"
            },
            {
                "id": "248",
                "name": "Tanjong Rambutan"
            },
            {
                "id": "249",
                "name": "Kampung Kepayang"
            },
            {
                "id": "250",
                "name": "Pusing"
            },
            {
                "id": "251",
                "name": "Gopeng"
            },
            {
                "id": "252",
                "name": "Malim Nawar"
            },
            {
                "id": "253",
                "name": "Tronoh"
            },
            {
                "id": "254",
                "name": "Tanjung Tualang"
            },
            {
                "id": "255",
                "name": "Jeram"
            },
            {
                "id": "256",
                "name": "Kampar"
            },
            {
                "id": "257",
                "name": "Mambang Di Awan"
            },
            {
                "id": "258",
                "name": "Sitiawan"
            },
            {
                "id": "259",
                "name": "Seri Manjong"
            },
            {
                "id": "260",
                "name": "Lumut"
            },
            {
                "id": "261",
                "name": "Pangkor"
            },
            {
                "id": "262",
                "name": "Ayer Tawar"
            },
            {
                "id": "263",
                "name": "Changkat Keruing"
            },
            {
                "id": "264",
                "name": "Bota"
            },
            {
                "id": "265",
                "name": "Bandar Seri Iskandar"
            },
            {
                "id": "266",
                "name": "Bruas"
            },
            {
                "id": "267",
                "name": "Parit"
            },
            {
                "id": "268",
                "name": "Lambor Kanan"
            },
            {
                "id": "269",
                "name": "Kuala Kangsar"
            },
            {
                "id": "270",
                "name": "Pengkalan Hulu"
            },
            {
                "id": "271",
                "name": "Intan "
            },
            {
                "id": "272",
                "name": "Gerik"
            },
            {
                "id": "273",
                "name": "Lenggong"
            },
            {
                "id": "274",
                "name": "Sauk"
            },
            {
                "id": "275",
                "name": "Enggor"
            },
            {
                "id": "276",
                "name": "Padang Rengas"
            },
            {
                "id": "277",
                "name": "Manong"
            },
            {
                "id": "278",
                "name": "Taiping"
            },
            {
                "id": "279",
                "name": "Selama"
            },
            {
                "id": "280",
                "name": "Rantau Panjang"
            },
            {
                "id": "281",
                "name": "Parit Buntar"
            },
            {
                "id": "282",
                "name": "Tanjong Piandang"
            },
            {
                "id": "283",
                "name": "Bagan Serai"
            },
            {
                "id": "284",
                "name": "Kuala Kurau"
            },
            {
                "id": "285",
                "name": "Simpang Ampat Semanggol"
            },
            {
                "id": "286",
                "name": "Batu Kurau"
            },
            {
                "id": "287",
                "name": "Kamunting"
            },
            {
                "id": "288",
                "name": "Kuala Sepetang"
            },
            {
                "id": "289",
                "name": "Simpang"
            },
            {
                "id": "290",
                "name": "Matang"
            },
            {
                "id": "291",
                "name": "Trong"
            },
            {
                "id": "292",
                "name": "Changkat Jering"
            },
            {
                "id": "293",
                "name": "Pantai Remis"
            },
            {
                "id": "294",
                "name": "Bandar Baharu"
            },
            {
                "id": "295",
                "name": "Tapah"
            },
            {
                "id": "296",
                "name": "Chenderiang"
            },
            {
                "id": "297",
                "name": "Temoh"
            },
            {
                "id": "298",
                "name": "Tapah Road"
            },
            {
                "id": "299",
                "name": "Bidor"
            },
            {
                "id": "300",
                "name": "Sungkai"
            },
            {
                "id": "301",
                "name": "Trolak"
            },
            {
                "id": "302",
                "name": "Slim River"
            },
            {
                "id": "303",
                "name": "Tanjong Malim"
            },
            {
                "id": "304",
                "name": "Behrang Stesen"
            },
            {
                "id": "305",
                "name": "Teluk Intan"
            },
            {
                "id": "306",
                "name": "Bagan Datoh"
            },
            {
                "id": "307",
                "name": "Selekoh"
            },
            {
                "id": "308",
                "name": "Sungai Sumun"
            },
            {
                "id": "309",
                "name": "Hutan Melintang"
            },
            {
                "id": "310",
                "name": "Ulu Bernam"
            },
            {
                "id": "311",
                "name": "Chenderong Balai"
            },
            {
                "id": "312",
                "name": "Langkap"
            },
            {
                "id": "313",
                "name": "Chikus"
            },
            {
                "id": "314",
                "name": "Kampung Gajah"
            }
        ]
    },
    {
        "id": "10",
        "name": "Perlis",
        "child": [
            {
                "id": "315",
                "name": "Kangar"
            },
            {
                "id": "316",
                "name": "Kuala Perlis"
            },
            {
                "id": "317",
                "name": "Padang Besar"
            },
            {
                "id": "318",
                "name": "Kaki Bukit"
            },
            {
                "id": "319",
                "name": "Kangar"
            },
            {
                "id": "320",
                "name": "Arau"
            },
            {
                "id": "321",
                "name": "Simpang Ampat"
            }
        ]
    },
    {
        "id": "11",
        "name": "Selangor",
        "child": [
            {
                "id": "322",
                "name": "Shah Alam"
            },
            {
                "id": "323",
                "name": "Klang"
            },
            {
                "id": "324",
                "name": "Pelabuhan Klang"
            },
            {
                "id": "325",
                "name": "Kapar"
            },
            {
                "id": "326",
                "name": "Bandar Puncak Alam"
            },
            {
                "id": "327",
                "name": "Telok Panglima Garang"
            },
            {
                "id": "328",
                "name": "Jenjarom"
            },
            {
                "id": "329",
                "name": "Banting"
            },
            {
                "id": "330",
                "name": "Tanjong Sepat"
            },
            {
                "id": "331",
                "name": "Pulau Indah "
            },
            {
                "id": "332",
                "name": "Pulau Carey"
            },
            {
                "id": "333",
                "name": "Kajang"
            },
            {
                "id": "334",
                "name": "Hulu Langat"
            },
            {
                "id": "335",
                "name": "Cheras"
            },
            {
                "id": "336",
                "name": "Seri Kembangan"
            },
            {
                "id": "337",
                "name": "Semenyih"
            },
            {
                "id": "338",
                "name": "Bangi"
            },
            {
                "id": "339",
                "name": "Bandar Baru Bangi"
            },
            {
                "id": "340",
                "name": "Beranang"
            },
            {
                "id": "341",
                "name": "Dengkil"
            },
            {
                "id": "342",
                "name": "Sepang"
            },
            {
                "id": "343",
                "name": "Sungai Pelek"
            },
            {
                "id": "344",
                "name": "Kuala Kubu Baru"
            },
            {
                "id": "345",
                "name": "Kerling"
            },
            {
                "id": "346",
                "name": "Rasa"
            },
            {
                "id": "347",
                "name": "Batang Kali"
            },
            {
                "id": "348",
                "name": "Kuala Selangor"
            },
            {
                "id": "349",
                "name": "Sungai Ayer Tawar"
            },
            {
                "id": "350",
                "name": "Sabuk Bernam"
            },
            {
                "id": "351",
                "name": "Sungai Besar"
            },
            {
                "id": "352",
                "name": "Sekinchan"
            },
            {
                "id": "353",
                "name": "Tanjong Karang"
            },
            {
                "id": "354",
                "name": "Batang Berjuntai"
            },
            {
                "id": "355",
                "name": "Bukit Rotan"
            },
            {
                "id": "356",
                "name": "Jeram"
            },
            {
                "id": "357",
                "name": "Petaling Jaya"
            },
            {
                "id": "358",
                "name": "Sungai Buloh"
            },
            {
                "id": "359",
                "name": "Puchong"
            },
            {
                "id": "360",
                "name": "Subang Jaya"
            },
            {
                "id": "361",
                "name": "Rawang"
            },
            {
                "id": "362",
                "name": "Batu Arang"
            },
            {
                "id": "363",
                "name": "Serendah"
            },
            {
                "id": "364",
                "name": "Cyberjaya"
            },
            {
                "id": "365",
                "name": "KLIA"
            },
            {
                "id": "366",
                "name": "Ampang"
            },
            {
                "id": "367",
                "name": "Batu Caves"
            }
        ]
    },
    {
        "id": "12",
        "name": "Terengganu",
        "child": [
            {
                "id": "368",
                "name": "Kuala Terengganu"
            },
            {
                "id": "369",
                "name": "Bukit Payong"
            },
            {
                "id": "370",
                "name": "Chalok"
            },
            {
                "id": "371",
                "name": "Sungai Tong"
            },
            {
                "id": "372",
                "name": "Marang"
            },
            {
                "id": "373",
                "name": "Kuala Berang"
            },
            {
                "id": "374",
                "name": "Ajil"
            },
            {
                "id": "375",
                "name": "Jerteh"
            },
            {
                "id": "376",
                "name": "Permaisuri"
            },
            {
                "id": "377",
                "name": "Kampung Raja"
            },
            {
                "id": "378",
                "name": "Kuala Besut"
            },
            {
                "id": "379",
                "name": "Dungun"
            },
            {
                "id": "380",
                "name": "Paka"
            },
            {
                "id": "381",
                "name": "Bukit Besi "
            },
            {
                "id": "382",
                "name": "Ketengah Jaya"
            },
            {
                "id": "383",
                "name": "Al Mukatfi Billah Shah"
            },
            {
                "id": "384",
                "name": "Cukai "
            },
            {
                "id": "385",
                "name": "Ayer Puteh"
            },
            {
                "id": "386",
                "name": "Ceneh"
            },
            {
                "id": "387",
                "name": "Kijal"
            },
            {
                "id": "388",
                "name": "Kemasek"
            },
            {
                "id": "389",
                "name": "Kerteh"
            }
        ]
    },
    {
        "id": "13",
        "name": "Sabah",
        "child": [
            {
                "id": "390",
                "name": "Kota Kinabalu"
            },
            {
                "id": "391",
                "name": "Putatan"
            },
            {
                "id": "392",
                "name": "Likas"
            },
            {
                "id": "393",
                "name": "Inanam"
            },
            {
                "id": "394",
                "name": "Tanjung Aru"
            },
            {
                "id": "395",
                "name": "Keningau"
            },
            {
                "id": "396",
                "name": "Kudat"
            },
            {
                "id": "397",
                "name": "Kota Marudu"
            },
            {
                "id": "398",
                "name": "Kota Belud"
            },
            {
                "id": "399",
                "name": "Tuaran"
            },
            {
                "id": "400",
                "name": "Tamparuli"
            },
            {
                "id": "401",
                "name": "Beverly"
            },
            {
                "id": "402",
                "name": "Tenghilan"
            },
            {
                "id": "403",
                "name": "Ranau"
            },
            {
                "id": "404",
                "name": "Penampang"
            },
            {
                "id": "405",
                "name": "Papar"
            },
            {
                "id": "406",
                "name": "Tambunan"
            },
            {
                "id": "407",
                "name": "Bongawan"
            },
            {
                "id": "408",
                "name": "Membakut"
            },
            {
                "id": "409",
                "name": "Kuala Penyu"
            },
            {
                "id": "410",
                "name": "Menumbok"
            },
            {
                "id": "411",
                "name": "Beaufort"
            },
            {
                "id": "412",
                "name": "Sipitang"
            },
            {
                "id": "413",
                "name": "Tenom"
            },
            {
                "id": "414",
                "name": "Nabawan"
            },
            {
                "id": "415",
                "name": "Sandakan"
            },
            {
                "id": "416",
                "name": "Beluran"
            },
            {
                "id": "417",
                "name": "Kota Kinabatangan"
            },
            {
                "id": "418",
                "name": "Pamol"
            },
            {
                "id": "419",
                "name": "Tawau"
            },
            {
                "id": "420",
                "name": "Lahad Datu"
            },
            {
                "id": "421",
                "name": "Kunak"
            },
            {
                "id": "422",
                "name": "Semporna"
            }
        ]
    },
    {
        "id": "14",
        "name": "Sarawak",
        "child": [
            {
                "id": "423",
                "name": "Kuching"
            },
            {
                "id": "424",
                "name": "Bau"
            },
            {
                "id": "425",
                "name": "Siburan"
            },
            {
                "id": "426",
                "name": "Kota Samarahan"
            },
            {
                "id": "427",
                "name": "Lundu"
            },
            {
                "id": "428",
                "name": "Asajaya"
            },
            {
                "id": "429",
                "name": "Serian"
            },
            {
                "id": "430",
                "name": "Simunjan"
            },
            {
                "id": "431",
                "name": "Sebuyau"
            },
            {
                "id": "432",
                "name": "Lingga"
            },
            {
                "id": "433",
                "name": "Pusa"
            },
            {
                "id": "434",
                "name": "Sri Aman"
            },
            {
                "id": "435",
                "name": "Roban"
            },
            {
                "id": "436",
                "name": "Saratok"
            },
            {
                "id": "437",
                "name": "Debak"
            },
            {
                "id": "438",
                "name": "Spaoh"
            },
            {
                "id": "439",
                "name": "Betong"
            },
            {
                "id": "440",
                "name": "Engkilili"
            },
            {
                "id": "441",
                "name": "Lubok Antu"
            },
            {
                "id": "442",
                "name": "Sibu"
            },
            {
                "id": "443",
                "name": "Sarikei"
            },
            {
                "id": "444",
                "name": "Belawai"
            },
            {
                "id": "445",
                "name": "Daro"
            },
            {
                "id": "446",
                "name": "Matu"
            },
            {
                "id": "447",
                "name": "Dalat"
            },
            {
                "id": "448",
                "name": "Balingian"
            },
            {
                "id": "449",
                "name": "Mukah"
            },
            {
                "id": "450",
                "name": "Bintangor"
            },
            {
                "id": "451",
                "name": "Julau"
            },
            {
                "id": "452",
                "name": "Kanowit"
            },
            {
                "id": "453",
                "name": "Kapit"
            },
            {
                "id": "454",
                "name": "Song"
            },
            {
                "id": "455",
                "name": "Belaga"
            },
            {
                "id": "456",
                "name": "Bintulu"
            },
            {
                "id": "457",
                "name": "Sebauh"
            },
            {
                "id": "458",
                "name": "Tatau"
            },
            {
                "id": "459",
                "name": "Miri"
            },
            {
                "id": "460",
                "name": "Baram"
            },
            {
                "id": "461",
                "name": "Lutong"
            },
            {
                "id": "462",
                "name": "Bekenu"
            },
            {
                "id": "463",
                "name": "Niah"
            },
            {
                "id": "464",
                "name": "Long Lama"
            },
            {
                "id": "465",
                "name": "Limbang"
            },
            {
                "id": "466",
                "name": "Nanga Medamit"
            },
            {
                "id": "467",
                "name": "Sundar"
            },
            {
                "id": "468",
                "name": "Lawas"
            }
        ]
    }
];