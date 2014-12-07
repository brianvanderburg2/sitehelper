<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Bootstrap the sitehelper scripts

if(!defined("__MRBAVII_SITEHELPER__"))
{
    define("__MRBAVII_SITEHELPER__", TRUE);

    require_once(__DIR__ . '/helper/bootstrap.php');
    require_once(__DIR__ . '/apps/bootstrap.php');
}

