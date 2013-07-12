
//  Begin Mensaje-------------------------------------------------------------------------------------------------------------->
/*
Created by A1 JavaScripts - http://www.a1javascripts.com/
This may be used freely as long as this msg is intact!
*/
msgFont='Arial,helvetiva';
msgFontSize="12";
msgFontColor="black"
/*
Here's the array that holds the text to change the divmessage to
when you mouseover. Change the text here
*/
function evento(dia,mes,titulo){
this.dia = dia
this.mes = mes
this.titulo = titulo
}

var eventos = new Array()
eventos[0] = new evento (12,7,"onclick=TINY.box.show({url:'CAL/julio.html',width:800,height:500})")
eventos[1] = new evento (19,7,"onclick=TINY.box.show({url:'CAL/julio.html',width:800,height:500})")
eventos[2] = new evento (31,7,"onclick=TINY.box.show({url:'CAL/julio.html',width:800,height:500})")



function b_writeIt(obj, text){
	document.getElementById(obj).innerHTML=text		
}

function changeText(num){
	b_writeIt('divMessage',eventos[num].titulo)
}

function borrarText(){
	b_writeIt('divMessage',' ')
}
//  End Mensaje-------------------------------------------------------------------------------------------------------------->


<!-- STEP ONE: Place the following script into a separate JavaScript file called: calendar.js	 -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
//  SET ARRAYS
var day_of_week = new Array('Do','Lu','Ma','Mi','Ju','Vi','Sa');
var month_of_year = new Array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Deciembre');

//  DECLARE AND INITIALIZE VARIABLES
var Calendar = new Date();
	    
var month = Calendar.getMonth();    // Returns month (0-11)
var today = Calendar.getDate();    // Returns day (1-31)
var weekday = Calendar.getDay();    // Returns day (1-31)

// Returns year
if(Calendar.getFullYear){
year = Calendar.getFullYear();//IE
}else{
year = Calendar.getYear()+1900;}//Mozilla Firefox

var DAYS_OF_WEEK = 7;    // "constant" for number of days in a week
var DAYS_OF_MONTH = 31;    // "constant" for number of days in a month
var cal;    // Used for printing

Calendar.setDate(1);    // Start the calendar day at '1'
Calendar.setMonth(month);    // Start the calendar month at now


/* VARIABLES FOR FORMATTING
NOTE: You can format the 'BORDER', 'BGCOLOR', 'CELLPADDING', 'BORDERCOLOR'
      tags to customize your caledanr's look. */

var TR_start = '<TR>';
var TR_end = '</TR>';
var highlight_start = '<TD WIDTH="15"><TABLE CELLSPACING=0 id=hoy BORDER=1 BGCOLOR=DEDEFF BORDERCOLOR=CCCCCC style="font-size:' +msgFontSize+'px; font-family:'+msgFont+'; color:'+msgFontColor+'"><TR><TD WIDTH=15><B><CENTER>';
var highlight_end   = '</CENTER></TD></TR></TABLE></B>';
var TD_start = '<TD WIDTH="15" height="15" id="TDS"><CENTER>';
var TD_end = '</CENTER></TD>';
var TD_start2 = '<TD WIDTH="15" height="15" id="TDS2"><CENTER>';
var TD_end2 = '</CENTER></TD>';


/* BEGIN CODE FOR CALENDAR
NOTE: You can format the 'BORDER', 'BGCOLOR', 'CELLPADDING', 'BORDERCOLOR'
tags to customize your calendar's look.*/

cal =  '<TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 BORDERCOLOR=BBBBBB style="font-size:' +msgFontSize+'px; font-family:'+msgFont+'; color:'+msgFontColor+'"><TR><TD>';
cal += '<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 style="font-size:' +msgFontSize+'px; font-family:'+msgFont+'; color:'+msgFontColor+'">' + TR_start;
cal += '<TD COLSPAN="' + DAYS_OF_WEEK + '" id="meses"><CENTER><B>';
cal += month_of_year[month]  + '   ' + year + '</B>' + TD_end + TR_end;
cal += TR_start;

//   DO NOT EDIT BELOW THIS POINT  //

// LOOPS FOR EACH DAY OF WEEK
for(index=0; index < DAYS_OF_WEEK; index++)
{
	cal += TD_start2 + day_of_week[index] + TD_end2;
}

cal += TD_end + TR_end;
cal += TR_start;

// FILL IN BLANK GAPS UNTIL TODAY'S DAY
for(index=0; index < Calendar.getDay(); index++)
cal += TD_start + '  ' + TD_end;

// LOOPS FOR EACH DAY IN CALENDAR
for(index=0; index < DAYS_OF_MONTH; index++) 
{
if( Calendar.getDate() > index )
{
  // RETURNS THE NEXT DAY TO PRINT
  week_day =Calendar.getDay();

  // START NEW ROW FOR FIRST DAY OF WEEK
  if(week_day == 0)
  cal += TR_start;

  if(week_day != DAYS_OF_WEEK)
  {
	  // SET VARIABLE INSIDE LOOP FOR INCREMENTING PURPOSES
	  var day  = Calendar.getDate();
		
	  // PRINTS DAY
	 	var iEvent;
		for(iEvent=0; iEvent < eventos.length; iEvent++)
		{
			if( ((month+1)==eventos[iEvent].mes)  && (day==eventos[iEvent].dia) )//cuando coincidan con los eventos-JAIDER
		  		day = "<a href=# id=acal " + eventos[iEvent].titulo + ">"+ day +"</a>";
		}
		
		 // HIGHLIGHT TODAY'S DATE
		 if( today==Calendar.getDate() )		 
		 	 cal += highlight_start + day + highlight_end + TD_end;
	 	 else	//dias normales
		  cal += TD_start + day + TD_end;	  	
  } 

  // END ROW FOR LAST DAY OF WEEK
  if(week_day == DAYS_OF_WEEK)
  cal += TR_end;
  }

  // INCREMENTS UNTIL END OF THE MONTH
  Calendar.setDate(Calendar.getDate()+1);

}// end for loop

cal += '</TD></TR></TABLE></TABLE>';

//  End Calendario---------------------------------------------------------------------------------------------------------------->
