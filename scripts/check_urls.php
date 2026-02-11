<?php
$urls = ['/backoffice','/backoffice/evenement/new','/backoffice/equipe/new','/backoffice/participation','/profile','/'];
foreach ($urls as $p) {
    $u = 'http://127.0.0.1:8000' . $p;
    $ch = curl_init($u);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    echo "$u $code $loc\n";
    curl_close($ch);
}
