<?
require_once('session.php');
$contest_id = 0;
if( isset($_GET['contest_id']) && valid_contest($_GET['contest_id']) )
{
	$contest_id = $_GET['contest_id'];
}
$cont_page = 'home.php';
if($contest_id > 0)
{
	$contest_info_array = get_contests($contest_id);
	$contest_info = $contest_info_array[0];
	$cont_page = ($contest_info['contest_daily'] == 'Yes') ? 'contest_add.php' : 'contest_add_other.php';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>SB CONTESTS ADMIN</title>
</head>

<script type="text/javascript">
<!--
    document.writeln('<frameset cols="220,*" rows="*" border="1" frameborder="1" framespacing="0">');
    document.writeln('    <frame src="menu.php?contest_id=<?php echo $contest_id; ?>" name="navFrame" id="navFrame" frameborder="0" />');
    document.writeln('    <frame src="<?php echo $cont_page; ?>?contest_id=<?php echo $contest_id; ?>&contestDate=<?php echo date('Y-m-d'); ?>" name="mainFrame" id="mainFrame" border="0" frameborder="0" />');
    document.writeln('    <noframes>');
    document.writeln('        <body bgcolor="#FFFFFF">');
    document.writeln('            <p>SB CONTESTS ADMIN is more friendly with a <b>frames-capable</b> browser.</p>');
    document.writeln('        </body>');
    document.writeln('    </noframes>');
    document.writeln('</frameset>');
//-->
</script>

<noscript>
<frameset cols="220,*" rows="*"  border="1" frameborder="1" framespacing="0">
    <frame src="menu.php?contest_id=<?php echo $contest_id; ?>" name="navFrame" id="navFrame" frameborder="0" />
    <frame src="contest_add.php?contest_id=<?php echo $contest_id; ?>&contestDate=<?php echo date('Y-m-d'); ?>" name="mainFrame" id="mainFrame" frameborder="0" />

    <noframes>
        <body bgcolor="#FFFFFF">
            <p>SB CONTESTS ADMIN is more friendly with a <b>frames-capable</b> browser.</p>
        </body>
    </noframes>
</frameset>
</noscript>

</html>