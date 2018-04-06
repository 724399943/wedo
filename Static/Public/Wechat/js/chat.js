var bol = true;
var _emobol = true;
var textSending = false;

var conn = {};

conn = new WebIM.connection({
    isMultiLoginSessions: WebIM.config.isMultiLoginSessions,
    https: typeof WebIM.config.https === 'boolean' ? WebIM.config.https : location.protocol === 'https:',
    url: WebIM.config.xmppURL,
    isAutoLogin: false,
    heartBeatWait: WebIM.config.heartBeatWait,
    autoReconnectNumMax: WebIM.config.autoReconnectNumMax,
    autoReconnectInterval: WebIM.config.autoReconnectInterval,
});
//初始化信息保存在本地
var init = function(messageData){
    // var messageData = messageData;
    var messageList = localStorage['messageList'];
    console.log(messageData);
    /****处理本地信息*****/ 
    var is_end = true;
    if( !messageList ){
      messageList = [];
      var node = {};
      node.id = messageData.from;
      node.total = 1;
      switch( messageData.messageType ){
        case 'txt':
          node.type = 1;
          node.content = messageData.data;
          break;
        case 'img':
          node.type = 2;
          node.content = "[图片]";
      };
      // node.content = messageData.data;
      node.time = (messageData.ext.time) ? messageData.ext.time : new Date().getTime();
      messageList.push(node);
      // console.log(messageList);
    }
    else{
      var messageList = JSON.parse(messageList);
      for(var j=0; j<messageList.length; j++){
        console.log(messageData);
        console.log(messageList[j].id);
        if( messageData.from == messageList[j].id){
          //本地有用户id,加total,换content
          ++messageList[j].total;
          switch( messageData.messageType ){
            case 'txt':
            messageList[j].type = 1;
            messageList[j].content = messageData.data;
            break;
          case 'img':
            messageList[j].type = 2;
            messageList[j].content = "[图片]";
          };
          messageList[j].time = (messageData.ext.time) ? messageData.ext.time : new Date().getTime();
          is_end = false;
          break;
        }
      }
      //本地没有用户id的消息，拼接json数据
      if( is_end ){
        var node = {};
        node.id = messageData.from;
        node.total = 1;
        switch( messageData.messageType ){
          case 'txt':
            node.type = 1;
            node.content = messageData.data;
          break;
        case 'img':
            node.type = 2;
            node.content = "[图片]";
        };
        node.time = (messageData.ext.time) ? messageData.ext.time : new Date().getTime();
        messageList.push(node);
      }
    }
    localStorage['messageList'] = JSON.stringify(messageList);
    // console.log(localStorage['messageList']);
};
// listern，添加回调函数
conn.listen({
    onOpened: function (message) {          //连接成功回调，连接成功后才可以发送消息
        //如果isAutoLogin设置为false，那么必须手动设置上线，否则无法收消息
        // 手动上线指的是调用conn.setPresence(); 在本例中，conn初始化时已将isAutoLogin设置为true
        // 所以无需调用conn.setPresence();
        conn.setPresence();
        console.log("opened");
    },
    onTextMessage: function (message) {
        message.messageType = 'txt';
        console.log(message);
        init(message);
        // window.messageInfo = message;
        window.initMessage();
        // console.log(window.messageInfo);
        // 在此接收和处理消息，根据message.type区分消息来源，私聊或群组或聊天室
        reTextmsg(message);
    },  //收到文本消息
    onEmojiMessage: function (message) {
        // 当为WebIM添加了Emoji属性后，若发送的消息含WebIM.Emoji里特定的字符串，connection就会自动将
        // 这些字符串和其它文字按顺序组合成一个数组，每一个数组元素的结构为{type: 'emoji(或者txt)', data:''}
        // 当type='emoji'时，data表示表情图像的路径，当type='txt'时，data表示文本消息
        init(message);
        reEmojimsg(message);
        // window.messageInfo = message;
        window.initMessage();
    },   //收到表情消息
    onPictureMessage: function (message) {
        // console.log(message);
        message.messageType = 'img';
        // window.messageData = message;
        init(message);
        var options = {url: message.url};
        options.onFileDownloadComplete = function () {
            // 图片下载成功
            reImage(message);
            // window.messageInfo = message;
            window.initMessage();
            console.log('Image download complete!');
        };
        options.onFileDownloadError = function () {
            // 图片下载失败
            console.log('Image download failed!');
        };
        WebIM.utils.download.call(conn, options);       // 意义待查

    }, //收到图片消息
    onCmdMessage: function (message) {
        console.log('CMD');
    },     //收到命令消息
    onAudioMessage: function (message) {
        console.log("Audio");
    },   //收到音频消息
    onLocationMessage: function (message) {
        console.log("Location");
    },//收到位置消息
    onFileMessage: function (message) {
        console.log("File");
    },    //收到文件消息
    onVideoMessage: function (message) {
        var node = document.getElementById('privateVideo');
        var option = {
            url: message.url,
            headers: {
                'Accept': 'audio/mp4'
            },
            onFileDownloadComplete: function (response) {
                var objectURL = WebIM.utils.parseDownloadResponse.call(conn, response);
                node.src = objectURL;
            },
            onFileDownloadError: function () {
                console.log('File down load error.')
            }
        };
        WebIM.utils.download.call(conn, option);
    },   //收到视频消息
    onPresence: function (message) {
        switch (message.type) {
            case 'subscribe':                           // 对方请求添加好友
                // 同意对方添加好友
                document.getElementById('agreeFriends').onclick = function (message) {
                    conn.subscribed({
                        to: 'asdfghj',
                        message: "[resp:true]"
                    });
                    // 需要反向添加对方好友
                    conn.subscribe({
                        to: message.from,
                        message: "[resp:true]"
                    });
                };
                // 拒绝对方添加好友
                document.getElementById('rejectFriends').onclick = function (message) {
                    conn.unsubscribed({
                        to: message.from,
                        message: "rejectAddFriend"                  // 拒绝添加好友回复信息
                    });
                };

                break;
            case 'subscribed':                          // 对方同意添加好友，已方同意添加好友
                break;
            case 'unsubscribe':                         // 对方删除好友
                break;
            case 'unsubscribed':                        // 被拒绝添加好友，或被对方删除好友成功
                break;
            case 'joinChatRoomSuccess':                 // 成功加入聊天室
                console.log('join chat room success');
                break;
            case 'joinChatRoomFaild':                   // 加入聊天室失败
                console.log('join chat room faild');
                break;
            case 'joinPublicGroupSuccess':              // 意义待查
                console.log('join public group success', message.from);
                break;
        }
    },       //收到联系人订阅请求（加好友）、处理群组、聊天室被踢解散等消息
    onRoster: function (message) {
        console.log('Roster');
    },         //处理好友申请
    onInviteMessage: function (message) {
        console.log('Invite');
    },  //处理群组邀请
    onOnline: function () {
        console.log('onLine');
    },                  //本机网络连接成功
    onOffline: function () {
        console.log('offline');
    },                 //本机网络掉线
    onError: function (message) {
        console.log(message);
    },           //失败回调
    onBlacklistUpdate: function (list) {
        // 查询黑名单，将好友拉黑，将好友从黑名单移除都会回调这个函数，list则是黑名单现有的所有好友信息
        console.log(list);
    }     // 黑名单变动
});

