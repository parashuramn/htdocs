<?php
	echo $this->Html->css('/app/webroot/css/backup_styles');
	$tmp_path = APP."tmp".DS;
	App::uses('ConnectionManager', 'Model');
	$db = ConnectionManager::getDataSource('default');
	$backup_path = $tmp_path."backups".DS;
	$backup_save = grassblade_config_get("backup_save");
	$backup_ftp = grassblade_config_get("backup_ftp");
	$backup_http = grassblade_config_get("backup_http");
	$backup_webdav = grassblade_config_get("backup_webdav");
	$backup_mail = grassblade_config_get("backup_mail");

//print_r($db->config);
?>
	<script type="text/javascript">
		
		$().ready(function() { 
			function forms(next){
				var ids = $(next).attr('id').split("_");
				$("#li_"+ids[1]).removeClass("active");
				$("#p_"+ids[1]).removeClass("digit_active");
				$("#p_1").addClass("digit");
				$(".block1").hide();
				$("#form_"+(parseInt(ids[1])+1)).show();
				$("#li_"+(parseInt(ids[1])+1)).addClass("active");
				$("#p_"+(parseInt(ids[1])+1)).addClass("digit_active");
			}
			
			function formQuery(){
				var query ="";
				if ($("#checkbox1").is(":checked")){
					query = $("#database").find("select, input").serialize();
					query += "&"+$("#sel_as").serialize();
					query += "&"+($("#select_"+$("#sel_as").val())).find("input").serialize();
					query += "&"+$("#sel_as").serialize();
					
					if ($("#sel_2").val()!=""){
						query += "&"+$("#sel_2").serialize();
						query += "&"+$("#form_"+$("#sel_2").val()).find("input").serialize();
					}
					if ($("#remove").is(':checked')){
						query += "&remove=1";
					}else{
						query += "&remove=0";
					}
				}
				return query;
			}
			
			function formQuery2(){
				if ($("#checkbox2").is(":checked")){
					var query = "";
					query = $("#files").find("select, input").serialize();
					if ($("#sel_2").val()!=""){
						query += "&"+$("#sel_2").serialize();
						query += "&"+$("#form_"+$("#sel_2").val()).find("input").serialize();
					}
					if ($("#remove").is(':checked')){
						query += "&remove=1";
					}else{
						query += "&remove=0";
					}
				}
				return query;
			}
					
			
			$("#next_3").bind("click", function(event){
				forms($(this));
				var loc = window.location.pathname;
				var dir = loc.substring(0, loc.lastIndexOf('/'));
				
				if ($("#checkbox1").is(":checked")){
					$("#bar").show();
					ajax1 = false;
					var query = formQuery();
					//console.log(dir+"/backup.php?"+query);
					$.ajax({
					url: ajaxurl + "/Configure/Backup/",
					dataType: "json",
					data: query,//"action=parse_url&url="+encodeURIComponent(yoururl),
					type: "GET",
					success: function(data) {
						if (data.errors.length==0)
						{
							$("#result").append("<p><span class=\"message\">Database Backup completed</span><br /></p>");
						}else{
							for (i in data.errors){
								$("#result").append("<p><span class=\"red\">Error: "+data.errors[i]+"</span><br /></p>");
							}
						}
						$("#bar").hide();
					}
					});
				};
				if ($("#checkbox2").is(":checked")){
					$('#file_bar').show();
					var query2 = formQuery2();
					ajax2 = false;
					$.ajax({
					url: ajaxurl + "/Configure/Backup/",
					dataType: "json",
					data: query2,//"action=parse_url&url="+encodeURIComponent(yoururl),
					type: "GET",
					success: function(data) {
						console.log(data);
						if (data.errors.length==0)
						{
							$("#result").append("<p><span class=\"message\">File backup completed</span><br /></p>");
						}else{
							alert(data.errors[0]);
						}
						$('#file_bar').hide();	
							
					}
				});
				}
				
			});
			// 
			$("#sel_as").bind("change", function(){ 
				$(".select_hide").hide();
				var val = $(this).val(); 
				$("#select_"+val).show();
			});
			
			$("#sel_2").bind("change", function(){ 
				$(".form_hide").hide();
				var val = $(this).val(); 
				$("#form_"+val).show();
			});
			
			$("#next_1").bind("click", function(){				
				var c1 = $("#checkbox1").is(":checked");
				var c2 = $("#checkbox2").is(":checked");
				if (c1 || c2){
					forms($(this));
					//$('input, select').attr('disabled', 'disabled');
					if (!c1){	
						$("#database").find('select, input').attr('disabled', true);
						$("#database").fadeTo(0, 0.6);
					}
					if (!c2){	
						$("#files").find('select, input').attr('disabled', true);
						$("#files").fadeTo(0, 0.6);
					}
					$("#next_2").click();
					$("#sel_2").change();
				}else{
					alert("Please, select your type of backup!");
				}
			});
			
			$("#next_2").bind("click", function(){
				var c1 = $("#checkbox1").is(":checked");
				var c2 = $("#checkbox2").is(":checked");
				var error = false;
				if (c1) {
					var fields = $("#database").find("input").each(function (i) {
						if ($(this).val()==""){ 
							if($(this).attr('id')!="password" && $(this).attr('id')!="temp")
								error = true;
						}
					});
				}
				if(c2){
					if($("#files\\[path\\]").val()==""){ error = true;}
				}
				if (error){
					alert("Please, fill requered fields");
				}
				else{
					forms($(this));
				}
			});
			
			$("#cron_3").bind("click", function(event){
				forms($(this));
				if ($("#checkbox1").is(":checked")){
					var query = formQuery();
					var loc = window.location.pathname;
					var full = "0 0 * * * * php " + loc + "backup.php?" + query;
					$("#result").append("<p><span class=\"message\">Cron string for Database Backup: </span><br /><span class=\"cron_string\">"+full+"</span></p>");
				}
				if ($("#checkbox2").is(":checked")){
					var query = formQuery2();
					var loc = window.location.pathname;
					var full2 = "0 0 * * * * php " + loc + "backup.php?" + query;
					$("#result").append("<p><span class=\"message\">Cron string for File backup: </span><br /><span class=\"cron_string\">"+full2+"</span></p>");
				}
			});		
		});
	</script>
