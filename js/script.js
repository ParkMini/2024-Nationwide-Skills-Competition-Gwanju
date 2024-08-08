// festival.json 불러오기
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

// Up comming 축제 섹션
async function upcommingSlide() {
    const today = new Date().toISOString().split("T")[0];
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