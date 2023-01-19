<?php
if (PHP_SAPI == 'cli')
{
    include 'index.gtk.php';
}
else
{
    include 'index.web.php';
}
?>