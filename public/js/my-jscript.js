/*
 * Written by: Efronny Pardede (Jul 2015)
 * Version   : v1.0
 * 
 * Please do not modify any single part or whole of this document without prior permission from Inferno Technology Community.
 */

/**
 * Alert method to costumize alert div
 */
(function ($) {
    "use strict";

    $.fn.alert = function () {
    	return this.each(function () {
    		var btn = "<button type=\"button\" class=\"close\"><span aria-hidden=\"true\">&times;</span></button>";
    		var $alert = $(this);
    		$alert.prepend(btn);
    		$alert.wrap("<div class=\"alert-wrapper\"></div>");

			if ($(this).hasClass("alert-success")) {
				$(this).prepend("<i class=\"fa fa-check\"></i>");
			} else if ($(this).hasClass("alert-warning")) {
				$(this).prepend("<i class=\"fa fa-warning\"></i>");
			} else if ($(this).hasClass("alert-info")) {
				$(this).prepend("<i class=\"fa fa-info\"></i>");
			} else if ($(this).hasClass("alert-danger")) {
				$(this).prepend("<i class=\"fa fa-ban\"></i>");
			}
    		
    		$(".close").click(function () {
    			$(this).closest(".alert-wrapper").slideUp();
    		});
    	});
    };

})(jQuery);

$(document).on("click", "[data-list-item='remove']", function () {
	$(this).parents("li").first().detach().remove();
	if ($("#kue-krit-list").children("li").length < 1) {
		$("#kue-krit-list").addClass("empty-list").html(
				"<li>" +
				"<div class=\"text alert-wrapper\">" +
				"<div class=\"alert alert-info\"><i class=\"fa fa-info\"></i> " +
				"Klik Add Item untuk menambahkan kriteria ke dalam kuesioner ini" +
				"</div>" +
				"</div>" +
				"</li>");
	}
});

/**
 * Register onclick event for add new kuesioner button
 */
$(document).on("click", "#btn-add-kue", function () {
	$("input[name=\"kue_name\"]").val("");
	$("#kue-krit-list").addClass("empty-list").html(
			"<li>" +
			"<div class=\"text alert-wrapper\">" +
			"<div class=\"alert alert-info\"><i class=\"fa fa-info\"></i> " +
			"Klik Add Item untuk menambahkan kriteria ke dalam kuesioner ini" +
			"</div>" +
			"</div>" +
			"</li>");
	//Show and expand kuesioner detail box
	showAndExpandBox($("#kue-detail-box"));
	$("input[name=\"kue_name\"]").focus();
	$("#btn_save_kue").val("Add");
});

/**
 * Register onclick event for kuesioner detail button
 */
$(document).on("click", "button[name='show_kue_detail']", function () {
	preloader("#kue-list-box .box-body");
	$("#kue-list-box .refresh-btn").attr("disabled", "disabled");
	$.post("/kuesioner/detail", { kue_id: $(this).val() }, function (data) {
		preloader_done("#kue-list-box .box-body");
		$("#kue-list-box .refresh-btn").removeAttr("disabled");
		var jObj = JSON.parse(data);
		
		if (jObj.success) {
			$("input[name=\"kue_name\"]").val(jObj.kue_name);
			//Sanitize kuesioner kriteria item list first
			$("#kue-krit-list").removeClass("empty-list").empty();
			
			for (var i = 0; i < jObj.kriteria_items.length; i++) {
				$("#kue-krit-list").append(
						"<li>" +
						"<span class=\"handle\">" +
                        "<i class=\"fa fa-ellipsis-v\"></i>\n" +
                        "<i class=\"fa fa-ellipsis-v\"></i>" +
                        "</span>" +
                        "<span class=\"text\">" + jObj.kriteria_items[i].sub_kriteria + "</span>" +
                        "<small class=\"label label-success\">" +
                        "<i class=\"fa fa-tag\"></i> " + jObj.kriteria_items[i].kriteria +
                        "</small>" +
						"<input name=\"krit_id\" type=\"hidden\" value=\"" + jObj.kriteria_items[i].id_kriteria + "\">" +
                        "<div class=\"tools\">" +
                        "<button type=\"button\" data-list-item=\"remove\" class=\"transparent-btn\">" +
                        "<i class=\"fa fa-trash-o\" title=\"Delete\"></i>" +
                        "</button>" +
                        "</div>" +
						"</li>");
			}
			
			$("#btn_save_kue").val("Update");
			//Show and expand kuesioner detail box
			showAndExpandBox($("#kue-detail-box"));
		} else {
			$("#kue-detail-box").css("display", "none");
			var holder = $(".ajax-response").empty();
			var alert = $("<div class=\"alert alert-danger\"></div>");
			
			for (var i = 0; i < jObj.messages.length; i++) {
				holder.append(alert.append(jObj.messages[i]));
			}
			$(".alert").alert();
		}
	});
});

