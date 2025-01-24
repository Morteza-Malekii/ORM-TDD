<?php
namespace App\Helpers;

use App\Extentions\confingFileNotFoundNewException;
use function PHPUnit\Framework\isNull;

class config 
{
    public static function getFileContent(string $filename){
        $filePath = realpath(__DIR__ . "/../Configs/" . $filename . ".php");
        if(!$filePath){
            throw new confingFileNotFoundNewException();
        }
        $result = require $filePath;
        return $result;
    }


    public static function get(string $filename , string $key = null ){
        $fileContent = config::getFileContent($filename);
        if(is_null($key)) return $fileContent;
        return $fileContent[$key] ?? null;
        
    }
}