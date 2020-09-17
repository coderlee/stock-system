layui.use(['element', 'layer', 'jquery', 'form'], function() {
    var element = layui.element, $ = layui.$ , form = layui.form;

    $('.statusToggle').click(function() {
        var dId = $(this).data('id');
        //切换评论显示状态
        $.get('/admin/news_discuss_show_toggle/' + dId, function(data) {
            if(data.type == 'ok') {
                layer.msg(data.message, {icon:1});
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                layer.msg(data.message, {icon:2});
            }
        });        
    });

    $('.discussDel').click(function() {
        var dId = $(this).data('id');
        layer.confirm(
            '真的确定要删除吗？'
            , function() {
                $.get('/admin/news_discuss_del/' + dId, function(data) {
                    if(data.type == 'ok') {
                        layer.msg(data.message, {icon:1});
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        layer.msg(data.message, {icon:2});
                    }
                });
            }
            , function() {
                layer.msg('感谢主公不杀之恩！');
            }
        );
    });
});