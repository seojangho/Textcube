// Google Map Plugin Common Library
// depends on Google Maps API

var geocoder = null;
var locationMap = null;

/**
 * @brief 지역로그와 연동되어 특정 위치와 연관된 엔트리 정보를 marker 형태로 맵에 추가한다.
 * @param Object response	GGlientGeocoder::getLocations() 메소드 호출에 의한 서버 응답 오브젝트
 * @param GMap2 gmap		GMap2 타입의 오브젝트
 * @param string address	화면에 표시될 주소 문자열
 * @param string title		화면에 표시될 링크 이름 문자열
 * @param string link		엔트리의 링크 URL
 * @param GLatLngBounds boundary	모든 marker를 포함하는 최소 영역을 알기 위한 GLatLngBounds 객체
 * @param Array locations			같은 위치에 여러 엔트리가 관련된 경우를 처리하기 위해 이미 처리된 엔트리들과 marker 정보를 담은 배열
 */
function GMap_addLocationMark(gmap, location_path, title, link, boundary, locations) {
	if (!geocoder)
		geocoder = new GClientGeocoder();
	var address = location_path.replace(/\//g, ' ').trim();
	geocoder.getLocations(address, function(response) {GMap_findLocationCallback(response, gmap, address, title, link, boundary, locations);});
}

/**
 * @brief 지정한 locative 오브젝트로부터 info window에 표시할 HTML을 작성한다.
 * @param Object locative	특정 위치에 대한 Marker 및 관련 정보와 엔트리들에 대한 정보를 담은 오브젝트
 */
function GMap_buildLocationInfoHTML(locative) {
	var html = '<div class="GMapInfo" style="text-align:left"><h4>' + locative.address.split(' ').pop() + '에 얽힌 이야기</h4><ul>';
	for (i = 0; i < locative.entries.length; i++) {
		html += '<li><a href="'+locative.entries[i].link+'">'+locative.entries[i].title+'</a></li>';
	}
	html += '</ul><address>'+locative.address+'</address></div>';
	return html;
}

/**
 * @brief (내부용 함수) geocoder.getLocations()에 의해 호출되는 비동기 콜백 함수
 */
function GMap_findLocationCallback(response, gmap, address, title, link, boundary, locations) {
	if (!response || response.Status.code != 200) {
		// alert('Can\'t retrieve this address "'+address+'"');
	} else {
		var place = response.Placemark[0];
		var point = new GLatLng(place.Point.coordinates[1], place.Point.coordinates[0]);
		var prev = null;
		// Check duplicated locations
		for (i = 0; i < locations.length; i++) {
			if (locations[i].point.equals(point)) {
				prev = locations[i];
				break;
			}
		}
		if (prev == null) {
			// Create a new marker for this location
			var marker = new GMarker(point, {'title': address.split(' ').pop()});
			var locative = {
				'point': point,
				'marker': marker,
				'address': address,
				'entries': new Array({'title': title, 'link': link})
			};
			locations.push(locative);
			marker.bindInfoWindowHtml(GMap_buildLocationInfoHTML(locative));
			gmap.addOverlay(marker);
			boundary.extend(point);
		} else {
			// Add information to the existing marker for here
			prev.entries.push({'title': title, 'link': link});
			prev.marker.bindInfoWindowHtml(null);
			prev.marker.bindInfoWindowHtml(GMap_buildLocationInfoHTML(prev));
		}
	}
	if (process_count != undefined)
		process_count++;
}

/* vim: set noet ts=4 sts=4 sw=4: */
