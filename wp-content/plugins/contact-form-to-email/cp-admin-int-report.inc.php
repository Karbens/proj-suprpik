<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = $_GET["cal"];

global $wpdb;

if ($this->item != 0)
    $myform = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id='.$this->item);


$current_page = intval($_GET["p"]);
if (!$current_page) $current_page = 1;
$records_per_page = 50;                                                                                  

$date_start = '';
$date_end = '';

$cond = '';
if ($_GET["search"] != '') $cond .= " AND (data like '%".$wpdb->escape($_GET["search"])."%' OR posted_data LIKE '%".$wpdb->escape($_GET["search"])."%')";
if ($_GET["dfrom"] != '') 
{ 
    $cond .= " AND (`time` >= '".$wpdb->escape($_GET["dfrom"])."')";
    $date_start = $_GET["dfrom"];
}    
if ($_GET["dto"] != '') 
{
    $cond .= " AND (`time` <= '".$wpdb->escape($_GET["dto"])." 23:59:59')";
    $date_end = $_GET["dto"];
}    
if ($this->item != 0) $cond .= " AND formid=".$this->item;

$events = $wpdb->get_results( "SELECT ipaddr,time,notifyto,posted_data FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );

// general initialization
$fields = array();
$fields["date"] = array();
$fields["ip"] = array();
$fields["notifyto"] = array();
foreach ($events as $item)
{
    $fields["date"]["k".substr($item->time,0,10)]++;
    $fields["time"]["k".substr($item->time,11,2)]++;
    $fields["notifyto"]["k".$item->notifyto]++;
    $fields["ip"]["k".$item->ipaddr]++;
    $params = unserialize($item->posted_data);
    foreach ($params as $param => $value)
        if (strlen($value) < 100)
            $fields[$param]["k".$value]++;    
}


// line graphs
$hourly_messages = '';
for ($i=0;$i<=23;$i++)
    if (isset($fields['time']['k'.($i<10?'0':'').$i])) 
        $hourly_messages .= $fields['time']['k'.($i<10?'0':'').$i].($i<23?',':'');
    else
        $hourly_messages .='0'.($i<23?',':'');
        
if ($date_start == '')        
    $date_start = substr(min(array_keys($fields["date"])),1);
if ($date_end == '')        
    $date_end = substr(max(array_keys($fields["date"])),1);

$daily_messages = '';
$date = $date_start;    
while ($date <= $date_end) 
{
    if (isset($fields['date']['k'.$date])) 
        $daily_messages .= ','.$fields['date']['k'.$date];
    else
        $daily_messages .=',0';
    $date = date("Y-m-d",strtotime($date." +1 day"));    
}   
$daily_messages = substr($daily_messages,1);

if (!isset($_GET["field"])) $_GET["field"] = 'time';

$color_array = array(/**'ff0000',*/'ff4500','ff6347','f08080', 'ff7f50', 'ff8c00', 'ffa500', 'ffa07a', 'fa8072', 'e9967a', 'f5deb3', 'ffe4c4', 'ffebcd', 'ffefd5');

?>
<link href="<?php echo plugins_url('css/style.css', __FILE__); ?>" type="text/css" rel="stylesheet" />   

<div class="wrap">
<h2><?php echo $this->plugin_name; ?> - Report</h2>

<input type="button" name="backbtn" value="Back to items list..." onclick="document.location='admin.php?page=<?php echo $this->menu_parameter; ?>';">

<div id="normal-sortables" class="meta-box-sortables">
 <hr />
 <h3>This report is for: <?php if ($this->item != 0) echo $myform[0]->form_name; else echo 'All forms'; ?></h3>
</div>


<form action="admin.php" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>" />
 <input type="hidden" name="cal" value="<?php echo $this->item; ?>" />
 <input type="hidden" name="report" value="1" />
 <input type="hidden" name="field" value="<?php echo esc_attr($_GET["field"]); ?>" />
 <nobr>Search for: <input type="text" name="search" value="<?php echo esc_attr($_GET["search"]); ?>" /> &nbsp; &nbsp; &nbsp;</nobr> 
 <nobr>From: <input type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr($_GET["dfrom"]); ?>" /> &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>To: <input type="text" id="dto" name="dto" value="<?php echo esc_attr($_GET["dto"]); ?>" /> &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>Item: <select id="cal" name="cal">
          <option value="0">[All Items]</option>
   <?php
    $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
    foreach ($myrows as $item)  
         echo '<option value="'.$item->id.'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.$item->form_name.'</option>'; 
   ?>
    </select></nobr>
 <nobr><span class="submit"><input type="submit" name="ds" value="Filter" /></span> &nbsp; &nbsp; &nbsp; 
 <span class="submit"><input type="submit" name="<?php echo $this->prefix; ?>_csv" value="Export to CSV" /></span></nobr>
</form>

<br />

<div style="border:1px solid black;width:320px;margin-right:10px;padding:0px;float:left;"> 
 <div style="border-bottom:1px solid black;padding:5px;background:#ECECEC;color:#21759B;font-weight: bold;">
   Submisions per day
 </div>
 <div class="canvas" id="cardiocontainer1" style="margin-left:10px;">
  <canvas id="cardio1"  width="300" height="200" questions='[{"color":"#00f","values":[<?php echo $daily_messages; ?>]}]'></canvas>
 </div>
 <div style="padding-right:5px;padding-left:5px;color:#888888;">* Submissions per day in the selected date range.<br />&nbsp;&nbsp; Days from <?php echo $date_start; ?> to <?php echo $date_end; ?>.</div>
</div> 

<div style="border:1px solid black;width:330px;margin:0px;padding:0px;float:left;"> 
 <div style="border-bottom:1px solid black;padding:5px;background:#ECECEC;color:#21759B;font-weight: bold;">
   Submisions per hour
 </div>
 <div class="canvas" id="cardiocontainer2" style="margin-left:10px;">
  <canvas id="cardio2"  width="312" height="200" questions='[{"color":"#00f","values":[<?php echo $hourly_messages; ?>]}]'></canvas>
 </div>
 <div style="padding-right:5px;padding-left:5px;color:#888888;">* Total submissions per hour in the selected date range.<br />&nbsp;&nbsp; Hours from 0 to 23.</div>
</div> 

 
<div style="clear:both"></div>
 
<hr /> 
<br />
<form action="admin.php" name="cfm_formrep" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>" />
 <input type="hidden" name="cal" value="<?php echo $this->item; ?>" />
 <input type="hidden" name="report" value="1" />
 <input type="hidden" name="search" value="<?php echo esc_attr($_GET["search"]); ?>" />
 <input type="hidden" id="dfrom" name="dfrom" value="<?php echo esc_attr($_GET["dfrom"]); ?>" />
 <input type="hidden" id="dto" name="dto" value="<?php echo esc_attr($_GET["dto"]); ?>" />    
 <strong>Select field for the report:</strong><br />
 <select name="field" onchange="document.cfm_formrep.submit();">
 <?php
  foreach ($fields as $item => $value)    
      echo '<option value="'.esc_attr($item).'"'.($_GET["field"]==$item?' selected':'').'>'.$item.'</option>';
 ?>
 </select>
 <br /><br />
</form>


<div id="dex_printable_contents">


<div style="border:1px solid black;width:650px;margin-right:10px;padding:0px;float:left;"> 
 <div style="border-bottom:1px solid black;padding:5px;background:#ECECEC;color:#21759B;font-weight: bold;">
   Report of values for: <em><?php echo $_GET["field"]; ?></em>
 </div>
 
<div style="padding:10px;">
<?php
  $arr = $fields[$_GET["field"]];
  arsort($arr, SORT_NUMERIC);
  $total = 0;
  $totalsize = 600;
  foreach ($arr as $item => $value)  
      $total += $value;
  $max = max($arr);
  $totalsize = round(600 / ($max/$total) );
  $count = 0;    
  foreach ($arr as $item => $value)
  {
      echo $value.' times: '.(strlen($item)>50?substr($item,1,50).'...':substr($item,1));
      echo '<div style="width:'.round($value/$total*$totalsize).'px;border:1px solid black;margin-bottom:3px;font-size:9px;background-color:#'.$color_array[$count].'">'.round($value/$total*100,2).'%</div>';       
      $count++;
      if ($count >= count($color_array)) $count = count($color_array)-1;
  }    
?>
</div>

 <div style="padding-right:5px;padding-left:5px;color:#888888;">&nbsp;&nbsp;* Number of times that appears each value. Percent in relation to the total of submissions.<br />&nbsp;&nbsp;&nbsp;&nbsp; Date range from <?php echo $date_start; ?> to <?php echo $date_end; ?>.</div>
</div>


<div style="clear:both"></div>


</div>

<p class="submit"><input type="button" name="pbutton" value="Print" onclick="do_dexapp_print();" /></p>

</div>


<script type="text/javascript">
 function do_select_rep(item)
 {
 }

 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>.cpnopr{display:none;};table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();    
 }
 
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 });
 