<div id="backup">		
<div class="step" align="center">
	<ul class="nav nav-pills" align="center">
		<li class="active" id="li_1">
			<a class="digit"><?php echo __("1"); ?></a> <a class="step_text"><?php echo __("STEP"); ?></a>
		</li>
		<li id="li_3"><a id="p_3" class="digit"><?php echo __("2"); ?></a> <a class="step_text"><?php echo __("STEP"); ?></a></li>
		<li id="li_4"><a id="p_4" class="digit"><?php echo __("3"); ?></a> <a class="step_text"><?php echo __("STEP"); ?></a></li>
		
	</ul>
</div>
<div class="block1" id="form_1" align="center">
	<p><?php echo __("Please, select what to backup!"); ?></p>
	<table >
		<tr>
			<td><input class="css-checkbox" type="checkbox" id="checkbox1" name="checkbox1" checked="checked"/><div class="labl"><label class="css-label" for="checkbox1"><?php echo __("Database"); ?></label></div></td>
			<td><input class="css-checkbox" type="checkbox" id="checkbox2" name="checkbox2" checked="checked"/><div class="labl"><label class="css-label" for="checkbox2"><?php echo __("Files"); ?></label></div></td>
		</tr>
	</table>
	<div class="btn gblue" id="next_1" ><?php echo __("NEXT"); ?></div>
</div>
<div class="block1" id="form_2" align="center" style="display:none">
	    <p><?php echo __("Field settings!"); ?></p>
		<table>
			<tr>
				<td>
					<form id="database">
					<input type="hidden" name="data" value="1" />
					</form>
				</td>
				<td class="right_table">
					<form id="files">
										<input type="hidden" name="files" value="1" />

					</form>
				</td>
			</tr>
		</table>
		<div class="btn gblue" id="next_2"><?php echo __("NEXT"); ?></div>
