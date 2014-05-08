var sbottom = 0;
function setCookie(cname,cvalue,exdays) {
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}
$.fn.tagsResize = function () {
	var containerHeight = $("div#tags-container").innerHeight(),
		navHeigth = $("div#nav-container").outerHeight(),
		padding = parseInt($("div#tags").css("padding-left")),
		newHeight = containerHeight - navHeigth;
	$("div#tags").css({"height": newHeight});
}
$.fn.showSel = function () {
	if (($("a#showHide").hasClass('hidden'))) {
		$('div#selection-wrap').addClass('fade-in');
		function doAnimation() {
			$("a#showHide").trigger("click");
		}
		var myTimeout = window.setTimeout(doAnimation, 500);
	} 
}

$.fn.showClose = function (closeLink) {
	if ( closeLink.css('display') === 'none' ) {
		closeLink.fadeIn('slow');
			} else {
				closeLink.fadeOut('slow');
			}
}
$.fn.makeDrag = function () {
	$("div.drag2").draggable({
		stack: "div.drag2",
		containment: "div.canvas"
	});
}
$.fn.addClose = function(){
	var closeLink;
	$(this).removeClass('drag').append('<a class="close" style="display: none;">X</a>');
	closeLink = $(this).children('a.close');
	$(this).click( function(){ //add close button and click event handler	
		$(this).showClose(closeLink);
	});
	if (!$.support.touch) {
		$(this).hoverIntent( 
			function() {
				if ( closeLink.css('display') == 'none' ) {
					closeLink.fadeIn();
				}
			},
			function() {
				closeLink.stop().fadeOut();
			}, 500
		);
	}
	closeLink.click(function(){
		$(this).parent('div').fadeOut('slow', function() {$(this).remove();});
	});
};
$.fn.sortPanels = function () {
	
	$( "div.canvas-active" ).droppable({
		accept: "div.drag",
		drop: function (event, ui) {
			var newPanel = ui.draggable.clone(),
				helperCoord = ui.helper.offset(),
				canvasCoord = $( "div.canvas-active" ).offset(),
				bWidth = parseInt($( "div.canvas-active" ).css('border-top-width')),
				coordX = helperCoord['left'] - canvasCoord['left'] + bWidth,
				coordY = helperCoord['top'] - canvasCoord['top'] + bWidth,
				cWidth = $( "div.canvas-active" ).outerWidth(),
				cHeight = $( "div.canvas-active" ).outerHeight(),
				pWidth = ui.draggable.outerWidth(),
				pHeight = ui.draggable.outerHeight();
			if (coordX < 0) {
				coordX = 0 + 10;
			} else if ((coordX + pWidth) > cWidth) {
				coordX = cWidth - pWidth - 15;
			}
			if (coordY < 0) {
				coordY = 0;
			} else if ((coordY + pHeight) > cHeight) {
				coordY = cHeight - pHeight - 18;
			}
			newPanel.removeClass("drag ui-draggable").addClass("drag2").css({
				"left": coordX,
				"top": coordY
			}).addClose();
			
			
			$( "div.canvas-active" ).append(newPanel);		
			$(document).makeDrag();			
		}
	});
}

$(window).resize(function(){
	$(document).tagsResize();
});	