$(document).ready(function() {
	
	/**
	 * Register every alert class as alert
	 */
	$(".alert").alert();
		/*var btn = "<button type=\"button\" class=\"close\"><span aria-hidden=\"true\">&times;</span></button>";
		var $alert = $(".alert");
		$alert.prepend(btn);
		$alert.wrap("<div class=\"alert-wrapper\"></div>");
		
		$alert.each(function () {
			if ($(this).hasClass("alert-success")) {
				$(this).prepend("<i class=\"fa fa-check\"></i>");
			} else if ($(this).hasClass("alert-warning")) {
				$(this).prepend("<i class=\"fa fa-warning\"></i>");
			} else if ($(this).hasClass("alert-info")) {
				$(this).prepend("<i class=\"fa fa-info\"></i>");
			} else if ($(this).hasClass("alert-danger")) {
				$(this).prepend("<i class=\"fa fa-ban\"></i>");
			}
		});
		
		$(".close").click(function () {
			$(this).closest(".alert-wrapper").slideUp();
		});*/
	
	/**
	 * Treat kuesioner list box as boxRefresh
	 */
	$("#kue-list-box").boxRefresh({source: "/kuesioner/listBox"});
	
	/**
	 * Register onclick event for add kriteria button
	 */
	$("#btn-add-kriteria").click(function () {
		showKriteriaModal("");
	});
	
	/**
	 * Register onclick event for edit kriteria button
	 */
	$(".btn-upd-kriteria").click(function () {
		showKriteriaModal($(this).prev("input[name=\"id\"]").val());
	});
	
	/**
	 * Register onclick event for delete kriteria button
	 */
	$(".btn-del-kriteria").click(function () {
		var id = $(this).siblings("input[name=\"id\"]").val();
		if (confirm("Apakah anda yakin ingin menghapus kriteria ini?")) {
			$.post("/kriteria/delt", {id: id}, function (data) {
				window.location.replace("kriteria");
			});
		}
	});
	
	/**
	 * Register onclick event for add kriteria item button in kuesioner page
	 */
	if ($("#kue-krit-list").length) {
		$("#btn_add_krit_item").click(function () {
			$("#krit-list-modal-body").html("<div class=\"preloader-holder\"></div>");
			preloader("#krit-list-modal-body .preloader-holder");
			$("#krit-list-modal .modal-footer button[name=\"confirm_button\"]").attr("disabled", "disabled");
			$("#krit-list-modal").modal('show');
			var exclude_krit_id = $("#kue-krit-list li input[name=\"krit_id\"]").map(function () {return $(this).val();}).get();
			$.post("/kriteria/chooseList", { exclude: exclude_krit_id }, function (data) {
				$("#krit-list-modal-body").html(data);
				$("#krit-list-modal .modal-footer button[name=\"confirm_button\"]").removeAttr("disabled");
				//Toggle checkbox in kriteria choose list table, on row clicked
				$("#krit-choose-table tr.link-row td:not(:first-child)").click(function () {
					var ele = $(this).prevAll().children("input:checkbox");
					ele.trigger( "click" );
				});
			});
		});
	}
	
	/**
	 * Save kuesioner button onclick event
	 */
	$("#btn_save_kue").click(function () {
		var kue_name = $("input[name=\"kue_name\"]").length ? $("input[name=\"kue_name\"]").val() : "";
		var krit_ids = $("#kue-krit-list li input[name=\"krit_id\"]");
		
		if (kue_name.length < 1) {
			alert('Nama Kuesioner Harus Diisi Terlebih Dahulu');
			$("input[name=\"kue_name\"]").focus();
		} else if (krit_ids.length < 1) {
			alert('Belum Ada Kriteria yang Dipilih Untuk Kuesioner Ini. Mohon Klik Tombol Add Item Untuk Menambahkan Kirteria.');
		} else {
			var krit_ids_val = krit_ids.map(function() {return $(this).val();}).get();
			var data = {kue_name: kue_name, krit_ids: krit_ids_val};
			var action = $(this).val();
			$("#btn_add_krit_item, #btn_save_kue").attr("disabled", "disabled");
			preloader("#kue-detail-box", 2);
			//Time to send POST data to server. Wish me luck!
			$.post("/kuesioner/update", { data: data, action: action }, function (data) {
				$("#btn_add_krit_item, #btn_save_kue").removeAttr("disabled");
				preloader_done("#kue-detail-box", 2);
				$(".ajax-response").html(data);
				$(".alert").alert();
				if ($(".ajax-response .alert-success").length) {
					$("#btn_save_kue").val("Update");
				}
				$("#kue-list-box .refresh-btn").click();
			});
		}
	});
	
	/**
	 * Add selected kriteria to kue-krit-list when confirm button hitted
	 */
	$("#krit-list-modal .modal-footer [name=\"confirm_button\"]").click(function () {
		if ($("#krit-choose-table").length) {
			//Get selected kriteria and register to kue-krit-list
			$("#krit-choose-table .link-row input:checkbox:checked").each(function () {
				var id_kriteria = $(this).val();
				var kriteria = $(this).parent().next();
				var sub_kriteria = kriteria.next();
				//Clear all kue-krit-list children for brand new kuesioner
				if ($("#kue-krit-list").hasClass("empty-list")) {
					$("#kue-krit-list").removeClass("empty-list").empty();
				}
				//Append the selected kriteria to kuesioner kriteria list
				$("#kue-krit-list").append(
						"<li>" +
						"<span class=\"handle\">" +
                        "<i class=\"fa fa-ellipsis-v\"></i>\n" +
                        "<i class=\"fa fa-ellipsis-v\"></i>" +
                        "</span>" +
                        "<span class=\"text\">" + sub_kriteria.html() + "</span>" +
                        "<small class=\"label label-success\">" + "<i class=\"fa fa-tag\"></i> " + kriteria.html() + "</small>" +
						"<input name=\"krit_id\" type=\"hidden\" value=\"" + id_kriteria + "\">" +
                        "<div class=\"tools\">" +
                        "<button type=\"button\" data-list-item=\"remove\" class=\"transparent-btn\">" +
                        "<i class=\"fa fa-trash-o\" title=\"Delete\"></i>" +
                        "</button>" +
                        "</div>" +
						"</li>");
			});
		}
	});

});

