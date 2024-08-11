// 달력 설정 함수
const setupCalendar = async (year, month) => {
    const festivals = await fetchFestival();
    const calendarBody = $("#calendar_body");
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    const currentMonthFestivals = festivals.filter(festival => {
        const festivalDate = new Date(festival.startdate);
        return festivalDate.getFullYear() === year && festivalDate.getMonth() === month;
    });

    // 달력 초기화
    calendarBody.empty();

    // 날짜 표시
    let dayCount = 1;
    for (let i = 0; i < 6; i++) {
        const row = $("<tr></tr>");
        for (let j = 0; j < 7; j++) {
            const cell = $("<td><span class='day'></span></td>");
            if (i === 0 && j < firstDay || dayCount > lastDate) {
                row.append(cell);
            } else {
                const daySpan = cell.find(".day");
                daySpan.text(dayCount);
                
                const festivalsOnThisDay = currentMonthFestivals.filter(festival => new Date(festival.startdate).getDate() === dayCount);
                festivalsOnThisDay.forEach(festival => {
                    const festivalP = $(`<p class="festival bg-primary text-white">${festival.title}</p>`);
                    festivalP.click(() => showBestModal(festival.id));
                    cell.append(festivalP);
                });

                row.append(cell);
                dayCount++;
            }
        }
        calendarBody.append(row);
    }

    // 달력 상단 제목 설정
    $("#calendar_month").text(`${year}년 ${month + 1}월`);
};

// 이전, 다음 버튼 클릭 이벤트
$("#prevMonthBtn").click(() => {
    const currentMonthText = $("#calendar_month").text();
    const currentYear = parseInt(currentMonthText.split("년")[0].trim());
    const currentMonth = parseInt(currentMonthText.split("년")[1].trim().replace("월", "")) - 1;
    setupCalendar(currentYear, currentMonth - 1);
});

$("#nextMonthBtn").click(() => {
    const currentMonthText = $("#calendar_month").text();
    const currentYear = parseInt(currentMonthText.split("년")[0].trim());
    const currentMonth = parseInt(currentMonthText.split("년")[1].trim().replace("월", "")) - 1;
    setupCalendar(currentYear, currentMonth + 1);
});

// 축제 데이터를 가져오는 함수
const fetchFestival = async () => {
    const response = await fetch("./B_Module/api/festivals.json");
    return await response.json();
};

// 특정 날짜를 기준으로 가까운 축제 목록을 가져오는 함수
const getFestivalFromStartDate = async (startDate) => {
    const festivals = await fetchFestival();
    const upcomingFestivals = festivals.filter(festival => festival.startdate >= startDate);
    upcomingFestivals.sort((a, b) => new Date(a.startdate) - new Date(b.startdate));
    return upcomingFestivals;
};

// 오늘 날짜
const today = new Date().toISOString().split("T")[0];
let currentPage = 1;
const itemsPerPage = 5;
let previousRegion = null; // 이전에 클릭된 지역을 저장할 변수

// Upcomming 축제 섹션을 설정하는 함수
const upcommingSlide = async () => {
    const festivals = await getFestivalFromStartDate(today);
    const upcommingSlideElem = $("#upcommingSlide .carousel-inner");
    upcommingSlideElem.append(`
    <div class="carousel-item active">
    <img src="./2024 제59회 전국기능경기대회 과제출제(광주)_2/선수제공파일/images/${festivals[0].photo}" class="d-block w-100" alt="..." style="height: 800px">
    <div class="carousel-caption d-none d-md-block">
    <h5>${festivals[0].title}</h5>
<p>${festivals[0].startdate} - ${festivals[0].enddate}<br>${festivals[0].place}</p>
</div>
`);
    for (let i = 1; i < 5; i++) {
        upcommingSlideElem.append(`
<div class="carousel-item">
    <img src="./2024 제59회 전국기능경기대회 과제출제(광주)_2/선수제공파일/images/${festivals[i].photo}" class="d-block w-100" alt="..." style="height: 800px">
    <div class="carousel-caption d-none d-md-block">
    <h5>${festivals[i].title}</h5>
<p>${festivals[i].startdate} - ${festivals[i].enddate}<br>${festivals[i].place}</p>
</div>
`);
    }
};

