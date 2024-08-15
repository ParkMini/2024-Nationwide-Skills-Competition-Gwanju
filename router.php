<?php
include "./C_Module/DBConnection.php";

$decodeJson = json_decode(file_get_contents('php://input'), true);

$path = $_SERVER["REQUEST_URI"];
$qs = explode("?", $path);
$resource = explode("/", $qs[0]);

// 기본적으로 API 요청 처리
if (isset($resource[1]) && $resource[1] === 'C_Module') {
    switch ($resource[3] ?? '') {
        case 'regist':
            include './C_Module/api/regist.php';
            break;
        case 'login':
            include './C_Module/api/login.php';
            break;
        case 'validate_token':
            include './C_Module/api/validate_token.php';
            break;
        case 'logout':
            include './C_Module/api/logout.php';
            break;
        case 'apikey':
            include './C_Module/api/apikey.php';
            break;
        case 'removeapikey':
            include './C_Module/api/removeapikey.php';
            break;
        case 'apikeylist':
            include './C_Module/api/apikeylist.php';
            break;


        default:
            http_response_code(400);
            echo json_encode(['error' => '잘못된 접근입니다.']);
            break;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => '잘못된 접근입니다.']);
}
