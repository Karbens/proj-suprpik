<?php
         

class CP_CFTEMAIL_BaseClass {       

    protected $item = 1;
    
    /** installation functions */
    public function install($networkwide)  {
    	global $wpdb;
     
    	if (function_exists('is_multisite') && is_multisite()) {
    		// check if it is a network activation - if so, run the activation function for each blog id
    		if ($networkwide) {
    	                $old_blog = $wpdb->blogid;
    			// Get all blog ids
    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
    			foreach ($blogids as $blog_id) {
    				switch_to_blog($blog_id);
    				$this->_install();
    			}
    			switch_to_blog($old_blog);
    			return;
    		}	
    	} 
    	$this->_install();	
    }    
    
    function get_param($key)
    {
        if (isset($_GET[$key]) && $_GET[$key] != '')
            return $_GET[$key];
        else if (isset($_POST[$key]) && $_POST[$key] != '')
            return $_POST[$key];
        else 
            return '';
    }
    
    function is_administrator()
    {
        return current_user_can('manage_options');
    }
    
    function get_site_url()
    {
        $url = parse_url(get_site_url());
        return rtrim($url["path"],"/");
    }
    
    
    function get_FULL_site_url()
    {
        $url = parse_url(get_site_url());
        $url = rtrim($url["path"],"/");
        $pos = strpos($url, "://");
        if ($pos === false)
            $url = 'http://'.$_SERVER["HTTP_HOST"].$url;
        return $url;
    }
    
    function cleanJSON ($str)
    {
        $str = str_replace('&qquot;','"',$str);
        $str = str_replace('	',' ',$str);
        $str = str_replace("\n",'\n',$str);
        $str = str_replace("\r",'',$str);      
        return $str;        
    }
    
    function add_field_verify ($table, $field, $type = "text") 
    {
        global $wpdb;
        $results = $wpdb->get_results("SHOW columns FROM `".$table."` where field='".$field."'");    
        if (!count($results))
        {               
            $sql = "ALTER TABLE  `".$table."` ADD `".$field."` ".$type; 
            $wpdb->query($sql);
        }
    }    
    
    

    private $option_buffered_item = false;
    private $option_buffered_id = -1;

    function get_option ($field, $default_value)
    {   
        global $wpdb;        
        if ($this->option_buffered_id == $this->item)
            $value = $this->option_buffered_item->$field;
        else
        {  
           $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items." WHERE id=".$this->item );
           $value = $myrows[0]->$field;           
           $this->option_buffered_item = $myrows[0];
           $this->option_buffered_id  = $this->item;
        }
        if ($value == '' && $this->option_buffered_item->form_structure == '')
            $value = $default_value;
        return $value;
    }
    
       
} // end class

?>