</div>
<div class="block1" id="form_3" align="center"  style="display:none">

	<div class="select">
		<p><?php echo __("Please, choose where to backup!"); ?></p>
		<table>
			<tr class="select_hide" id="select_csv">
				<td colspan="2">
					<table>
						<tr>							
							<td class="label"><?php echo __("Delimeter"); ?> </td>
							<td class="lab_val"><input class="input1"  type="text" id="delimeter" name="csv[delimeter]" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="select_hide" id="select_xml">
				<td colspan="2">
					<table>
						<tr>
							<td class="label"><?php echo __("Root"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="root" name="xml[root]" /></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Db tag"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="db_tag" name="xml[db_tag]"/></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Db property"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="db_prop" name="xml[db_prop]"/></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Table tag"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="table_tag" name="xml[table_tag]"/></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Table property"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="table_prop" name="xml[table_prop]"/></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Row table"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="row_tag" name="xml[row_tag]"/></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Field table"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="field_tag" name="xml[field_tag]"/></td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Field property"); ?> </td>
							<td class="lab_val"><input class="input1" type="text" id="field_prop" name="xml[field_prop]"/></td>
						</tr>
					</table>
				</td>
			</tr>	
			<tr>
				<td class="label"><?php echo __("Save to"); ?> </td>
				<td class="lab_val">
					<div class="field">
						<select  id="sel_2" name="select_to" value="">
							<option value=""><?php echo __("Select..."); ?></option>
							<option value="mail"><?php echo __("Mail"); ?></option>
							<!--<option value="http">Http</option>-->
							<option value="save" selected><?php echo __("Save"); ?></option>
							<option value="ftp"><?php echo __("FTP"); ?></option>
							<!--<option value="webdav">Webdav</option>-->
						</select>
					</div>
				</td>
			</tr>
			<tr class="form_hide" id="form_mail">
				<td colspan="2">
					<table>
						<tr>
							<td class="label"><?php echo __("Email"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="email" name="mail[email]" value="<?php echo h(@$backup_mail["email"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Email From"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="email" name="mail[emailfrom]" value="<?php echo h(@$backup_mail["emailfrom"]); ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="form_hide" id="form_http">
				<td colspan="2">
					<table>
						<tr>
							<td class="label"><?php echo __("Server"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="serv_1" name="http[server]" value="<?php echo h(@$backup_http["server"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("User"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="user_1" name="http[user]" value="<?php echo h(@$backup_http["user"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Password"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="pass_1" name="http[pass]" value="<?php echo h(@$backup_http["pass"]); ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="form_hide" id="form_save">
				<td colspan="2">
					<table>
						<tr>
							<td class="label"><?php echo __("Path"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="path" name="save[path]" value="<?php echo !empty($backup_save["path"])? h($backup_save["path"]):h($backup_path); ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="form_hide" id="form_ftp">
				<td colspan="2">
					<table>
						<tr>
							<td class="label"><?php echo __("Server"); ?>"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="serv_2" name="ftp[server]" value="<?php echo h(@$backup_ftp["server"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("User"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="user_2" name="ftp[user]" value="<?php echo h(@$backup_ftp["user"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Password"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="pass_2" name="ftp[pass]" value="<?php echo h(@$backup_ftp["pass"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Path for saving"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="dest" name="ftp[dest]" value="<?php echo h(@$backup_ftp["dest"]); ?>">
							</td>
						</tr>	
					</table>
				</td>
			</tr>
			<tr class="form_hide" id="form_webdav">
				<td colspan="2">
					<table>
						<tr>
							<td class="label"><?php echo __("Server"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="serv_3" name="webdav[server]" value="<?php echo h(@$backup_webdav["server"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("User"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="user_3" name="webdav[user]" value="<?php echo h(@$backup_webdav["user"]); ?>">
							</td>
						</tr>
						<tr>
							<td class="label"><?php echo __("Password"); ?> </td>
							<td class="lab_val">
								<input class="input1" type="text" id="pass_3" name="webdav[pass]" value="<?php echo h(@$backup_webdav["pass"]); ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="label"><?php echo __("Remove after sending"); ?> </td>
				<td class="lab_val">
					<div class="field">
						<input type="checkbox" id="remove" name="remove" checked>
					</div>
				</td>
			</tr>
		</table>
		<div>
			<table>
				<tr>
					<td><div class="btn gblue" id="cron_3"><?php echo __("CRON"); ?></div></td>
					<td><div class="btn gblue" id="next_3"><?php echo __("CREATE"); ?></div></td>
				</tr>
			</table>	
		</div>
	</div>
</div>



<div class="block1" id="form_4" align="center"  style="display:none">
	<div id="bar">
		<p><?php echo __("Processing Database Backup"); ?></p>
	</div>
	<div id="file_bar">
		<p><?php echo __("Processing File Backup"); ?></p>
	</div>
	<div id="result"></div>
</div>
</div>

