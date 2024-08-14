<?php
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 유저 아이디와 비밀번호 추출
    $userid = $decodeJson['userid'];
    $password = $decodeJson['password'];

    try {
        // 데이터베이스 연결 (연결 코드가 이미 존재한다고 가정)
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `userid` = :userid");
        $stmt->execute(['userid' => $userid]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['userpassword'])) {
            // 새로운 토큰 생성
            $newToken = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare("UPDATE `users` SET `token` = :token WHERE `userid` = :userid");
            $stmt->execute(['token' => $newToken, 'userid' => $userid]);

            // 토큰 및 기타 사용자 정보를 JSON으로 반환
            echo json_encode([
                'username' => $user['username'],
                'userid' => $user['userid'],
                'token' => $newToken,
                'apikey' => $user['apikey'] ?? null
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => '아이디 또는 비밀번호가 잘못되었습니다.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => '서버 오류 발생']);
    }
}
?>
