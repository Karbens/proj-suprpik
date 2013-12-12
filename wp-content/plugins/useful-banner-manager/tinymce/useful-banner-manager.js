(function() {
   tinymce.create('tinymce.plugins.usefulbannermanager', {
      init : function(ed, url) {
          ed.addCommand('mceusefulbannermanager', function() {
        		ed.windowManager.open({
        			file : ajaxurl + '?action=useful_banner_manager',
        			inline : 1
        		}, {
        			plugin_url : url
        		});
          });

          ed.addButton('usefulbannermanager', {
                title : 'Useful Banner Manager',
                cmd : 'mceusefulbannermanager',
                image : url+'/useful-banner-manager-button.png',

          });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : 'Useful Banner Manager',
            author : 'Ruben Sargsyan',
            authorurl : 'http://www.rubensargsyan.com',
            infourl : 'http://www.rubensargsyan.com',
            version : '1.0'
         };
      }
   });
   tinymce.PluginManager.add('usefulbannermanager', tinymce.plugins.usefulbannermanager);
})();