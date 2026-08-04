var existe_temp=0;
function getNoPedido(tipo_peticion)
{
 $.ajax({
	  
	  type: "POST",
	  url: "php/inventario/get_NoPedido.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  datatype: 'json',
	  success: function(response){
		   js_pedido=JSON.parse(response);
		  if(js_pedido.status=='correcto')
		  {
			no_pedido=js_pedido.no_pedido;
			$('#no_pedido').val(no_pedido);
			if(tipo_peticion==0)
			{
			  if(js_pedido.tmp=='si')
			  {
				  if(existe_temp==0)
				  {
				  carga_tmp();				
				  existe_temp=1;
				  }
			  }
			}
		  }
		  else
		  {
			   alert('No se pudo cargar el numero de pedido');
		  }
	  },
	  beforeSend:function()
	  {
		  
		   $("#add_err").html("Loading...")
	  }
  });	
}