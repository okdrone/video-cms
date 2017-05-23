// ### Video display
var videoDom = document.getElementById("J_prismPlayer");
if(videoDom) {

    var dpr = $('html').attr('data-dpr');
    var v_height = '350px';

    if (dpr !== undefined && dpr > 2) {
        v_height = '525px';
    }

    // 初始化播放器
    var player = new prismplayer({
        id: "J_prismPlayer", // 容器id
        source: $('#video_link').val(),// 视频地址
        cover: $('#video_thumb').val(),// 视频地址
        autoplay: false,    //自动播放：否
        trackLog: false,
        playsinline: true,
        width: "100%",       // 播放器宽度
        height: v_height      // 播放器高度
    });

    // 监听播放器的pause事件
    player.on("play", function () {
        console.log('play');

        if($('#cu').val() === '1'){
            player.pause();
            complete_info();
        } else {

            // Collect play action
            var collectData = {};
            collectData.id = $('#video_id').val().trim();
            collectData.action = 'play';
            collectData.time = player.getCurrentTime();
            collect(collectData);
        }


    });
    player.on("pause", function () {
        console.log('pause');
        var currentTime = player.getCurrentTime();

        // Collect pause action
        var collectData = {};
        collectData.id = $('#video_id').val().trim();
        collectData.action = 'pause';
        collectData.time = currentTime;
        collect(collectData);
    });
    player.on('ended', function () {
        question_dialog();
        var testBtn = $('#test_btn');
        var disableBtn = $(testBtn).find('.disable');
        $(disableBtn).hide();
        var enableBtn = $(testBtn).find('.enable');
        $(enableBtn).show();
        $(enableBtn).bind('click', question_dialog);

        // Collect ended action
        var collectData = {};
        collectData.id = $('#video_id').val().trim();
        collectData.action = 'end';
        collectData.time = player.getCurrentTime();
        collect(collectData);
    });

    // Collect open action
    var collectData = {};
    collectData.id = $('#video_id').val().trim();
    collectData.action = 'open';
    collect(collectData);
}

function question_dialog() {
    $('#question-confirm').dialog({
        resizable: false,
        height: "auto",
        width: "9.0625rem",
        modal: true,
        buttons: [{
            text: "马上开始",
            class: 'open',
            click: function () {

                var video_id = $('#video_id').val();
                console.log(video_id);

                window.location = '/video-cms/index.php?g=Video&m=Index&a=question&id=' + video_id;
                $(this).dialog("close");
            }
        }, {
            text: "稍后再说",
            class: 'close',
            click: function () {
                $(this).dialog("close");
            }
        }]
    });
}

// Just for test
//$('#test_btn').click(question_dialog);

// ### Control title slide down
$('#title_btn').on('click', function () {
    if($(this).hasClass('up')){
        $(this).removeClass('up');
        $(this).addClass('down');
        $('#title_desc').show('slow');
    } else if ($(this).hasClass('down')) {
        $(this).removeClass('down');
        $(this).addClass('up');
        $('#title_desc').hide('slow');
    }
});


// ### Complete user info
//if($('#cu').val() === '1'){
function complete_info() {
    $('#complete_info').dialog({
        resizable: false,
        height: "auto",
        width: "9.0625rem",
        modal: true,
        buttons: [{
            text: "确定",
            class: 'ok',
            click: function () {
                if(!$('#agreement').is(':checked')){
                    alert('请先阅读并同意我们的服务协议');
                    return false;
                }
                var real_name = $('#real_name').val();
                var sex = $('#sex').val();
                var age = $('#age').val();
                var phone = $('#phone').val();
                if(real_name === ''){
                    alert('请输入真实姓名');
                    return false;
                }
                if(sex === ''){
                    alert('请输入性别');
                    return false;
                }
                if(age === ''){
                    alert('请输入年龄');
                    return false;
                }
                if(phone === ''){
                    alert('请输入手机号码');
                    return false;
                }

                $.getJSON('/video-cms/index.php?g=Video&m=Index&a=complete_info&' + $('#complete-form').serialize(),function (data, status) {
                    if(data !== undefined && data.code === 0){
                        $('#cu').val('');
                    }
                });
                $(this).dialog("close");
            }
        }]
    });
}

function collect(data){
    $.get('/video-cms/index.php?g=Video&m=Collect&a=video&' + $.param(data), function (data) {
        console.log(data);
    })
}