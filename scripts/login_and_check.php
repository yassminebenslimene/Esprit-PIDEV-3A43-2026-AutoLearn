<?php
function http_get($url, &$headers=null, &$body=null){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $res = curl_exec($ch);
    $h_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($res, 0, $h_len);
    $body = substr($res, $h_len);
    curl_close($ch);
    return $res;
}

function http_post($url, $postFields, &$responseHeaders=null){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
    $res = curl_exec($ch);
    $h_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $responseHeaders = substr($res, 0, $h_len);
    $body = substr($res, $h_len);
    curl_close($ch);
    return [$responseHeaders, $body];
}

$base = 'http://127.0.0.1:8000';
$users = [
    ['email'=>'test.admin@example.com','password'=>'AdminPass1!','label'=>'Admin'],
    ['email'=>'test.student@example.com','password'=>'StudentPass1!','label'=>'Student'],
];

foreach($users as $u){
    echo "\n=== Testing login for {$u['label']} ({$u['email']}) ===\n";
    // GET login page to extract CSRF (use cookie file to persist session)
    $cookieFile = sys_get_temp_dir()."/cookies_".($u['label']).".txt";
    $h = null; $b = null;
    $ch = curl_init($base.'/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $resGet = curl_exec($ch);
    $h_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $b = substr($resGet, $h_len);
    curl_close($ch);
    if(preg_match('/name="_csrf_token" value="([^"]+)"/i', $b, $m)){
        $token = $m[1];
        echo "CSRF token OK\n";
    } else {
        echo "No CSRF token found\n";
        continue;
    }

    // perform POST login
    $post = [
        '_username' => $u['email'],
        '_password' => $u['password'],
        '_csrf_token' => $token,
    ];

    // perform POST login
    $ch = curl_init($base.'/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Autolearn-check/1.0');
    curl_setopt($ch, CURLOPT_REFERER, $base . '/login');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
    ]);

    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    $h_len = $info['header_size'] ?? 0;
    $resHeaders = substr($res, 0, $h_len);
    $resBody = substr($res, $h_len);
    curl_close($ch);

    echo "Login POST HTTP code: ".($info['http_code'] ?? 'N/A')."\n";
    // look for Location header
    if(preg_match('/Location:\s*(.+)\r\n/i', $resHeaders, $m)){
        $loc = trim($m[1]);
        echo "Redirect to: $loc\n";
        // follow the redirect once using the same cookie jar
        if(strpos($loc, 'http')===0){
            $follow = $loc;
        } else {
            $follow = rtrim($base, '/') . '/' . ltrim($loc, '/');
        }
        $ch = curl_init($follow);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Autolearn-check/1.0');
        $followRes = curl_exec($ch);
        $followInfo = curl_getinfo($ch);
        curl_close($ch);
        echo "Followed redirect -> HTTP ".($followInfo['http_code'] ?? 'N/A')."\n";
    } else {
        // no redirect: dump small part of body to help debug
        echo "Login response body snippet:\n".substr(strip_tags($resBody),0,400)."\n";
    }

    // Now request /backoffice and /profile using the same cookie
    $ch = curl_init($base.'/backoffice');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "/backoffice -> HTTP $code\n";

    $ch = curl_init($base.'/profile');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "/profile -> HTTP $code\n";

    // Check access to evenement new
    $ch = curl_init($base.'/backoffice/evenement/new');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "/backoffice/evenement/new -> HTTP $code\n";

    // If 200, show whether form present
    if($code==200){
        if(strpos($res,'name="titre"')!==false || strpos($res,'name="evenement[titre]"')!==false){
            echo "Evenement form present\n";
        } else {
            echo "Evenement form not detected\n";
        }
    }

    // Check equipe/new
    $ch = curl_init($base.'/backoffice/equipe/new');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "/backoffice/equipe/new -> HTTP $code\n";
    if($code==200){
        if(strpos($res,'name="nom"')!==false || strpos($res,'name="equipe[nom]"')!==false){
            echo "Equipe form present\n";
        } else {
            echo "Equipe form not detected\n";
        }
    }

    // Check participation index
    $ch = curl_init($base.'/backoffice/participation');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "/backoffice/participation -> HTTP $code\n";
    if($code==200){
        if(strpos($res,'name="statut"')!==false || strpos($res,'name="participation[statut]"')!==false){
            echo "Participation form/index present\n";
        }
    }
}
