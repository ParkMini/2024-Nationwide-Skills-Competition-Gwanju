<?php
header('Content-Type: application/json');

// POST 요청인지 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['apikey_id'])) {
    $userId = $_POST['user_id'];
    $apiKeyId = $_POST['apikey_id'];

    try {
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // apikeyrequests 테이블의 상태를 삭제(0)로 변경
        $deleteApiKeyRequest = $pdo->prepare("UPDATE apikeyrequests SET status = '0', deleted_at = NOW() WHERE id = :apikey_id AND userid = :userid");
        $deleteApiKeyRequest->execute([':apikey_id' => $apiKeyId, ':userid' => $userId]);

        // users 테이블에서 해당 사용자의 apikey 필드를 NULL로 설정
        $removeUserApiKey = $pdo->prepare("UPDATE users SET apikey = NULL WHERE id = :userid");
        $removeUserApiKey->execute([':userid' => $userId]);

        // 트랜잭션 커밋
        $pdo->commit();

        // 응답으로 성공 결과 반환
        echo json_encode(['result' => true]);

        // 로그아웃 처리 (토큰 삭제)
        setcookie("token", "", time() - 3600, "/"); // 토큰 삭제
    } catch (Exception $e) {
        // 트랜잭션 롤백
        $pdo->rollBack();
        echo json_encode(['result' => false, 'error' => $e->getMessage()]);
    }
} else {
    // 잘못된 요청 처리
    echo json_encode(['error' => '잘못된 요청입니다.']);
}
?>
