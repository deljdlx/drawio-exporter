<?php



spl_autoload_register(function ($calledClassName) {

    $namespace = "JDLX\DrawioConverter";
    $classFolderName = "class";

    $normalizedClassName = preg_replace('`^\\\\`', '', $calledClassName);


    if(strpos($normalizedClassName, $namespace) === 0) {

        $relativeClassName = str_replace($namespace, '', $normalizedClassName);
        $relativePath = str_replace('\\', '/', $relativeClassName);


        $definitionClass = __DIR__.'/' . $classFolderName . $relativePath . '.php';

        if(is_file($definitionClass)) {
            include($definitionClass);
        }
    }



});