//Window Loaded
$(document).ready(function(){
	function doResize() {
		$(document).tagsResize();
	}
	var menutimer = window.setTimeout(doResize, 1000);
	
	//add page
	var childCount = 0, 
		activePage = 0,
		pageWidth = $('div.canvas-active').outerWidth(),
		pageMargin = parseInt($('div.canvas-active').css('margin-right')),
		newWidth = 0,
		toolsWidth = $('div#tools').outerWidth(),
		html = '<div class="canvas canvas-active ui-sortable"><div class="handle"></div></div>',
		children = $('#canvas-cont').children('div.canvas'),
		activePos = 0,
		pos = 0,
		childCount = 1;

	$('#add-page').click(function(){	
		activePos = $('#canvas-out-cont').scrollLeft();
		childCount++;
		newWidth = ((pageWidth * childCount) + (pageMargin * childCount * 2)) + (4 * childCount * 2) + toolsWidth;
		$('div.canvas-active').removeClass('canvas-active');
		$('#canvas-cont').css({width: newWidth}).append(html);
		$("div.handle").each( function () {
			$(this).click(function () {
				$('div.canvas-active').removeClass('canvas-active');
				$(this).parent('.canvas').addClass('canvas-active');
			});
		});
		$('#canvas-out-cont').scrollLeft(activePos);
		pos = $('div.canvas-active').position();
		$('#canvas-out-cont').delay(200).animate({
			scrollLeft: pos['left']
		}, 1000);
		$(document).sortPanels();
	});
//Remove page
	$('#remove-page').click(function() {
		if (childCount > 1) {
			var con = confirm('Estas seguro que quieres borrar la pagina?');
			if (con == true){
				$('div.canvas-active').remove();
				$('div#canvas-cont div.canvas:last-child').addClass('canvas-active');
				childCount--;
				activePage--;
				newWidth = ((pageWidth * childCount) + (pageMargin * childCount * 2)) + (4 * childCount * 2) + toolsWidth;
				$('#canvas-cont').animate({width: newWidth}, 1000);
			} else {
				return;
			}
		}
	});
//Load tagged images
	var sbottom = $('div#selection-wrap').css('bottom'),
		name = new Array(),
		paginas = new Array();
	$('ul#tags li a').each(function(i) {
		name[i] = $(this).attr('name');
		$(this).click(function(){
			$('div#selection').load('selection.php', {myid: name[i]}, function() {
				$('div#selection-wrap').showSel();
				$( "div.drag" ).draggable({
					start: function( event, ui ) {
						var panels = 0;
						var widePanels = 0;
						$("div.canvas-active").children('div.paneles').each( function () {
							if ( $(this).hasClass('wide') ) {
								widePanels++;
							} else {
								panels++;;
							}
						});
					},
					helper: "clone"
				});
			});
		});
	});
//Submit
	$('a#sub_close').click(function(){
		$(this).parents('div#submission').fadeOut();
	});
	$('a#submit').click(function () {
		$("div.canvas").each(function(pageNum){
			var userPanels = new Array(),
				pageName = $(this).attr('name'),
				panels = $(this).children('div.paneles');
			
			if (panels.length != 0) {
				panels.each(function(i) {
					var panelInfo = new Array();
					panelInfo[0] = $(this).attr('name');
					panelInfo[1] = parseFloat($(this).css('left'));
					panelInfo[2] = parseFloat($(this).css('top'));
					panelInfo[3] = parseInt($(this).css('z-index'));
					userPanels[i] = panelInfo;
				});
				$('div#submission').fadeIn();
			} else {
				window.alert("¡Aquí no hay nada! Esto no es una página de arte conceptual.");
				return;
			}
			paginas[pageNum] = userPanels;
		});
	});
	$('form#submit').submit(function (event) {
		var title = $("input#title").val(),
			name = $("input#name").val(),
			email = $("input#email").val(),
			desc = $("textarea#desc").val(),
			patt1=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		if (title == '') {
			title = 'Sin Título';
		}
		if (name == '') {
			name = 'Anónimo';
		}
		if ((email != '')&&(!patt1.test(email))) {
			window.alert("Not a fucking email");
			return;
		}
		$.get('submit.php', {
			mypages: paginas,
			mytitle: title,
			myname: name,
			myemail: email,
			mydesc: desc
			}, function(data) {
				var redirect = 'gallery.php?id=' + data.id + '&thank=you';
				window.location.assign(redirect);			
		}, "json");
		event.preventDefault();
	});
	
//ShowHide panels
	$('a#showHide').click(function(){
		var button = this;
		if ($(button).hasClass('hidden')) {
			$('div#selection-wrap').removeClass('slide-down').addClass('slide-up');
			function doAnimation() {
				$(button).toggleClass('rotate').toggleClass('hidden');
			}
			var myTimeout = window.setTimeout(doAnimation, 1000);
		} else {
			$('div#selection-wrap').removeClass('slide-up').addClass('slide-down');
			function doAnimation() {
				$(button).delay(800).toggleClass('rotate').toggleClass('hidden');
			}
			var myTimeout = window.setTimeout(doAnimation, 1000);
		}
	});
	
//Sortable Panels
	$(document).sortPanels();
	
//Sortable Pages
	var intervalID;
	$("div#canvas-cont").sortable({
		helper: 'clone',
		appendTo: 'div#container',
		zIndex: 10000,
		axis: "x",
		handle: "div.handle",
		placeholder: "page-highlight",
		scroll: false,
		tolerance: "pointer",
		start: function (event, ui) {
			var	sidebarWidth = $('div#sidebar').outerWidth(),
				scrollPos =  $('#canvas-out-cont').scrollLeft()
				sensitivity = 40;

			intervalID = window.setInterval(function(){
				var handlePos = $('div.ui-sortable-helper div.handle').offset();
				if ((handlePos['left'] <= (sidebarWidth + sensitivity)) && ( scrollPos > 0 )){
					scrollPos -= 400;
					$('#canvas-out-cont').stop().animate({
						scrollLeft: scrollPos
					}, 800);
				}
				if (handlePos['left'] >= ($('#container').outerWidth() - sensitivity)){
					scrollPos += 400;
					$('#canvas-out-cont').stop().animate({
						scrollLeft: scrollPos
					}, 800);
				}
			},800);
		},
		stop: function() {
			window.clearInterval(intervalID);
		}
	});
	$("a.comic-pages").click(function(event){
		event.preventDefault();
		if ($(this).hasClass('active-comic-page') == false) {
			var address = $(this).attr("href"),
				storyCanvas = $(this).parent().siblings("div.canvas");
			address = address + ' div.canvas div';
			$(storyCanvas).load(address);
			$(this).siblings().each(function() {
				if ($(this).hasClass('active-comic-page')){
					$(this).removeClass('active-comic-page');
				}
			});
			$(this).addClass('active-comic-page');
		}
	});
	
	$("div#selector li").each(function(){
		$(this).click(function(){
			var max = $(this).val()
			setCookie("max", max, 7);
			$(this).addClass("active").siblings().removeClass("active");
			location.reload();
		});
	});
});

