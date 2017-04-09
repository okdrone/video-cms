// ### Question list

var questionItem = $('.question-item');
var question_count = $(questionItem).length;
var question_selected_count = 0;

function count_selected_questions() {
    question_selected_count = 0;

    $(questionItem).each(function (i, e) {
        $(e).find('li').each(function (ii, ee) {
            if($(ee).hasClass('selected'))
                question_selected_count++;
        });
    });

    $('.question-status .text .x').html(question_selected_count);
    $('.question-status .text .y').html(question_count);

    var button = $('.question-status .btn button');
    if(question_selected_count === question_count){
        button.removeAttr('disabled');
    } else {
        button.attr('disabled', 'disabled');
    }
}

count_selected_questions();

$(questionItem).find('li').click(function () {
    $(this).parent().find('li').removeClass('selected');
    $(this).addClass('selected');
    count_selected_questions();
});

$('#question_submit').click(function () {

    var answers = [];

    $(questionItem).each(function (i, e) {
        var ques_id = $(e).attr('data');
        $(e).find('li').each(function (ii, ee) {
            if($(ee).hasClass('selected')){
                answers['\'' + ques_id + '\''] = $(ee).find('.option').attr('data');
            }
        });

    });

    console.log(answers);

    var video_id = $('#video_id').val();
    $.post('/index.php?g=Video&m=Index&a=answer&id=' + video_id, JSON.stringify(answers), function (a, b, c) {
        console.log(a,b,c);
    })
});