<?php
$html = file_get_contents('http://127.0.0.1:8000/login');
if ($html === false) {
    echo "FAILED TO FETCH\n";
    exit(1);
}
$start = strpos($html, '<form');
$end = strpos($html, '</form>', $start);
if ($start === false || $end === false) {
    echo "FORM NOT FOUND\n";
    exit(1);
}
$snippet = substr($html, $start, $end - $start + 7);
echo $snippet;
