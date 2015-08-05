<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 15-7-29
 * Time: 上午10:46
 */
error_reporting(E_ALL^E_NOTICE);
define('PATH', dirname(__FILE__).'/');

define('VIEW_LIST', '_view_list');
define('PATH_VIEWS', PATH.'views/');
define('PATH_CONFIG', PATH.'config/');
define('PATH_LAYOUT', PATH.'layout/');
define('PATH_WEB', PATH.'web/');
define('PATH_CACHE', PATH.'cache/');

require PATH_CONFIG.'common.php';

$router = isset($_GET['r'])&&$_GET['r'] ? $_GET['r'] : '_view_list';

if(!preg_match('/^[a-z0-9_-]*[\/]*[a-z0-9_-]*$/i', $router)){
    showMessage('bad request');
    return;
}
if($router == VIEW_LIST){
    $files = getDir(PATH_VIEWS, 'html');
    echo "<body><div id='main'></div>";
    foreach ($files as $f) {
        $f = substr($f, 0, strpos($f, ".html"));

        echo "<script src=\"index.php?r={$f}\" type='template/text'></script>";
        echo "<script>window.onload=function(){document.getElementById('main').innerHTML=('All template file are updated')}</script>";
    }
    echo "</body>";
}else{
    $file = PATH_VIEWS.$router.".html";
    if(is_file($file)){
        extract(loadConfig($file));
        ob_start();
        include $file;
        $content = template(ob_get_contents());
        ob_end_clean();
        $content = preg_replace("/---[\\d\\D]*?---/", '', $content ,1);
        file_put_contents(PATH_CACHE.md5($router), $content);
        ob_start();
        include PATH_CACHE.md5($router);
        $content = ob_get_contents();


        ob_end_clean();

        $file = PATH_WEB.'/'.$router.'.html';
        $filepath = dirname($file);
        if(!is_dir($filepath)){
            mkdir($filepath, 0777, true);
        }

        file_put_contents(PATH_WEB.'/'.$router.'.html', $content);
        echo $content;

    }else{
        showMessage('No '.$file.'.html template');
    }
}

function template($template){
    $template = preg_replace_callback("/{layout\\s+(.+?)}/i", function($m){
        return file_get_contents('layout/'.$m[1].".html");
    }, $template);
    $template = preg_replace("/{(\\$.*?)}/i", "<?php echo \\1;?>", $template);

    return $template;
}

function config($name){
    require PATH_CONFIG.'common.php';
    return $config[$name];
}

function loadConfig($file){
    $f = fopen($file, 'r');
    $line = trim(fgets($f));
    $var = array();
    if($line == '---'){
        while(!feof($f)){
            $line = trim(fgets($f));
            if($line == '---'){
                break;
            }else{
                $t = explode("=", $line);
                if(is_array($t) && count($t) == 2){
                    if(strpos($t[1], '$config' )!== false){
                        $temp = explode(".", $t[1])[1];
                        $var[$t[0]] = config($temp);
                    }else{
                        $var[$t[0]] = $t[1];
                    }

                }

            }
        }
    }
    return $var;
}

function searchDir($path,&$data, $prefix = false){
    if(is_dir($path)){
        $dp=dir($path);
        while($file=$dp->read()){
            if($file!='.'&& $file!='..'){
                searchDir($path.'/'.$file,$data, $prefix);
            }
        }
        $dp->close();
    }
    if(is_file($path)){
        $file = substr($path, strlen(PATH_VIEWS) + 1);
        if($prefix !== false && strpos($path, $prefix)!== false){
            $data[]=$file;
        }else{
            $data[]=$file;
        }
    }
}
function getDir($dir, $prefix = false){
    $data=array();
    searchDir($dir,$data, $prefix);
    return   $data;
}

function showMessage($str){
    echo $str;
}

