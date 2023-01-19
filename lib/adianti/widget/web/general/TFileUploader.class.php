<?php
/**
 * File uploader listener
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFileUploader
{
    function show()
    {
        $folder = 'tmp/';
        if (isset($_REQUEST['qqfile']))
        {
            $file = $_REQUEST['qqfile'];
            $path = $folder.$file;
            $input = fopen("php://input", "r");
            $temp = tmpfile();
            $realSize = stream_copy_to_stream($input, $temp);
            fclose($input);
            
            if ($realSize != $_SERVER["CONTENT_LENGTH"])
            {            
                die("{'error':'size error'}");
            }
            
            if (is_writable($folder))
            {
                $target = fopen($path, 'w');
                fseek($temp, 0, SEEK_SET);
                stream_copy_to_stream($temp, $target);
                fclose($target);
                echo "{success:true, target:'{$file}'}";
            }
            else
            {
                die("{'error':'not writable: $path'}");
            }
        }
        else
        {
            $file = $_FILES['qqfile']['name'];
            $path = $folder.$file;
            
            if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path))
            {
                die("{'error':'permission denied'}");
            }
            echo "{success:true, target:'{$file}'}";
        }
    }
}
?>