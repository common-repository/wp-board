<?php

global $totnew;
require_once('../../../../wp-load.php');
global $wpdb;
global $current_user;
get_currentuserinfo();	

if(isset($_GET["action"])&&$_GET["action"]=="update"){		
			get_currentuserinfo();
			$usrname = $current_user->ID;
			$text=str_replace('"',"'",($_POST["text"]));
			$table = $wpdb->prefix . "wpb_table";
			update_usermeta($usrname, 'last_login', current_time('mysql'));	
			if(isset($_POST["important"])&&$_POST["important"]=="ok"&&current_user_can('administrator')){
				$sql="INSERT into ".$table." (author,text,date,status) values ('".$usrname."','".$text."','".current_time('mysql')."',1)";
			if(get_option("automailimponly")=="1"){
					$userlist=$wpdb->get_results("SELECT * from ".$wpdb->users);
					foreach($userlist as $maillist){
					wp_mail("$maillist->user_email", 'There is a new important message!', $text);	
					}					
				}
			}
			else{ $sql="INSERT into ".$table." (author,text,date) values ('".$usrname."','".$text."','".current_time('mysql')."')";
			if(get_option("automailimponly")=="1"||get_option("automail")=="1"){
					$userlist=$wpdb->get_results("SELECT * from ".$wpdb->users);
					foreach($userlist as $maillist){
					wp_mail("$maillist->user_email", 'There is a new message!', $text);	
					}					
				}
			
			}
			$wpdb->query($sql);	
			$sql= "select count(*) as tot from ".$table;
		$tot =$wpdb->get_results($sql);
		$toti= $tot[0]->tot;
		if($toti>get_option("boarddb_limit")&&get_option("boarddb_limit")!="0")  
			{
				$diff=$toti-get_option("boarddb_limit");
				$sql= "DELETE from ".$table." order by date limit ".$diff;
				$tot =$wpdb->get_results($sql);
			}
						
}

if(isset($_GET["action"])&&$_GET["action"]=="modp"&&isset($_GET["postID"])&&isset($_POST["newtext"])){				
			$table = $wpdb->prefix . "wpb_table";
			get_currentuserinfo();
			$ID = $current_user->ID;
			$postauthor=$wpdb->get_results("SELECT author from ".$table." where ID=".$_GET["postID"]);
			$postauthor=$postauthor[0]->author;
			if($postauthor==$ID||current_user_can('administrator')){
				if(isset($_POST["imp"])&&$_POST["imp"]=="on")
					$sql="UPDATE ".$table." set text='".str_replace('"',"'",($_POST["newtext"]))."' , status = 1 where ID=".$_GET["postID"];
						else $sql="UPDATE ".$table." set text='".str_replace('"',"'",($_POST["newtext"]))."' , status = 0 where ID=".$_GET["postID"];
			$wpdb->query($sql);				
			
			}
	}

if(isset($_GET["action"])&&$_GET["action"]=="delete"&&isset($_GET["postID"])){
			$table = $wpdb->prefix . "wpb_table";
			get_currentuserinfo();
			$ID = $current_user->ID;
			$postauthor=$wpdb->get_results("SELECT author from ".$table." where ID=".$_GET["postID"]);
			$postauthor=$postauthor[0]->author;
			if($postauthor==$ID||current_user_can('administrator')){
			$sql="DELETE from ".$table." where id=".$_GET["postID"];
			$wpdb->query($sql);		
				
			}					
		
	}

if(isset($_GET["action"])&&$_GET["action"]=="refresh"){


	echo'<table width="100%" class="boardtable">
	<tr>
		<th>Author</th><th>Text</th><th>Date</th>
	</tr>';
	$table = $wpdb->prefix . "wpb_table";
	$results=$wpdb->get_results("SELECT * FROM ".$table." order by date desc");
	$i=0;	
	
	$usrname = $current_user->ID;	
	$last = get_usermeta($usrname, 'last_login');	
	$sql= "select count(*) as tot from ".$table." where date > '".$last."' and author!=".$usrname;    
    $tot =$wpdb->get_results($sql);	
	$tot=$tot[0]->tot;	
	
	echo '<form name="multidelform" method="post" action="?action=multidel" target="_self">';
	$totc=get_option("board_limit");
	$counter=0;
	foreach ($results as $result)
		{
			if($totc!="0")
				if($totc>$counter)
					$counter++;
				else break;
		if($result->status=="1") echo"<tr class='important_row'>";
			else if($i<$tot)echo "<tr class='new_row'>";
			else echo "<tr class='normal_row'>";
		$i++;
		$IDpost=$result->id;
		$IDV=$result->author;
		if($IDV=='0') $author="auto-message";
			else{		
				$val = $wpdb->get_results("SELECT user_login from ".$wpdb->users." where ID=".$IDV);	
				$author=$val[0]->user_login;
				}
		if(strcmp(substr($result->text,0,60),$result->text)==0)
			$text=$result->text;
				else $text=	substr($result->text,0,60)."...";
		
		//RIGUARDANTE IL TEMPO
		$timestamp=$result->date;
		$timediff=$wpdb->get_results("SELECT TIMESTAMPDIFF (MINUTE,'".$timestamp."','".current_time('mysql')."') as prova");
		$timediff=$timediff[0]->prova;
		if($timediff==0) $dataval="< 1 min";	
		if($timediff>0&&$timediff<60) $dataval=$timediff. " min";	
		if($timediff>60&&$timediff<((60*24)-1)) $dataval=intval($timediff/60). " hr";
		if($timediff>(60*24)){
		$timediff=$wpdb->get_results("SELECT TIMESTAMPDIFF (DAY,'".$timestamp."','".current_time('mysql')."') as prova");
		$timediff=$timediff[0]->prova;			
		$dataval=$timediff. " days";
		}
		$textp=str_replace("<br />","<br/>\\",nl2br(addslashes($result->text)));
		
		echo "<td class='board_cell' onclick=\"popupdata('".$author."','".$textp."','".$timestamp."')\">".$author."</td>"; 
		echo "<td class='board_cell' onclick=\"popupdata('".$author."','".$textp."','".$timestamp."')\" width=\"50%\" >".nl2br($text)."</td>"; 
		echo "<td class='board_cell'>".$dataval."</td>";
		
		if($IDV==$current_user->ID||current_user_can('administrator'))
		{
		echo "<td class='board_cell'><a onclick=\"popup('".$IDpost."','".$author."','".$textp."','".$timestamp."')\"> <img src='" .plugins_url( "wp-board/img/edit.jpg" ). "' width='15' height='15' title='Modifica'> </a></td>";
		echo "<td class='board_cell'><img onclick='deletereq(".$IDpost.")' src='" .plugins_url( "wp-board/img/del.jpg" ). "' width='15' height='15' title='Elimina'></td>";
	//	echo "<td class='board_cell'><input type='checkbox' name='multidel[]' value='".$IDpost."' ></td>";
		}		
		
		echo "</tr>";
			
		}		
	//echo'</table><input type="submit" value="Cancella notifiche multiple">
	echo '</form>';
	update_usermeta($usrname, 'last_login', current_time('mysql'));	
	}
	

?>