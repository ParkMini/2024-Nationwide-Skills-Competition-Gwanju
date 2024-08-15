<?php
header('Content-Type: application/json');

// 사용자가 로그인한 상태인지 확인
if (!isset($_COOKIE['token'])) {
    echo json_encode(['error' => '로그인이 필요합니다.']);
    exit();
}

// 사용자 정보 가져오기
$user = $pdo->prepare("SELECT * FROM `users` WHERE `token` = :token");
$user->execute([':token' => $_COOKIE['token']]);
$user = $user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => '유효하지 않은 사용자입니다.']);
    exit();
}

try {
    // 해당 사용자의 API Key 신청 목록 가져오기
    $apiKeysStmt = $pdo->prepare("SELECT * FROM apikeyrequests WHERE userid = :userid ORDER BY requested_at DESC");
    $apiKeysStmt->execute([':userid' => $user['id']]);
    $apiKeys = $apiKeysStmt->fetchAll(PDO::FETCH_ASSOC);

    // 결과 반환
    echo json_encode(['apiKeys' => $apiKeys]);
} catch (Exception $e) {
    echo json_encode(['error' => 'API Key 목록을 불러오는 중 오류가 발생했습니다: ' . $e->getMessage()]);
}
?>
