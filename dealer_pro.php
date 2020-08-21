<?php
/*
Plugin Name: Dealer Pro
Description: Dealer Pro
Version: 1.0.0
Plugin URI: https://www.fiverr.com/wpmubeen 
Author: LogicsBuffer
Author URI: http://logicsbuffer.com/
*/
add_action('show_user_profile', 'my_user_profile_edit_action');
add_action('edit_user_profile', 'my_user_profile_edit_action');
add_action( 'save_post', 'wpt_save_events_meta', 1, 2 );
add_shortcode('dealer_dashboard', 'dealerDashboard');
add_shortcode('registration_dealer', 'registrationPortal');
add_action('wp_enqueue_scripts', 'geo_my_wp_script_front_css');
add_action('wp_enqueue_scripts', 'geo_my_wp_script_front_js');
add_action('admin_enqueue_scripts', 'geo_my_wp_script_back_css');
add_action('init', 'wp_astro_init');
add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );
add_shortcode( 'mail_template', 'mail_template');
add_shortcode( 'show_admin_dashboard', 'wp_show_admin_dashboard');
add_shortcode( 'file_uplaoder', 'wp_file_uplaoder');
add_filter('wp_mail_from_name', 'from_dealer_name');	
add_filter('wp_mail_from', 'from_dealer_from');
add_action( 'wp_ajax_yezter_request_sign',  'send_request_signin' );	
add_action( 'wp_ajax_nopriv_yezter_request_sign',  'send_request_signin' );
add_action('wp_ajax_cvf_upload_files', 'cvf_upload_files');
add_action('wp_ajax_nopriv_cvf_upload_files', 'cvf_upload_files'); // Allow front-end submission 
add_shortcode('dealers', 'allDealers');
register_activation_hook( __FILE__,  'install' );
function dealerFiles($userID){
	$args = array(
	  'author' => $userID,
	  'post_type' => 'file',  
	  'orderby'       =>  'post_date',
	  'order'         =>  'DESC',
	  'posts_per_page' => -1 // no limit
	);
	$results = get_posts( $args );
	
	?>
	<div class="row info-wrapper minheightsec">
	
		<table class="table table-bordered">
			
		<?php
		if($results){ ?>
			<thead>
				<tr>
					<th class="tdcol counter" >#</th>
					<th class="tdcol datetime" >Create Time</th>
					<th class="tdcol manufacture" >Manufacturer</th>
					<th class="tdcol model" >Model</th>
					<th class="tdcol dealer" >Dealer</th>
					<th class="tdcol options" >Options</th>
					<th class="tdcol status" >Status</th>
					<th class="tdcol file" >File</th>
					<th class="tdcol file" >View Details</th>
				</tr>
			</thead>
			<tbody><?php
			$count = 0;
			foreach($results as $post){ $count ++;
				$manufacturer = get_post_meta( $post->ID, 'manufacturer', true );
				$model = get_post_meta( $post->ID, 'model', true );
				$fuel_type = get_post_meta( $post->ID, 'fuel_type', true );
				$variant = get_post_meta( $post->ID, 'variant', true );
				$stage = get_post_meta( $post->ID, 'stage', true );
				$vehicle_registration = get_post_meta( $post->ID, 'vehicle_registration', true );
				$transmission = get_post_meta( $post->ID, 'transmission', true );
				$mileage = get_post_meta( $post->ID, 'mileage', true );
				$tool_used = get_post_meta( $post->ID, 'tool_used', true );
				$vehicle_age = get_post_meta( $post->ID, 'vehicle_age', true );
				$free_options = get_post_meta( $post->ID, 'free_options', true );
				$paid_options = get_post_meta( $post->ID, 'paid_options', true );
				$dealer_file = get_post_meta( $post->ID, 'dealer_file', true );
				$comments = get_post_meta( $post->ID, 'comments', true );
				$currentdate = get_the_date( 'g:ia d/m/y' , $post->ID );
				
				$postdate = $post->post_date;
				
				//$postDate = date("j F Y", $postdate);
				//$postDate = date("j M", $postdate);
			
				$datetime = date( "j M Y h:i:s A", strtotime( $postdate ) );
				$paidoption = get_post_meta( $post->ID, 'paid_options', true );
				
				$post_author_id = get_post_field( 'post_author', $post->ID );
				//$username = get_user_meta($post_author_id,'full_name',true);
				$username = get_user_meta($post_author_id,'user_company',true);
				$user_info = get_userdata($post_author_id);
				$user_email = $user_info->user_email;
				$voidstatus = get_post_meta( $post->ID ,'void_reason',true );
	
				$filestatus = get_post_meta( $post->ID ,'dealer_post_status',true );
				if(!empty($filestatus)){
					$status = $filestatus;
				}else{
					$status = 'Processing';
				}
				$download_img_link = plugins_url('images/download_img.png', __FILE__);
				$file_n = '<a href="'.$dealer_file.'" class="invoice_link btn btn-default d-right margin-clear" download><img src="'.$download_img_link.'"></a>';
			?>


			<tr>
				<td class="tdcount " ><?php echo $count; ?></td>
				<td class="dateloop" ><?php echo $datetime; ?></td>
				<td class="manloop" ><?php echo $manufacturer; ?></td>
				<td class="modelloop" ><?php echo $model; ?></td>
				<td class="dealerloop" ><?php echo $username; ?></td>
				<td class="optionloop" >
					<b>Free Option: </b><?php echo $free_options; ?><br/>
					<b>Paid Option: </b><?php echo $paidoption; ?><br/>
				</td>
				<td class="statusloop" style="text-transform: capitalize;" ><?php echo $status; ?></td>
				<td class="fileloop" >
					<?php echo $file_n;
					/* if( $status == 'Cancel' ){ ?>
						<b>Voided</b><br/>
						<p><b>Voided Reason: </b><?php echo $voidstatus; ?></p><?php
					}else{
					?>
					<form action="" style="border-bottom: 2px solid #fee289;" class="ci_form2" id="form_void" method="post">
						<input name="post_id" value="<?php echo $post->ID; ?>" type="hidden">
						<div >
							<label class="btn btn-submit void_check_btn upload_btn_check" >
								<input id="void_check_<?php echo $count; ?>" counter="<?php echo $count; ?>" required name="void_check" value="yes" type="checkbox"> 
								<span class="void_check_text2">Void</span>
							</label>
							<div id="void_msg_<?php echo $count; ?>" class="show_void_text text_reason" style="display:none;">
								<textarea placeholder="Void Reason" required class="void_reason <?php echo $count; ?>" name="void_reason"></textarea>
							</div>
							<input class="btn btn-submit reupload_file" name="void_file_submit" Value="Resubmit" type="submit">
						</div>
					</form>
					<form action=""  onsubmit="reuploadfile_<?php echo $count; ?>();"  class="ci_form2" id="form_aprovded_<?php echo $count; ?>" method="post">	
						<input name="post_id" value="<?php echo $post->ID; ?>" type="hidden">
						<input name="reupload_file_url" id="chkfile_<?php echo $count; ?>" required value="" type="hidden">
						<div class="btn btn-submit void_check_btn upload_btn_check">
							<label class="void_label22">
								<input  id="upload_btn_check_<?php echo $count; ?>" required  name="void_check" value="approved_file" type="checkbox"> 
								<span class="void_check_text ">Approved</span>
							</label>
							<div class= "upload-response-<? echo $count; ?>"></div>
							<div class="upload_buttons" id="uploadform_<?php echo $count; ?>" style="display: none;">
								<?php echo do_shortcode('[file_uplaoder count ='.$count.']'); ?>
							</div>
						</div>
						<input class="btn btn-submit reupload_file" style="display:none;" id="aprove_btn_<?php echo $count; ?>" name="reupload_file_submit" Value="Resubmit" type="submit">
					</form>
					<?php
					} */
					?>
					<script>
						/* function reuploadfile_<?php echo $count; ?>(){
							event.preventDefault();
							var uploadurl = jQuery("#chkfile_<?php echo $count; ?>").val();
							
							if(uploadurl) {
								jQuery( "#form_aprovded_<?php echo $count; ?>" ).submit();
							}else{
								alert('file upload is required');
								return false;
							}
							
						} */
						
						jQuery('#void_check_<?php echo $count; ?>').change(function(){
							if (this.checked) {
								jQuery('#void_msg_<?php echo $count; ?>').show( );
							}
							else {
								jQuery('#void_msg_<?php echo $count; ?>').hide( );
							}                   
						});
						
						jQuery('#upload_btn_check_<?php echo $count; ?>').change(function(){
							if (this.checked) {
								jQuery('#uploadform_<?php echo $count; ?>').show( );
							}
							else {
								jQuery('#uploadform_<?php echo $count; ?>').hide( );
							}                   
						});
						
						
						// When the Upload button is clicked...
						//jQuery( "#uploadfile_<? echo $count; ?>" ).click(function(e) {
						jQuery("#uploadfile_<? echo $count; ?>").on("change", function (e) {
							jQuery(".upload-response-<? echo $count; ?>").html('');
							jQuery("#aspk_gif_img_<? echo $count; ?>").show();
							jQuery("#uploadfile_<? echo $count; ?>").attr('disabled','disabled');
							btn_upload_id = jQuery(this).attr('data_val');
							console.log(btn_upload_id);
							e.preventDefault;
							
							
							if(jQuery('.files-data-<? echo $count; ?>').prop('files').length > 0){
								var fd = new FormData();
								var files_data = jQuery('.upload-form .files-data-<? echo $count; ?>'); // The <input type="file" /> field
								console.log(files_data);
								// Loop through each data and create an array file[] containing our files data.
								jQuery.each(jQuery(files_data), function(i, obj) {
									jQuery.each(obj.files,function(j,file){
										fd.append('files[' + j + ']', file);
									})
								});
								
								// our AJAX identifier
								fd.append('action', 'cvf_upload_files');  
								
								// Remove this code if you do not want to associate your uploads to the current page.
							 //   fd.append('post_id', <?php echo $post->ID; ?>); 

								jQuery.ajax({
									type: 'POST',
									action: 'cvf_upload_files',
									url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
									data: fd,
									contentType: false,
									processData: false,
									success: function(response){
										var obj = JSON.parse(response);
										console.log(obj);
										if( obj.st == 'pass'){
											jQuery('#aprove_btn_<?php echo $count; ?>').show( );
											jQuery('#chkfile_<?php  echo $count; ?>').val( obj.fileurl );
											jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
											jQuery('#uploadform_<?php echo $count; ?>').hide( );
											jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										}else{
											jQuery('#aprove_btn_<?php echo $count; ?>').hide( );
											jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
											jQuery('#uploadfile_<? echo $count; ?>').attr('checked', false); 
											jQuery('#uploadform_<?php echo $count; ?>').hide( );
											jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										}
										
									}
								});
							}else{
								jQuery('#uploadfile_<? echo $count; ?>').attr('checked', false); 
								jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
								jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
								alert('Please select any file and then continue');
							}
						});
					</script>
				</td>
				<td class="details" >
					<a class="btn btn-primary"  href="<?php echo home_url().'/admin-dashboard?fileid='.$post->ID; ?>">Full Info</a>
				</td>
			</tr>
			<?php
			} ?>
			</tbody><?php
		}else{
			echo "<h2 class='centertext'>No Record Found!</h2>";
		}
			?>
		</table>
	</div>
	<?php
}
function allDealers(){
	ob_start();
	$user_id = get_current_user_id();
	$user = new WP_User( $user_id );
	$role_user = $user->roles[0];
?>
<style>
thead {
    background: #43b0ef;
    color: white;
    font-weight: 800!important;
}
.leftpad{
	padding-left:10px !important;
}
.info-wrapper{
	margin-bottom:3em;
}
.buttonlink{
	text-transform: capitalize;
    font-size: 15px;
}
</style>
<?php
	if( $role_user == 'administrator'){ ?>
		<div class="container" action="profileinfo">
			<div class="row" ><h1 style="text-align:center;"><a href="<?php echo home_url().'/admin-dashboard'; ?>"><input type="button" value="Admin Dashboard" class="buttonlink btn btn-primary"></a></h1></div>
		</div>
		<?php
		if( isset($_GET['userid']) ){
			$userID = $_GET['userid'];
			$user_info = get_userdata($userID);
			$user_email = $user_info->user_email;
			$name = get_user_meta( $userID, 'full_name', true );
			$company = get_user_meta( $userID, 'user_company', true );
			$mobile = get_user_meta( $userID, 'user_mobile', true );
			$address = get_user_meta( $userID, 'user_address',true );
		?>
		<div class="row" action="profileinfo">
			<p><a href="<?php echo get_permalink(); ?>"><input type="button" value="Back" class="btn btn-primary"></a></p>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="userdetail leftpad" >Name</th>
						<th class="userdetail" >Company name</th>
						<th class="userdetail" >Mobile Number</th>
						<th class="userdetail" >Email</th>
						<th class="userdetail" >Address</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="leftpad"> <?php echo $name; ?></td>
						<td> <?php echo $company; ?></td>
						<td> <?php echo $mobile; ?></td>
						<td> <?php echo $user_email; ?></td>
						<td> <?php echo $address; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<h1 class="uploadedfiles">Uploaded Files</h1>
			<?php
			dealerFiles($userID);
		}else{
			wp_show_dealer_list();
		}
	}else{
		?><h3>Only Admin can see this page.</h3><?php 
	}
	return ob_get_clean();
}

function cvf_upload_files(){
    
    $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
    $valid_formats = array("zip","rar","tun","pak", "mod", "org", "ori", "mpc", "cod", "unq", "dec", "fpf", "dat", "bin" ,"fpf"); // Supported file types
    $max_file_size = 100 * 1024 * 1024 * 1024; // in 100mb
    $max_image_upload = 1; // Define how many images can be uploaded to the current post
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;

    $attachments = get_posts( array(
        'post_type'         => 'attachment',
        'posts_per_page'    => -1,
        'post_parent'       => $parent_post_id,
        'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ) );

    // Image upload handler
    if( $_SERVER['REQUEST_METHOD'] == "POST" ){
        
        // Check if user is trying to upload more than the allowed number of images for the current post
       // if( ( count( $attachments ) + count( $_FILES['files']['name'] ) ) > $max_image_upload ) {
         //   $upload_message[] = "Sorry you can only upload " . $max_image_upload . " images for each Ad";
       // } else {
            
            foreach ( $_FILES['files']['name'] as $f => $name ) {
                $extension = pathinfo( $name, PATHINFO_EXTENSION );
                // Generate a randon code for each file name
               // $new_filename = cvf_td_generate_random_code( 20 )  . '.' . $extension;
                $new_filename = $name;
                
                if ( $_FILES['files']['error'][$f] == 4 ) {
                    continue; 
                }
                
                if ( $_FILES['files']['error'][$f] == 0 ) {
                    // Check if image size is larger than the allowed file size
                    if ( $_FILES['files']['size'][$f] > $max_file_size ) {
                        $upload_message[] = "$name is too large!.";
						
						$ret = array('act' => 'show_state','st'=>'fail','msg'=>'<p class ="fail_message" style="color:red;">'.$name.' is too large!</p>');
						echo json_encode($ret);
						exit;
						
                        continue;
                    
                    // Check if the file being uploaded is in the allowed file types
                    } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
                        $upload_message[] = "$name is not a valid format";
						
						$ret = array('act' => 'show_state','st'=>'fail','msg'=>'<p class ="fail_message" style="color:red;">'.$name.' is not a valid format</p>');
						echo json_encode($ret);
						exit;
                        continue; 
                    
                    } else{ 
                        // If no errors, upload the file...
                        if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename ) ) {
                            
                            $count++; 

                            $filename = $path.$new_filename;
                            $filetype = wp_check_filetype( basename( $filename ), null );
                            $wp_upload_dir = wp_upload_dir();
                            $attachment = array(
                                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                                'post_content'   => '',
                                'post_status'    => 'inherit'
                            );
                            // Insert attachment to the database
                            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                            
                            // Generate meta data
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename ); 
                            wp_update_attachment_metadata( $attach_id, $attach_data );
                            
                        }
                    }
                }
            }
     ////   }
    }
    // Loop through each error then output it to the screen