// BEST 축제 섹션을 설정하는 함수
const bestFestival = async () => {
    let festivals = await fetchFestival();
    festivals.sort((a, b) => b.like - a.like);
    festivals = festivals.splice(0, 5);
    const bestFestivalElem = $("#bestFestival");

    festivals.forEach(festival => {
        bestFestivalElem.append(`
        <div class="card" style="width: 18rem;">
        <img src="./2024%20제59회%20전국기능경기대회%20과제출제(광주)_2/선수제공파일/images/${festival.photo}" class="card-img-top" alt="이미지">
        <div class="card-body">
        <h5 class="card-title">${festival.title}</h5>
        <p class="card-text">${festival.startdate} - ${festival.enddate}<br>${festival.place}<br>좋아요 : ${festival.like}개</p>
        <button type="button" class="btn btn-primary" onclick="showBestModal(${festival.id})">
            더보기
        </button>
        </div>
        </div>
        `);
    });
};

// BEST 축제 모달을 표시하는 함수
const showBestModal = async (id) => {
    const festivals = await fetchFestival();
    const festival = festivals.find(festival => festival.id === id);

    // 모달 내용 업데이트
    $("#bestModalTitle").html(festival.title);
    $("#bestModalBody").html(`
        <img src="./2024%20제59회%20전국기능경기대회%20과제출제(광주)_2/선수제공파일/images/${festival.photo}" alt="이미지" width="100%">
        <table class="table">
            <tr>
                <th scope="col">축제 기간</th>
                <td>${festival.startdate} - ${festival.enddate}</td>
            </tr>
            <tr>
                <th scope="col">개최 장소</th>
                <td>${festival.place}</td>
            </tr>
            <tr>
                <th scope="col">축제 개최조직</th>
                <td>${festival.organization}</td>
            </tr>
            <tr>
                <th scope="col">담당 지자체</th>
                <td>${festival.government}</td>
            </tr>
            <tr>
                <th scope="col">담당 부서</th>
                <td>${festival.department}</td>
            </tr>
            <tr>
                <th scope="col">담당자 직책</th>
                <td>${festival.position}</td>
            </tr>
            <tr>
                <th scope="col">담당자명</th>
                <td>${festival.staff}</td>
            </tr>
            <tr>
                <th scope="col">담당자 연락처</th>
                <td>${festival.tel}</td>
            </tr>
        </table>
        <table class="table">
            <tr>
                <th scope="col">내국인 수</th>
                <th scope="col">외국인 수</th>
                <th scope="col">총 방문객 수</th>
            </tr>
            <tr>
                <td>${festival.visitor_native_2023.toLocaleString()}명</td>
                <td>${festival.visitor_foreigner_2023.toLocaleString()}명</td>
                <td>${festival.visitor_total_2023.toLocaleString()}명</td>
            </tr>
        </table>
    `);
    $('#bestModal').modal('show');
};

// 초기화 및 선택 옵션 설정 함수
const findFestivalInit = async () => {
    const festivals = await fetchFestival();
    const festivalSidoElem = $("#festivalSido");
    const festivalGunguElem = $("#festivalGungu");
    const festivalType = $("#festivalType");
    const sido = [], gungu = [], type = [];

    festivals.forEach(festival => {
        festival.sido = festival.sido.replace(" ", "");
        festival.gungu = festival.gungu.replace(" ", "");

        if (!sido.includes(festival.sido)) {
            sido.push(festival.sido);
            festivalSidoElem.append(new Option(festival.sido, festival.sido));
        }

        if (!gungu.includes(festival.gungu) && festival.gungu !== "") {
            gungu.push(festival.gungu);
            festivalGunguElem.append(new Option(festival.gungu, festival.gungu));
        }

        if (!type.includes(festival.type)) {
            type.push(festival.type);
            festivalType.append(new Option(festival.type, festival.type));
        }
    });

    // 드롭다운 변경 시 자동으로 검색 실행 및 지도 반영
    $("#festivalSido").change(function () {
        const selectedSido = $(this).val();
        highlightRegion(selectedSido);
        loadFestivals();
    });

    $("#festivalGungu, #festivalType").change(loadFestivals);
};

// 날짜 입력 형식을 YYYY-MM-DD로 변환하는 함수
const dateWithHyphenConverter = (target) => {
    target.value = target.value.replace(/\D/g, '').replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3');
};

// 전역 변수 추가
let currentFestivals = [];

