<?php
    //autoload classes
    spl_autoload_register(function ($class) {
        include_once(__DIR__ . "/classes/" . $class . ".php");
    });

    //autostart sessions
    session_start();