/*     if ( isset( $upload_message ) ) :
        foreach ( $upload_message as $msg ){        
            printf( __('<p class="bg-danger">%s</p>', 'wp-trade'), $msg );
        }
    endif; */
    $file_link = wp_get_attachment_url( $attach_id );
	
	if( empty($file_link) ){ 
		$ret = array('act' => 'show_state','st'=>'fail','msg'=>'<p class ="fail_message" style="color:red;">File upload fail please try again!</p>');
		echo json_encode($ret);
		exit;
	}else{ 
		$ret = array('act' => 'show_state','st'=>'pass','msg'=>'<p class = "bg-success">File added successfully!</p>' , 'fileurl'=>$file_link );
		echo json_encode($ret);
		exit;
		//echo '<div style="display:none;" class="display_uploaded_image"><img src="'.$file_link.'"></div>';   
	}
    
    exit();
}

// Random code generator used for file names.
function cvf_td_generate_random_code($length=10) {
 
   $string = '';
   $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
 
   for ($p = 0; $p < $length; $p++) {
       $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
 
   return $string;
 
}

function my_user_profile_edit_action($user) {
  $user_address =  get_user_meta($user->ID, 'user_address');
  $user_company =  get_user_meta($user->ID, 'user_company');
  $user_vatnumber =  get_user_meta($user->ID, 'user_vatnumber');
  $user_mobile =  get_user_meta($user->ID, 'user_mobile');
?>
<h3>Dealer Details</h3>
<table class="form-table">
<tbody>
<tr class="user-description-wrap">
	<th><label for="user_address">Address</label></th>
	<td><input name="user_address" type="text" id="user_address" value="<?php echo $user_address[0]; ?>">
</tr>
<tr class="user-description-wrap">
	<th><label for="user_company">Company</label></th>
	<td><input name="user_company" type="text" id="user_company" value="<?php echo $user_company[0]; ?>">
</tr>
<!--
<tr class="user-profile-picture">
	<th><label for="user_vatnumber"> VAT number</label></th>
	<td>
		<input name="user_vatnumber" type="text" id="user_vatnumber" value="<?php echo $user_vatnumber[0]; ?>">
	</td>
</tr>-->
<tr class="user-profile-picture">
	<th><label for="user_mobile">Mobile</label></th>
	<td>
		<input name="user_mobile" type="text" id="user_mobile" value="<?php echo $user_mobile[0]; ?>">
	</td>
</tr>
</tbody>
</table>
<?php 
}
add_action('personal_options_update', 'my_user_profile_update_action');
add_action('edit_user_profile_update', 'my_user_profile_update_action');
function my_user_profile_update_action($user_id) {
	$user_mobile = $_POST['user_mobile'];
	$user_vatnumber = $_POST['user_vatnumber'];
	$user_address = $_POST['user_address'];
	$user_company = $_POST['user_company'];
	update_user_meta( $user_id, 'user_company', $user_company );
	update_user_meta( $user_id, 'user_mobile', $user_mobile );
	update_user_meta( $user_id, 'user_vatnumber', $user_vatnumber ); 
	update_user_meta($user_id, 'user_address',$user_address );
}

function wp_file_uplaoder($atts){
	 extract( shortcode_atts( array (
        'count' => '1',
    ), $atts ) );
	$count;
	?>
	<div class = "col-md-12 upload-form">
		<div class = "form-group">
			<div id="aspk_gif_img_<? echo $count; ?>" style="display:none;">
				<img class="gifimgbottom" style="width: 10em;position: absolute;" src="<?php echo plugins_url('images/gif-progress.gif', __FILE__); ?>">
			</div>
			<input type = "hidden" required name = "file_count" value="<? echo $count; ?>"/>
			<input id="uploadfile_<? echo $count; ?>" required type = "file" name = "files[]"  class = "files-data-<? echo $count; ?> form-control" multiple />
		</div>
		<!--<div class = "form-group">
			<input type = "button" id="uploadfile_<? echo $count; ?>" data_val="<? echo $count?>" value= "Upload"  class = "btn btn-primary btn-upload" />
		</div>-->
	</div>

<?php
}
function install(){
	add_role( 'dealer', 'dealer', array( 'read' => true, 'level_0' => true ) );
}
function wp_show_admin_dashboard(){
	global $wpdb;
	
	$user_id = get_current_user_id();
	$user = new WP_User( $user_id );
	$role_user = $user->roles[0];
	ob_start();	
	?>
	<style>
	.info-wrapper{
		margin-bottom:3em;
	}
	.minheightsec{
		min-height:14em;
	}
	.centertext{
		text-align:center;
	}
	.gifimgbottom{
		bottom: 13px;
	}
	.leftpad{
		 padding-left: 10px !important;
	}
	.rightpad{
		 padding-right: 10px !important;
	}
	.headingtitle{
		color: #1569ae;
		font-family: "Raleway", Raleway;
		font-weight: 600;
		font-size: 46px;
		text-align:center;
	}
	</style>
	<?php		
	if( $role_user == 'administrator'){
		?>
		<div class="container info-wrapper">
			<div class="row">
				<div class="col-md-10">
					<h1 class="headingtitle">Admin Dashboard </h1>
				</div>
				<div class="col-md-2" style="text-align:right;"><a href="<?php echo home_url().'/dealers'; ?>"><input type="button" value="Dealers" class="btn btn-primary"></a> </div>
			</div>
		</div>
		<?php
		if(isset($_GET['fileid'])){
			$fileid = $_GET['fileid'];
			show_detailfile($fileid);
		}else{
			
			if(isset($_POST['reupload_file_submit'])){
				$post_id = $_POST['post_id'];
				$manufacturer = get_post_meta($post_id,'manufacturer');
				$model = get_post_meta($post_id,'model');
				$fuel_type = get_post_meta($post_id,'fuel_type');
				$stage = get_post_meta($post_id,'stage');
				$void_check = $_POST['void_check'];
				$post_author_id = get_post_field( 'post_author', $post_id );
				$user_info = get_userdata($post_author_id);
				$user_email = $user_info->user_email;
				$username = $user_info->user_nicename;
				$user_mobile = get_user_meta($post_author_id,'user_mobile');
				$user_mobile = $user_mobile[0];
				
				if($user_mobile){
					$message = "Remapped file for ".$manufacturer[0]." , ".$model[0]. " ,  car reg uploaded to the dealer portal";
					$cerd_admin = array( 'number_to' =>$user_mobile ,'message' => $message );
					$response = twl_send_sms( stripslashes_deep( $cerd_admin  ) );						
				
					if( is_wp_error( $response ) ) {
						?>
						<h2 style='color:red;'><?php echo $response->get_error_message(); ?></h2>
						<?php
					}
				}
				$reupload_file = $_POST['reupload_file_url'];				
				$reupload_file = rtrim($reupload_file,',');

				add_filter( 'wp_mail_content_type','wp_set_content_type' );
				$html_temp = mail_template();
				//#manufacture# >> #model# >> #fuel_type# >> #stage#
				$html_temp = str_replace( '#manufacture#' ,$manufacturer[0], $html_temp );
				$html_temp = str_replace( '#model#' ,$model[0], $html_temp );
				$html_temp = str_replace( '#fuel_type#' ,$fuel_type[0], $html_temp );
				$html_temp = str_replace( '#stage#' ,$stage[0], $html_temp );
				//$user_email = 'mubeeniqbal82@gmail.com';
				wp_mail( $user_email, 'Remapped File', $html_temp);
				remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
				
				update_post_meta($post_id,'admin_approved_dealerfile',$reupload_file);
				update_post_meta($post_id,'dealer_file',$reupload_file);
				update_post_meta($post_id,'dealer_post_status','Completed');
				
				
			}
			
			if(isset($_POST['void_file_submit'])){
				$post_id = $_POST['post_id'];
				$manufacturer = get_post_meta($post_id,'manufacturer');
				$model = get_post_meta($post_id,'model');
				$fuel_type = get_post_meta($post_id,'fuel_type');
				$stage = get_post_meta($post_id,'stage');
				$void_check = $_POST['void_check'];
				$post_author_id = get_post_field( 'post_author', $post_id );
				$user_info = get_userdata($post_author_id);
				$user_email = $user_info->user_email;
				$username = $user_info->user_nicename;
				$user_mobile = get_user_meta($post_author_id,'user_mobile');
				$user_mobile = $user_mobile[0];
				$car_reg = get_post_meta( $post_id, 'vehicle_registration', true );
				
				$void_reason = $_POST['void_reason'];
				update_post_meta($post_id,'void_reason',$void_reason);
				
				if($user_mobile){
					//$message = "Your remapped file car reg for that file has been voided. Please login";
					$message = "Your remapped file ".$car_reg." has been voided. Please login";
					$cerd_admin = array( 'number_to' =>$user_mobile ,'message' => $message );
					$response = twl_send_sms( stripslashes_deep( $cerd_admin  ) );	
				
					if( is_wp_error( $response ) ) {
						?>
						<h2 style='color:red;'><?php echo $response->get_error_message(); ?></h2>
						<?php
					}
				}
				add_filter( 'wp_mail_content_type','wp_set_content_type' );
				$html_temp = mail_void_msg();
				//#manufacture# >> #model# >> #fuel_type# >> #stage#
				$html_temp = str_replace( '#voidmsgmail#' ,$message, $html_temp );
				//$user_email = 'mubeeniqbal82@gmail.com';
				wp_mail( $user_email, 'Your Remapped File voided', $html_temp);
				remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
				
				update_post_meta($post_id,'dealer_post_status','Cancel');
			}

			$ids = get_users( array('role' => 'dealer' ,'fields' => 'ID') );
			$args = array(
			'author' => implode(',', $ids),
			  'post_type' => 'file',  
			  'orderby'       =>  'post_date',
			  'order'         =>  'DESC',
			  'posts_per_page' => -1 // no limit
			);
			$results = get_posts( $args );
			
			?>
			<!--<a href="<?php echo get_permalink(); ?>"><input type="button" value="Back" class="btn btn-primary"></a>-->
			<div class="row container info-wrapper minheightsec">
			
				<table class="table table-bordered">
					
				<?php
				if($results){ ?>
					<thead>
						<tr>
							<th class="tdcol counter leftpad" >#</th>
							<th class="tdcol datetime" >Create Time</th>
							<th class="tdcol manufacture" >Manufacturer</th>
							<th class="tdcol model" >Model</th>
							<th class="tdcol dealer" >Dealer</th>
							<th class="tdcol options" >Options</th>
							<th class="tdcol status" >Status</th>
							<th class="tdcol file" >File</th>
							<th class="tdcol file rightpad" >View Details</th>
						</tr>
					</thead>
					<tbody><?php
					$count = 0;
					foreach($results as $post){ $count ++;
						$manufacturer = get_post_meta( $post->ID, 'manufacturer', true );
						$model = get_post_meta( $post->ID, 'model', true );
						$fuel_type = get_post_meta( $post->ID, 'fuel_type', true );
						$variant = get_post_meta( $post->ID, 'variant', true );
						$stage = get_post_meta( $post->ID, 'stage', true );
						$vehicle_registration = get_post_meta( $post->ID, 'vehicle_registration', true );
						$transmission = get_post_meta( $post->ID, 'transmission', true );
						$mileage = get_post_meta( $post->ID, 'mileage', true );
						$tool_used = get_post_meta( $post->ID, 'tool_used', true );
						$vehicle_age = get_post_meta( $post->ID, 'vehicle_age', true );
						$free_options = get_post_meta( $post->ID, 'free_options', true );
						$paid_options = get_post_meta( $post->ID, 'paid_options', true );
						$dealer_file = get_post_meta( $post->ID, 'dealer_file', true );
						$comments = get_post_meta( $post->ID, 'comments', true );
						$currentdate = get_the_date( 'g:ia d/m/y' , $post->ID );
						
						$postdate = $post->post_date;
						
						//$postDate = date("j F Y", $postdate);
						//$postDate = date("j M", $postdate);
					
						$datetime = date( "j M Y h:i:s A", strtotime( $postdate ) );
						$paidoption = get_post_meta( $post->ID, 'paid_options', true );
						
						$post_author_id = get_post_field( 'post_author', $post->ID );
						//$username = get_user_meta($post_author_id,'full_name',true);
						$username = get_user_meta($post_author_id,'user_company',true);
						$user_info = get_userdata($post_author_id);
						$user_email = $user_info->user_email;
						$voidstatus = get_post_meta( $post->ID ,'void_reason',true );
			
						$filestatus = get_post_meta( $post->ID ,'dealer_post_status',true );
						if(!empty($filestatus)){
							$status = $filestatus;
						}else{
							$status = 'Processing';
						}
						$download_img_link = plugins_url('images/download_img.png', __FILE__);
						$file_n = '<a href="'.$dealer_file.'" class="invoice_link btn btn-default d-right margin-clear" download><img src="'.$download_img_link.'"></a>';
					?>


					<tr>
						<td class="tdcount leftpad" ><?php echo $count; ?></td>
						<td class="dateloop" ><?php echo $datetime; ?></td>
						<td class="manloop" ><?php echo $manufacturer; ?></td>
						<td class="modelloop" ><?php echo $model; ?></td>
						<td class="dealerloop" ><?php echo $username; ?></td>
						<td class="optionloop" >
							<b>Free Option: </b><?php echo $free_options; ?><br/>
							<b>Paid Option: </b><?php echo $paidoption; ?><br/>
						</td>
						<td class="statusloop" style="text-transform: capitalize;" ><?php echo $status; ?></td>
						<td class="fileloop " >
							<?php echo $file_n;
							/* if( $status == 'Cancel' ){ ?>
								<b>Voided</b><br/>
								<p><b>Voided Reason: </b><?php echo $voidstatus; ?></p><?php
							}else{
							?>
							<form action="" style="border-bottom: 2px solid #fee289;" class="ci_form2" id="form_void" method="post">
								<input name="post_id" value="<?php echo $post->ID; ?>" type="hidden">
								<div >
									<label class="btn btn-submit void_check_btn upload_btn_check" >
										<input id="void_check_<?php echo $count; ?>" counter="<?php echo $count; ?>" required name="void_check" value="yes" type="checkbox"> 
										<span class="void_check_text2">Void</span>
									</label>
									<div id="void_msg_<?php echo $count; ?>" class="show_void_text text_reason" style="display:none;">
										<textarea placeholder="Void Reason" required class="void_reason <?php echo $count; ?>" name="void_reason"></textarea>
									</div>
									<input class="btn btn-submit reupload_file" name="void_file_submit" Value="Resubmit" type="submit">
								</div>
							</form>
							<form action=""  onsubmit="reuploadfile_<?php echo $count; ?>();"  class="ci_form2" id="form_aprovded_<?php echo $count; ?>" method="post">	
								<input name="post_id" value="<?php echo $post->ID; ?>" type="hidden">
								<input name="reupload_file_url" id="chkfile_<?php echo $count; ?>" required value="" type="hidden">
								<div class="btn btn-submit void_check_btn upload_btn_check">
									<label class="void_label22">
										<input  id="upload_btn_check_<?php echo $count; ?>" required  name="void_check" value="approved_file" type="checkbox"> 
										<span class="void_check_text ">Approved</span>
									</label>
									<div class= "upload-response-<? echo $count; ?>"></div>
									<div class="upload_buttons" id="uploadform_<?php echo $count; ?>" style="display: none;">
										<?php echo do_shortcode('[file_uplaoder count ='.$count.']'); ?>
									</div>
								</div>
								<input class="btn btn-submit reupload_file" style="display:none;" id="aprove_btn_<?php echo $count; ?>" name="reupload_file_submit" Value="Resubmit" type="submit">
							</form>
							<?php
							} */
							?>
							<script>
								/* function reuploadfile_<?php echo $count; ?>(){
									event.preventDefault();
									var uploadurl = jQuery("#chkfile_<?php echo $count; ?>").val();
									
									if(uploadurl) {
										jQuery( "#form_aprovded_<?php echo $count; ?>" ).submit();
									}else{
										alert('file upload is required');
										return false;
									}
									
								} */
								
								jQuery('#void_check_<?php echo $count; ?>').change(function(){
									if (this.checked) {
										jQuery('#void_msg_<?php echo $count; ?>').show( );
									}
									else {
										jQuery('#void_msg_<?php echo $count; ?>').hide( );
									}                   
								});
								
								jQuery('#upload_btn_check_<?php echo $count; ?>').change(function(){
									if (this.checked) {
										jQuery('#uploadform_<?php echo $count; ?>').show( );
									}
									else {
										jQuery('#uploadform_<?php echo $count; ?>').hide( );
									}                   
								});
								
								
								// When the Upload button is clicked...
								//jQuery( "#uploadfile_<? echo $count; ?>" ).click(function(e) {
								jQuery("#uploadfile_<? echo $count; ?>").on("change", function (e) {
									jQuery(".upload-response-<? echo $count; ?>").html('');
									jQuery("#aspk_gif_img_<? echo $count; ?>").show();
									jQuery("#uploadfile_<? echo $count; ?>").attr('disabled','disabled');
									btn_upload_id = jQuery(this).attr('data_val');
									console.log(btn_upload_id);
									e.preventDefault;
									
									
									if(jQuery('.files-data-<? echo $count; ?>').prop('files').length > 0){
										var fd = new FormData();
										var files_data = jQuery('.upload-form .files-data-<? echo $count; ?>'); // The <input type="file" /> field
										console.log(files_data);
										// Loop through each data and create an array file[] containing our files data.
										jQuery.each(jQuery(files_data), function(i, obj) {
											jQuery.each(obj.files,function(j,file){
												fd.append('files[' + j + ']', file);
											})
										});
										
										// our AJAX identifier
										fd.append('action', 'cvf_upload_files');  
										
										// Remove this code if you do not want to associate your uploads to the current page.
									 //   fd.append('post_id', <?php echo $post->ID; ?>); 

										jQuery.ajax({
											type: 'POST',
											action: 'cvf_upload_files',
											url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
											data: fd,
											contentType: false,
											processData: false,
											success: function(response){
												var obj = JSON.parse(response);
												console.log(obj);
												if( obj.st == 'pass'){
													jQuery('#aprove_btn_<?php echo $count; ?>').show( );
													jQuery('#chkfile_<?php  echo $count; ?>').val( obj.fileurl );
													jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
													jQuery('#uploadform_<?php echo $count; ?>').hide( );
													jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
													jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
												}else{
													jQuery('#aprove_btn_<?php echo $count; ?>').hide( );
													jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
													jQuery('#uploadfile_<? echo $count; ?>').attr('checked', false); 
													jQuery('#uploadform_<?php echo $count; ?>').hide( );
													jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
													jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
												}
												
											}
										});
									}else{
										jQuery('#uploadfile_<? echo $count; ?>').attr('checked', false); 
										jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
										jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										alert('Please select any file and then continue');
									}
								});
							</script>
						</td>
						<td class="details rightpad" >
							<a class="btn btn-primary"  href="<?php echo get_permalink().'?fileid='.$post->ID; ?>">Full Info</a>
						</td>
					</tr>
					<?php
					} ?>
					</tbody><?php
				}else{
					echo "<h2 class='centertext'>No Record Found!</h2>";
				}
					?>
				</table>
			</div>
		<?php
		return ob_get_clean();
		}

      }else{
		  echo '<h3>Only Admin can see this page.</h3>';
	  }
}

function show_detailfile($fileid){
	$manufacturer = get_post_meta( $fileid, 'manufacturer', true );
	$model = get_post_meta( $fileid, 'model', true );
	$fuel_type = get_post_meta( $fileid, 'fuel_type', true );
	$variant = get_post_meta( $fileid, 'variant', true );
	$stage = get_post_meta( $fileid, 'stage', true );
	$vehicle_registration = get_post_meta( $fileid, 'vehicle_registration', true );
	$transmission = get_post_meta( $fileid, 'transmission', true );
	$mileage = get_post_meta( $fileid, 'mileage', true );
	$tool_used = get_post_meta( $fileid, 'tool_used', true );
	$vehicle_age = get_post_meta( $fileid, 'vehicle_age', true );
	$free_options = get_post_meta( $fileid, 'free_options', true );
	$paid_options = get_post_meta( $fileid, 'paid_options', true );
	$dealer_file = get_post_meta( $fileid, 'dealer_file', true );
	$void_reason = get_post_meta( $fileid, 'void_reason', true );
	$comments = get_post_meta( $fileid, 'comments', true );
	$currentdate = get_the_date( 'l F j, Y' , $fileid );
	$filestatus = get_post_meta( $fileid, 'dealer_post_status', true ); 
	$chargedID = get_post_meta( $fileid, 'dealer_charge_id', true ); 
	
	$post_author_id = get_post_field( 'post_author', $fileid );

	$user_info = get_userdata( $post_author_id );
	$user_email = $user_info->user_email;
	$username = $user_info->user_nicename;
	$user_mobile = get_user_meta($post_author_id,'user_mobile',true);
	$fullname = get_user_meta( $post_author_id ,'full_name',true);
				
	if( empty($filestatus) ){
		$filestatus = 'Processing';
	}
	$count = 1;
	
	if(isset($_POST['reupload_file_submit'])){
		$post_id = $_POST['post_id'];
		$manufacturer = get_post_meta($post_id,'manufacturer');
		$model = get_post_meta($post_id,'model');
		$fuel_type = get_post_meta($post_id,'fuel_type');
		$stage = get_post_meta($post_id,'stage');
		$void_check = $_POST['void_check'];
		$post_author_id = get_post_field( 'post_author', $post_id );
		$user_info = get_userdata($post_author_id);
		$user_email = $user_info->user_email;
		$username = $user_info->user_nicename;
		$user_mobile = get_user_meta($post_author_id,'user_mobile');
		$user_mobile = $user_mobile[0];
		$car_reg = get_post_meta( $post_id, 'vehicle_registration', true );
		
		$updateddate = date('j M Y h:i:s A');
		if($user_mobil){
			$message = "Remapped file for ".$manufacturer[0]." , ".$model[0]. " , ".$car_reg." uploaded to the dealer portal";
			$cerd_admin = array( 'number_to' =>$user_mobile ,'message' => $message );
			$response = twl_send_sms( stripslashes_deep( $cerd_admin  ) );						
		
			if( is_wp_error( $response ) ) {
				?>
				<h2 style='color:red;'><?php echo $response->get_error_message(); ?></h2>
				<?php
			}
		}
		$reupload_file = $_POST['reupload_file_url'];				
		$reupload_file = rtrim($reupload_file,',');

		add_filter( 'wp_mail_content_type','wp_set_content_type' );
		$html_temp = mail_template();
		//#manufacture# >> #model# >> #fuel_type# >> #stage#
		$html_temp = str_replace( '#manufacture#' ,$manufacturer[0], $html_temp );
		$html_temp = str_replace( '#model#' ,$model[0], $html_temp );
		$html_temp = str_replace( '#fuel_type#' ,$fuel_type[0], $html_temp );
		$html_temp = str_replace( '#stage#' ,$stage[0], $html_temp );
		//$user_email = 'mubeeniqbal82@gmail.com';
		wp_mail( $user_email, 'Remapped File', $html_temp);
		remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
		
		update_post_meta($post_id,'admin_approved_dealerfile',$reupload_file);
		//update_post_meta($post_id,'dealer_file',$reupload_file); // this is dealer actual file
		update_post_meta($post_id,'dealer_post_status','Completed');
		update_post_meta($post_id,'file_updated_date', $updateddate );
		?><h1>File Approved Successfully</h1><?php
		
	}
	
	if(isset($_POST['void_file_submit'])){
		$post_id = $_POST['post_id'];
		$manufacturer = get_post_meta($post_id,'manufacturer');
		$model = get_post_meta($post_id,'model');
		$fuel_type = get_post_meta($post_id,'fuel_type');
		$stage = get_post_meta($post_id,'stage');
		$post_author_id = get_post_field( 'post_author', $post_id );
		$user_info = get_userdata($post_author_id);
		$user_email = $user_info->user_email;
		$username = $user_info->user_nicename;
		$user_mobile = get_user_meta($post_author_id,'user_mobile');
		$user_mobile = $user_mobile[0];
		$car_reg = get_post_meta( $post_id, 'vehicle_registration', true );
		
		$chargedID = get_post_meta( $post_id, 'dealer_charge_id', true ); 
		
		$updateddate = date('j M Y h:i:s A');
		
		$void_reason = $_POST['void_reason'];
		update_post_meta($post_id,'void_reason',$void_reason);
		if($user_mobile){
			//$message = "Your remapped file car reg for that file has been voided. Please login";
			$message = "Your remapped file ".$car_reg." has been voided. Please login";
			$cerd_admin = array( 'number_to' =>$user_mobile ,'message' => $message );
			$response = twl_send_sms( stripslashes_deep( $cerd_admin  ) );	
			
			if( is_wp_error( $response ) ) {
				?>
				<h2 style='color:red;'><?php echo $response->get_error_message(); ?></h2>
				<?php
			}
			?>
			<h1>File Voided Successfully</h1>
			<?php
		}

		add_filter( 'wp_mail_content_type','wp_set_content_type' );
		$html_temp = mail_void_msg();
		//#manufacture# >> #model# >> #fuel_type# >> #stage#
		$html_temp = str_replace( '#voidmsgmail#' ,$message, $html_temp );
		//$user_email = 'mubeeniqbal82@gmail.com';
		wp_mail( $user_email, 'Your Remapped File voided', $html_temp);
		remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
		
		update_post_meta($post_id,'dealer_post_status','Cancel');
		update_post_meta($post_id,'file_updated_date', $updateddate );
		
		$secret_key = get_option( 'dealer_secret_key' );
		require_once(ABSPATH.'/wp-content/plugins/dealer_pro/stripe-php/init.php');
		
		
		  \Stripe\Stripe::setApiKey( $secret_key );
		$refund = \Stripe\Refund::create([
			'charge' => $chargedID,
		]);
	}

	?>
	<style>
	.textright{
		text-align:right;
		font-weight:bold;
	}
	.lastfile-row{
		margin-bottom: 80px;
	}
	.row.mb-20 {
		padding: 4px;
	}
	.top-margin{
		margin-top:2em;
	}
	.void_reason2{
		resize:none;
	}
	.control-label{
		text-align:right;
	}
	.rightborder{
		border-right: 1px solid;
	}
	.upload-response-1 {
		margin-top: 10px !important;
		clear: both;
	}
	.aspk_gif_img_1 img{
		bottom: auto !important;
	}
	p.bg-success {
		font-size: 17px;
		padding: 12px 20px 12px;
		color: black;
	}
	p.fail_message {
		background: black;
		font-size: 17px;
		padding: 12px 20px 12px;
	}
	</style>
	<div class="container">
	<a href="<?php echo get_permalink(); ?>"><input type="button" value="Back" class="btn btn-primary"></a>
	<?php
	$fileupdated = get_post_meta($fileid,'file_updated_date', true );
	if( empty($fileupdated) ){
	?>
		<div class="row top-margin" >
			<div class="col-md-6 rightborder"><h2>Void File</h2>
				<form method="post" action="">
					<input name="post_id" value="<?php echo $fileid; ?>" type="hidden">
					<div class="row mb-20">
						<label class="col-sm-3 control-label" >Void Reason<span class="requiredinput">:*</span></label>
						<div class="col-sm-9">
							<textarea placeholder="Void Reason" required class="void_reason2 <?php echo $count; ?>" name="void_reason"></textarea>
						</div>
					</div>
					<div class="row mb-20">
						<label class="col-sm-3 control-label" ></label>
						<label class="col-sm-3 control-label2" >
							<input class="btn btn-submit btn btn-primary" name="void_file_submit" Value="Resubmit" type="submit">
						</label>
					</div>
				</form>
			</div>
			<div class="col-md-6"><h2>Approved File</h2>
				<form method="post" action="">
					<input name="post_id" value="<?php echo $fileid; ?>" type="hidden">
					<input name="reupload_file_url" id="chkfile_<?php echo $count; ?>" required value="" type="hidden">
					<label class="col-sm-3 control-label" >Approved<span class="requiredinput">:*</span></label>
					<div class="col-sm-9">
						<div class="upload_buttons2" id="holduploadform_<?php echo $count; ?>" >
							<?php echo do_shortcode('[file_uplaoder count ='.$count.']'); ?>
						</div>
						<div class= "upload-response-<? echo $count; ?>"></div>
					</div>
					<div class="row mb-20">
						<label class="col-sm-9 control-label" ></label>
						<label class="col-sm-3 control-label" >
							<input class="btn btn-primary reupload_file2" id="aprove_btn_<?php echo $count; ?>" name="reupload_file_submit" Value="Resubmit" type="submit">
						</label>
					</div>
				</form>
				<script>
					// When the Upload button is clicked...
								//jQuery( "#uploadfile_<? echo $count; ?>" ).click(function(e) {
						jQuery("#uploadfile_<? echo $count; ?>").on("change", function (e) {
							jQuery(".upload-response-<? echo $count; ?>").html('');
							jQuery("#aspk_gif_img_<? echo $count; ?>").show();
							jQuery("#aprove_btn_<? echo $count; ?>").attr('disabled','disabled');
							jQuery("#uploadfile_<? echo $count; ?>").attr('disabled','disabled');
							btn_upload_id = jQuery(this).attr('data_val');
							console.log(btn_upload_id);
							e.preventDefault;
							
							
							if(jQuery('.files-data-<? echo $count; ?>').prop('files').length > 0){
								var fd = new FormData();
								var files_data = jQuery('.upload-form .files-data-<? echo $count; ?>'); // The <input type="file" /> field
								console.log(files_data);
								// Loop through each data and create an array file[] containing our files data.
								jQuery.each(jQuery(files_data), function(i, obj) {
									jQuery.each(obj.files,function(j,file){
										fd.append('files[' + j + ']', file);
									})
								});
								
								// our AJAX identifier
								fd.append('action', 'cvf_upload_files');  
								
								// Remove this code if you do not want to associate your uploads to the current page.
							 //   fd.append('post_id', <?php echo $post->ID; ?>); 

								jQuery.ajax({
									type: 'POST',
									action: 'cvf_upload_files',
									url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
									data: fd,
									contentType: false,
									processData: false,
									success: function(response){
										var obj = JSON.parse(response);
										console.log(obj);
										if( obj.st == 'pass'){
											jQuery('#chkfile_<?php  echo $count; ?>').val( obj.fileurl );
											jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
											jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aprove_btn_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										}else{
											jQuery("#uploadfile_<? echo $count; ?>").val(null);
											jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
											jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aprove_btn_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										}
										
									}
								});
							}else{
								jQuery("#uploadfile_<? echo $count; ?>").val(null);
								jQuery("#aprove_btn_<? echo $count; ?>").attr('disabled',false);
								jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
								jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
								alert('Please select any file and then continue');
							}
						});
				</script>
			</div>
		</div>
	<?php
	}else{
		echo '<h1 class="top-margin btn-primary">File Updated on '.$fileupdated. ' And File status is '.$filestatus.'</h1>';
	}
	?>
		<div class="row mb-20 top-margin">
			<label class="col-sm-3 textright"><h1> File Info</h1></label>
			<div class="col-sm-9">&nbsp;</div>
		</div>

		<div class="row mb-20">
			<label class="col-sm-3 textright">Date:</label>
			<div class="col-sm-9"><?php echo $currentdate; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Name:</label>
			<div class="col-sm-9"><?php echo $fullname; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Email:</label>
			<div class="col-sm-9"><?php echo $user_email; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Mobile Number:</label>
			<div class="col-sm-9"><?php echo $user_mobile; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">File Status:</label>
			<div class="col-sm-9"><?php echo $filestatus; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Manufacturer:</label>
			<div class="col-sm-9"><?php echo $manufacturer; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Model:</label>
			<div class="col-sm-9"><?php echo $model; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Fuel type:</label>
			<div class="col-sm-9"><?php echo $fuel_type; ?></div>
		</div>

		<div class="row mb-20">
			<label class="col-sm-3 textright">Variant:</label>
			<div class="col-sm-9"><?php echo $variant; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Stage:</label>
			<div class="col-sm-9"><?php echo $stage; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Vehicle Registration:</label>
			<div class="col-sm-9"><?php echo $vehicle_registration; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Transmission:</label>
			<div class="col-sm-9"><?php echo $transmission; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Mileage:</label>
			<div class="col-sm-9"><?php echo $mileage; ?></div>
		</div>
		<div class="row mb-20">
			<label class="col-sm-3 textright">Vehicle age:</label>
			<div class="col-sm-9"><?php echo $vehicle_age; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Tool used:</label>
			<div class="col-sm-9"><?php echo $tool_used; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Free Options:</label>
			<div class="col-sm-9"><?php echo $free_options; ?></div>
		</div>
	
		<div class="row mb-20">
			<label class="col-sm-3 textright">Paid Options:</label>
			<div class="col-sm-9"><?php echo $paid_options; ?></div>
		</div>
		
		<div class="row mb-20">
			<label class="col-sm-3 textright">Comments:</label>
			<div class="col-sm-9"><?php echo $comments; ?></div>
		</div>
		
		<div class="row mb-20 lastfile-row">
			<label class="col-sm-3 textright">Dealer File:</label>
			<div class="col-sm-9"><a href="<?php echo $dealer_file; ?>"  >Download</a></div>
		</div>
	</div>
	<?php
}

function wp_show_dealer_list(){
	$blogusers = get_users( 'blog_id=1&orderby=nicename&role=dealer' );
	// Array of WP_User objects.
	$site_url = get_permalink();
	?>
<style>
.no-padding {
    padding-right: 0;
}

.info {
    text-align: center;
    background: #000;
    min-height: 115px;
    position: relative;
}

.info img {
    width: 90px;
    width: 90px;
    border-radius: 50px;
    border: 3px solid #fff;
    position: absolute;
    bottom: -40px;
    left: 0;
    right: 0;
    margin: auto;
}

.person-info {
    text-align: center;
    padding-top: 50px;
}

.person-info span {
    display: block;
	font-weight: bold;
    padding-bottom: 10px;
}

.social-links {
    display: flex;
    justify-content: center;
}

.person-info .social-links a {
    background: none;
    border: unset;
    background: #5972a9;
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 40px;
    padding: 0;
    border-radius: 50%;
    margin-right: 10px;
    margin-top: 10px;
}
.person-info a{
    background: #43b0ef;
    padding: 10px 20px 10px 20px;
    display: inline-block;
    border-radius: 10px;
    color: white;
    font-weight: bold;
}
.person-info .gender {
    padding-bottom: 10px;
    border-bottom: 1px solid #eeeeee;
}
.info-wrapper{
	margin-bottom:3em;
}
.centertext{
	text-align:center;
}
</style>
	<div class="content info-wrapper">
		<div class="container">
		<h1 class="centertext">Dealers List</h1>
		<div class="row">
		<?php
		$count_files_number = 1;
		if($blogusers){
			foreach ( $blogusers as $user ) {  
				$totalpost = count_user_posts( $user->ID , 'file' ); 
				$fullname = get_user_meta($user->ID,'full_name',true);
				if(empty($fullname)){
					$fullname = get_user_meta($user->ID,'first_name',true);
				} ?>
				
				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="info-wrapper">
						<div class="info">
							<img src="<?php echo plugins_url('images/user.png', __FILE__); ?>" alt="image">
						</div>
						<div class="person-info">
							<h5><?php echo $fullname; ?></h5>
							<span>Total Uploaded Files (<?php echo $totalpost; ?>)</span>
							<a href="<?php echo get_permalink().'?userid='.$user->ID; ?>">View Detail</a>
							<!--
							<div class="social-links">
								<div class="facebook">
									<a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
								</div>
								<div class="instagram">
									<a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
								</div>
							</div>-->
						</div>

					</div>
				</div>
				<?php
			}
		}else{ ?>
			<div class="main_admin_files22">No Record Found</div>
		<?php	
		}
		
		?>
		</div>	
		</div>	
	</div>	
	<?php
}

function send_request_signin(){
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	if( empty( $email ) || empty( $password ) ){
		$ret = array('act' => 'show_state','st'=>'fail','msg'=>'Please enter required fields');
		echo json_encode($ret);
		exit;
	}else{
		$creds = array();
					
		$creds['user_login'] = $email;
		$creds['user_password'] = $password;
		$creds['remember'] = true;
		$user = wp_signon( $creds, false );
		//$user = wp_authenticate($email, $password);
		//$login = $user->data->user_login;
		if ( is_wp_error($user) ){
			$msg = $user->get_error_message();
			$msg = str_replace( 'Lost your password?' ,'', $msg );
			$ret = array('act' => 'show_state','st'=>'fail','msg'=>$msg );
			echo json_encode($ret);
			exit;
		}else{
			$ret = array('act' => 'show_state','st'=>'ok','msg'=>'pass' );
			echo json_encode($ret);
			exit;
		}
	}
	
}
function from_dealer_name(){
	$wpfrom = "info@dd-remapping.co.uk";
	return $wpfrom;
}
		
function from_dealer_from(){
	$wpfrom = 'dd-remapping.co.uk';
	return $wpfrom;
}
function random($length = 8){      
	$chars = '123456789bcdfghjklmnprstvwxzaeiou@#$%^';
	$result = '';
	for ($p = 0; $p < $length; $p++)
	{
		$result .= ($p%2) ? $chars[mt_rand(19, 23)] : $chars[mt_rand(0, 18)];
	}
	return $result;
}	
function wp_set_content_type(){
	return "text/html";
}
function confirm_user_mail(){
	ob_start();
	?>
	<div>
  <div>
    <div style="background:black;text-align:center;">
		<div class="" style="">
			<a href="<?php echo home_url(); ?>">
			<img style="width:150px" src="<?php echo plugins_url('images/logo-invoice.jpeg', __FILE__); ?>">
			</a>
		</div>
    </div>
    <div>
      <div style="background: #000;padding: 25px 0px;">
        <div class="" style="text-align: center;font-family: arial;">
          <h2 style="color: #337ab7;">Welcome to DD Remapping</h2>
          <p style="color: #fff;">Username: #username# </p>
          <p style="color: #fff;">Password: #password# </p>
		  <p style="color: #fff;">
			<a  href="<?php echo home_url().'/dealer-registration'; ?>" style="text-decoration:none;color:#fff;padding:5px 15px;background-color:#19558f;font-size:14px;border-radius:3px" target="_blank" >Click Here</a>  to login
		  </p>
        </div>
      </div>
    </div>
	<div style="background: #666;margin-top: 0; padding: 15px 25px; font-weight: 600;color: #eee;">
		<div style="text-align:center;">
			<p>Best Regards,</p>
			<p style="color:white;"><?php echo home_url(); ?> Team.</p>
		</div>
	</div>
  </div>
</div>
	<?php
	$html = ob_get_clean();
	return $html;
}
function welcome(){
	ob_start();
	?>
	<div>
  <div>
    <div style="background:black;text-align:center;">
		<div class="" style="">
			<a href="<?php echo home_url(); ?>">
			<img style="width:150px" src="<?php echo plugins_url('images/logo-invoice.jpeg', __FILE__); ?>">
			</a>
		</div>
    </div>
    <div>
      <div style="background: #000;padding: 25px 0px;">
        <div class="" style="text-align: center;font-family: arial;">
          <!--<h2 style="color: #337ab7;">Welcome to DD Remapping</h2>-->
          <p style="color: #fff;"> #welcomemsg# </p>
		
			<div class="" style="text-align: center;margin-top: 20px;width: 100%;color:white;">
				<div style="padding-top:0.7em;">DD Remapping </div>
				<div style="padding-top:0.7em;">07377126544</div>
				<div style="padding-top:0.7em;">Info@dd-remapping.co.uk </div>
				<!--<p>Registered company No: 06724570 | VAT No : 264980273</p>-->
			</div>
		</div>
      </div>
    </div>
  </div>
</div>
	<?php
	$html = ob_get_clean();
	return $html;
}

function mail_void_msg(){
		ob_start();
	?>
<div>
  <div>
    <div style="background:black;text-align:center;">
		<div class="" style="">
			<a href="<?php echo home_url(); ?>">
			<img style="width:150px" src="<?php echo plugins_url('images/logo-invoice.jpeg', __FILE__); ?>">
			</a>
		</div>
    </div>
    <div>
      <div style="background: #000;padding: 25px 0px;">
        <div class="" style="text-align: center;font-family: arial;">
          <h2 style="color: #337ab7;">Remapped File</h2>
          <p style="color: #fff;">#voidmsgmail#</p>
          <!--<div style="font-weight: 600;color: #fff;">#manufacture# &gt;&gt; #model# &gt;&gt; #fuel_type# &gt;&gt; #stage#</div>-->
			<div style="text-align:center;margin-top: 20px;"><a href="<?php echo home_url(); ?>/dealer-regisdivation/" style="color: #337ab7;font-weight: bolder;"> &gt;&gt; Log into the dealer portal &lt;&lt; </a></div>
			<div style="text-align:center;color:white;padding-top:3em;">DD Remapping </div>
			<div style="text-align:center;color:white;padding-top:0.7em;">07377126544</div>
			<div style="text-align:center;color:white;padding-top:0.7em;">Info@dd-remapping.co.uk</div>
	    </div>
	 </div>
    </div>
  </div>
</div>
	<?php
	$html_pdf = ob_get_clean();
	return $html_pdf;
}
		
function mail_template(){
		ob_start();
	?>
<div>
  <div>
    <div style="background:black;text-align:center;">
		<div class="" style="">
			<a href="<?php echo home_url(); ?>">
			<img style="width:150px" src="<?php echo plugins_url('images/logo-invoice.jpeg', __FILE__); ?>">
			</a>
		</div>
    </div>
    <div style="height:100%;">
      <div style="background: #000;padding: 25px 0px;">
        <div class="" style="text-align: center;font-family: arial;">
          <h2 style="color: #337ab7;">Remapped File</h2>
          <p style="color: #fff;">Your remapped file is ready for the following vehicle:</p>
          <div style="font-weight: 600;color: #fff;">#manufacture# &gt;&gt; #model# &gt;&gt; #fuel_type# &gt;&gt; #stage#</div>
      
			<p style="text-align:center;margin-top: 20px;"><a href="<?php echo home_url(); ?>/dealer-regisdivation/" style="color: #337ab7;font-weight: bolder;"> &gt;&gt; Log into the dealer portal &lt;&lt; </a></p>
			<p style="text-align:center;margin-top: 20px;color:white;">DD Remapping </p>
			<p style="text-align:center;color:white;">07377126544</p>
			<p style="text-align:center;color:white;">Info@dd-remapping.co.uk </p>
		</div>
	 </div>
    </div>

  </div>
</div>
	<?php
	$html_pdf = ob_get_clean();
	return $html_pdf;
}
function mail_template_file_rec(){
		ob_start();
	?>
<div>
  <div>
    <div style="background:black;text-align:center;">
		<div class="" style="">
			<a href="<?php echo home_url(); ?>">
			<img style="width:150px" src="<?php echo plugins_url('/images/logo-invoice.jpeg', __FILE__); ?>">
			</a>
		</div>
    </div>
    <div>
      <div style="background: #000;padding: 25px 0px;">
        <div class="" style="text-align: center;font-family: arial;">
          <h2 style="color: #337ab7;">File received</h2>
          <p style="color: #fff;padding: 0 55px;">Thank you for uploading your file, we can confirm that we have received it and it is currently in the queue.</p>
          <div style="font-weight: 600;color: #fff;">To check the status of your file please go to the dealer portal.</div>
			<div style="text-align:center;"><a href="<?php echo home_url(); ?>/dealer-registration/" style="color: #337ab7;font-weight: bolder;"> &gt;&gt; Log into the dealer portal &lt;&lt; </a></div>
			<div style="text-align:center;margin-top: 40px;color:white;">DD Remapping </div>
			<div style="text-align:center;color:white;">07377126544</div>
			<div style="text-align:center;color:white !important;">Info@dd-remapping.co.uk </div>
	    </div>
	 </div>
    </div>
   
  </div>
</div>
	<?php
	$html_pdf = ob_get_clean();
	return $html_pdf;
}
function pdfpage(){
	ob_start();
	?>
	<table>
		<body>
		<tr>
			<td class="" style="width: 350px;float: left;"><img style ="width:150px" src="<?php echo plugins_url('images/logo-invoice.jpeg', __FILE__); ?>"></td>
			<td>
				<div class="col-sm-4 control-label" style="text-align: right;font-family: arial;font-size: 14px;width: 350px;float: left;">
				 <div style="line-height: 16px;font-weight: 600;">DD-Remapping</div>
				 <div style="line-height: 16px;font-weight: 600;">info@dd-remapping.co.uk</div>
				 <div style="line-height: 16px;font-weight: 600;">Tel: 07377126544</div>
				</div>
			</td>
		</tr>

		<tr>
		<td class="" style="width: 100%;margin: 0% 0%;">
			<div class="" style="background-color: #000;color: #fff;padding: 5px 10px;">Invoice</div>
			<div class="" style="background-color: #ddd;color: #000;padding: 5px 10px;">#company_name#</div>
		</td>

		<td>
		<div class="" style="width: 100%;">
			<div class="" style="background-color: #fff;color: #fff;padding: 5px 10px;margin-top: 21px;"></div>
			<div class="" style="background-color: #ddd;color: #000;padding: 5px 10px;">
				<div>Invoice number:#invoicenumber#</div>
				<div>Invoice date:#invoicedate#</div>
				<div>Due Date:#invoicedate_order#</div>
			</div>
		</div>
		</td>
		</tr>



		<tr style="margin-bottom: 25px;">
		<td colspan="2" style="width:100%;">	
			<div class="" style="margin-top: 35px;">
				<div class="col-sm-12" style="width: 100%%;margin: 0 1%;margin-top: 35px;">
				<div class="head" style="border: 1px solid #999;background: #dddddd;padding: 4px 8px;font-weight: bold;">Items:</div>
				<div class="items" style="border: 1px solid #999;padding: 5px;">#invoice_item# - &#163; #total_amount#</div>
				</div>
			</div>
		</td>
		</tr>

		<tr>
			
		<td>	
		</td>
		<td style="margin-left: 10px;">	
			<div class="" style="display:inline-block;width: 48%;">
				<div style="border: 1px solid #999;padding: 5px 20px 5px 5px;font-weight: bold;">Subtotal:</div>
				<!--<div style="border: 1px solid #999;padding: 5px 20px 5px 5px;font-weight: bold;">20% VAT:</div>-->
				<div style="border: 1px solid #999;padding: 5px 20px 5px 5px;font-weight: bold;background: #ddd;">Total:</div>
			</div>

			<div class="" style="display:inline-block;width: 48%;">
				<div style="border: 1px solid #999;padding: 5px 20px 5px 5px;">&#163;#total_amount#</div>
				<!--<div style="border: 1px solid #999;padding: 5px 20px 5px 5px;">&#163;#tax_amount#</div>-->
				<div style="border: 1px solid #999;padding: 5px 20px 5px 5px;background: #ddd;">&#163;#total_amount#</div>
			</div>

		</td>
		</tr>

		<tr>
		<td colspan="2">
			<div class="" style="text-align: center;margin-top: 20px;width: 100%;">
				<div style="padding-top:0.7em;">DD Remapping </div>
				<div style="padding-top:0.7em;">17 Willowbridge Close Retford </div>
				<div style="padding-top:0.7em;">Dn22 6pg</div>
				<div style="padding-top:0.7em;">Uk </div>
				<div style="padding-top:0.7em;">07377126544</div>
				<div style="padding-top:0.7em;">Info@dd-remapping.co.uk </div>
				<!--<p>Registered company No: 06724570 | VAT No : 264980273</p>-->
			</div>
		</td>
		</tr>

		</body>

	</table>

	<?php
	$html_pdf = ob_get_clean();
	return $html_pdf;
}
function dealers_aproval(){
	global $wpdb;
	
	$date = date('Y-m-d');
	$admin_email = get_option('admin_email');
	if(isset($_POST['disapprove_user'])){
		$id = $_POST['dealid'];

		$sql = "select * from {$wpdb->prefix}dd_dealers WHERE id={$id} ";
		$user = $wpdb->get_row($sql);
		$email = $user->email_user;
		$password = $user->password;
		if($user){
			$sql = "UPDATE {$wpdb->prefix}dd_dealers SET status = 'rejected', updated_date = '{$date}' where id ={$id}";
			$wpdb->query($sql);
			
			$sqldel = "UPDATE {$wpdb->prefix}dd_dealers SET status = 'deleted', updated_date = '{$date}' where email_user ='{$email}' AND status = '0'";
			$wpdb->query($sqldel);
		}
	}
	if(isset($_POST['create_user'])){
		$id = $_POST['dealid'];
		
		$sql = "select * from {$wpdb->prefix}dd_dealers WHERE id={$id} ";
		$user = $wpdb->get_row($sql);
		$email = $user->email_user;
		$password = $user->password;
		
		if($user){
			$user_id = wp_create_user( $email, $password, $email );
			if($user_id){
				$my_user = new WP_User( $user_id );
				$my_user->set_role( "dealer" );
				update_user_meta( $user_id, 'full_name', $user->username );
				update_user_meta( $user_id, 'user_company', $user->company );
				update_user_meta( $user_id, 'user_mobile', $user->mobile_number );
				update_user_meta( $user_id, 'user_address',$user->address );
				$sql = "UPDATE {$wpdb->prefix}dd_dealers SET status = '1', updated_date = '{$date}' where id ={$id}";
				
				$sqldel = "UPDATE {$wpdb->prefix}dd_dealers SET status = 'deleted', updated_date = '{$date}' where email_user ='{$email}' AND status = '0'";
				
				$wpdb->query($sql);	
				$wpdb->query($sqldel);	
							
				$welcome = confirm_user_mail();
				$welcome = str_replace( '#username#' ,$email, $welcome );
				$welcome = str_replace( '#password#' ,$password, $welcome );
				
				add_filter( 'wp_mail_content_type','wp_set_content_type' );
				wp_mail( $email, 'Welcome to DD Remapping', $welcome );
				//wp_mail( $admin_email, 'Welcome to DD Remapping', $welcome);
				remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
			}
		}
	}
	$sql = "select * from {$wpdb->prefix}dd_dealers WHERE status = '0' group by  email_user";
	$results = $wpdb->get_results($sql);
	
	?>
	<table style="width:100%;">
		<h1>All Dealers</h1>
		<tr>
			<td><h3>Name</h3></td>
			<td><h3>Company Name</h3></td>
			<td><h3>Email</h3></td>
			<td><h3>Approve</h3></td>
		</tr>
		<?php
		if($results){
			foreach($results as $rs){
		?>
		<tr><td><?php echo $rs->username; ?></td>
			<td><?php echo $rs->company; ?></td>
			<td><?php echo $rs->email_user; ?></td>
			<td>
			<form method="post" action="" >
			<input type="hidden" name="dealid" value="<?php echo $rs->id; ?>">
			<input type="submit" name="disapprove_user" class="btn btn-primary" value="Disapprove">
			<input type="submit" name="create_user" class="btn btn-primary" value="Approve">
			</form>
			</td>
		</tr>
		<?php
			}
		}
		?>
	</table>
	<?php
}
function wpdocs_register_my_custom_menu_page(){

   add_menu_page( 

        __( 'Dealers', 'textdomain' ),

        'Dealers',

        'manage_options',

        'all_dealers',

        'dealers_aproval'

    ); 
	
	 add_menu_page( 

        __( 'Stripe Setting', 'textdomain' ),

        'Stripe Gateway Setting',

        'manage_options',

        'stripe_setting',

        'stripe_setting_menu_page'

    ); 

}
function stripe_setting_menu_page(){


	if(isset($_POST['save_setting_dd'])){
		$file_price = $_POST['file_price']; 
		$stripeKey = $_POST['_stripe_key'];
		$stripeSecret_Key = $_POST['_secret_key'];
		$save_mobile = $_POST['admin_mobile_num'];

		update_option('file_price',$file_price);
		
		update_option( 'dealer_stripe_key', $stripeKey );
		update_option( 'dealer_secret_key', $stripeSecret_Key );
		update_option( 'admin_mobile', $save_mobile );

		echo "<h1>Setting Saved Successfully</h1>";
	}

	$file_price = get_option('file_price');
	
	$stripe_key = get_option( 'dealer_stripe_key' );
	$secret_key = get_option( 'dealer_secret_key' );
	$admin_mobile = get_option( 'admin_mobile' );



   ?>

<form action="" class="ci_form" id="form" method="post">

<h2>Stripe Setting</h2>

<div class="">
<label>Stripe Key</label>
<input type="text" class="form-control dog_name_1" placeholder="_stripe_key" value="<?php echo $stripe_key ?>" required="required" name="_stripe_key">
</div>
<div class="">
<label>Secret Key</label>
<input type="text" class="form-control dog_name_1" placeholder="Secret Key" value="<?php echo $secret_key ?>" required="required" name="_secret_key">
</div>
<div class="">
<label>Default Price</label>
<input type="text" class="form-control dog_name_1" placeholder="Price" value="<?php echo $file_price ?>" required="required" name="file_price">
</div>
<h2>Admin Mobile<h2>
<div class="">
<label>Mobile Number</label>
<input type="text" class="form-control dog_name_1" placeholder="Number" value="<?php echo $admin_mobile ?>" required="required" name="admin_mobile_num">
</div>
<div class="">
<!--<input type="submit" name="submit_price" id="feed_setting" value="Submit"> -->
<input type="submit" class="btn btn-primary" name="save_setting_dd" id="feed_setting" value="Submit">
</div>


</form>

   <?php

}
function wp_astro_init() {
	//require_once( ABSPATH . '/wp-content/plugins/dealer_pro/ajax-file-upload/ajax-file-upload.php' );
	//require_once( ABSPATH . '/wp-content/plugins/dealer_pro/arfaly-mass-multi-file-uploader/arfaly-press.php' );
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'dd_dealers';
	$charset_collate = $wpdb->get_charset_collate();
	
	$sql = "CREATE TABLE $table_name (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  username varchar(525) DEFAULT NULL,
	  email_user varchar(525) DEFAULT NULL,
	  password varchar(525) DEFAULT NULL,
	  address longtext DEFAULT NULL,
	  account_type varchar(525) DEFAULT NULL,
	  account_number varchar(525) DEFAULT NULL,
	  routing_number varchar(525) DEFAULT NULL,
	  company varchar(525) DEFAULT NULL,
	  mobile_number varchar(225) DEFAULT NULL,
	  created_date date DEFAULT NULL,
	  updated_date date DEFAULT NULL,
	  status varchar(225) DEFAULT NULL,
	  UNIQUE KEY id (id)
	)";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
			
	$labels = array(
    'name'                  => _x( 'file', 'Post Type General Name', 'text_domain' ),
    'singular_name'         => _x( 'file', 'Post Type Singular Name', 'text_domain' ),
    'menu_name'             => __( 'file', 'text_domain' ),
    'name_admin_bar'        => __( 'file', 'text_domain' ),
    'archives'              => __( 'Item Archives', 'text_domain' ),
    'attributes'            => __( 'Item Attributes', 'text_domain' ),
    'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
    'all_items'             => __( 'All Items', 'text_domain' ),
    'add_new_item'          => __( 'Add New Item', 'text_domain' ),
    'add_new'               => __( 'Add New', 'text_domain' ),
    'new_item'              => __( 'New Item', 'text_domain' ),
    'edit_item'             => __( 'Edit Item', 'text_domain' ),
    'update_item'           => __( 'Update Item', 'text_domain' ),
    'view_item'             => __( 'View Item', 'text_domain' ),
    'view_items'            => __( 'View Items', 'text_domain' ),
    'search_items'          => __( 'Search Item', 'text_domain' ),
    'not_found'             => __( 'Not found', 'text_domain' ),
    'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
    'featured_image'        => __( 'Featured Image', 'text_domain' ),
    'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
    'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
    'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
    'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
    'items_list'            => __( 'Items list', 'text_domain' ),
    'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
    'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
  );
  $args = array(
    'label'                 => __( 'file', 'text_domain' ),
    'description'           => __( 'Post Type Description', 'text_domain' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor','excerpt', 'thumbnail', 'custom-fields', 'page-attributes' ),
    'hierarchical'          => false,
    'rewrite'               => true,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'capability_type'       => 'post',
  );
  register_post_type( 'file', $args );
}

/**
 * If you wanted to have two sets of metaboxes.
 */
function add_events_metaboxes_v2() {
	add_meta_box(
		'wpt_events_location',
		'File Date',
		'wpt_events_location',
		'file',
		'normal',
		'high'
	);
}
/**
 * Output the HTML for the metabox.
 */
function wpt_events_location() {
	global $post;
	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'event_fields' );
	// Get the location data if it's already been entered
	$fuel_type = get_post_meta( $post->ID, 'fuel_type', true );
	$manufacturer = get_post_meta( $post->ID, 'manufacturer', true );
	$model = get_post_meta( $post->ID, 'model', true );
	$fuel_type = get_post_meta( $post->ID, 'fuel_type', true );
	$variant = get_post_meta( $post->ID, 'variant', true );
	$stage = get_post_meta( $post->ID, 'stage', true );
	$vehicle_registration = get_post_meta( $post->ID, 'vehicle_registration', true );
	$transmission = get_post_meta( $post->ID, 'transmission', true );
	$mileage = get_post_meta( $post->ID, 'mileage', true );
	$tool_used = get_post_meta( $post->ID, 'tool_used', true );
	$vehicle_age = get_post_meta( $post->ID, 'vehicle_age', true );
	$free_options = get_post_meta( $post->ID, 'free_options', true );
	$paid_options = get_post_meta( $post->ID, 'paid_options', true );
	$invoice_link = get_post_meta( $post->ID, 'invoice_link', true );
	$invoice_price = get_post_meta( $post->ID, 'invoice_price', true );
	$dealer_file = get_post_meta( $post->ID, 'dealer_file', true );
	$void_reason = get_post_meta( $post->ID, 'void_reason', true );
	$comments = get_post_meta( $post->ID, 'comments', true );
	// Output the field
	echo '<h3><b class="col-sm-3 control-label">Vehicle Details</b></h3>';
	
	echo '<p><b class="col-sm-3 control-label">Manufacturer<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="manufacturer" value="' . esc_textarea( $manufacturer )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Model<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="model" value="' . esc_textarea( $model )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Fuel type<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="fuel_type" value="' . esc_textarea( $fuel_type )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Variant<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="variant" value="' . esc_textarea( $variant )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Stage<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="stage" value="' . esc_textarea( $stage )  . '" class="widefat">';
	
	echo '<h3 class="col-sm-9 col-sm-offset-3">More details</h3>';
	
	echo '<p><b class="col-sm-3 control-label">Vehicle registration<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="vehicle_registration" value="' . esc_textarea( $vehicle_registration )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Transmission<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="transmission" value="' . esc_textarea( $transmission )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Mileage<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="mileage" value="' . esc_textarea( $mileage )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Vehicle age<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="vehicle_age" value="' . esc_textarea( $vehicle_age )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Tool used<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="tool_used" value="' . esc_textarea( $tool_used )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Free options<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="free_options" value="' . esc_textarea( $free_options )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Paid options<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="paid_options" value="' . esc_textarea( $paid_options )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Dealer File<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="dealer_file" value="' . esc_textarea( $dealer_file )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Void Reason<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="void_reason" value="' . esc_textarea( $void_reason )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Invoice Link<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="invoice_link" value="' . esc_textarea( $invoice_link )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Invoice price<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="invoice_price" value="' . esc_textarea( $invoice_price )  . '" class="widefat">';
	echo '<p><b class="col-sm-3 control-label">Comments<span class="requiredinput">:*</span></b></p>';
	echo '<input type="text" name="comments" value="' . esc_textarea( $comments )  . '" class="widefat">';
}
/**
 * Save the metabox data
 */
