/*
 * Written by: Efronny Pardede (Jul 2015)
 * Version   : v1.0
 * 
 * Please do not modify any single part or whole of this document without prior permission from Inferno Technology Community.
 */

/**
 * Define alert() method
 */
(function ($) {
    "use strict";

    $.fn.alert = function () {
    	return this.each(function () {
    		var btn = $("<button type=\"button\" class=\"close\"><span aria-hidden=\"true\">&times;</span></button>");
    		if ($(this).children(btn).length) return; //Skip if already processed
    		
    		$(this).prepend(btn);
    		$(this).wrap("<div class=\"alert-wrapper\"></div>");

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
	$(this).parents("li").first().remove();
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
	//Load responden list
	$("#btn_save_kue").attr("disabled", "disabled");
	$("#responden-list-wrapper").load("/kuesioner/respondenList", { kue_id: 0 }, function () {
		$("#btn_save_kue").removeAttr("disabled");
		//Show and expand kuesioner detail box
		showAndExpandBox($("#kue-detail-box"));
		$("input[name=\"kue_name\"]").focus();
	});
	$("#btn_save_kue").val("Add");
});

/**
 * Trigger .link-row class 
 */
$(document).on("click", "tr.link-row td:not(:first-child)", function () {
	var ele = $(this).parent().find("td:first-child input:checkbox");
	ele.trigger( "click" );
});

/**
 * Register onclick event for kuesioner detail button
 */
$(document).on("click", "button[name='show_kue_detail']", function () {
	var kue_id = $(this).val();
	$("input[name='detail_kue_id']").val(kue_id);
	preloader("#kue-list-box");
	$("#kue-list-box .refresh-btn").attr("disabled", "disabled");
	$.post("/kuesioner/detail", { kue_id: kue_id }, function (data) {
		if ($(data).hasClass("alert-danger") == false) {
			$("#kue-krit-list").removeClass("empty-list").html(data);
			var kue_name = $("#kue-krit-list input[name=\"kue_name\"]");
			$("input[name=\"kue_name\"]").val(kue_name.val());
			kue_name.remove();
			
			if ($("#responden-list-wrapper").length) {
				//Load responden list
				$("#btn_save_kue").attr("disabled", "disabled");
				$("#responden-list-wrapper").load("/kuesioner/respondenList", { kue_id: kue_id }, function () {
					$("#btn_save_kue").removeAttr("disabled");
					preloader_done("#kue-list-box");
					$("#kue-list-box .refresh-btn").removeAttr("disabled");
					//Show and expand kuesioner detail box
					showAndExpandBox($("#kue-detail-box"));
				});
				
				$("#btn_save_kue").val("Update");
			} else {
				if (!!$("input[name=\"krit_weight[]\"]:first").attr("disabled")) {
					$("#btn_submit_kue").css("display", "none");
				} else {
					$("#btn_submit_kue").css("display", "");
				}
				
				preloader_done("#kue-list-box");
				$("#kue-list-box .refresh-btn").removeAttr("disabled");
				//Show and expand kuesioner detail box
				showAndExpandBox($("#kue-detail-box"));
			}
		} else {
			preloader_done("#kue-list-box");
			$("#kue-list-box .refresh-btn").removeAttr("disabled");
			$("#kue-detail-box").css("display", "none");
			$(".ajax-response").html(data);
			$(".alert").alert();
		}
	});
});

$(document).ready(function() {
	
	/**
	 * Register every alert class as alert
	 */
	$(".alert").alert();
	
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
			});
		});
	}
	
	/**
	 * Save kuesioner button onclick event
	 */
	$("#btn_save_kue").click(function () {
		var kue_id = $("input[name=\"detail_kue_id\"]").val();
		var kue_name = $("input[name=\"kue_name\"]").length ? $("input[name=\"kue_name\"]").val() : "";
		var krit_ids = $("#kue-krit-list li input[name=\"krit_id\"]");
		var responden_list = $("#responden-list-wrapper table tr td:first-child input:checkbox:checked");
		
		if (kue_name.length < 1) {
			alert('Nama Kuesioner Harus Diisi Terlebih Dahulu');
			$("input[name=\"kue_name\"]").focus();
		} else if (krit_ids.length < 1) {
			alert('Belum Ada Kriteria yang Dipilih Untuk Kuesioner Ini. Mohon Klik Tombol Add Item Untuk Menambahkan Kirteria.');
		} else {
			var krit_ids_val = krit_ids.map(function() {return $(this).val();}).get();
			var chosen_responden = [];
			if (responden_list.length) {
				chosen_responden = responden_list.map(function() {return $(this).val()}).get();
			}
			var data = {kue_name: kue_name, krit_ids: krit_ids_val, chosen_responden: chosen_responden};
			var action = $(this).val();
			$("#btn_add_krit_item, #btn_save_kue").attr("disabled", "disabled");
			preloader("#kue-detail-box", 2);
			//Time to send POST data to server. Wish me luck!
			$.post("/kuesioner/update", { kue_id: kue_id, data: data, action: action }, function (data) {
				$("#btn_add_krit_item, #btn_save_kue").removeAttr("disabled");
				preloader_done("#kue-detail-box", 2);
				$(".ajax-response").html(data);
				$(".alert").alert();
				if ($(".ajax-response .alert-success").length && action === "Add") {
					$("input[name='detail_kue_id']").val(-1);
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
	
	/**
	 * Onclick listener for add respondent button
	 */
	$("#btn-add-responden").click(function () {
		var bh = $("#responden-box").children(".box-header");
		bh.find(".box-title").html("Tambah Responden");
		bh.children(".box-tools").remove();
		$("#responden-box").children(".box-body").removeClass("table-responsive").removeClass("no-padding").load("/user/form", function(){
			$("#responden-box").children(".box-footer").css("display", "");
		});
	});

});

/**
 * Call this function to show kriteria modal (used only on kriteria page)
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
	return box.css("display") === "none";
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
