(function(window){
	var searchBtnVisible = true;
	var searchInput = null;
	var searchSubmit = null;
	var windowHeight = 0;
	var sideBannerHeight = 0;
	var positionType = "absolute";
	var homeBannerHeight = 0;
	$(document).ready(function(){		
		searchInput = $('#searchInput');
		searchSubmit = $('#searchSubmit');
		searchInput.bind('input propertychange', searchChange);
		searchChange();
		
		$(document).scroll(scrollChange);
		$('#content').resize();
		sideBannerHeight = $('.sidebanner').height();
		if($('header.banner').length){
			homeBannerHeight = $('header.banner').height();
		}
	});
	function scrollChange(evt){
		if(sideBannerHeight > 0){
			if(windowHeight >= sideBannerHeight + 42 + 60){
				var st = $(document).scrollTop();
				if(st >= homeBannerHeight ){
					if(positionType != "fixed"){
						$('.sidebanner').css({'top': "60px", 'position':'fixed'});
						positionType = "fixed";
					}
					return;
				}
			}
			
			if(positionType != "absolute"){
				$('.sidebanner').css({'top': "auto", 'position':'absolute'});
				positionType = "absolute";
			}
		}
	}
	function searchChange(evt){
		var searchText = searchInput.val();
		if(searchText.length >= 1 && searchBtnVisible == false){
			searchBtnVisible = true;			
			searchSubmit.css('visibility', 'visible');
			searchInput.css({'border-top-right-radius':'0px', 'border-bottom-right-radius':'0px'});
		}else if(searchText.length == 0 && searchBtnVisible == true){
			searchBtnVisible = false;
			searchSubmit.css('visibility', 'hidden');
			searchInput.css({'border-top-right-radius':'3px', 'border-bottom-right-radius':'3px'});
		}
	}
	$(window).resize(function(evt){		
		$('#content').css('min-height', $(window).height() - 203 + "px");
		windowHeight = $(window).height();
		scrollChange();
	});
	
}(window));