function wpt_save_events_meta( $post_id, $post ) {
	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times.
	if ( ! isset( $_POST['manufacturer'] ) || ! wp_verify_nonce( $_POST['event_fields'], basename(__FILE__) ) ) {
		return $post_id;
	}
	// Now that we're authenticated, time to save the data.
	// This sanitizes the data from the field and saves it into an array $events_meta.
	$events_meta['manufacturer'] = esc_textarea( $_POST['manufacturer'] );
	$events_meta['fuel_type'] = esc_textarea( $_POST['fuel_type'] );
	$events_meta['model'] = esc_textarea( $_POST['model'] );
	$events_meta['variant'] = esc_textarea( $_POST['variant'] );
	$events_meta['stage'] = esc_textarea( $_POST['stage'] );
	$events_meta['vehicle_registration'] = esc_textarea( $_POST['vehicle_registration'] );
	$events_meta['transmission'] = esc_textarea( $_POST['transmission'] );
	$events_meta['mileage'] = esc_textarea( $_POST['mileage'] );
	$events_meta['vehicle_age'] = esc_textarea( $_POST['vehicle_age'] );
	$events_meta['tool_used'] = esc_textarea( $_POST['tool_used'] );
	$events_meta['fuel_type'] = esc_textarea( $_POST['fuel_type'] );
	$events_meta['free_options'] = esc_textarea( $_POST['free_options'] );
	$events_meta['paid_options'] = esc_textarea( $_POST['paid_options'] );
	$events_meta['invoice_link'] = esc_textarea( $_POST['invoice_link'] );
	$events_meta['invoice_price'] = esc_textarea( $_POST['invoice_price'] );
	$events_meta['dealer_file'] = esc_textarea( $_POST['dealer_file'] );
	$events_meta['void_reason'] = esc_textarea( $_POST['void_reason'] );
	$events_meta['comments'] = esc_textarea( $_POST['comments'] );
	// Cycle through the $events_meta array.
	// Note, in this example we just have one item, but this is helpful if you have multiple.
	foreach ( $events_meta as $key => $value ) :
		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}
		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}
		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}
	endforeach;
}

