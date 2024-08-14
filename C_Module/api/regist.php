<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 회원가입 처리
    $username = $decodeJson['username'] ?? null;
    $userid = $decodeJson['userid'] ?? null;
    $password = $decodeJson['password'] ?? null;
    $captcha = $decodeJson['captcha'] ?? null;

    if (!$username || !$userid || !$password || !$captcha) {
        http_response_code(400);
        echo json_encode(['error' => '모든 필드를 입력해주세요.']);
        exit;
    }

    // CAPTCHA 검증
    if ($captcha !== $_COOKIE['captcha']) {
        http_response_code(400);
        echo json_encode(['error' => '잘못된 CAPTCHA 입니다.']);
        exit;
    }

    // 비밀번호 해싱 및 사용자 정보 DB에 저장
    try {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (role, username, userid, userpassword) VALUES ('u', :username, :userid, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            echo json_encode(['success' => '회원가입이 완료되었습니다.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => '회원가입 중 오류가 발생했습니다.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => '회원가입 중 오류가 발생했습니다.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // CAPTCHA 이미지 생성 및 반환
    $captcha_code = '';
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $length = 6;

    for ($i = 0; $i < $length; $i++) {
        $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
    }

    // CAPTCHA 코드를 쿠키에 저장 (50분 동안 유효)
    setcookie('captcha', $captcha_code, time() + 3000, "/");

    $width = 150;
    $height = 50;
    $image = imagecreate($width, $height);
    $background_color = imagecolorallocate($image, 255, 255, 255); // 흰색 배경
    $text_color = imagecolorallocate($image, 0, 0, 0); // 검은색 텍스트
    $line_color = imagecolorallocate($image, 64, 64, 64); // 회색 선

    // 방해선 추가
    for ($i = 0; $i < 5; $i++) {
        imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
    }

    // 기본 비트맵 폰트를 사용하여 텍스트를 그림
    $font_size = 5; // 기본 비트맵 폰트 크기
    $x = 10; // 시작 x 좌표
    for ($i = 0; $i < strlen($captcha_code); $i++) {
        $y = rand(5, 15); // y 좌표를 랜덤하게 설정하여 텍스트의 위치를 약간씩 조정
        imagestring($image, $font_size, $x, $y, $captcha_code[$i], $text_color);
        $x += 20; // 다음 문자 x 좌표
    }

    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
} else {
    http_response_code(405);
    echo json_encode(['error' => '허용되지 않는 메서드입니다.']);
}
?>
