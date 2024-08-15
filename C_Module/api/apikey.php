<?php
header('Content-Type: application/json');

// POST 요청인지 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    try {
        // 사용자가 이미 승인된 API Key를 가지고 있는지 확인
        $existingApprovedKey = $pdo->prepare("SELECT * FROM apikeyrequests WHERE userid = :userid AND status = '2'");
        $existingApprovedKey->execute([':userid' => $userId]);

        if ($existingApprovedKey->rowCount() > 0) {
            // 이미 승인된 API Key가 있는 경우, 신청을 막음
            echo json_encode(['error' => '이미 승인된 API Key가 존재합니다.']);
        } else {
            // 승인된 API Key가 없는 경우, 새로운 신청 기록을 삽입
            $requestApiKey = $pdo->prepare("INSERT INTO apikeyrequests (userid, status, requested_at) VALUES (:userid, '1', NOW())");
            $requestApiKey->execute([':userid' => $userId]);

            // 응답: API Key가 아직 발급되지 않았음을 알림
            echo json_encode(['apikey' => null]);
        }
    } catch (Exception $e) {
        // 예외 처리
        echo json_encode(['error' => 'API Key 신청 중 오류가 발생했습니다: ' . $e->getMessage()]);
    }
} else {
    // 잘못된 요청 처리
    echo json_encode(['error' => '잘못된 요청입니다.']);
}
?>