// 축제 목록을 표시하는 함수
const displayFestivals = (page) => {
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedFestivals = currentFestivals.slice(startIndex, endIndex);

    const tableBody = $("#findFestival tbody");
    tableBody.empty();

    if (paginatedFestivals.length === 0) {
        tableBody.append("<tr><td colspan='6'>조건에 충족하는 축제가 없습니다</td></tr>");
        return;
    }

    paginatedFestivals.forEach(festival => {
        tableBody.append(`
            <tr>
                <td>${festival.sido}</td>
                <td>${festival.gungu}</td>
                <td>${festival.title}</td>
                <td>${festival.type}</td>
                <td>${festival.startdate}</td>
                <td><button class="btn btn-primary" onclick="showBestModal(${festival.id})">더보기</button></td>
            </tr>
        `);
    });

    // 이전, 다음 버튼 활성화/비활성화
    $(".btn-previous").prop("disabled", page === 1);
    $(".btn-next").prop("disabled", endIndex >= currentFestivals.length);
};

// 축제를 로드하는 함수
const loadFestivals = async () => {
    let festivals = await fetchFestival();
    const festivalSido = $("#festivalSido").val();
    const festivalGungu = $("#festivalGungu").val();
    const festivalType = $("#festivalType").val();
    const festivalStartDate = $("#festivalStartDate").val() || today;
    const festivalTitle = $("#festivalTitle").val().toLowerCase();

    // 조건에 맞는 축제 필터링
    currentFestivals = festivals.filter(festival => {
        return (!festivalSido || festival.sido === festivalSido) && (!festivalGungu || festival.gungu === festivalGungu) && (!festivalType || festival.type === festivalType) && festival.startdate >= festivalStartDate && (!festivalTitle || festival.title.toLowerCase().includes(festivalTitle));
    });

    // 날짜 순으로 정렬
    currentFestivals.sort((a, b) => new Date(a.startdate) - new Date(b.startdate));

    // 현재 페이지 초기화
    currentPage = 1;

    // 축제 목록을 표시
    displayFestivals(currentPage);

    // 이전 버튼 클릭 이벤트
    $(".btn-previous").off("click").on("click", () => {
        if (currentPage > 1) {
            currentPage--;
            displayFestivals(currentPage);
        }
    });

    // 다음 버튼 클릭 이벤트
    $(".btn-next").off("click").on("click", () => {
        if ((currentPage * itemsPerPage) < currentFestivals.length) {
            currentPage++;
            displayFestivals(currentPage);
        }
    });
};

// 지도에서 해당 지역을 하이라이트하는 함수
const highlightRegion = (regionName) => {
    const svgDoc = document.querySelector('object').contentDocument;

    // 지역 이름과 ID 매핑에서 선택한 지역을 찾음
    const regionId = Object.keys(nameMapping).find(key => nameMapping[key] === regionName);
    if (!regionId) return;

    const region = svgDoc.getElementById(regionId);

    if (previousRegion) {
        previousRegion.style.fill = ''; // 이전 지역을 원래 색상으로 복원
    }

    region.style.fill = '#e74c3c'; // 선택된 지역을 하이라이트
    previousRegion = region; // 현재 선택된 지역을 저장
};

// SVG 파일이 로드된 후 이벤트를 적용하는 함수
function applyClickEvents() {
    const svgDoc = document.querySelector('object').contentDocument;

    // 모든 path 요소에 이벤트 리스너를 추가
    const regions = svgDoc.querySelectorAll('path');
    regions.forEach(function (region) {
        region.addEventListener('click', function () {
            // 이전에 클릭된 지역이 있다면 원래 색상으로 되돌림
            if (previousRegion) {
                previousRegion.style.fill = ''; // 기본 색상으로 복원
            }

            // 현재 클릭된 지역을 하이라이트
            region.style.fill = '#e74c3c'; // 하이라이트 색상

            // 광역자치 단체명을 설정
            const regionName = nameMapping[region.id];
            $("#festivalSido").val(regionName);

            // 자동으로 검색 실행
            loadFestivals();

            // 현재 클릭된 지역을 이전 지역으로 저장
            previousRegion = region;
        });
    });
}

const nameMapping = {
    KR11: "서울",
    KR26: "부산",
    KR27: "대구",
    KR28: "인천",
    KR29: "광주",
    KR30: "대전",
    KR31: "울산",
    KR50: "세종",
    KR41: "경기",
    KR42: "강원",
    KR43: "충북",
    KR44: "충남",
    KR45: "전북",
    KR46: "전남",
    KR47: "경북",
    KR48: "경남",
    KR49: "제주",
};
