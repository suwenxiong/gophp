<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 15-7-29
 * Time: 上午10:46
 */
define('VIEW_LIST', '_view_list');
define('PATH_VIEWS', 'views/');
define('PATH_CONFIG', 'config/');
define('PATH_LAYOUT', 'layout/');
define('PATH_WEB', 'web/');

require PATH_CONFIG.'common.php';

$router = isset($_GET['r'])&&$_GET['r'] ? $_GET['r'] : '_view_list';
if(!preg_match('/^[a-z0-9_-]*[\/]*[a-z0-9_-]*$/i', $router)){
    showMessage('bad request');
    return;
}
if($router == VIEW_LIST){

}else{
    $file = PATH_VIEWS.$router.".html";
    if(is_file($file)){
        extract(loadConfig($file));
        ob_start();
        include $file;
        $content = template(ob_get_contents());
        ob_end_clean();
        $content = preg_replace("/---[\\d\\D]*?---/", '', $content ,1);
        file_put_contents('cache/'.$router, $content);
        ob_start();
        include 'cache/'.$router;
        $content = ob_get_contents();


        ob_end_clean();
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



function showMessage($str){
    echo $str;
}
