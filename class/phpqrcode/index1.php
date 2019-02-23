<?php
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

echo "<h1>PHP QR Code</h1><hr/>";

//set it to writable location, a place for temp generated PNG files
$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
//dirname(__FILE__)=C:\Dropbox\server\UniServerZ_1\www\ta\phpqrcode
//DIRECTORY_SEPARATOR = \
//print_r(DIRECTORY_SEPARATOR);die();

//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

include "qrlib.php";

//ofcourse we need rights to create temp dir
if (!file_exists($PNG_TEMP_DIR)) {
	mkdir($PNG_TEMP_DIR);
}
$time = time();
$filename = $PNG_TEMP_DIR . "{$time}.png";

//processing form input
//remember to sanitize user input in real-life solution !!!
$errorCorrectionLevel = 'L';
$matrixPointSize = 4;
$_REQUEST['data'] = "https://www.ugm.com.tw";
QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);

//display generated file
echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" /><hr/>';

// benchmark
//QRtools::timeBenchmark();
