<?php
// DB 연결 및 초기 설정
include "./C_Module/DBConnection.php";

// 로그인 여부 확인
if (!isset($_COOKIE["token"])) {
    header("Location: /");
    exit();
}

// 사용자 정보 가져오기
$user = $pdo->prepare("SELECT * FROM `users` WHERE `token` = :token");
$user->execute([':token' => $_COOKIE["token"]]);
$user = $user->fetch(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./2024%20제59회%20전국기능경기대회%20과제출제(광주)_2/bootstrap-5.3.3-dist/css/bootstrap.css">
    <title>My Page</title>
</head>

<body id="top">
<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary navbar bg-dark border-bottom border-body ">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="./logo.png" alt="로고 이미지" width="100px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Up comming 축제</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">BEST 축제</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">축제 찾기</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">이달의 축제</a>
                    </li>
                </ul>
                <ul class="navbar-nav mb-2 mb-lg-0" id="authMenu">
                    <!-- 오른쪽 메뉴 (토큰 여부에 따라 변경됨) -->
                </ul>
            </div>
        </div>
    </nav>
</header>

<div id="mypageContainer" class="container mt-4">
    <!-- API Key 신청 버튼 -->
    <button id="requestApiKeyBtn" class="btn btn-primary mb-3">API Key 신청</button>

    <!-- API Key 신청 리스트 -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>이름</th>
            <th>아이디</th>
            <th>신청일자</th>
            <th>신청상태</th>
            <th>승인일자</th>
            <th>API Key</th>
            <th>삭제일자</th>
            <th>삭제</th>
        </tr>
        </thead>
        <tbody id="apiKeyList">
        <!-- JavaScript를 통해 리스트가 채워질 것입니다. -->
        </tbody>
    </table>
</div>

<footer class="text-center mt-5">
    <img src="./logo.png" alt="로고" width="128">
    <p>Copyright : (C)Korea Cultural Festival Potal 2024 All rights reserved.</p>
    <div id="social">
        <a href="#"><img src="./call.png" alt="휴대폰"></a>
        <a href="#"><img src="./message.png" alt="메시지"></a>
        <a href="#"><img src="./feedback.png" alt="피드백"></a>
    </div>
</footer>

<script src="./js/jquery-3.7.1.min.js"></script>
<script src="./2024%20제59회%20전국기능경기대회%20과제출제(광주)_2/bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<script src="./js/script.js"></script>
<script>
    $(document).ready(function () {
        // 토큰 유효성 확인 및 헤더 설정
        checkTokenAndSetHeader();

        // 초기화: 승인된 API Key가 있는지 확인하고 신청 버튼 활성화/비활성화
        checkApiKeyStatus();

        // API Key 신청 버튼 클릭 이벤트 처리
        $('#requestApiKeyBtn').on('click', function () {
            requestApiKey();
        });

        // API Key 삭제 처리
        $(document).on('click', '.deleteApiKeyBtn', function () {
            const apiKeyId = $(this).data('apikey-id');
            deleteApiKey(apiKeyId);
        });

        // API Key 상태 확인 및 리스트 업데이트
        function checkApiKeyStatus() {
            $.get('./C_Module/api/apikeylist', function (data) {
                const apiKeys = data.apiKeys;

                // API Key 리스트 업데이트
                updateApiKeyList(apiKeys);

                // 만약 승인된 API Key가 있으면 신청 버튼 비활성화
                const hasApprovedKey = apiKeys.some(apiKey => apiKey.status === '2');
                $('#requestApiKeyBtn').prop('disabled', hasApprovedKey);
            });
        }

        // API Key 신청
        function requestApiKey() {
            const userId = <?= json_encode($user['id']) ?>;

            $.post('./C_Module/api/apikey', { user_id: userId }, function (response) {
                if (response.apikey) {
                    alert('API Key 신청이 완료되었습니다.');
                    checkApiKeyStatus();  // 신청 후 리스트를 갱신
                } else {
                    alert('API Key 신청에 실패했습니다.');
                }
            }, 'json');
        }

        // API Key 삭제
        function deleteApiKey(apiKeyId) {
            const userId = <?= json_encode($user['id']) ?>;

            $.post('./C_Module/api/removeapikey', { user_id: userId, apikey_id: apiKeyId }, function (response) {
                if (response.result) {
                    alert('API Key 삭제가 완료되었습니다.');
                    checkApiKeyStatus();  // 삭제 후 리스트를 갱신
                    window.location.href = '/';  // 로그아웃 처리
                } else {
                    alert('API Key 삭제에 실패했습니다.');
                }
            }, 'json');
        }

        // API Key 리스트 갱신
        function updateApiKeyList(apiKeys) {
            const listContainer = $('#apiKeyList');
            listContainer.empty();

            apiKeys.forEach(apiKey => {
                const row = $('<tr>');

                row.append(`<td>${apiKey.username}</td>`);
                row.append(`<td>${apiKey.userid}</td>`);
                row.append(`<td>${apiKey.requested_at || ''}</td>`);
                row.append(`<td>${apiKey.status}</td>`);
                row.append(`<td>${apiKey.issued_at || ''}</td>`);
                row.append(`<td>${apiKey.apikey || ''}</td>`);
                row.append(`<td>${apiKey.deleted_at || ''}</td>`);

                if (apiKey.status === '2') {  // 승인된 경우 삭제 버튼 추가
                    row.append(`<td><button class="deleteApiKeyBtn btn btn-danger" data-apikey-id="${apiKey.id}">삭제</button></td>`);
                } else {
                    row.append('<td></td>');
                }

                listContainer.append(row);
            });
        }
    });
</script>
</body>
</html>
