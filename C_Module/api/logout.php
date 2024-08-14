<?php
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 파싱
    $token = $decodeJson['token'] ?? null;

    if ($token) {
        try {
            // 데이터베이스 연결 (연결 코드가 이미 존재한다고 가정)
            $stmt = $pdo->prepare("UPDATE `users` SET `token` = NULL WHERE `token` = :token");
            $stmt->execute(['token' => $token]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => '서버 오류 발생']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => '잘못된 요청']);
    }
}
?>