// 登录
function chatLogin(username, password) {
    var options = { 
        apiUrl: WebIM.config.apiURL,
        user: username,
        pwd: password,
        appKey: WebIM.config.appkey
    };
    conn.open(options);
    // console.log(2);
}

// 发送消息
var sendPrivateText = function(userInfo, to) {
    var ext = {};
    ext['user'] = {};
    ext['user']['id'] = userInfo['id'];
    ext['user']['nickname'] = userInfo['nickname'];
    ext['user']['headimgurl'] = userInfo['headimgurl'];
    if (textSending) {
        return;
    }
    textSending = true;
    var msgInput = document.getElementById("talkInputId");
    var imsg = msgInput.innerText;
    
    if (imsg == null || imsg.length == 0) {
        textSending = false;
        return;
    }
    
    var id = conn.getUniqueId();                 // 生成本地消息id
    var msg = new WebIM.message('txt', id);      // 创建文本消息
    msg.set({
        msg: imsg,                  // 消息内容
        to: to,                          // 接收消息对象（用户id）
        roomType: false,
        action : 'action',                     //用户自定义，cmd消息必填
        ext :ext,    //用户自扩展的消息内容（群聊用法相同）
        success: function (id, serverMsgId) {
            var test = WebIM.utils.parseLink(WebIM.utils.parseEmoji(encode(imsg))); 
            time();
            var cont = "<div class='em-widget-right'><div class='em-widget-msg-wrapper'><i class='icon-corner-right'><img src="+ext['user'].headimgurl+"></i><div class='em-widget-msg-container em-widget-msg-txt'>"+ test +"</div><div class='emcaoTips'><div class='triangle-up'></div><div class='operation'><span class='Del'>删除</span> <span>|</span> <span class='Copy'>复制</span></div></div></div></div>";
            $('.em-widget-chat').append(cont);
            saveChat(to, 1, test, '');
        }
    });
    msg.body.chatType = 'singleChat';
    conn.send(msg.body);
    
    msgInput.innerText = "";
    msgInput.focus();
    setTimeout(function() {
        textSending = false;
    }, 1000);
}

