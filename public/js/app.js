$(function(){
    // Голосование за комментарии
    $(document).on('click', '.comm-up-id', function() {
        var comm_id = $(this).data('id');
        $.ajax({
            url: '/votes/' + comm_id,
            type: 'POST',
            data: {comm_id: comm_id},
        }).done(function(data) {
            $('#up' + comm_id + '.voters').addClass('active');
            $('#up' + comm_id).find('.score').html('+');
        });
    });

    // подписка на блог
    $(document).on("click", ".hide-space-id", function(){      
        var space_id  = $(this).data('id');  
        $.ajax({
            url: '/space/hide/' + space_id,
            type: 'POST',
            data: {space_id: space_id},
        }).done(function(data) {
            location.reload();
           // $('#up' + tag_id + '.voters').addClass('active');
            //$('#up' + tag_id).find('.score').html('+');
        });
    }); 
});