</script>


<script type='text/javascript' src='<?php echo plugins_url('js/excanvas.min.js', __FILE__); ?>'></script>
<script type="text/javascript">
var $ = jQuery.noConflict();
$j(document).ready(function(){
		    /////////////////////////canvas//////////////////////////
		    $(window).load(function(){
                drawGraph($("#cardio1"), $("#cardiocontainer1"));
                drawGraph($("#cardio2"), $("#cardiocontainer2"));
                function drawGraph(canvas, canvasContainer)
                {
		            if( typeof(G_vmlCanvasManager) != 'undefined' ){ G_vmlCanvasManager.init(); G_vmlCanvasManager.initElement(canvas[0]); }
		            ctx = canvas[0].getContext("2d"); 
		            var data = jQuery.parseJSON(canvas.attr("questions"));
		            var height = canvas.attr("height");
		            var width = canvas.attr("width");
		            var maxquestions = 0,maxpos = 0,minpos = 0,interval = 5;
		            
		            jQuery.each(data,function(index,v){
		                maxquestions = (maxquestions<v.values.length)?v.values.length:maxquestions;    
		                postmp = 0;
		                jQuery.each(v.values,function(index1,v1){
		                    maxpos = (maxpos<v1)?v1:maxpos;    
		                    minpos = (minpos>v1)?v1:minpos;                
		                }); 
		                    
		            });		        
		            maxpos = maxpos;//Math.ceil(maxpos/interval)*interval;
		            minpos = 0; //Math.floor(minpos/interval)*interval;		        
		            interval = Math.ceil(maxpos / 10);
		            total = maxpos - minpos + interval;	
		            h = Math.round(height/total); 
		            var start = 10;
		            var radius = 2;
		            if (maxquestions>1)
		                w = Math.round((width-start-radius)/(maxquestions-1));
		            else
		                w =  width/2;   
		            	 
		            if(ctx)
		            {
		                for (i=0;i<total/interval;i++)
		                {
		                    if ((maxpos-i*interval) >= 0) canvasContainer.append('<div class="legend" style="top:'+(parseInt((i*interval+interval/2)*h-5))+'px">'+(maxpos-i*interval)+'</div>');
		                    ctx.beginPath();
                            ctx.moveTo(start,Math.round((i*interval+interval/2)*h) );
                            ctx.lineTo(width,Math.round((i*interval+interval/2)*h) ); 
                            ctx.stroke();
		                }
		                jQuery.each(data,function(index,v){
		                    ctx.beginPath();
		                    ctx.strokeStyle = v.color;
		                    ctx.fillStyle = v.color;
		                     
		                    //ctx.moveTo(start,Math.round((maxpos+interval/2)*h) );
		                    var i = 0,j = 0;
		                    jQuery.each(v.values,function(index1,v1){
		                        j=-v1;   		 
		                        if (i!=0)                   
		                            ctx.lineTo(i*w+start,Math.round((maxpos+interval/2)*h+j*h));        
		                        else
		                            ctx.moveTo(i*w+start,Math.round((maxpos+interval/2)*h+j*h));    
		                        i++;	                
		                     });
		                
		                     ctx.stroke();
		                     var i = 0,j = 0;
		                     jQuery.each(v.values,function(index1,v1){
		                         j=-v1;   		                       	                    
		                         ctx.beginPath();
		                         ctx.arc(i*w+start,Math.round((maxpos+interval/2)*h+j*h), radius, 0, 2 * Math.PI, true); 
		                         ctx.fill();        
		                         i++;	                
		                     });
		                });
		            }
		        }
            });
		  
		    ////////////////////////end canvas///////////////////////    
});
</script>			










