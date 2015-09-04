#!/usr/bin/php
<?php
function parse_js_array ( $jsstring )
{
    $jsstring = preg_replace_callback ( "/(0x[0-9a-fA-F]{1,10})\,(0x[0-9a-fA-F]{1,10})\,(0x[0-9a-fA-F]{1,10})/mis", "convert", $jsstring );

    return json_decode ( $jsstring);
}

function convert ( $match )
{
    //[(((h[0] >> 16) & 0xFF) + 1900), (((h[0] >>> 8) & 0xFF) + 1), h[1], h[2]].join(',')

    $date = hexdec($match[1]);
    $dl = hexdec($match[2]) * 1024;
    $ul = hexdec($match[3]) * 1024;

    $year = ($date >> 16 & 0xff)+1900;
    $month = ($date >> 8 & 0xff)+1;
    $day = ($date & 0xff);

    $date = mktime ( 0,0,0,$month, $day, $year );

    return sprintf ( '"%s",%s,%s', date ( "Y-m-d", $date ), $dl, $ul );
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}