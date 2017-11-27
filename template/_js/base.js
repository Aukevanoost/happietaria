$(document).ready(function(){

});


$("#trigger").click(function(){
    if($("#trigger").attr("state") == "inactive"){
        $("#trigger").attr("state","active");
        $("#trigger").find("i").attr('class', 'fa fa-times fa-2x');
        //$("#slide1").css( { marginLeft : "-450px" } );
        $("#slide1").addClass("slideLeft");
        $("#slide2").addClass("slideLeft");
        //$("#sideMenu").css( { right : "0px" } );
        $("#sideMenu").addClass("openedMenu");
    }else{
        $("#trigger").attr("state","inactive");
        $("#trigger").find("i").attr('class', 'fa fa-bars fa-2x');
        //$("#slide1").css( { marginLeft : "0px" } );
        $("#slide1").removeClass("slideLeft");
        $("#slide2").removeClass("slideLeft");
        //$("#sideMenu").css( { right : "-450px" } );
        $("#sideMenu").removeClass("openedMenu");
    }
});

$("#rightMobIcon").click(function(){
    if($("#rightMobIcon").attr("state") == "inactive"){
        $("#rightMobIcon").attr("state","active");
        $("#rightMobIcon").find("i").attr('class', 'fa fa-times fa-2x');
        $("#mobileNav").addClass("mobMenuOpen");
    }else{
        $("#rightMobIcon").attr("state","inactive");
        $("#rightMobIcon").find("i").attr('class', 'fa fa-bars fa-2x');
        $("#mobileNav").removeClass("mobMenuOpen");
    }
});

$(".userAcc").click(function(){
	$("#rightMobIcon").attr("state","inactive");
	$("#rightMobIcon").find("i").attr('class', 'fa fa-bars fa-2x');
	$("#mobileNav").removeClass("mobMenuOpen");
   
});



$( "#rightSide" ).click(function() {
	if($("#dropDown").hasClass("opened")){
		$( "#dropDown" ).fadeOut( "fast", function() {
			// Animation complete
		});
		$("#dropDown").removeClass("opened");

	}else{
		$( "#dropDown" ).fadeIn( "fast", function() {
			// Animation complete
		});	
		$("#dropDown").addClass("opened");		
	}

});

