<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = intval($_GET["cal"]);

global $wpdb;

$message = "";

if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfpoll_update_actions_mlist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="https://wordpress.org/support/plugin/cp-polls#new-post">support service</a> for verification and solution. Thank you.';
        return;
    }
     
    $wpdb->query( $wpdb->prepare( 'DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE id=%d', $_GET['ld'] ) );       
    $message = "Item deleted";
}
else if (isset($_GET['import']) && $_GET['import'] == '1')
{        
    $verify_nonce = wp_verify_nonce( $_POST['rsave'], 'cfpoll_update_actions_mlist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="https://wordpress.org/support/plugin/cp-polls#new-post">support service</a> for verification and solution. Thank you.';
        return;
    }
        
    $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_POLLS_DEFAULT_form_structure)));
    $form = $form[0];    
    
    if (($handle = fopen($_FILES['importfile']['tmp_name'], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rowdata = array(); 
            $formatted_data = '';
            $num = count($data);
            $row++;
            
            $time  = strip_tags($data[0]);
            $ip    = strip_tags($data[1]);
            $email = strip_tags($data[2]);
            
            for ($c=3; $c < $num; $c++)
                if (isset($form[$c-3]))
                {
                    $rowdata[$form[$c-3]->name] = $data[$c]; //echo $data[$c] . "<br />\n";
                    $formatted_data .= $form[$c-3]->title. ": ". $data[$c] . "\n\n";
                }                    
            $wpdb->insert($wpdb->prefix.$this->table_messages, array( 
                                   'formid' => $this->item,
                                   'time' => $time,
                                   'ipaddr' => $ip,
                                   'notifyto' => $email,
                                   'data' => $formatted_data,
                                   'posted_data' => serialize($rowdata),
                             ), 
                                   array('%d','%s','%s','%s','%s','%s')
                             );            
        }
        fclose($handle);
    }    
    $message = "CSV File Imported.";
}

if ($this->item != 0)
    $myform = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id=%d', $this->item) );


$current_page = isset($_GET["p"]) ? intval($_GET["p"]) : 0;
if (!$current_page) $current_page = 1;
$records_per_page = 50;                                                                                  

$cond = '';
if (!empty($_GET["search"])) $cond .= " AND (id=".intval($search)." OR data like '%".esc_sql($_GET["search"])."%' OR posted_data LIKE '%".esc_sql($_GET["search"])."%')";
if (!empty($_GET["dfrom"])) $cond .= " AND (`time` >= '".esc_sql($_GET["dfrom"])."')";
if (!empty($_GET["dto"])) $cond .= " AND (`time` <= '".esc_sql($_GET["dto"])." 23:59:59')";
if ($this->item != 0) $cond .= " AND formid=".$this->item;

$events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );
$total_pages = ceil(count($events) / $records_per_page);

if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".esc_html($message)."</strong></p></div>";

$nonce = wp_create_nonce( 'cfpoll_update_actions_mlist' );

?>
<script type="text/javascript">
 function cp_deleteMessageItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&ld='+id+'&r='+Math.random();
    }
 }
</script>
<div class="wrap">
<h1><?php echo esc_html($this->plugin_name); ?> - Message List</h1>

<input type="button" name="backbtn" value="Back to items list..." onclick="document.location='admin.php?page=<?php echo $this->menu_parameter; ?>';">


<div id="normal-sortables" class="meta-box-sortables">
 <hr />
 <h3>This message list is from: <?php if ($this->item != 0) echo esc_html($myform[0]->form_name); else echo 'All forms'; ?></h3>
</div>


<form action="admin.php" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>" />
 <input type="hidden" name="cal" value="<?php echo $this->item; ?>" />
 <input type="hidden" name="list" value="1" />
 <nobr>Search for: <input type="text" name="search" value="<?php echo esc_attr( isset($_GET["search"]) ? $_GET["search"] : '' ); ?>" /> &nbsp; &nbsp; &nbsp;</nobr> 
 <nobr>From: <input type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr( isset($_GET["dfrom"]) ? $_GET["dfrom"] : '' ); ?>" /> &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>To: <input type="text" id="dto" name="dto" value="<?php echo esc_attr( isset($_GET["dto"]) ? $_GET["dto"] : '' ); ?>" /> &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>Item: <select id="cal" name="cal">
          <option value="0">[All Items]</option>
   <?php
    $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
    foreach ($myrows as $item)  
         echo '<option value="'.$item->id.'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.htmlentities($item->form_name).'</option>'; 
   ?>
    </select></nobr>
 <nobr><span class="submit"><input type="submit" name="ds" value="Filter" /></span> &nbsp; &nbsp; &nbsp; 
 <span class="submit"><input type="submit" name="<?php echo $this->prefix; ?>_csv" value="Export to CSV" /></span></nobr>
