
<?php
function includeAndCache($filePath)
{
    $cacheTime = 86400;
    global $cacheTime;
    $absolutePath = realpath($filePath);
    if (!isset($_SESSION['cached_content'][$absolutePath]) || (time() - $_SESSION['cached_content'][$absolutePath]['time'] > $cacheTime)) {
        ob_start();
        include($filePath);
        $content = ob_get_contents();
        ob_end_clean();
        $_SESSION['cached_content'][$absolutePath] = [
            'content' => $content,
            'time' => time()
        ];
    } else {
        $content = $_SESSION['cached_content'][$absolutePath]['content'];
    }
    echo $content;
}