function handle_dealerform( $user_id ){
	$user = new WP_User( $user_id );
	$manufacturer = $_POST['usermanufacturer'];
	$model = $_POST['usermodel'];
	$fuel_type = $_POST['fuelselect'];
	$variant = $_POST['uservariant'];
	$stage = $_POST['stage'];
	$vehicle_registration = $_POST['reg']; 
	$transmission = $_POST['transmission'];
	$mileage = $_POST['mileage']; 
	$vehicle_age = $_POST['yearmanufactured'];
	$tool_used = $_POST['toolused'];
	$additional_amount = $_POST['additional_amount'];

	$comments = $_POST['comments'];
	$free_options_arr = $_POST['free_options'];
	$free_options = '';
	if($free_options_arr){
		$free_options =implode(", ",$free_options_arr);
	}
	$paid = $_POST['paid_options'];
	
	$user_mobile = get_user_meta($user_id,'user_mobile',true);


	$otherAmount = $additional_amount; 
	if($paid){
		$paid_options =implode(", ",$paid);
		//$otherAmount = array_sum($paid);
	}
	$tot_otheramount = 0;
	if($paid){
		foreach( $paid as $p){
			if( $p == "Exhaust Flap Control" ){
				$tot_otheramount += 25 ; 
			}elseif( $p == "Launch Control" ){
				$tot_otheramount += 25 ; 
			}elseif( $p == "No-Lift Shift" ){
				$tot_otheramount += 25 ; 
			}elseif( $p == "Pops & Bangs" ){
				$tot_otheramount += 20 ; 
			}elseif( $p == "Hard Rev Cut" ){
				$tot_otheramount += 15 ; 
			}elseif( $p == "Start-Stop Deactivation" ){
				$tot_otheramount += 10 ; 
			}elseif( $p == "Tuning On Sport Button" ){
				$tot_otheramount += 10 ; 
			}elseif( $p == "Cylinder On Demend" ){
				$tot_otheramount += 10 ; 
			}elseif( $p == "Sport Displays Calibration Module" ){
				$tot_otheramount += 15 ; 
			}
		}
	}

	$dealer_file = $_POST['dealer_file'];
	
	if( empty( $manufacturer ) || empty( $model ) || empty( $fuel_type ) || empty( $variant ) || empty( $stage ) || empty( $vehicle_registration ) || empty( $transmission ) || empty( $mileage ) || empty( $vehicle_age ) || empty( $tool_used ) || empty( $dealer_file ) ){
		
		if( empty( $dealer_file )){
			?>
					<h2 style='color:red;'>Please Upload File to remap</h2>
			<?php
		}else{
			?>
				<h2 style='color:red;'>Please enter all required fields</h2>	
<?php
		}
	}else{
		$secret_key = get_option( 'dealer_secret_key' );
		$stripe_key = $secret_key;
		
		//delete_user_meta($user_id, '_stripe_customerID' );
		$user_id = get_current_user_id();
		$address = get_user_meta($user_id, 'user_address', true);
		$companyName = get_user_meta($user_id, 'user_company', true);
		$CustomerID = get_user_meta($user_id, '_stripe_customerID', true);
		$stripeToken = $_POST['stripeToken']; 
		
		$defaultAmount = get_option( 'file_price' );
		
		if($stage == 'No tuning'){
			$stage_price = 0;
		}else{
			$stage_price = 20;
		}
		//comment amount that is coming from hidden fields
		/* $amount_invoice = $defaultAmount + $otherAmount ;
		$amount = $defaultAmount + $otherAmount ;
		$amount = $amount * 100; */
		
		// Make dynamic Price
		$amount_invoice = $tot_otheramount + $defaultAmount + $stage_price;
		$amount = $defaultAmount + $tot_otheramount ;
		$amount = $amount * 100;
		
		$taxAmount  = $defaultAmount/100 * 20;
		$fullTotal = $taxAmount + $defaultAmount;
		
		$fullTotal = $amount_invoice;
		
		$dateinvoice = date('Y-m-d');
		$invoiceItem = $dateinvoice.' '.$model.' '.$vehicle_registration.' '.$stage;
		
		if ( empty( $stripeToken ) ) {
			?>
				<h2 style='color:red;'>Card required</h2>
			<?php
		}elseif(!empty($stripeToken) ){
			$stripe_api_key = get_option( 'dealer_stripe_key' );
			
			$provider_user_info = get_userdata($user_id);
			
			$tocken =  $stripeToken;
			$email = $provider_user_info->data->user_email;
			
			/* require_once(ABSPATH.'/wp-content/plugins/dealer_pro/tocken/register_stripe.php'); 
			$customer_id = $customer->id; 
			
			$stripe_user_name = $customer->sources->data[0]->name;
			update_user_meta($user_id, '_stripe_customerID', $customer_id);
			
			$customer_key = $customer_id;
			require_once(ABSPATH.'/wp-content/plugins/dealer_pro/tocken/payment_charge.php'); */
			
			$secret_key = get_option( 'dealer_secret_key' );
			require_once(ABSPATH.'/wp-content/plugins/dealer_pro/stripe-php/init.php');
		
		$url = get_permalink();		
		try {
			 \Stripe\Stripe::setApiKey( $secret_key );
			$customer = \Stripe\Customer::create([
				  'email' => $email,
				  'source'  => $_POST['stripeToken'],
			  ]);
		}catch(Stripe\Error\Base $e) { ?>
			<script>
				//jQuery(document).ready(function(){
					window.location.href = "<?php echo $url; ?>";
					
					jQuery("#submitvehicle").removeClass("active");
					jQuery("#submit_files").removeClass("active");
					jQuery("#activeinvoice").addClass("active");
					jQuery("#invoices").addClass("active in");
				//});
			</script>
			<?php
		}
			$charge = \Stripe\Charge::create([ 'customer'  => $customer->id, 'amount' => $amount, 'currency' => 'GBP', 'source' => $token ]);
		
			if($charge->paid == true){
				$chargedID = $charge->id;
				?>
				<script>
					jQuery(document).ready(function(){
						jQuery("#submitvehicle").removeClass("active");
						jQuery("#submit_files").removeClass("active");
						jQuery("#activeinvoice").addClass("active");
						jQuery("#invoices").addClass("active in");
					});
				</script>
				<h1>Thanks Your File Has Successfully Been Sent</h1>
				<?php
				$my_post = array(
				  'post_title'    		 => $model,
				  'post_content' 		 => $variant,
				  'post_type'            => 'file',
				  'post_status'          => 'publish',
				  'post_author'    		 => $user_id				  
				 );
				$post_ins_id = wp_insert_post( $my_post );
				
				$date = date('Y-m-d');
				
				$html_pdf = pdfpage();
				$html_pdf = str_replace( '#invoicenumber#' ,$post_ins_id, $html_pdf );
				$html_pdf = str_replace( '#invoicedate#' ,$date, $html_pdf );
				$html_pdf = str_replace( '#invoicedate_order#' ,$date, $html_pdf );
				$html_pdf = str_replace( '#total_amount#' ,$amount_invoice, $html_pdf );
				$html_pdf = str_replace( '#tax_amount#' ,$taxAmount, $html_pdf );
				$html_pdf = str_replace( '#total_invoice_amount#' ,$fullTotal, $html_pdf );
				$html_pdf = str_replace( '#company_name#' ,$companyName, $html_pdf );
				$html_pdf = str_replace( '#invoice_item#' ,$invoiceItem, $html_pdf );

				require_once( dirname(__FILE__) . '/dompdf/dompdf_config.inc.php');

				$dompdf = new DOMPDF();
				$dompdf->load_html($html_pdf);
				//$dompdf->set_paper("A4", "Portrait");
				$dompdf->set_paper("A4", "Portrait");
				$dompdf->render();
						
				$pdf_gen = $dompdf->output();
					 
				$now = new DateTime();
				$now->format('Y-m-d H:i:s');    // MySQL datetime format
				$datetime = $now->getTimestamp();
				$upload_dir = wp_upload_dir();
				$user_dirname = $upload_dir['basedir'].'/invoices';
				if ( ! file_exists( $user_dirname ) ) {
					wp_mkdir_p( $user_dirname );
				}
				$output_file = fopen( $user_dirname.'/'.'dealer-invoice'.$post_ins_id.'.pdf' , 'wb' );

				$downloadPDF = home_url()."/wp-content/uploads/invoices/dealer-invoice".$post_ins_id.".pdf";

				//write the pdf the into the output file
				fwrite($output_file, $pdf_gen);

				fclose($output_file);
				
				//update_post_meta( $post_ins_id, '_pdf_download', $downloadPDF );
				
				update_post_meta( $post_ins_id, 'fuel_type', $fuel_type );
				update_post_meta( $post_ins_id, 'manufacturer', $manufacturer );
				update_post_meta( $post_ins_id, 'model', $model );
				update_post_meta( $post_ins_id, 'variant', $variant );
				update_post_meta( $post_ins_id, 'stage', $stage );
				update_post_meta( $post_ins_id, 'vehicle_registration', $vehicle_registration );
				update_post_meta( $post_ins_id, 'transmission', $transmission );
				update_post_meta( $post_ins_id, 'mileage', $mileage );
				update_post_meta( $post_ins_id, 'tool_used', $tool_used );
				update_post_meta( $post_ins_id, 'vehicle_age', $vehicle_age );
				update_post_meta( $post_ins_id, 'free_options', $free_options);
				update_post_meta( $post_ins_id, 'paid_options', $paid_options );
				update_post_meta( $post_ins_id, 'invoice_link', $downloadPDF );
				update_post_meta( $post_ins_id, 'invoice_price', $fullTotal );
				update_post_meta( $post_ins_id, 'dealer_file', $dealer_file );
				update_post_meta( $post_ins_id, 'comments', $comments );
				update_post_meta( $post_ins_id, 'dealer_post_status', 'Processing' );
				update_post_meta( $post_ins_id, 'dealer_charge_id', $chargedID );
			
				$user_info = get_userdata($user_id);
				$user_email = $user_info->user_email;
				$admin_email = get_option('admin_email');
				
				$html_temp = mail_template_file_rec();
				add_filter( 'wp_mail_content_type','wp_set_content_type' );
				//wp_mail( 'mubeeniqbal82@gmail.com', 'File Received', $html_temp );
				wp_mail( $user_email, 'File Received', $html_temp );
				wp_mail( $admin_email, 'File Received', $html_temp);
				remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
				
				add_filter( 'wp_mail_content_type','wp_set_content_type' );
				wp_mail( $user_email, 'File Received', $html_temp );
				remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
				$admin_mobile = get_option( 'admin_mobile' );
				$user_mobile = get_user_meta($user_id, 'user_mobile', true);
				
				if( $admin_mobile ){
					$cerd_admin = array( 'number_to' =>$admin_mobile ,'message' => 'New file UPLOADED');
					$response = twl_send_sms( stripslashes_deep( $cerd_admin  ) );
				
					if( is_wp_error( $response ) ) {
						?>
						<h2 style='color:red;'><?php echo $response->get_error_message(); ?></h2>
						<?php
					}
				}
				
				if( $user_mobile ){
					$user_message = 'Your remapped file for '.$vehicle_registration.' has been uploaded.';
					$cerd_user = array( 'number_to' =>$user_mobile ,'message' => $user_message);
					$response = twl_send_sms( stripslashes_deep( $cerd_user  ) );
					if( is_wp_error( $response ) ) {
						?>
						<h2 style='color:red;'><?php echo $response->get_error_message(); ?></h2>
						<?php
					}
				}
				$url = get_permalink( ).'?invoice=yes';
				//wp_redirect( $url  );
			}else{ ?>
				<h2 style='color:red;'>You have insufficient amount</h2>
				<?php
			}
		}
	}
}

