<?php
wp_enqueue_script(‘jquery’);
global $last_sel;
global $wpdb;
?>

<style>	
table.boardtable{
border:none
background: -moz-linear-gradient(top, #FFFFFF, #CCCCCC);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC));
}
textarea.fixed{
resize:none;
}
.hiddenpopup
{
	visibility: hidden;
	display: none;
	z-indez: 1;
}
.unhiddenpopup
{
	position: absolute;
	top:0px;
	bottom:0px;
	left:0px;
	right:0px;
	background: rgba(255, 255, 255, 0.8);
	visibility:visible;
	
	z-index: 100;
}
a.right{
position:absolute;
right:10px;
}
div.proppopup{

position:absolute;
top:50px;
bottom:100px;
left:50px;
right:50px;
background: -moz-linear-gradient(top, #FFFFFF, #CCCCCC);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC));
}

tr.normal_row{	
background: -moz-linear-gradient(top, #FFFFFF, #CCCCCC);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC));
}
tr.normal_row:hover{	
background: -moz-linear-gradient(top, #FFFFFF, #999999);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#999999));
}

tr.important_row{
background: -moz-linear-gradient(top, #FFFFFF, #FF0000);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#FF0000));	
}
tr.important_row:hover{
background: -moz-linear-gradient(top, #FFFFFF, #CC0000);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CC0000));	
}

tr.new_row{
background: -moz-linear-gradient(top, #FFFFFF, #00FF33);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#00FF33));	
}
tr.new_row:hover{
background: -moz-linear-gradient(top, #FFFFFF, #33CC00);
background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#33CC00));	
}
td.board_cell{
text-align:center;	
height: 25px;
padding: 5px;	
}
</style>


<script type="text/javascript" src=<?php echo '"'.plugins_url( "wp-board/js/functions.js" ).'"'; ?>></script>
<script type="text/javascript">
	
	function submitreq(){
if(document.formboard.text.value=="")
	alert("Non lasciare il campo vuoto");
else{
	xmlhttp=GetXmlHttpObject();
   		if (xmlhttp==null)
   		{
        	alert ("Your browser does not support XMLHTTP!");
        	return;
   		}
		var testo=document.formboard.text.value;
		xmlhttp.onreadystatechange=reffun;
  xmlhttp.open("POST",<?php echo '"'.plugins_url( "wp-board/php/actions.php" ).'?action=update"'; ?>,true);
  xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
  <?php
  if(current_user_can('administrator'))
  echo '
  if(document.formboard.important.checked==true)
  xmlhttp.send("text="+testo+"&important=ok");
  else  xmlhttp.send("text="+testo);
  document.formboard.text.value="";
  document.formboard.important.checked=false;';
  else echo 'xmlhttp.send("text="+testo);
  		document.formboard.text.value="";
  ';
	?>
	document.getElementById("actiondiv").className='unhiddenpopup';
	var text = "<center><b>Creating a new message...</b><br><br><img src=' "+<?php echo '"'.plugins_url( "wp-board/img/loader2.gif" ).'"'; ?>+"'></center>";
	document.getElementById("actiondiv").innerHTML=text;	
 	
	
}
}

function deletereq(postID){
xmlhttp=GetXmlHttpObject();
   if (xmlhttp==null)
   {
        alert ("Your browser does not support XMLHTTP!");
        return;
   }
   xmlhttp.onreadystatechange=reffun;
  var txtstr = <?php echo '"'.plugins_url( "wp-board/php/actions.php" ).'?action=delete&postID="';?>; 
  xmlhttp.open("GET",txtstr+postID,true);
  xmlhttp.send(null);
	document.getElementById("actiondiv").className='unhiddenpopup';
	var text = "<center><b>Deleting...</b><br><br><img src=' "+<?php echo '"'.plugins_url( "wp-board/img/loader2.gif" ).'"'; ?>+"'></center>";
	
 	document.getElementById("actiondiv").innerHTML=text;
	
}

function refresh(){
xmlhttp=GetXmlHttpObject();
   if (xmlhttp==null)
   {
        alert ("Your browser does not support XMLHTTP!");
        return;
   }
  xmlhttp.onreadystatechange=getData;
  xmlhttp.open("GET",<?php echo '"'.plugins_url( "wp-board/php/actions.php" ).'?action=refresh"'; ?>,true);
  xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
  xmlhttp.send(null);
  document.getElementById("actiondiv").className='unhiddenpopup';
	var text = "<center><b>Loading...</b><br><br><img src=' "+<?php echo '"'.plugins_url( "wp-board/img/loader2.gif" ).'"'; ?>+"'></center>";
document.getElementById("actiondiv").innerHTML=text;	
    
}

function modreq(postID){
xmlhttp=GetXmlHttpObject();
   if (xmlhttp==null)
   {
        alert ("Your browser does not support XMLHTTP!");
        return;
   }
   	 xmlhttp.onreadystatechange=reffun;
   	 var txtstr = <?php echo '"'.plugins_url( "wp-board/php/actions.php" ).'?action=modp&postID="';?>;
     xmlhttp.open("POST",txtstr+postID,true);
  xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
   var testo=document.form_popup.newtext.value;   
   var imp;
   <?php
  if(current_user_can('administrator'))
  echo '
   if(document.form_popup.imp.checked==true)
   		imp="on";
   	else imp="off";
	xmlhttp.send("newtext="+testo+"&imp="+imp);';
  else echo 'xmlhttp.send("newtext="+testo);';
	?>		
	document.getElementById("actiondiv").className='unhiddenpopup';
	var text = "<center><b>Updating...</b><br><br><img src=' "+<?php echo '"'.plugins_url( "wp-board/img/loader2.gif" ).'"'; ?>+"'></center>";
	document.getElementById("actiondiv").innerHTML=text;	
}
 
function popup(postID,author,textj,date) 
{
var item = document.getElementById("board_div");
if (item) {
	if(item.className=='hiddenpopup')
 	{
 	var text = "<center><b>Modifica la notifica</b></center><br>";
 	text+="Author : "+author+"<br>";
 	text+="<form name='form_popup' method='post'>";
 	text+="Message : <br> <center><textarea name='newtext' class='fixed' cols='30'>"+textj.replace(/<br\/>/g,"\r\n")+"</textarea></center><br>"; 	
 	<?php
 	if(current_user_can('administrator'))
		echo 'text +="Importante : <input name =\'imp\' type=\'checkbox\' ><br>";';
		
 	?>
 	text+="<input type='button' onclick='modreq("+postID+")' value='Aggiorna la notifica' >";
 	text+="</form>"; 		
 	document.getElementById("proppopup").innerHTML=text;	
 		
 	item.className='unhiddenpopup';
 	}
 	else item.className='hiddenpopup';
	}
}

function popupdata(author,textj,date) 
{
var item = document.getElementById("board_div");
if (item) {
	if(item.className=='hiddenpopup')
 	{
 	var text = "<center><b>Visualizza la notifica</b></center><br>";
 	text+="Author : "+author+"<br>";
 	text+="Text : <br><center>"+textj+"</center><br>";
 	text+="Date of creation : "+date+"<br>";
 	document.getElementById("proppopup").innerHTML=text;	
 	item.className='unhiddenpopup';
 	}
 	else item.className='hiddenpopup';
	}
}
	
	
</script>

<?php	
	
	if(isset($_GET["action"])&&$_GET["action"]=="multidel"&&isset($_POST["multidel"])){
		
		foreach($_POST["multidel"]as $multidel)
		{
			$table = $wpdb->prefix . "wpb_table";
			get_currentuserinfo();
			$ID = $current_user->ID;
			$postauthor=$wpdb->get_results("SELECT author from ".$table." where ID=".$multidel);
			$postauthor=$postauthor[0]->author;
			if($postauthor==$ID||current_user_can('administrator')){
			$sql="DELETE from ".$table." where id=".$multidel;
			$wpdb->query($sql);		
			}
		}
			
	}
		get_currentuserinfo();
		$usrname = $current_user->ID;	  
		$last = get_usermeta($usrname, 'last_login');
		$table = $wpdb->prefix . "wpb_table";
		$sql= "select count(*) as tot from ".$table." where date > '".$last."' and author!=".$usrname;
    	$tot =$wpdb->get_results($sql);
		$tot=$tot[0]->tot;	
		if($tot!="0"){
		 	$sql= "select count(*) as tot from ".$table." where date > '".$last."' and status=1";
    		$totimp =$wpdb->get_results($sql);
			$totimp=$totimp[0]->tot;	
			if($totimp!='0') $textimp="di cui ".$totimp." importanti";
			else $textimp='';
		 	echo "<script type='text/javascript'> alert('Hai ".$tot." nuove notifiche ".$textimp."') </script>";
		 }
		
	echo 'Insert a new message<br>	
	<div id="board_div" class="hiddenpopup">
	<a onclick="popup(\'0\')" class="right">Close me</a><br><br>
	<div id="proppopup" class="proppopup"></div>
	</div>	
	<div id="actiondiv" class="hiddenpopup">
	</div>	
	<div id="formdiv" style="z-index: 10">	
	<form name="formboard" method="post" target="_self" action="?action=update">
	Text :<br><center><textarea name="text" class="fixed" cols="55" rows="3"></textarea></center><br>';
	if(current_user_can('administrator'))
		echo 'Important :<input type="checkbox" value="important" name="important"><br>';
	echo '<input type="button" onclick="submitreq()" value="Insert the message">
	</form></div><hr><br>
	<div id="boardcontent" style="height:'.get_option("board_height").'px; overflow:auto; z-index: 10;">
	<script text="text/javascript"> refresh();</script>';
	echo "</div>";
	echo "<br> <P ALIGN='right'><i>Powered by <a href='http://xploreautomation.com' target='_blank'>Automazione Open Source</a></i></P>";
	?>
	
 