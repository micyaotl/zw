<?php 

$objetivo = '../zw/lib';
$enlace = './lib';
symlink($objetivo, $enlace);

echo readlink($enlace);

?>