function update_profile($user_id){
	$name = get_user_meta( $user_id, 'full_name', true );
	$company = get_user_meta( $user_id, 'user_company', true );
	$mobile = get_user_meta( $user_id, 'user_mobile', true );
	$address = get_user_meta( $user_id, 'user_address',true );
	?>
	<form action="" id="profileform" class="form-horizontal standardajax standardajax-validate-only elite-review-form js-portalform" enctype="multipart/form-data" method="post">
	
		<div class="row mb-20">
			<label class="col-sm-3 control-label">Name<span class="requiredinput">:*</span></label>
			<div class="col-sm-9">
				<input name="dealname" value="<?php echo $name; ?>" required type="text" class="form-control formrequired">
			</div>
		</div>
		<div class="row mb-20">
			<label class="col-sm-3 control-label">Company name<span class="requiredinput">:*</span></label>
			<div class="col-sm-9">
				<input name="companyname" value="<?php echo $company; ?>" required type="text" class="form-control formrequired">
			</div>
		</div>
		<div class="row mb-20">
			<label class="col-sm-3 control-label">Mobile Number Uk Only<span class="requiredinput">:*</span></label>
			<div class="col-sm-9">
				<input name="mobile" value="<?php echo $mobile; ?>" required type="text" class="form-control formrequired">
			</div>
		</div>
		<div class="row mb-20">
			<label class="col-sm-3 control-label">Address <span class="requiredinput"></span></label>
			<div class="col-sm-9">
				<textarea name="address" rows="4"><?php echo $address; ?></textarea>
			</div>
		</div>
		<p class="text-right">
			<input name="update_profiledeal" type="submit" class="btn btn-default" value="Update">
		</p>
	</form>
	<?php
}

