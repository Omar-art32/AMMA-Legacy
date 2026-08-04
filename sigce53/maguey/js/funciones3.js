$(document).ready(function(){
	verlistado3();
});

function verlistado3(){
	var randomnumber=Math.random()*11;
	$.post("libs/listar3.php", {
		randomnumber:randomnumber
	}, function(data){
		$("#contenido3").html(data);
	});
}