/**
 * Register getKriteriaForm function()
 */
function showKriteriaModal(id) {
	$("#kriteria-modal-body").html("<div class=\"preloader-holder\"></div>");
	preloader("#kriteria-modal-body .preloader-holder");
	$("#kriteriaModal").on("shown.bs.modal", function() {
		if ($("#kriteria_form input[name=\"data[kriteria]\"]").length) {
			$("#kriteria_form input[name=\"data[kriteria]\"]").focus();
		}
	});
	$("#kriteriaModal .modal-footer button[name=\"action\"]").attr("disabled", "disabled");
	$("#kriteriaModal").modal('show');
	// Request and load kriteria form data
	$.post("/kriteria/form", { id: id }, function (data) {
		//setTimeout(function () {
		$("#kriteria-modal-body").html(data);
		if ($("#kriteria_form").length) {
			$("#kriteriaModal .modal-footer button[name=\"action\"]").attr("value", isNaN(parseInt(id)) ? "Add" : "Update");
			$("#kriteriaModal .modal-footer button[name=\"action\"]").removeAttr("disabled");
			$("#kriteria_form input[name=\"data[kriteria]\"]").focus();
		}
		//}, 5000);
	});
}

/**
 * Register preloader function() to load preloader animation
 */
function preloader(eId, imgn) {
	imgn = imgn || "";
	$(eId).append("<div class=\"overlay\"></div><div class=\"preloader-img" + imgn + "\"></div>");
}

/**
 * This function will remove preloader effect
 * @param eId
 * @param imgn
 */
function preloader_done(eId, imgn) {
	imgn = imgn || "";
	$(eId).children(".overlay, .preloader-img" + imgn).remove();
}

/**
 * Use this function to check whether the box is closed?
 */
function isBoxClosed(box) {
	return box.css("display") == "none";
}

/**
 * Use this function to check whether the box is visible?
 */
function isBoxVisible(box) {
	return box.css("display").length == 0;
}

/**
 * This function will show and expand the specified box
 */
function showAndExpandBox(box) {
	//Force the box in collapsed state
	box.addClass("collapsed-box");
    //Convert minus into plus
    box.find("[data-widget='collapse']").children(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
    box.find(".box-body, .box-footer").css("display", "none");
	//Then show and expand the box
	box.css("display", "");
	box.find("[data-widget='collapse']").click();
}