</form>

<br />
                             
<?php


echo paginate_links(  array(
    'base'         => 'admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&list=1%_%&dfrom='.esc_url(isset($_GET["dfrom"]) ? $_GET["dfrom"] : '' ).'&dto='.esc_url(isset($_GET["dto"]) ? $_GET["dto"] : '' ).'&search='.esc_url(isset($_GET["search"]) ? $_GET["search"] : '' ),
    'format'       => '&p=%#%',
    'total'        => $total_pages,
    'current'      => $current_page,
    'show_all'     => False,
    'end_size'     => 1,
    'mid_size'     => 2,
    'prev_next'    => True,
    'prev_text'    => __('&laquo; Previous'),
    'next_text'    => __('Next &raquo;'),
    'type'         => 'plain',
    'add_args'     => False
    ) );

?>

<div id="dex_printable_contents">
<table class="wp-list-table widefat fixed pages" cellspacing="0">
	<thead>
	<tr>
      <th style="padding-left:7px;font-weight:bold;">ID</th>
	  <th style="padding-left:7px;font-weight:bold;">Date</th>
	  <th style="padding-left:7px;font-weight:bold;">Email</th>
	  <th style="padding-left:7px;font-weight:bold;">Message</th>
	  <th style="padding-left:7px;font-weight:bold;"  class="cpnopr">Options</th>	
	</tr>
	</thead>
	<tbody id="the-list">
	 <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
	  <tr class='<?php if (!($i%2)) { ?>alternate <?php } ?>author-self status-draft format-default iedit' valign="top">
        <td><?php echo $events[$i]->id; ?></td>
		<td><?php echo substr($events[$i]->time,0,16); ?></td>
		<td><?php echo esc_html(sanitize_email($events[$i]->notifyto)); ?></td>
		<td><?php 
		       
		        $data = $events[$i]->data;		        
		        $posted_data = unserialize($events[$i]->posted_data);		        
		        foreach ($posted_data as $item => $value)
		            if (strpos($item,"_url") && $value != '')		         
		            {
		                $data = str_replace ($posted_data[str_replace("_url","",$item)],'<a href="'.$value.'" target="_blank">'.$posted_data[str_replace("_url","",$item)].'</a><br />',$data);  		                
		            }    
		        echo str_replace("\n","<br />", str_replace('<','&lt;',$data) ); 
		    ?></td>
		<td class="cpnopr">
		  <input type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="Delete" onclick="cp_deleteMessageItem(<?php echo $events[$i]->id; ?>);" />                             
		</td>
      </tr>
     <?php } ?>
	</tbody>
</table>
</div>

<p class="submit"><input type="button" name="pbutton" value="Print" onclick="do_dexapp_print();" /></p>

</div>

<?php if ($this->item) { ?>
<div id="normal-sortables" class="meta-box-sortables">

 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Import CSV File</span></h3>
  <div class="inside">
  
   <form name="CPImportForm" action="admin.php?page=CP_Polls&cal=<?php echo $this->item; ?>&list=1&import=1" method="post" enctype="multipart/form-data">
   <input type="file" name="importfile" />
   <input type="hidden" name="rsave" value="<?php echo $nonce; ?>" />
   <input type="submit" name="pbuttonimport" value="Import"/>
   <p>Instructions: Comma separated CSV file. One record per line, one field per column. <strong>Don't use a header row with the field names</strong>.</p>
   <p>The first 3 columns into the CSV file are the <strong>time, IP address and email address</strong>, if you don't have this information then leave the first three columns empty. 
      After those initial columns the fields (columns) must appear in the same order than in the form.</p>
   <p>Sample format for the CSV file:</p>
   <pre>
    <span style="color:#009900;">2013-04-21 18:50:00, 192.168.1.12, john@sample.com,</span> "john@sample.com", "sample subject", "sample message text"
    <span style="color:#009900;">2013-05-16 20:49:00, 192.168.1.24, jane.smith@sample.com,</span> "jane.smith@sample.com", "other subject", "other message"
   </pre>
   </form>
  </div>
</div>
<?php } ?>

<script type="text/javascript">
 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>.cpnopr{display:none;};table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();    
 }
 
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 });
 
</script>














