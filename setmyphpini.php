#!/usr/bin/env php
<?php
define('MY_PHP_INI_FILENAME', 'my_php.ini');

// use strict;
set_error_handler(function($errno, $errstr, $errfile, $errline){
    echo "ERROR({$errno}) at {$errfile}:{$errline}, {$errstr}\n";
    exit(1);
});

$config_dir = PHP_CONFIG_FILE_SCAN_DIR; // thanks @tanakahisateru san!

echo "config-file-scan-dir: {$config_dir}\n";

if (!file_exists($config_dir) || filetype($config_dir) !== 'dir') {
    echo "config-file-scan-dir is not exists (or not dir).\n",
    "need `mkdir {$config_dir}`";
    exit(1);
}

// iniファイルを選ぶ、ユーザー定義のiniがなければ、ini.sampleをつかう
$ini_file_path = find_my_ini([PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION]);

if ($ini_file_path === false) {
    echo "PHP version is ".PHP_MAJOR_VERSION .".". PHP_MINOR_VERSION .".". PHP_RELEASE_VERSION.
        ". not found any version matched ".MY_PHP_INI_FILENAME."\n";
    exit(1);
}

echo "found: {$ini_file_path} \ndo you want use this? y/n:";
if (trim(fgets(STDIN)) !== 'y') {
    echo "abort successfully\n";
    exit(0);
}

$my_php_ini = file_get_contents($ini_file_path);

if (file_exists($config_dir . "/" . MY_PHP_INI_FILENAME)) {
    echo "️already exists " . MY_PHP_INI_FILENAME . " in destination dir.\n",
    "do you want overwrite? y/n:";
    if (trim(fgets(STDIN)) !== 'y') {
        echo "abort successfully\n";
        exit(0);
    }
}

file_put_contents($config_dir . "/" . MY_PHP_INI_FILENAME, $my_php_ini);

echo "done\n";

// ==END==

function find_my_ini(array $arr){
    if($_ = check_ini_exists($arr[0]."/".$arr[1]."/".$arr[2]))
        return $_;

    if($_ = check_ini_exists($arr[0]."/".$arr[1]))
        return $_;

    if($_ = check_ini_exists($arr[0]))
        return $_;

    if($_ = check_ini_exists_in_sample($arr[0]."/".$arr[1]."/".$arr[2]))
        return $_;

    if($_ = check_ini_exists_in_sample($arr[0]."/".$arr[1]))
        return $_;

    if($_ = check_ini_exists_in_sample($arr[0]))
        return $_;

    return false;
}

function check_ini_exists($path){
    if(file_exists($_ = __DIR__.'/ini/'.$path."/".MY_PHP_INI_FILENAME))
        return $_;

    else
        return false;
}

function check_ini_exists_in_sample($path){
    if(file_exists($_ = __DIR__.'/ini.sample/'.$path."/".MY_PHP_INI_FILENAME))
        return $_;

    else
        return false;
}
