<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

include("headers.php");
include("settings.php");

if(function_exists('glob')) {
    // provide a wrapper
    function _glob($pattern, $flags = 0) {
        return glob($pattern, $flags);
    }
} else if(function_exists('opendir') && function_exists('readdir')) {
    // we can't redefine glob() if it has been disabled
    function _glob($pattern, $flags = 0) {
        $path = dirname($pattern);
        $filePattern = basename($pattern);
        if(is_dir($path) && ($handle = opendir($path)) !== false) {
            $matches = array();
            while(($file = readdir($handle)) !== false) {
                if(($file[0] != '.')
                    && fnmatch($filePattern, $file)
                    && (!($flags & GLOB_ONLYDIR) || is_dir("$path/$file"))) {
                    $matches[] = "$path/$file" . ($flags & GLOB_MARK ? '/' : '');
                }
            }
            closedir($handle);
            if(!($flags & GLOB_NOSORT)) {
                sort($matches);
            }
            return $matches;
        }
        return false;
    }
} else {
    function _glob($pattern, $flags = 0) {
        return false;
    }
}
function globr($sDir, $sPattern, $nFlags = null)
{
    if (($aFiles = \_glob("$sDir/$sPattern", $nFlags)) == false) {
        $aFiles = array();
    }
    if (($aDirs = \_glob("$sDir/*", GLOB_ONLYDIR)) != false) {
        foreach ($aDirs as $sSubDir) {
            if (is_link($sSubDir)) {
                continue;
            }

            $aSubFiles = globr($sSubDir, $sPattern, $nFlags);
            $aFiles = array_merge($aFiles, $aSubFiles);
        }
    }
    return $aFiles;
}

function stringEndsWith($haystack, $needle)
{
    if ('' === $needle) {
        return true;
    }

    $lastCharacters = substr($haystack, -strlen($needle));

    return $lastCharacters === $needle;
}
$files = array();

// If we have an action to perform
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {

    $backupDir = __DIR__ . '/../backups';
    $files = globr($backupDir, '*');

    foreach ($files as $index => $file) {
        if (stringEndsWith($file, '.zip') || '.gitkeep' == $file || is_dir($file)) {
            unset($files[$index]);
            continue;
        }

        $targetFile    = str_replace($backupDir, $_SERVER['DOCUMENT_ROOT'], $file);
        $targetContent = @file_get_contents($file);

        if ($targetContent) {
            @file_put_contents($targetFile, $targetContent);
            @unlink($file);
        }
    }

}
?>
<!DOCTYPE html>

<html>
<head>
    <title>Restoring files</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="github-manager.css">
</head>

<body class="githubManager">

<h1>restoring files</h1>

<div style="display: inline-block; width: 620px; height: 340px; overflow-y: auto">

    <ul>
    <?php
    if (!empty($files)) {
        foreach ($files as $file) {
            echo "<li>" . $file . "</li>";
        }
    } else {
        echo "No file to restore";
    }

    ?>
    </ul>
</div>

</body>

</html>
