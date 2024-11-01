<?php
get_currentuserinfo();
$usrname = $current_user->ID;
$last = get_usermeta($usrname, 'last_login');
$table = $wpdb->prefix . "wpb_table";
$sql= "select count(*) as tot from ".$table." where date > '".$last."'";
$tot =$wpdb->get_results($sql);
$tot=$tot[0]->tot;       
global $wp_admin_bar; 

 if($tot!=0){
		 	$sql= "select count(*) as tot from ".$table." where date > '".$last."' and status=1";
    		$totimp =$wpdb->get_results($sql);
			$totimp=$totimp[0]->tot;	
			if($totimp!='0'){
			 $wp_admin_bar->add_menu(array('title' => "New messages (".$tot.") important (".$totimp.")" , 'id' => "wp-board-menu" , 'href' => admin_url()));
			$sql= "select author,text from ".$table." where date > '".$last."' and status=1 order by date desc";
			$tot =$wpdb->get_results($sql);
			foreach ($tot as $result)
				{
				if(strcmp(substr($result->text,0,60),$result->text)==0)
				$text=$result->text;
				else $text=	substr($result->text,0,60)."...";
				
				if($result->author=='0') $author="auto-message";
				else{		
				$val = $wpdb->get_results("SELECT user_login from ".$wpdb->users." where ID=".$result->author);	
				$author=$val[0]->user_login;
				}
					
				$wp_admin_bar->add_menu(array('title' => "(IMP) ".$author." : ".$text , 'parent' => "wp-board-menu", 'href' => admin_url()));
				}
			$sql= "select author,text from ".$table." where date > '".$last."' and status!=1 order by date desc";
			$tot =$wpdb->get_results($sql);
			foreach ($tot as $result)
				{
				if(strcmp(substr($result->text,0,60),$result->text)==0)
				$text=$result->text;
				else $text=	substr($result->text,0,60)."...";
				
				if($result->author=='0') $author="auto-message";
				else{		
				$val = $wpdb->get_results("SELECT user_login from ".$wpdb->users." where ID=".$result->author);	
				$author=$val[0]->user_login;
				}
				$wp_admin_bar->add_menu(array('title' => $author." : ".$text , 'parent' => "wp-board-menu",'href' => admin_url()));
				
				}
			}
			else {
				$wp_admin_bar->add_menu(array('title' => "New messages (".$tot.") No important ones", 'id' => "wp-board-menu",'href' => admin_url()));
				$sql= "select author,text from ".$table." where date > '".$last."'";
				$tot =$wpdb->get_results($sql);
			foreach ($tot as $result)
				{
				if(strcmp(substr($result->text,0,60),$result->text)==0)
				$text=$result->text;
				else $text=	substr($result->text,0,60)."...";
				
				if($result->author=='0') $author="auto-message";
				else{		
				$val = $wpdb->get_results("SELECT user_login from ".$wpdb->users." where ID=".$result->author);	
				$author=$val[0]->user_login;
				}
				$wp_admin_bar->add_menu(array('title' => $author." : ".$text , 'parent' => "wp-board-menu", 'href' => admin_url()));
				
				
				}
			}
			} 
else  $wp_admin_bar->add_menu(array('title' => "There aren't new messages",'href' => admin_url()));  
//update_usermeta($usrname, 'last_login', current_time('mysql'));
 ?>      