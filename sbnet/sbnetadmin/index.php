<?php
require_once('session.php');

$mainFrameSrc = 'home.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>SB CONTESTS ADMIN</title>
</head>

<script type="text/javascript">
<!--
    document.writeln('<frameset cols="305,*" rows="*" border="1" frameborder="1" framespacing="0">');
    document.writeln('    <frame src="menu.php" name="navFrame" id="navFrame" frameborder="0" />');
    document.writeln('    <frame src="<?php echo $mainFrameSrc ?>" name="mainFrame" id="mainFrame" border="0" frameborder="0" />');
    document.writeln('    <noframes>');
    document.writeln('        <body bgcolor="#FFFFFF">');
    document.writeln('            <p>SB CONTESTS ADMIN is more friendly with a <b>frames-capable</b> browser.</p>');
    document.writeln('        </body>');
    document.writeln('    </noframes>');
    document.writeln('</frameset>');
//-->
</script>

<noscript>
<frameset cols="305,*" rows="*"  border="1" frameborder="1" framespacing="0">
    <frame src="menu.php" name="navFrame" id="navFrame" frameborder="0" />
    <frame src="<?php echo $mainFrameSrc ?>" name="mainFrame" id="mainFrame" frameborder="0" />

    <noframes>
        <body bgcolor="#FFFFFF">
            <p>SB CONTESTS ADMIN is more friendly with a <b>frames-capable</b> browser.</p>
        </body>
    </noframes>
</frameset>
</noscript>

</html>