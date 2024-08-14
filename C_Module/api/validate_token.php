<?php
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 파싱
    $decodeJson = json_decode(file_get_contents('php://input'), true);
    $token = $decodeJson['token'] ?? null;

    if (!$token) {
        echo json_encode(['valid' => false]);
        exit();
    }

    try {
        // 데이터베이스에서 토큰 유효성 확인
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `token` = :token");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();

        if ($user) {
            echo json_encode(['valid' => true]);
        } else {
            echo json_encode(['valid' => false]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => '서버 오류 발생']);
    }
}
?>