function dealerDashboard( ) {
	global $wpdb;
	
	$user_id = get_current_user_id();
	//delete_user_meta($user_id, '_stripe_customerID' );
	ob_start();
	/* error_reporting(E_ALL);
	ini_set('display_errors', 1); */

if ( is_user_logged_in() ) { 
	
	if(isset($_POST['submit_vehicle'])){
		handle_dealerform( $user_id );
	}
	
	if( isset($_POST['update_profiledeal']) ){
		update_user_meta( $user_id, 'full_name', $_POST['dealname'] );
		update_user_meta( $user_id, 'user_company', $_POST['companyname'] );
		update_user_meta( $user_id, 'user_mobile', $_POST['mobile'] );
		update_user_meta( $user_id, 'user_address', $_POST['address'] );
		
		echo '<h4 style="color:#fdd147;">Profile Updated Successfully</h4>';
	}
	$redirect = home_url('dealer-registration');
	if( isset($_GET['invoice']) ){ ?>
		<script>
			jQuery(document).ready(function(){
				jQuery("#submitvehicle").removeClass("active");
				jQuery("#submit_files").removeClass("active");
				jQuery("#activeinvoice").addClass("active");
				jQuery("#invoices").addClass("active in");
			});
		</script>
		<?php
	}
	?>
<div class="container1122" style="margin-bottom:4em;">
	<ul class="nav nav-tabs">
		<li ><a data-toggle="tab" href="#profile_dealer">Profile</a></li>
		<li ><a data-toggle="tab" href="#new_upload_files">UPLOADED</a></li>
		<li id="submitvehicle" class="active"><a data-toggle="tab" href="#submit_files">Submit New</a></li>
		<li id="activeinvoice"><a data-toggle="tab" href="#invoices">INVOICES</a></li>
		<h3><a href="<?php echo wp_logout_url( $redirect ); ?>" class="btn btn-default pull-right margin-clear">Logout</a></h3>
	</ul>

	<div class="tab-content">
		<div id="profile_dealer" class="tab-pane fade in">
			<div class="tab-content"style="padding:1em;" >
				<?php update_profile($user_id); ?>
			</div>
		</div>
		<div id="new_upload_files" class="tab-pane fade in">
	<?php                   

			$args = array(
			  'author'        =>  $user_id,
			  'post_type' => 'file',  
			  'orderby'       =>  'post_date',
			  'order'         =>  'DESC',
			  'posts_per_page' => -1 // no limit
			);


			$current_user_posts = get_posts( $args );
			?>
			<div class="container22"><div class="row ">
			<?php
			if(!empty($current_user_posts)){
			
			
				foreach($current_user_posts as $post){
					$manufacturer = get_post_meta( $post->ID, 'manufacturer', true );
					$model = get_post_meta( $post->ID, 'model', true );
					$fuel_type = get_post_meta( $post->ID, 'fuel_type', true );
					$variant = get_post_meta( $post->ID, 'variant', true );
					$stage = get_post_meta( $post->ID, 'stage', true );
					$vehicle_registration = get_post_meta( $post->ID, 'vehicle_registration', true );
					$transmission = get_post_meta( $post->ID, 'transmission', true );
					$mileage = get_post_meta( $post->ID, 'mileage', true );
					$tool_used = get_post_meta( $post->ID, 'tool_used', true );
					$vehicle_age = get_post_meta( $post->ID, 'vehicle_age', true );
					$free_options = get_post_meta( $post->ID, 'free_options', true );
					$paid_options = get_post_meta( $post->ID, 'paid_options', true );
					$dealer_file = get_post_meta( $post->ID, 'dealer_file', true );
					$void_reason = get_post_meta( $post->ID, 'void_reason', true ); 
					$filestauts = get_post_meta( $post->ID, 'dealer_post_status', true ); 
					$comments = get_post_meta( $post->ID, 'comments', true );
					//$currentdate = get_the_date( 'l F j, Y' , $post->ID );
					$currentdate = get_the_date( 'j M Y h:i:s A' , $post->ID );
					
					//$datetime = date( "j M Y h:i:s A", strtotime( $postdate ) );
					
					$approved_file = get_post_meta( $post->ID, 'admin_approved_dealerfile', true );
					
					$updateddate = get_post_meta( $post->ID ,'file_updated_date', true );
					
					?>
					<div class="main_container">
						<div id="show_date"><?php echo $currentdate;
							if($updateddate){
							?>
							<span style="padding-left:2em;"> <?php if($filestauts == 'Cancel') { echo 'Cancelled On: '.$updateddate; }else{  echo 'Completed On: '.$updateddate; } ?></span><?php 
							} ?>
						</div> 
						<div class="show_data">
							<?php echo '<div class="manufacturer">'.$manufacturer.' >> '.$model.' >> '.$vehicle_age.' >> '.$transmission.' </div>'; ?>
							<div class="stage_row"><div><?php echo $stage; ?>'</div></div>
						<?php 
							if($filestauts == 'Processing'){
								echo '<div class="download_file_main" style="text-align:right;"><a style="pointer-events: none;" class="btn btn-default btn btn-submit" download>Pending</a></div>';
							}elseif($filestauts == 'Cancel'){
								echo '<p style="clear:left;" class="void_reason"><span style="font-weight:bold;">Void Reason: </sapn>'.$void_reason.'</p>';
								echo '<div class="download_file_main" style="text-align:right;"><a href="'.$dealer_file.'" style=""  class="void_link btn btn-danger margin-clear" download>Void File</a></div>';
							}else{
								echo '<div class="download_file_main" style="text-align:right;"><a href="'.$approved_file.'" class="btn-primary btn btn-submit" download>Download File</a></div>';
							
								// this will be future work
								//echo '<div class="download_file_main" style="text-align:right;"><span style="padding-right:1em;"><a href="'.$dealer_file.'" class="btn-default btn btn-submit" download>Certificate</a></span><a href="'.$approved_file.'" class="btn-primary btn btn-submit" download>Download File</a></div>';
							} ?>
						</div>
					</div>
					<?php 
				}
			}else{
				echo '<div class="main_container"><p>No files uploaded yet</p></div>';
			}
			?>
			</div>
			</div>
		</div>
		<div id="submit_files" class="tab-pane fade in active">
			<div class="tab-content" id="submit_files">
			  <!--<form action="" id="mem_form" class="form-horizontal standardajax standardajax-validate-only elite-review-form js-portalform" enctype="multipart/form-data" method="post">-->
			  <form action="" id="mem_form" class="form-horizontal standardajax standardajax-validate-only elite-review-form js-portalform"  method="post">
				<!--<input type="hidden" name="userdata" value="true">
				<input type="hidden" name="method" value="upload">
				<input type="hidden" name="manufacturer">
				<input type="hidden" name="model">
				<input type="hidden" name="variant"> 
				<div class="gotcha">Please do not fill this: <input type="text" name="gotcha"></div>-->
				<div class="row">
				  <h4 class="col-sm-9 col-sm-offset-3 mt-30">Select vehicle:</h4>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label">Manufacturer<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<input name="usermanufacturer" value="<?php if(isset($_POST['usermanufacturer'])){ echo $_POST['usermanufacturer']; } ?>" required type="text" class="form-control formrequired">
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label">Model<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<input name="usermodel" value="<?php if(isset($_POST['usermodel'])){ echo $_POST['usermodel']; }  ?>"  required type="text" class="form-control formrequired">
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label">Fuel type<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<select name="fuelselect" required class="form-control formrequired">
					  <option value="">Select fuel type</option>
					  <option value="Diesel" <?php if(  isset($_POST['fuelselect']) && $_POST['fuelselect'] == 'Diesel' ){ echo 'selected'; } ?> >Diesel</option>
					  <option value="Petrol" <?php if(  isset($_POST['fuelselect']) && $_POST['fuelselect'] == 'Petrol' ){ echo 'selected'; } ?>>Petrol</option>
					</select>
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label">Variant<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<input name="uservariant" value="<?php if(isset($_POST['uservariant'])){ echo $_POST['uservariant']; } ?>"  required type="text" class="form-control formrequired">
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label">Stage<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<select id="stage_dealer" name="stage" required class="form-control formrequired">
					   <option data-value="0" value="No tuning" <?php if(  isset($_POST['stage']) && $_POST['stage'] == 'No tuning' ){ echo 'selected'; } ?> >No tuning</option>
					   <option  data-value="20" value="Economy" <?php if(  isset($_POST['stage']) && $_POST['stage'] == 'Economy' ){ echo 'selected'; } ?> >Economy</option>
					   <option data-value="20" value="Stage 1" <?php if(  isset($_POST['stage']) && $_POST['stage'] == 'Stage 1' ){ echo 'selected'; } ?> >Stage 1</option>
					   <option data-value="20" value="Stage 2" <?php if(  isset($_POST['stage']) && $_POST['stage'] == 'Stage 2' ){ echo 'selected'; } ?> >Stage 2</option>
					   <option data-value="20" value="Stage 3" <?php if( isset($_POST['stage']) && $_POST['stage'] == 'Stage 3' ){ echo 'selected'; } ?> >Stage 3</option>
					   <option data-value="20" value="Stage 4" <?php if(  isset($_POST['stage']) && $_POST['stage'] == 'Stage 3' ){ echo 'selected'; } ?> >Stage 4</option>
					</select>
				  </div>
				</div>
				<div class="row mb-30">
				  <p class="col-sm-9 col-sm-offset-3"></p>
				</div>
				<div class="row">
				  <h4 class="col-sm-9 col-sm-offset-3">More details:</h4>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="reg">Vehicle registration<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<input id="reg" name="reg" value="<?php if(isset($_POST['reg'])){ echo $_POST['reg']; } ?>" required type="text" placeholder="EG. YE61 DNK" class="form-control formrequired placeholder">
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="name">Transmission<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<select name="transmission" required class="form-control formrequired">
					  <option value="">Select:</option>
					  <option value="Manual 5 speed" <?php if( isset($_POST['transmission']) && $_POST['transmission'] == 'Manual 5 speed' ){ echo 'selected'; } ?>  >Manual 5 speed</option>
					  <option value="Manual 6 speed" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'Manual 6 speed' ){ echo 'selected'; } ?> >Manual 6 speed</option>
					  <option value="Automatic" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'Automatic' ){ echo 'selected'; } ?>>Automatic</option>
					  <option value="DSG" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'DSG' ){ echo 'selected'; } ?>>DSG</option>
					  <option value="DSG 6" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'DSG 6' ){ echo 'selected'; } ?>>DSG 6</option>
					  <option value="DSG 7" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'DSG 7' ){ echo 'selected'; } ?>>DSG 7</option>
					  <option value="DKG" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'DKG' ){ echo 'selected'; } ?>>DKG</option>
					  <option value="DCT" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'DCT' ){ echo 'selected'; } ?>>DCT</option>
					  <option value="SMG" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'SMG' ){ echo 'selected'; } ?>>SMG</option>
					  <option value="SMG 2" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'SMG 2' ){ echo 'selected'; } ?>>SMG 2</option>
					  <option value="Tiptronic" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'Tiptronic' ){ echo 'selected'; } ?>>Tiptronic</option>
					  <option value="Multitronic" <?php if( isset($_POST['transmission']) &&  $_POST['transmission'] == 'Multitronic' ){ echo 'selected'; } ?>>Multitronic</option>
					</select>
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="name">Mileage<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
	<input id="mileage" required name="mileage" value="<?php if(isset($_POST['mileage'])){ echo $_POST['mileage']; } ?>" type="text" placeholder="Please enter numbers only" data-regex="[0-9]+" class="form-control formrequired placeholder">
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="name">Vehicle age<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<select name="yearmanufactured" required class="form-control formrequired">
						<option value="">Select:</option>
						<?php
						$yearend = date('Y');
						$startyear = 1995 ; 
						for( $startyear ; $startyear  <= $yearend ; $startyear ++ ){ ?>
							<option value="<?php echo $startyear; ?>" <?php if(  isset($_POST['yearmanufactured']) && $startyear == $_POST['yearmanufactured'] ){ echo 'selected'; } ?> ><?php echo $startyear; ?></option><?php
						}
						?>
					</select>
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="location">Tool used<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9">
					<input id="toolused" name="toolused" value="<?php if(isset($_POST['toolused'])){ echo $_POST['toolused']; } ?>" required type="text" placeholder="The tool you used to read the vehicle" class="form-control formrequired placeholder">
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="location">Free options:</label>
				  <div class="col-sm-9">
					<div class="well">
					  <div class="row">
						<div class="col-xs-6"><label>
						  <input type="checkbox"  value="DPF Disable" name="free_options[]"> DPF Disable
						  </label><br><label>
						  <input type="checkbox" value="EGR Disable" name="free_options[]"> EGR Disable
						  </label><br><label>
						  <input type="checkbox" value="Speed Limiter Disable" name="free_options[]"> Speed Limiter Disable
						  </label><br><label>
						  <input type="checkbox" value="RPM Limiter Increase" name="free_options[]"> RPM Limiter Increase
						  </label><br>
						</div>
						<div class="col-xs-6"><label>
						  <input type="checkbox" value="Lambda/O2 Disable" name="free_options[]"> Lambda/O2 Disable
						  </label><br><label>
						  <input type="checkbox" value="Intake Manifold Flap Disable" name="free_options[]"> Intake Manifold Flap Disable
						  </label><br><label>
						  <input type="checkbox" value="AdBlue (SCR) Disable" name="free_options[]"> AdBlue (SCR) Disable
						  </label><br><label>
						  <input type="checkbox" value="Specific DTC Disable" name="free_options[]"> Specific DTC Disable
						  </label><br>
						</div>
					  </div>
					</div>
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="location">Paid options:</label>
				  <div class="col-sm-9">
					<div class="well">
					  <div class="row">
						<div class="col-xs-12"><label>
						  <input class="paid_options" type="checkbox" data-value="25" value="Exhaust Flap Control" name="paid_options[]"> Exhaust Flap Control <span class="label label-warning">+£25
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="25" value="Launch Control" name="paid_options[]"> Launch Control <span class="label label-warning">+£25
						  </span></label><br><label>
						  <input class="paid_options" type="checkbox" data-value="25" value="No-Lift Shift" name="paid_options[]"> No-Lift Shift <span class="label label-warning">+£25
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="20" value="Pops &amp; Bangs" name="paid_options[]"> Pops &amp; Bangs <span class="label label-warning">+£20
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="15" value="Hard Rev Cut" name="paid_options[]"> Hard Rev Cut <span class="label label-warning">+£15
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="10" value="Start-Stop Deactivation" name="paid_options[]"> Start-Stop Deactivation <span class="label label-warning">+£10
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="10" value="Tuning On Sport Button" name="paid_options[]"> Tuning On Sport Button <span class="label label-warning">+£10
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="10" value="Cylinder On Demend" name="paid_options[]"> Cylinder On Demend <span class="label label-warning">+£10
						  </span></label><br><label>
						  <input class="paid_options"  type="checkbox" data-value="15" value="Sport Displays Calibration Module" name="paid_options[]"> Sport Displays Calibration Module <span class="label label-warning">+£15
						  </span></label><br>
						</div>
					  </div>
					</div>
				  </div>
				</div>
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="location">Comments:</label>
				  <div class="col-sm-9">
					<textarea class="form-control" name="comments"><?php if(isset($_POST['comments'])){ echo $_POST['comments']; } ?></textarea>
				  </div>
				</div>
					<?php
				$CustomerID22 = get_user_meta($user_id, '_stripe_customerID', true);
				if( empty( $CustomerID ) ){
				?>
				<div class="row mb-20">
					<label class="col-sm-3 control-label" for="name">Add Card Info<span class="requiredinput">:*</label>
					<div class="col-sm-9" >
						<p style="color:green;display:none;font-weight:bold;font-size:18px;" id="msgcreditsaved">Your Card has been saved successfully</p>
						<input type="button" onclick="saveinfo();" id="credit_cardinfo22" class="btn btn-primary" value="Pay with Card " />
					</div>
				</div>
				<?php
				}
				$count = 1;
				?>
				
				<div class="row mb-20">
				  <label class="col-sm-3 control-label" for="name">File to remap<span class="requiredinput">:*</span></label>
				  <div class="col-sm-9" >
					<div id="upformdeal"><?php echo do_shortcode('[file_uplaoder count ='.$count.']'); ?></div>
					<input type="hidden" id="chkfile_<?php  echo $count; ?>" value="" name="dealer_file">
					<div class="upload-response-<? echo $count; ?>"></div>
				</div>
					<input type="hidden" name="default_amount" value="<?php echo get_option( 'file_price' ); ?>" id="default_amount">
					<input type="hidden" name="additional_amount" value="0" id="additional_amount">
					<input type="hidden" name="total_amount" value="<?php echo get_option( 'file_price' ); ?>" id="dealer_totPrice">
				</div>
				<script src="https://checkout.stripe.com/checkout.js"></script>
				<style>
					.upload-response-1 {
						margin-top: 10px !important;
						clear: both;
					}
					.aspk_gif_img_1 img{
						bottom: auto !important;
					}
					p.bg-success {
						font-size: 17px;
						padding: 12px 20px 12px;
						color: black;
					}
					p.fail_message {
						background: black;
						font-size: 17px;
						padding: 12px 20px 12px;
					}
				</style>
					
				<script>
						
				jQuery( document ).ready(function() {
					
					//jQuery( "#uploadfile_<? echo $count; ?>" ).click(function(e) {
					jQuery("#uploadfile_<? echo $count; ?>").on("change", function (e) {
							jQuery(".upload-response-<? echo $count; ?>").html('');
							jQuery("#aspk_gif_img_<? echo $count; ?>").show();
							jQuery("#submitvehiclebtn").attr('disabled','disabled');
							jQuery("#uploadfile_<? echo $count; ?>").attr('disabled','disabled');
							//jQuery(".files-data-<? echo $count; ?>").css("visibility", "hidden");
							btn_upload_id = jQuery(this).attr('data_val');
							console.log(btn_upload_id);
							e.preventDefault;
							
							if(jQuery('.files-data-<? echo $count; ?>').prop('files').length > 0){
								var fd = new FormData();
								var files_data = jQuery('.upload-form .files-data-<? echo $count; ?>'); // The <input type="file" /> field
								// Loop through each data and create an array file[] containing our files data.
								jQuery.each(jQuery(files_data), function(i, obj) {
									jQuery.each(obj.files,function(j,file){
										fd.append('files[' + j + ']', file);
									})
								});
								
								// our AJAX identifier
								fd.append('action', 'cvf_upload_files');  
								
								// Remove this code if you do not want to associate your uploads to the current page.
							 //   fd.append('post_id', <?php echo $post->ID; ?>); 

								jQuery.ajax({
									type: 'POST',
									action: 'cvf_upload_files',
									url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
									data: fd,
									contentType: false,
									processData: false,
									success: function(response){
										var obj = JSON.parse(response);
										jQuery("#submitvehiclebtn").attr('disabled',false);
										console.log(obj.msg);
										if( obj.st == 'pass'){
											jQuery('#chkfile_<?php  echo $count; ?>').val( obj.fileurl );
											jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
											jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										}else{
											jQuery("#uploadfile_<? echo $count; ?>").val(null);
											jQuery('.upload-response-<? echo $count; ?>').html( obj.msg );
											jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
											jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
										}
										
									}
								});
							}else{
								jQuery("#uploadfile_<? echo $count; ?>").val(null);
								jQuery(".files-data-<? echo $count; ?>").show();
								jQuery("#uploadfile_<? echo $count; ?>").attr('disabled',false);
								jQuery("#submitvehiclebtn").attr('disabled',false);
								jQuery("#aspk_gif_img_<? echo $count; ?>").hide();
								alert('Please select any file and then continue');
							}
						});
					
					
					// Handler for .ready() called.
					jQuery( "#multi-7208-0" ).hide();

					jQuery( "#multi-7208-0 .close-btn" ).click(function() {
					  jQuery( "#multi-7208-0" ).hide();
					});

					jQuery( ".upload_gallary" ).click(function() {
					  jQuery( "#multi-7208-0" ).show();
					});


					jQuery( "#multi-7208-0 .close-btn" ).click(function() {
						
						var image_gallery = [];
						jQuery('.preview').each(function(){
							image_gallery.push(jQuery(this).attr('link'));
						});
						image_gallery_c = image_gallery.join();
						jQuery('#file_dealer').html(image_gallery_c)
					});

				});

				
				function sum(input){
							 
				 if (toString.call(input) !== "[object Array]")
					return false;
					  
							var total =  0;
							for(var i=0;i<input.length;i++)
							  {                  
								if(isNaN(input[i])){
								continue;
								 }
								  total += Number(input[i]);
							   }
							 return total;
							}
				jQuery('.paid_options').on('change', function() {
					  var favorite = [];
					  var total=0;
						   jQuery.each(jQuery("input[name='paid_options[]']:checked"), function(){ 
								favorite.push(jQuery(this).attr('data-value'));
							});
						   total = sum(favorite);
						   var additional_amount = jQuery('#additional_amount').val(total);
							var default_amount =  jQuery('#default_amount').val();
							var additional_amount = jQuery('#additional_amount').val();
							var total_amount = parseInt(default_amount)  + parseInt(additional_amount);
							jQuery('#dealer_totPrice').val(total_amount);
							console.log(total_amount);
				});
					var handler = StripeCheckout.configure
					({
						key: '<?php echo get_option( 'dealer_stripe_key' ); ?>',
						image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
						token: function(token) 
						{
							jQuery('#mem_form').append("<input type='hidden' name='stripeToken' value='" + token.id + "' />"); 
							jQuery('#credit_cardinfo22').hide(); 
							jQuery('#msgcreditsaved').show(); 
							setTimeout(function(){
								//jQuery('#mem_form').submit(); 
								
							}, 200); 
						}
					});
					jQuery(".checkbox").change(function() {
						if(this.checked) {
							//Do stuff
						}
					});
					function saveinfo(){
				
						//var amount_feild = jQuery('#dealer_totPrice').val(total_amount);
						var amount = jQuery('#dealer_totPrice').val();
						
						var stagePrice = jQuery("#stage_dealer option:selected" ).attr('data-value'); 
						
						var amount = parseInt(stagePrice) +  parseInt(amount) ;
						jQuery('#dealer_totPrice').val(amount);
						
						total=amount*100;
						//total = 100;
						handler.open({
						name: "Pay with Card",
						panelLabel: "Add Card",
						currency : 'GBP',
						description: 'Charges( '+amount+' GBP )',
						//description: 'Save card information',
						amount: total
						});
					}
				</script>
				<p class="text-right">
				  <input name="submit_vehicle" id="submitvehiclebtn" type="submit" class="btn btn-default" value="Submit File">
				</p>
			  </form>
			  
			</div>
		</div>
		<div id="invoices" class="tab-pane fade">
			<?php                   
			$args = array(
			  'author'        =>  $user_id,
			  'post_type' => 'file',  
			  'orderby'       =>  'post_date',
			  'order'         =>  'DESC',
			  'posts_per_page' => -1 // no limit
			);


			$current_user_posts = get_posts( $args );
			?>
			<div class="container">
			<div class="row">
			<?php
			if(!empty($current_user_posts)){
			foreach($current_user_posts as $post){
				$invoice_link = get_post_meta( $post->ID, 'invoice_link', true );
				$invoice_number = get_post_meta( $post->ID, 'invoice_number', true );
				$invoice_price = get_post_meta( $post->ID, 'invoice_price', true );
				 echo '<div class="row invoice_main"><div class="col-md-8"><div class="btn btn-default d-right margin-clear invoice_link">Invoice # '.$post->ID.' - &#163;'.$invoice_price.'</div></div>';
				 echo '<div class="col-md-4"><a href="'.$invoice_link.'" class="invoice_link" download>Download PDF</a></div></div>';
			}

			?>
			</div>
			<?php 
			}else{
				echo '<div class="main_container"><p>No files uploaded yet</p></div>';
			}
			?>
			</div>
		</div>
	</div>
