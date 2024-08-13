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
        // 여기에 다른 API 엔드포인트들을 추가할 수 있습니다.
        // case 'logout':
        //     include './controller/api/logout.php';
        //     break;
        default:
            http_response_code(400);
            echo json_encode(['error' => '잘못된 접근입니다.']);
            break;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => '잘못된 접근입니다.']);
}
