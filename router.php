#!/usr/bin/php5

<?php
include_once "common.php";

$page = "http://192.168.1.1/bwm-monthly.asp";
$credentials = "root:password";

// create curl resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, $page);

//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


$headers = array(
    "POST ".$page." HTTP/1.0",
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Authorization: Basic " . base64_encode($credentials)
);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// $output contains the output string
$output = curl_exec($ch);

//echo "<pre>" . htmlentities($output) . "</pre>";
if ( preg_match('/daily_history = (.*?);/mis', $output, $matches, PREG_OFFSET_CAPTURE) )
{
    $daily_results = parse_js_array($matches[1][0]);
}

if ( preg_match('/monthly_history = (.*?);/mis', $output, $matches, PREG_OFFSET_CAPTURE) )
{
    $monthly_results = parse_js_array($matches[1][0]);
}


// close curl resource to free up system resources
curl_close($ch);

echo "\${color #B8FF00}Monthly Bandwidth Usage\${color white}\n";
foreach ( array_reverse($monthly_results) as $k=>$result)
{
    if ( $k >= 3 ) break;

    if ( $k == 0 )
    {
        echo '${font Bitstream Vera:size=10:style=Bold}';
    }
    else
        echo '${font Bitstream Vera:size=10}';

    $date = date("F", strtotime ( $result[0] . "+ 1 day"  ));
    echo sprintf ( "%-25s \${alignr}%-12s \${alignr}%s\n", $date,formatSizeUnits($result[1]),formatSizeUnits($result[2]));
}


echo "\n\${color #B8FF00}Daily Bandwidth Usage\${color white}\n";
foreach ( array_reverse($daily_results) as $k=>$result)
{
    if ( $k >= 3 ) break;

    if ( $k == 0 )
    {
        echo '${font Bitstream Vera:size=10:style=Bold}';
    }
    else
        echo '${font Bitstream Vera:size=10}';

    $date = date("F jS", strtotime ( $result[0] ));
    echo sprintf ( "%-25s \${alignr}%-12s \${alignr}%s\n", $date,formatSizeUnits($result[1]),formatSizeUnits($result[2]));
}