// 单聊发送图片消息
var sendPrivateImg = function (userInfo, to) {
    var ext = {};
    ext['user'] = {};
    ext['user']['id'] = userInfo['id'];
    ext['user']['nickname'] = userInfo['nickname'];
    ext['user']['headimgurl'] = userInfo['headimgurl'];

    var id = conn.getUniqueId();                   // 生成本地消息id
    var msg = new WebIM.message('img', id);        // 创建图片消息
    var input = document.getElementById('em-widget-img-input');  // 选择图片的input
    var file = WebIM.utils.getFileUrl(input);      // 将图片转化为二进制文件
    var allowType = {
        'jpg': true,
        'gif': true,
        'png': true,
        'bmp': true
    };
    if (file.filetype.toLowerCase() in allowType) {
        var option = {
            apiUrl: WebIM.config.apiURL,
            file: file,
            to: to,                       // 接收消息对象
            roomType: false,
            chatType: 'singleChat',
            action : 'action',                     //用户自定义，cmd消息必填
            ext :ext,    //用户自扩展的消息内容（群聊用法相同）
            onFileUploadError: function () {      // 消息上传失败
                console.log('onFileUploadError');
            },
            onFileUploadComplete: function () {   // 消息上传成功
                console.log('onFileUploadComplete');
            },
            success: function () {                // 消息发送成功
                console.log("发送成功");
                var _url = msg.value.url;
                time();
                var html = "<div class='em-widget-right'><div class='em-widget-msg-wrapper'><i class='icon-corner-right'><img src="+ext['user'].headimgurl+"></i><div class='em-widget-msg-container em-widget-msg-img'><a href='javascript:;''><img class='em-widget-imgview' src='"+_url+"'></a></div></div></div>";
                $('.em-widget-chat').append(html);
                saveChat(to, 2, '', _url);
            },
            flashUpload: WebIM.flashUpload
        };
        msg.set(option);
        conn.send(msg.body);
    }
};

// 显示表情
var showEmotionDialog = function() {    
    if(bol){
        $("#emotionUl").parent().removeClass('hide');
        bol = false;
    }else{
        $("#emotionUl").parent().addClass('hide');
        bol = true;
    }
    if(_emobol){
        loadEmotion();
        _emobol = false;
    }
};
$('#talkInputId').click(function(){
    $("#emotionUl").parent().addClass('hide');
    bol = true;
})
// 加载表情
var loadEmotion = function(){
    // Easemob.im.Helper.EmotionPicData设置表情的json数组
    var sjson = WebIM.Emoji,
        data = sjson.map,
        path = sjson.path;
    
    for ( var key in data) {
        var emotions = $('<img>').attr({
            "id" : key,
            "class" :　'e-face emoji',
            "src" : path + data[key],
            "style" : "cursor:pointer;",
            "data-value" : key
        }).click(function() {
            selectEmotionImg(this);
        });
        $('#emotionUl li').append(emotions).appendTo($('#emotionUl'));
    }

}
// 选择表情
var selectEmotionImg = function(selImg) {
    var txt = document.getElementById("talkInputId");
    txt.value = txt.value + selImg.id;
    txt.focus();
};

