const fetchFestival = async () => {
    const response = await fetch("./B_Module/api/festivals.json");
    return await response.json();
}

// 특정 날짜를 기준으로 가까운 축제 목록
const getFestivalFromStartDate = async (startDate) => {
    const festivals = await fetchFestival();
    const upcomingFestivals = festivals.filter(festival => festival.startdate >= startDate);
    upcomingFestivals.sort((a, b) => new Date(a.startdate) - new Date(b.startdate));
    return upcomingFestivals;
}

/** 전역 변수 */
// 오늘 날짜
const today = new Date().toISOString().split("T")[0];

// Up comming 축제 섹션
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
`)
    for (let i = 1; i < 5; i++) {
        upcommingSlideElem.append(`
<div class="carousel-item">
    <img src="./2024 제59회 전국기능경기대회 과제출제(광주)_2/선수제공파일/images/${festivals[i].photo}" class="d-block w-100" alt="..." style="height: 800px">
    <div class="carousel-caption d-none d-md-block">
    <h5>${festivals[i].title}</h5>
<p>${festivals[i].startdate} - ${festivals[i].enddate}<br>${festivals[i].place}</p>
</div>
`)
    }
}

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
}

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
}