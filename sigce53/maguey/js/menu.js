//Para iluminar el menu
function act(num)
{
	x=0;
	for(x=1;x<=3;x++)
	{
		enl="vin"+x;
		if(x!=num)
		{
			if($("#"+enl).hasClass("active"))
			{
				$("#"+enl).removeClass("active");
			}
		}
	}
	enl="vin"+num;
	$("#"+enl).addClass("active");
	
}
function act2(num)
{
	x=0;
	for(x=1;x<=3;x++)
	{
		enl="vinr"+x;
		if(x!=num)
		{
			if($("#"+enl).hasClass("active"))
			{
				$("#"+enl).removeClass("active");
			}
		}
	}
	enl="vinr"+num;
	$("#"+enl).addClass("active");
	$("#mnr").css("display","none");
}
