<?php 

$tgt = '../../aw/lib';
$lnk = './lib';
symlink($tgt, $lnk);
exec('ln -s '.$tgt.' '.$lnk);
//mklink /d .\lib ..\zw\lib\
$tgt2 = '../../feelriviera.com/html/visual';
$lnk2 = './visual';
symlink($tgt2, $lnk2);
exec('ln -s '.$tgt2.' '.$lnk2);

if(function_exists('readlink')) {
$lnk = readlink($lnk);
$lnk2 = readlink($lnk2);
}

echo $lnk.
'<br />'
.$lnk2;
