function insert_useful_banner_manager_shortcode() {
	var shortcode = '[useful_banner_manager';

    var banners_ids = document.getElementsByName('banners_ids[]');

    if(banners_ids.length>0){
        banners_shortcode = '';

        for(var i=0; i<banners_ids.length; i++){
            if(banners_ids[i].checked){
                banners_shortcode += ','+banners_ids[i].value;
            }
        }

        if(banners_shortcode!=''){
            shortcode += ' banners='+banners_shortcode.substring(1);
        }
    }

    if(document.getElementById('rotate').checked){
        shortcode = shortcode.replace('useful_banner_manager','useful_banner_manager_banner_rotation')

        var interval = parseInt(document.getElementById('interval').value);

        if(isNaN(interval)==false && interval>0){
            shortcode += ' interval=' + interval;
        }

        var width = parseInt(document.getElementById('width').value);

        if(isNaN(width)==false && width>0){
            shortcode += ' width=' + width;
        }

        var height = parseInt(document.getElementById('height').value);

        if(isNaN(height)==false && height>0){
            shortcode += ' height=' + height;
        }

        if(document.getElementById('orderby').checked){
            shortcode += ' orderby=rand';
        }
    }else{
        var count = parseInt(document.getElementById('count').value);

        if(isNaN(count)==false && count>0){
            shortcode += ' count=' + count;
        }
    }

    shortcode += ']';

	if(window.tinyMCE) {
        window.tinyMCE.execInstanceCommand(window.tinyMCE.activeEditor.id, 'mceInsertContent', false, shortcode);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}