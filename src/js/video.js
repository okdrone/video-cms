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
        width: "100%",       // 播放器宽度
        height: v_height      // 播放器高度
    });

    // 监听播放器的pause事件
    player.on("play", function () {
        console.log('play');
    });
    player.on("pause", function () {
        console.log('pause');
    });
    player.on('ended', function () {
        question_dialog();
        var testBtn = $('#test_btn');
        var disableBtn = $(testBtn).find('.disable');
        $(disableBtn).hide();
        var enableBtn = $(testBtn).find('.enable');
        $(enableBtn).show();
        $(enableBtn).bind('click', question_dialog);
    });
}

function question_dialog() {
    $('#question-confirm').dialog({
        resizable: false,
        height: "auto",
        width: 580,
        modal: true,
        buttons: [{
            text: "马上开始",
            class: 'open',
            click: function () {
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
$('#test_btn').click(question_dialog);

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
if($('#cu').val() === 1){
    alert('You need to complete your info!');
}