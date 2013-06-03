$(document).bind("mobileinit", function(){
	$.mobile.defaultPageTransition = "slideup";//transiciones de abajo hacia arriba, si tengo pag internas dentro de mi Aplicacion
	$.mobile.defaultDialogTransition = "turn";//transicion donde la pag gira
	$.mobile.loadingMessage = "Por favor espere un momento...";//es el loading o cargando.
	$.mobile.pageLoadErrorMessage = "No se ha podido cargar la página solicitada";//aqui no hay error 404 en jqmobile.

	//Eventos de Gestos
	$('#zonaSensible').bind('tap'), function() {

	};
	$('#zonaSensible').bind('taphold'), function() {//es un click sostenido en el Iphone

	};
	$('#zonaSensible').bind('swipeleft'), function() {//es el clasico pasar pag a la izq.

	};
	$('#zonaSensible').bind('swiperight'), function() {//es el clasico pasar pag a la dercha.

	};

	//Eventos de Orientación
	$('#zonaSensible').bind('orientationchange'), function() {//para reescalar objeto en device. apaisarlo.

	};

	//Eventos de Scroll
	$('#zonaSensible').bind('scrollstart'), function() {//inicio el scroll

	};
	$('#zonaSensible').bind('scrollstop'), function() {

	};

	//Eventos de Página
	$('#zonaSensible').bind('pageinit'), function() {//funciona apenas se carga la pag  interna como externa

	};
	$('#zonaSensible').bind('pageloadfailed'), function() {//

	};
	$('#zonaSensible').bind('pagechange'), function() {//cambia la pag

	};
	$('#zonaSensible').bind('pageshow'), function() {//mostrar pag

	};
	$('#zonaSensible').bind('pagehide'), function() {//oculta cuando se cierra la pag

	};

	//Eventos de Layout
	$('#zonaSensible').bind('updatelayout'), function() {

	};
	$('#zonaSensible').bind('animationComplete'), function() {

	};
});