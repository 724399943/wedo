$(".province").each(function() {
	if ( $(this).next('.city').length <= 0 ) {
		var string = [];
		string.push('&nbsp;&nbsp;<select class="city select-date" name="city" style="display:inline-block;">');
		string.push('<option value="-1">--未选择--</option>');
		string.push('</select>');
		$(this).after(string.join(''));
	}
});

$(".city").each(function() {
	if ( $(this).next('.county').length <= 0 ) {
		var string = [];
		string.push('&nbsp;&nbsp;<select class="county select-date" name="county" style="display:inline-block;">');
		string.push('<option value="-1">--未选择--</option>');
		string.push('</select>');
		$(this).after(string.join(''));
	}
});

// $(".county").each(function() {
// 	if ( $(this).next('.community').length <= 0 ) {
// 		var string = [];
// 		string.push('&nbsp;&nbsp;<select class="community select-date" name="community" style="display:inline-block;">');
// 		string.push('<option value="-1">--未选择--</option>');
// 		string.push('</select>');
// 		$(this).after(string.join(''));
// 	}
// });

var zoneData = [];
// 填充数据
function fullData(data, element, address_id) {
	var string = [];
	string.push('<option value="" selected="selected">' + '请选择' + '</option>');
	for ( var i in data ) {
		if(address_id == data[i].id ) {
			string.push('<option value="' + data[i].id + '">' + data[i].region_name + '</option>');
		} else {
		   string.push('<option value="' + data[i].id + '">' + data[i].region_name + '</option>');
		}
	}
	element.css('display', 'inline-block');
	element.html(string.join(''));
	if(!address_id){
	element.trigger("change");	
	}
}

// 省列表改变
$(document).on('change', ".province", function() {
	var id 				= $(this).val();
	var fullElement 	= $(".city");
	if ( zoneData[id] != undefined ) {
		fullData(zoneData[id], fullElement);
	} else {
		$.ajax({
			url: getZoneAddress,
			type: 'POST',
			dataType: 'json',
			data: {pid: id}
		}).done(function(data) {
			zoneData[id] = data['data']['data'];
			fullData(zoneData[id], fullElement);
		});
	}
});

// 市列表改变
$(document).on('change', ".city", function() {
	var id 				= $(this).val();
	var fullElement 	= $(".county");

	if ( zoneData[id] != undefined ) {
		fullData(zoneData[id], fullElement);
	} else {
		$.ajax({
			url: getZoneAddress,
			type: 'POST',
			dataType: 'json',
			data: {pid: id}
		}).done(function(data) {
			if ( data['data']['data'].length <= 0 ) {
				fullElement.hide();
			} else {
				zoneData[id] = data['data']['data'];
				fullData(zoneData[id], fullElement);
			}
		});
	}
});

// 区列表改变
// $(document).on('change', ".county", function() {
// 	var id 				= $(this).val();
// 	var fullElement 	= $(".community");

// 	if ( zoneData[id] != undefined ) {
// 		fullData(zoneData[id], fullElement);
// 	} else {
// 		$.ajax({
// 			url: getZoneAddress,
// 			type: 'POST',
// 			dataType: 'json',
// 			data: {pid: id}
// 		}).done(function(data) {
// 			console.log(data);
// 			if ( data['data']['data'].length <= 0 ) {
// 				fullElement.hide();
// 			} else {
// 				zoneData[id] = data['data']['data'];
// 				fullData(zoneData[id], fullElement);
// 			}
// 		});
// 	}
// });