</div><!-- end container loged in -->
<?php
} else {
	?>
	<h1>Login required to access this page <a style="color:#fdd247;" href="<?php echo home_url('dealer-registration'); ?>"><input type="button" value="Click Here" style="background: #fdd247;border-radius: 10px;"></a> to login</h1>
	<?php
	$html = ob_get_clean();
	return $html;
	}
}

function registrationPortal(){
	global  $wpdb ;
	
	if (  is_user_logged_in() ) {
		?>
		<div class="container" style="height:20em;padding:1em;margin:1em 0 2em 0;">
		<h1>You are already logged In <a style="color:#fdd247;" href="<?php echo home_url('dealer-portal'); ?>"><input type="button" value="View Dealer Portal" style="background: #fdd247;border-radius: 10px;"></a> </h1>
		</div>
		<?php
	}else{
		if(isset($_POST['submit_user'])){
			//$method = $_POST['method'];
			$name = $_POST['user_name'];
			$company = $_POST['company'];
			$email = $_POST['emaildeal'];
			$mobile =  $_POST['mobile'];
			$address = $_POST['address'];
			//$vatnumber = $_POST['vatnumber'];
			$password = $_POST['password'];
			$password2 = $_POST['password2']; 
			/* $email_insert = str_replace( '@' ,'?', $email );
			$password = str_replace( '@' ,'?', $password ); */
			$date = date('Y-m-d');
		
			if (is_numeric($mobile)) {	
				if ( username_exists( $email ) == true  ) {
					echo '<h2>This email already exists.</h2>';
						
				}elseif (  email_exists($email) == false ) {
					 $sql = "INSERT INTO {$wpdb->prefix}dd_dealers(username,email_user,password,address,company,mobile_number,created_date,status) VALUES('{$name}',' {$email} ','{$password}','{$address}','{$company}','{$mobile}','{$date}','0' )";
					$wpdb->query($sql);
					
					echo "<p class='confirmmsg' style='font-size: 20px;color: #fdd147;font-weight: 600;'>Your request has been sent to the admin for approval you can login  after admin approval.</p>";
					$admin_email = get_option('admin_email');
					
					$msgtoadmin = "<p style='color:white;'>A dealer has signed up to the dealer portal.Please login to approve.</p>";
					$msgtouser = "<p style='color:white;'>Your dealer registration has been submitted. You will recieve an email when DD remapping approve your account.</p><p style='color:white;'>Thanks DD Remapping</p>";
					
					add_filter( 'wp_mail_content_type','wp_set_content_type' );
					$welcomemail = welcome();
					$welcomemail = str_replace( '#welcomemsg#' ,$msgtoadmin, $welcomemail );
					wp_mail( $admin_email, 'Dealer Registeration Request', $welcomemail);
					
					/* $welcomemail = welcome();
					$welcomemail = str_replace( '#welcomemsg#' ,$msgtouser, $welcomemail );
					wp_mail( $email, 'Dealer Registeration Submitted', $welcomemail);
					
					remove_filter( 'wp_mail_content_type', 'wp_set_content_type' ); */
					
					
				}
			}else{
				echo '<h2>Please enter valid Mobile Number.</h2>';
			}
		} 
		
		if(isset($_POST['sign_forgot']) ){
			$forgot_email = $_POST['user_loginname'];
			
			$user = get_user_by( 'email', $forgot_email );
			$user_id = $user->ID;
			
			if($user_id){
				$password = random();
				
				wp_set_password( $password, $user_id );
				
				add_filter( 'wp_mail_content_type','wp_set_content_type' );
				
				wp_mail( $forgot_email, 'New Password', 'Your New Password is '.$password );
				
				remove_filter( 'wp_mail_content_type', 'wp_set_content_type' );
				
				echo "<h2>Please check your email for password retrieval</h2>";
			}else{
				echo "<h2>User with this email not exist</h2>";
			}
		}
		ob_start();
		?>
		<div class="container11" style="margin-bottom:4em;">
			<div class="row">
				 <div class="col-md-8">
					<ul class="nav nav-tabs">
					   <li class=""><a href="#tab-login" data-toggle="tab" aria-expanded="false">LOG IN</a></li>
					   <li class="active"><a href="#tab-register" data-toggle="tab" aria-expanded="true">REGISTER</a></li>
					</ul>          

					<div class="tab-content">
					   <div class="tab-pane fade" id="tab-login">
							<form action="" id="login_form32" class="form-horizontal top_padding"  method="post">
								<div class="form-group">
									<label  class="col-sm-3 control-label">Email<span class="requiredinput">:*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control formrequired"  name="user_loginname" id="email_dealer" value="">
										 <div  style="display:none;color:red;font-size:18px;" id="email_wronge">Email is required</div>
									</div>
								</div>
								<div class="form-group">
									<label  class="col-sm-3 control-label">Password<span class="requiredinput">:*</span></label>
									<div class="col-sm-5">
									<input type="password" id="password_dealer" class="form-control formrequired"  name="user_paswd" value="">
									 <div  style="display:none;color:red;font-size:18px;" id="password_wronge">Password is required</div>
									</div>
								</div>
								<div class="form-group">
									<h3 id="123aspk_fail" style="color:red;display:none;"></h3>
									<div class="col-sm-offset-3 col-sm-9">
										<div id="aspk_gif_img" style="display:none;">
											<img style="width:10em;" src="<?php echo plugins_url('images/wait.gif', __FILE__); ?>">
										</div>
									   <input name="sign_inuser_submit" type="button" id="submit_btn_sign" onclick="signin_user();" class="btn btn-default" value="SIGN IN">
									   <p><a href="#!" onclick="forgotForm();">Forgot Password</a></p>
									</div>
								 </div>
								 <script>
									function forgotForm(){
										jQuery("#forgot32").show();
										jQuery("#login_form32").hide();
									}
									jQuery("#email_dealer").focusin(function(){
										jQuery("#email_wronge").hide();
									});
									jQuery("#password_dealer").focusin(function(){
										jQuery("#password_wronge").hide();
									});
									function signin_user(){
										var email = jQuery("#email_dealer").val();
										var password = jQuery("#password_dealer").val();
										
										var emaildealer = jQuery("#email_dealer").val().length;
										var pswd_dealer = jQuery("#password_dealer").val().length;
										if ( emaildealer < 1  ) {
											jQuery("#email_wronge").show();
											return false;
										}
										if ( pswd_dealer < 1  ) {
											jQuery("#password_wronge").show();
											return false;
										}
							
										jQuery("#123aspk_fail").hide();
										jQuery("#aspk_gif_img").show();
										jQuery("#submit_btn_sign").attr('disabled','disabled');
										
										var data = {
												'action': 'yezter_request_sign',
												'email': email,
												'password': password
												
											};
										var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
										// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
										
										jQuery.post(ajaxurl, data, function(response) {
											jQuery("#aspk_gif_img").hide();
											jQuery("#submit_btn_sign").attr('disabled',false);
											var obj = JSON.parse(response);
											console.log(obj);
											if( obj.st == 'fail'){
												jQuery("#123aspk_fail").html( obj.msg );
												jQuery("#123aspk_fail").show();
											}else{
												//location.reload();
												window.location.href = "<?php echo home_url('dealer-portal'); ?>";
											}
											
										});
									}

								  </script>
							</form>
							<form action="" class="form-horizontal top_padding" id="forgot32" style="display:none;"  method="post">
								<div class="form-group">
									<label  class="col-sm-3 control-label">Email<span class="requiredinput">:*</span></label>
									<div class="col-sm-5">
										<input type="email" required class="form-control formrequired"  name="user_loginname" value="<?php if( isset($_POST['user_loginname'] ) ){ echo $_POST['user_loginname']; } ?>">
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-9">
									   <input name="sign_forgot" type="submit" id="submit_btn_forgot" class="btn btn-default" value="Forgot">
									   <p><a href="#!" onclick="signinForm();">Sign In</a></p>
									</div>
								 </div>
								 <script>
									function signinForm(){
										jQuery("#forgot32").hide();
										jQuery("#login_form32").show();
									}
								</script>
							</form>
							</div>
					   <div class="tab-pane fade active in" id="tab-register">
						  <form action="" class="form-horizontal top_padding"  method="post">
							 <input type="hidden" name="method" value="register">
							 <div class="form-group">
								<label  class="col-sm-5 control-label">Name<span class="requiredinput">:*</span></label>
								<div class="col-sm-7">
								   <input type="text" required class="form-control formrequired" id="name32" name="user_name" value="<?php if( isset($_POST['user_name'] ) ){ echo $_POST['user_name']; } ?>">
								   <div  style="display:none;color:red;font-size:18px;" id="name_32">Name is required</div>
								</div>
							 </div>
							 <div class="form-group">
								<label for="inputEmail2" class="col-sm-5 control-label">Company name<span class="requiredinput">:*</span></label>
								<div class="col-sm-7">
								   <input type="text" class="form-control formrequired" id="company32" required name="company" value="<?php if( isset($_POST['company'] ) ){ echo $_POST['company']; } ?>">
									<div  style="display:none;color:red;font-size:18px;" id="company_32">Company is required</div>
								</div>
							 </div>
							 <div class="form-group">
								<label  class="col-sm-5 control-label">Email<span class="requiredinput">:*</span></label>
								<div class="col-sm-7">
								   <input type="email" required class="form-control formrequired" id="email32" name="emaildeal" value="<?php if( isset($_POST['emaildeal'] ) ){ echo $_POST['emaildeal']; } ?>">
								   <div  style="display:none;color:red;font-size:18px;" id="email_32">Email is required</div>
								</div>
							 </div>
							 <div class="form-group">
								<label for="inputEmail4" class="col-sm-5 control-label">Mobile number * (UK only):</label>
								<div class="col-sm-7">
								   <input type="text" required class="form-control" id="mobile_number" name="mobile" value="<?php if( isset($_POST['mobile'] ) ){ echo $_POST['mobile']; } ?>" >
								   <div  style="display:none;color:red;font-size:18px;" id="show_validnum">Please enter a valid UK number</div>
								</div>
							 </div>
							 <div class="form-group">
								<label  class="col-sm-5 control-label">Billing address<span class="requiredinput">:*</span></label>
								<div class="col-sm-7">
								   <textarea required class="form-control"  name="address" rows="4"><?php if( isset($_POST['address'] ) ){ echo $_POST['address']; } ?></textarea>
								   
								   <div  style="display:none;color:red;font-size:18px;" id="billing_address">Billing address is required</div>
								</div>
							 </div>
							 <!--
							 <div class="form-group">
								<label  class="col-sm-5 control-label">VAT number:</label>
								<div class="col-sm-7">
								   <input type="text" class="form-control" id="valnumber32" name="vatnumber"  value="<?php if( isset($_POST['vatnumber'] ) ){ echo $_POST['vatnumber']; } ?>" >
								</div>
							 </div>-->
							 <div class="form-group">
								<label for="inputPassword1" class="col-sm-5 control-label">Password<span class="requiredinput">:*</span></label>
								<div class="col-sm-7">
								   <input type="password" required value="<?php if( isset($_POST['password'] ) ){ echo $_POST['password']; } ?>" class="form-control" id="chk_pass" name="password">
								</div>
							 </div>
							 <div class="form-group">
								<label  class="col-sm-5 control-label">Retype password<span class="requiredinput">:*</span></label>
								<div class="col-sm-7">
								   <input type="password" required class="form-control" id="confirm_password" value="<?php if( isset($_POST['password2'] ) ){ echo $_POST['password2']; } ?>" name="password2">
								   <div  style="display:none;color:red;font-size:18px;" id="showmsg_psd">Password does not match</div>
								</div>
							 </div>
							 <div class="form-group">
								<div class="col-sm-offset-5 col-sm-9">
								   <input name="submit_user" type="submit" id="submit_btn_dealer" class="btn btn-default" value="REGISTER">
								</div>
							 </div>
						  </form>
					   </div>
					</div>
				 </div>
				 <script>
				 function validateNumber(num) {
					 if(isNaN(num)) {
						 return false;
					 } else if(String(num).charAt(0) != 0 || String(num).charAt(1) != 7 || String(num).length != 11 ) {
						 return false;
					 } else {
						 return true;
					 }
				 }
					jQuery( document ).ready(function() {
						 jQuery("#mobile_number").focusout(function(){
							 var num = jQuery("#mobile_number").val();
							var checknum = validateNumber(num);
							
							if(!checknum){
								jQuery("#show_validnum").show();
								jQuery("#submit_btn_dealer").attr("disabled","disabled");
							} else {
								jQuery("#show_validnum").hide();
								jQuery("#submit_btn_dealer").attr("disabled",false);
							}
							
							console.log(checknum);
						});
						//name front end handling
						/* jQuery("#name32").focusout(function(){
							var name = jQuery("#name32").val().length;
							if ( name < 1  ) {
								jQuery("#submit_btn_dealer").attr("disabled","disabled");
								jQuery("#name_32").show();
								return false;
							}else{
								jQuery("#submit_btn_dealer").attr("disabled",false);
								jQuery("#name_32").hide();
							}
							
						});
						jQuery("#company32").focusout(function(){
							var name = jQuery("#company32").val().length;
							if ( name < 1  ) {
								jQuery("#submit_btn_dealer").attr("disabled","disabled");
								jQuery("#mobile_number").show();
								return false;
							}else{
								jQuery("#submit_btn_dealer").attr("disabled",false);
								jQuery("#mobile_number").hide();
							}
							
						}); */
						jQuery("#confirm_password").focusout(function(){
							var password = jQuery("#chk_pass").val();
							var confirmPassword = jQuery("#confirm_password").val();
							if (password != confirmPassword) {
								jQuery("#submit_btn_dealer").attr("disabled","disabled");
								jQuery("#showmsg_psd").show();
								return false;
							}
							if (password == confirmPassword) {
								jQuery("#submit_btn_dealer").attr("disabled",false);
								jQuery("#showmsg_psd").hide();
							}
						});
					});
				 </script>
				 <div class="col-md-4 sky-sidebar">
					<div id="search-form">
					</div>
					<div class="well mb-30">
					   <div class="feature-box text-center">
						  <div class="icon">
							 <i class="glyph-icon flaticon-ribbon"></i>
						  </div>
						  <div class="content">
							 <h6><a href="<?php echo get_permalink(); ?>">Dealer Opportunities</a></h6>
							 <p>Become a dealer and benefit from our years of experience in the industry</p>
						  </div>
					   </div>
					</div>
				 </div>
			  </div>
		   </div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}

function geo_my_wp_script_back_css() {
wp_register_style('geo_my_wp_style_back', plugins_url('css/dealer_pro_back.css',__FILE__));
 wp_enqueue_style('geo_my_wp_style_back');
}

function geo_my_wp_script_front_css() {
		/* CSS */
        wp_register_style('geo_my_wp_style_bootstrap', plugins_url('css/bootstrap.min.css',__FILE__));
        wp_register_style('geo_my_wp_style', plugins_url('css/dealer_pro111.css',__FILE__), array(), null );
        //wp_register_style('include_dealer', plugins_url('css/dealer_pro11.css',__FILE__));
        wp_enqueue_style('geo_my_wp_style');
        wp_enqueue_style('geo_my_wp_style_bootstrap');
}

		add_action( 'wp_ajax_my_ajax_rt', 'my_ajax_rt' );
		add_action( 'wp_ajax_nopriv_my_ajax_rt', 'my_ajax_rt' );
function my_ajax_rt() {
	
}

function geo_my_wp_script_back_js() {
	
}



function geo_my_wp_script_front_js() {
		wp_register_script('dealer_pro_js', plugins_url('js/dealer_pro11.js', __FILE__ ),array('jquery'));
        wp_enqueue_script('dealer_pro_js');
		wp_register_script('bootstrap_wp_script', plugins_url('js/bootstrap.min.js', __FILE__ ),array('jquery'));
        wp_enqueue_script('bootstrap_wp_script');

}
