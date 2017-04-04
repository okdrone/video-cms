// ### Video display
var videoDom = document.getElementById("J_prismPlayer");
console.log(videoDom);
if(videoDom) {

    var dpr = $('html').attr('data-dpr');
    var v_height = '350px';

    if (dpr !== undefined && dpr > 2) {
        v_height = '525px';
    }

    // 初始化播放器
    var player = new prismplayer({
        id: "J_prismPlayer", // 容器id
        source: "{$video.video_link}",// 视频地址
        cover: "/data/upload/{$smeta.thumb}",// 视频地址
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
    player.on('ended', question_dialog);
}

function question_dialog() {
    $("#question-confirm").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Delete all items": function () {
                $(this).dialog("close");
            },
            Cancel: function () {
                $(this).dialog("close");
            }
        }
    });
}

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
})