// 收到文字消息
var reTextmsg = function(message){
    var msg = message.data;
    var ext = message.ext;
    var cont = "<div class='em-widget-left'><div class='em-widget-msg-wrapper'><i class='icon-corner-left'><img src="+ext['headimgurl']+"></i><div class='em-widget-msg-container em-widget-msg-txt'>"+msg+"</div></div></div>"
    time();
    $('.em-widget-chat').append(cont);
    $("#Talker").text(ext.nickname);
    var talker = "<li><div class='headImgbox'><img src="+ext.headimgurl+"></div><span class='name'>"+ext.nickname+"</span></li>";
    $("#Jcontact").append(talker);
}
// 收到表情消息
var reEmojimsg = function(message){
    var _emoji = message.data;
    var ext = message.ext;
    var appendTo = [];
    for (var i = 0, l = _emoji.length; i < l; i++) {
        if(_emoji[i].type == "txt"){
            appendTo.push(_emoji[i].data);    
        }else{
            appendTo.push("<img class='emoji' src="+_emoji[i].data+">");
        }
    }
    var html = "<div class='em-widget-left'><div class='em-widget-msg-wrapper'><i class='icon-corner-left'><img src="+ext['headimgurl']+"></i><div class='em-widget-msg-container em-widget-msg-txt'>"+ appendTo.join('') +"</div></div></div>";
    time();
    $('.em-widget-chat').append(html);
    $('#Talker').text(ext.nickname);
    var talker = "<li><div class='headImgbox'><img src="+ext.headimgurl+"></div><span class='name'>"+ext.nickname+"</span></li>";
    $("#Jcontact").append(talker);
}
//收到图片消息
var reImage = function(message){
    var _url = message.url;
    var ext = message.ext;
    var html = "<div class='em-widget-left'><div class='em-widget-msg-wrapper'><i class='icon-corner-left'><img src="+ext['headimgurl']+"></i><div class='em-widget-msg-container em-widget-msg-img'><a href='javascript:;''><img class='em-widget-imgview' src='"+_url+"'></a></div></div></div>";
    time();
    $('.em-widget-chat').append(html);
    $('#Talker').text(ext.nickname);
    var talker = "<li><div class='headImgbox'><img src="+ext.headimgurl+"></div><span class='name'>"+ext.nickname+"</span></li>";
    $("#Jcontact").append(talker);
}

//消息时间
var time = function(){
    var timecont = "<div class='em-widget-date'><span>"+(new Date().getMonth()+1)+"月"+(new Date().getDate())+"日 "+(new Date().getHours())+":"+(new Date().getMinutes())+"</span></div>"
    $('.em-widget-chat').append(timecont);
}

var encode = function ( str ) {
    if ( !str || str.length === 0 ) return "";
    var s = '';
    s = str.replace(/&amp;/g, "&");
    s = s.replace(/<(?=[^o][^)])/g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    //s = s.replace(/\'/g, "&#39;");
    s = s.replace(/\"/g, "&quot;");
    s = s.replace(/\n/g, "</*br*/>");
    return s;
};

var saveChat = function (to_id,type,txt,uri) {
    var requestJson = {type:type};
    requestJson.content = ( type == 1 ) ? txt : uri;
    requestJson = JSON.stringify(requestJson);
    to_id = to_id.replace(/wedo_/, '');
    $.ajax({
        url: '/Chat/saveChat',
        type: 'POST',
        dataType: 'json',
        data: {to_id:to_id, content:requestJson}
    })
    .done(function() {
        console.log("success");
    });
}
WebIM.Emoji = {
    path: '/Static/Public/ImApi/img/faces/'  /*表情包路径*/
    , map: {
        '[):]': 'ee_1.png',
        '[:D]': 'ee_2.png',
        '[;)]': 'ee_3.png',
        '[:-o]': 'ee_4.png',
        '[:p]': 'ee_5.png',
        '[(H)]': 'ee_6.png',
        '[:@]': 'ee_7.png',
        '[:s]': 'ee_8.png',
        '[:$]': 'ee_9.png',
        '[:(]': 'ee_10.png',
        '[:\'(]': 'ee_11.png',
        '[:|]': 'ee_12.png',
        '[(a)]': 'ee_13.png',
        '[8o|]': 'ee_14.png',
        '[8-|]': 'ee_15.png',
        '[+o(]': 'ee_16.png',
        '[<o)]': 'ee_17.png',
        '[|-)]': 'ee_18.png',
        '[*-)]': 'ee_19.png',
        '[:-#]': 'ee_20.png',
        '[:-*]': 'ee_21.png',
        '[^o)]': 'ee_22.png',
        '[8-)]': 'ee_23.png',
        '[(|)]': 'ee_24.png',
        '[(u)]': 'ee_25.png',
        '[(S)]': 'ee_26.png',
        '[(*)]': 'ee_27.png',
        '[(#)]': 'ee_28.png',
        '[(R)]': 'ee_29.png',
        '[({)]': 'ee_30.png',
        '[(})]': 'ee_31.png',
        '[(k)]': 'ee_32.png',
        '[(F)]': 'ee_33.png',
        '[(W)]': 'ee_34.png',
        '[(D)]': 'ee_35.png'
    }
};