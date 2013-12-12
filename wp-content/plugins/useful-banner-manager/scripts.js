function useful_banner_manager_rotate_banners(useful_banner_manager_banners_rotation_block, interval_between_rotations){
    jQuery(function($){
        $("#"+useful_banner_manager_banners_rotation_block+" .useful_banner_manager_rotating_banner").each(function(){
            if($(this).css("display")!="none"){
                if($(this).next().html()!=null){
                    $(this).fadeOut(1000,function(){
                        $(this).next().fadeIn(1000);
                    });
                }else{
                    $("#"+useful_banner_manager_banners_rotation_block+" .useful_banner_manager_rotating_banner:last").fadeOut(1000,function(){
                        $("#"+useful_banner_manager_banners_rotation_block+" .useful_banner_manager_rotating_banner:first").fadeIn(1000);
                    });
                }
            }
        });
    });

    setTimeout("useful_banner_manager_rotate_banners('"+useful_banner_manager_banners_rotation_block+"',"+interval_between_rotations+")",interval_between_rotations);
}