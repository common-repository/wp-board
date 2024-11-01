
<div class="wrap">
<h2>WP-Board</h2>

<script type="text/javascript">
	
	function checkform(){
	var error=0;
	var reg = /^\d+$/;	
	if(!reg.test(document.adminform.board_limit.value))
	{document.getElementById("div-max").innerHTML="<font color='red'>Insert only numbers</font>";
	error=1;
	}
	else document.getElementById("div-max").innerHTML="";
	
	if(!reg.test(document.adminform.db_limit.value))
	{document.getElementById("div-dbmax").innerHTML="<font color='red'>Insert only numbers</font>";
	error=1;
	}
	else document.getElementById("div-dbmax").innerHTML="";
	if(!reg.test(document.adminform.db_height.value))
	{document.getElementById("div-dbheight").innerHTML="<font color='red'>Insert only numbers</font>";
	error=1;
	}
	else document.getElementById("div-dbheight").innerHTML="";	
	if(error==1)  document.adminform.submitbutton.disabled=true;
			else  document.adminform.submitbutton.disabled=false;
	}
	
	function updatecheck(){	
	if(document.adminform.automsg.checked==true)
	{
	var newdiv = document.createElement('div');
	//newdiv.innerHTML='<i>Notifiche attive:</i><br>	Creazione bozza <input type="checkbox" name="automsg_draft" <?php if(strcmp(get_option("automsg-draft"),"1")==0) echo "checked"; ?>><br>Passaggio in Revisione <input type="checkbox" name="automsg_rev" <?php if(get_option("automsg-rev")==1) echo "checked"; ?>> <br>Pubblicazione <input type="checkbox" name="automsg_pub" <?php if(get_option("automsg-pub")==1) echo "checked"; ?>> <br>Cancellazione <input type="checkbox" name="automsg_del" <?php if(get_option("automsg-del")==1) echo "checked"; ?>> <br>';
	document.getElementById("divcheck").appendChild(newdiv);
	}
	else document.getElementById("divcheck").innerHTML="";	
								}	
</script>


<?php 

if(isset($_GET["action"])&&$_GET["action"]=="update"){
	
	if($_POST["board_limit"]==""||$_POST["db_limit"]==""||$_POST["db_height"]=="")
		echo "<script type='text/javascript'> alert('You left at least one  empty!'); </script>";
	else{
		update_option("board_limit",$_POST["board_limit"]);
		update_option("board_height",$_POST["db_height"]);
		update_option("boarddb_limit",$_POST["db_limit"]);
		if(isset($_POST["mail"]))		{
				if($_POST["mail"]=="none"){
						update_option("automailimponly",0);
						update_option("automail",0);
				}
				if($_POST["mail"]=="all"){
						update_option("automailimponly",0);
						update_option("automail",1);
				}
				if($_POST["mail"]=="imp"){
						update_option("automailimponly",1);
						update_option("automail",0);
				}		
			
		}
		
		
		if(isset($_POST["automsg"])) 
		{
		update_option("automsg",1);
		if(isset($_POST["automsg_draft"]))
			update_option("automsg-draft",1);
			 else  update_option("automsg-draft",0);
		if(isset($_POST["automsg-rev"]))
			update_option("automsg-rev",1);
			 else update_option("automsg-rev",0);
		if(isset($_POST["automsg-pub"]))
			update_option("automsg-pub",1);
			 else update_option("automsg-pub",0);
		if(isset($_POST["automsg-del"]))
			update_option("automsg-del",1);		
			 else update_option("automsg-del",0);
		}
		 else {
		 	update_option("automsg",0);
			update_option("automsg-draft",0);
			update_option("automsg-rev",0);
			update_option("automsg-pub",0);
			update_option("automsg-del",0);
		 	}
			echo '<div id="message" class="updated fade"> 
					Data <strong>Update</strong></div>';	
	}
	
}

?>
	<table width="100%">
		
		<tr>
			<td colspan="2"><hr></td>
		</tr>
	<form id="adminform" name="adminform" method="post" target="_self"  action="?page=<?php echo $_GET['page']; ?>&action=update">
		<tr>
			<td>
				<center><b>General</b></center>
			</td>	
			<td>
		Number of records in the database :  
		<?php
		$table = $wpdb->prefix . "wpb_table";
		$sql= "select count(*) as tot from ".$table;
		$tot =$wpdb->get_results($sql);
		$toti= $tot[0]->tot;
		if($toti>get_option("boarddb_limit")&&get_option("boarddb_limit")!="0")  
			{
				$diff=$toti-get_option("boarddb_limit");
				$sql= "DELETE from ".$table." order by date limit ".$diff;
				$tot =$wpdb->get_results($sql);
				echo get_option("boarddb_limit");
			}
			else echo $toti;
		?><br>
		Max number of records in the database : <input name="db_limit" onchange="checkform()" value="<?php echo get_option("boarddb_limit")?>">
		<div ID="div-dbmax"></div>
		<br>
		Activate the notification while changing the state of an article : <input onclick="updatecheck()" type="checkbox" name="automsg" <?php if(get_option("automsg")==1) echo "checked" ?> >
		
		<br>
		<br>
		don't send e-mail when there is a new message : <input  type="radio" name="mail" value="none" <?php if(get_option("automail")==0&&get_option("automailimponly")==0) echo "checked" ?> ><br>
		Auto send e-mail anytime there is a new message : <input  type="radio" name="mail" value="all" <?php if(get_option("automail")==1) echo "checked" ?> ><br>
		Auto send e-mail only when there is a new important message : <input  type="radio" value="imp" name="mail" <?php if(get_option("automailimponly")==1) echo "checked" ?> ><br>
		<br>
		<div id="divcheck">			
		</div>
		<script type="text/javascript">
		updatecheck();		
		</script>
					</td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>
				<center><b>Board</b></center>
			</td>	
			<td>
		Max number of messages in the board : <input name="board_limit" onchange="checkform()" value="<?php echo get_option("board_limit")?>">
		<div ID="div-max"></div>
		<br>
		Board lenght (pixel) : <input name="db_height" onchange="checkform()" value="<?php echo get_option("board_height")?>">
		<div ID="div-dbheight"></div>
		
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
		</table>
		<input type="submit" name="submitbutton" value="Salva modifiche">
	
		
	</form>	



</div>