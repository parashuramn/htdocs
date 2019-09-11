<?php
	ini_set('display_errors', 0);
	function excelEncode(&$value, $key){$value = iconv('UTF-8', 'Windows-1251', $value);}
	
	class Backup{
		public $driver="";
		public $host="";
		public $username="";
		public $password="";
		public $database="";
		public $db=false; 
		
		
		// connecting
		public static function init ($params){
			$back = new Backup();
			$back->driver 	= $params["driver"];
			$back->host 	= $params['host'];
			$back->username	= $params['username'];
			$back->password	= $params['password'];
			$back->database	= $params['database'];
			$back->temp = (isset($params["temp"]) && $params["temp"]!="")?$params["temp"]:dirname($_SERVER['REQUEST_URI'])."/";
			$back->errors = array();
			switch($back->driver){
				case 'mysql':
					if ($back->db =  mysql_connect($back->host, $back->username, $back->password)){
						if (!mysql_select_db($back->database, $back->db)){
							$back->errors[] = "Can't select database";
						}
					}else{
						$back->errors[] = "Connection error";
					}
					break;
				case 'mysqli':
					if ($back->db =  mysqli_connect($back->host, $back->username, $back->password, $back->database)){
					}else{
						$back->errors[] = "Connection error";
					}
					break;
				case 'pdo':					
					try {
						$back->db = new PDO('mysql:host='.$back->host.';dbname='.$back->database, $back->username, $back->password);
					}catch(PDOException $e){
						$back->errors[] = "Pdo connection error: ". $e->getMessage();
					}
					break;
			}
			return $back;
		}
		
		// form sql file
		public function as_sql(){
			if (count ($this->errors)>0){ return $this;}
			try{
				$this->filename = $this->database.".sql";
				$file = $this->temp.$this->filename;
				$f = fopen($file, "w+");
				if (!$f){
					$this->errors[] = "As Sql function error: Can't open file ".$file; return $this;
				}
				$tables = $this->getTables();
				$tables_query = $this->formSqlTables($tables);
				fwrite($f, $tables_query);
				foreach ($tables as $name=>$rows){
					$count_query = "SELECT COUNT(*) as mc FROM `".$name."`";
					$count = $this->execQuery($count_query);
					$steps = ceil($count[0]["mc"]/100);
					for ($i=0; $i<$steps; $i++)
					{
						$query_values = "SELECT * FROM `".$name."` LIMIT ".($i*100).", 100";
						$values = $this->execQuery($query_values);
						$query = "";
						if (count($values>0)){
							$query = "INSERT INTO ".$name." VALUES ";
							$val_query ="";	
							for ($n=0; $n<count($values); $n++){
								// Одна строка
								$val_query.= "(";
								foreach ($values[$n] as $key=>$value){
									$val_query .= '"'.addslashes($value).'", ';
								}
								$val_query = substr($val_query, 0, strlen($val_query)-2); 
								$val_query .= "),".chr(10);
							}
							$val_query = substr($val_query, 0, strlen($val_query)-2); 
							$query.= $val_query.";".chr(10);
							fwrite($f, $query);
						}
					}
				}
				fclose($f);
			}catch(Exception $e){
				$this->errors = "Sql function error: ".$e->getMessage();
			}	
			return $this;
			
		}
		
		private function getTables(){
			$result = array();
			$tables = $this->execQuery("SHOW TABLE STATUS");
			foreach ($tables as $table)	{
				$name = $table["Name"];
				$result[$name]["table"] = $this->execQuery("DESCRIBE `".$name."`");
				$result[$name]["create"] = $this->execQuery("SHOW CREATE TABLE `".$name."`");
				$result[$name]["Engine"] = $table["Engine"];
				$result[$name]["Auto_increment"] = $table["Auto_increment"];
				$result[$name]["Collation"] = $table["Collation"];
			}
			return $result;
		}
		
		private function formSqlTables($tables)	{
			$query = "";
			$query_post = "";
			foreach ($tables as $name=>$rows){
				$query.=$rows["create"][0]["Create Table"].";".chr(10);
			}
			return $query;
		}
		
	
		public function as_csv($delimeter=";"){
			if (count ($this->errors)>0){ return $this;}
			try{
				$tables = $this->getTables();
				$numb = 0;
				foreach ($tables as $name=>$rows){
					$this->filename[$numb] = $name.".csv";
					$f = fopen($this->temp.$this->filename[$numb], "w+");
					
					$header = array();
					foreach($rows['table'] as $row)
					{
						$header[] = $row["Field"];
					}
					array_walk($header, 'excelEncode');
					fputcsv($f, $header,$delimeter);
					$count_query = "SELECT COUNT(*) as mc FROM `".$name."`";
					$count = $this->execQuery($count_query);
					$steps = ceil($count[0]["mc"]/100);
					for ($i=0; $i<$steps; $i++)
					{
						$query_values = "SELECT * FROM `".$name."` LIMIT ".($i*100).", 100";
						$values = $this->execQuery($query_values);
						foreach ($values as $value)
						{
							array_walk($value, 'excelEncode');
							fputcsv($f, $value,$delimeter);
						}
					}
					fclose($f);
					$numb++;
				}
			}catch(Exception $e){
				$this->errors[] = "Csv function error".$e->getMessage();
			}
			return $this;
		}
		private function writeLevel($f, $level, $str)
		{
			$string = str_repeat(chr(9),$level).$str.chr(10);
			fwrite($f, $string);
		}
		
		public function as_xml(	$params = array(
									"root"=>"mysql", 
									"db_tag"=>"database", 
									"db_prop"=>"name", 
									"table_tag"=>"table", 
									"table_prop"=>"name", 
									"row_tag"=>"row", 
									"field_tag"=>"field",
									"field_prop"=>"name")
								){
			if (count ($this->errors)>0){ return $this;}
			try{
				$this->filename = $this->database.".xml";
				$f = fopen($this->temp.$this->filename, "w+");
				$level=0;
				$this->writeLevel($f, $level,'<?xml version="1.0"?>');
				$level++;
				// Root
				if (isset($params["root"])){
					$this->writeLevel($f, $level,"<".$params["root"].">");
					$level++;
				}
				// Database
				if (isset($params["db_prop"])){
					$this->writeLevel($f, $level,'<'.$params["db_tag"].' '.$params["db_prop"].'="'.$this->database.'">');
					$level++;
				}
				elseif(isset($params["db_tag"])){
					if ($params["db_tag"]==""){
						$this->writeLevel($f, $level,'<'.$this->database.'>');
					}else{
						$this->writeLevel($f, $level,'<'.$params["db_tag"].'>');
					}
					$level++;
				}
				
				$tables = $this->getTables();
				foreach ($tables as $name=>$rows){
					// Table open
					if (isset($params["table_prop"])){
						$this->writeLevel($f, $level,'<'.$params["table_tag"].' '.$params["table_prop"].'="'.$name.'">');
						$level++;
					}elseif(isset($params["table_tag"])){
						if ($params["table_tag"]==""){
							$this->writeLevel($f, $level,'<'.$name.'>');
						}else{
							$this->writeLevel($f, $level,'<'.$params["table_tag"].'>');
						}
						$level++;
					}	
					$count_query = "SELECT COUNT(*) as mc FROM `".$name."`";
					$count = $this->execQuery($count_query);
					$steps = ceil($count[0]["mc"]/100);
					// hundreds of rows
					for ($i=0; $i<$steps; $i++)
					{
						$query_values = "SELECT * FROM `".$name."` LIMIT ".($i*100).", 100";
						$values = $this->execQuery($query_values);
						$query_100 = "";
						// steps by rows
						for ($n=0; $n<count($values); $n++){
							$str ="";
							if (isset($params["row_tag"])){
								$str.=str_repeat(chr(9),$level).'<'.$params["row_tag"].'>'.chr(10);
								$level++;
							}
							foreach ($values[$n] as $key=>$value){
								// Steps by fields
								if (isset($params["field_prop"])){
									$str.=str_repeat(chr(9),$level).'<'.$params["field_tag"].' '.$params["field_prop"].'="'.$key.'">'.$value.'</'.$params["field_tag"].'>'.chr(10);
								}
								elseif (isset($params["field_tag"])){
									if ($params["field_tag"]==""){
										$str.=str_repeat(chr(9),$level).'<'.$key.'>'.$value.'</'.$key.'>'.chr(10);
									}else{
										$str.=str_repeat(chr(9),$level).'<'.$params["field_tag"].'>'.$value.'</'.$params["field_tag"].'>'.chr(10);
									}
								}
							}
							// rows close
							if (isset($params["row_tag"])){
								$level--;
								$str.=str_repeat(chr(9),$level).'</'.$params["row_tag"].'>'.chr(10);
							}
							fwrite($f, $str);
						}
					}
					
					// Table close
					if(isset($params["table_tag"]))	{
						$level--;
						if ($params["table_tag"]==""){
							$this->writeLevel($f, $level,'</'.$name.'>');
						}else{
							$this->writeLevel($f, $level,'</'.$params["table_tag"].'>');
						}
					}
				}
				// Database close
				if(isset($params["db_tag"])){
					$level--;
					if ($params["db_tag"]==""){
						$this->writeLevel($f, $level,'</'.$this->database.'>');
					}else{
						$this->writeLevel($f, $level,'</'.$params["db_tag"].'>');
					}
				}
				// Root close
				if (isset($params["root"])){
					$level--;
					$this->writeLevel($f, $level,"</".$params["root"].">");
				}
				fclose($f);
			}catch(Exception $e){
				$this->errors[] = "Xml function error: ".$e->getMessage();
			}
			return $this;
		}
		
		public function mail($email, $emailFrom=null){
			if (count ($this->errors)>0){ return $this;}
			$this->sendFile($email,$this->filename, $emailFrom);
			return $this;
		}
		
		private function sendFile($email, $files, $emailFrom=null){
			try{
				
				$from = "Base backuper <noreply@mailer.com>"; 
				if ($emailFrom!=null){
					$from = "Base backuper <".$emailFrom.">"; 
				}
				$subject = "Backup by ".date("d.M H:i"); 
				$message = "Backup by ".date("Y.m.d H:i:s")."\n ".count($files)." attachments";
				$headers = "From: $from";
				$semi_rand = md5(time()); 
				$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
				$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
				/*$message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
				"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
				for($i=0;$i<count($files);$i++){
					if(is_file($files[$i])){
						$message .= "--{$mime_boundary}\n";
						$fp =    @fopen($this->temp.$files[$i],"rb");
					$data =    @fread($fp,filesize($this->temp.$files[$i]));
								@fclose($fp);
						$data = chunk_split(base64_encode($data));
						$message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
						"Content-Description: ".basename($files[$i])."\n" .
						"Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" . 
						"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
						}
				}
				$message .= "--{$mime_boundary}--";*/
				$message = "Backup of GrassBlade LRS (".Router::url('/', true) .") at ". date("Y.m.d H:i:s");				
				$returnpath = "-f noreply@mailer.com";
				if ($emailFrom!=null){
					$returnpath = "-f ".$emailFrom;
				}
				$ok = gb_mail( $email, $subject, $message, $emailFrom, TMP.$files );
				//$ok = @mail($email, $subject, $message, $headers, $returnpath);
			}catch (Exception $e){
				$this->errors[] = "Email function error: ".$e->getMessage();
			}
			if($ok){ 
				return true; 
			}else { 
				$this->errors[] = "Email sending error";
				return false; 
			}
		}
		
		public function http($params){
			if (count ($this->errors)>0){ return $this;}
			if (is_array($this->filename)){
				foreach($this->filename as $filename){
					if (!$this->sendHttp($params, $filename)){
						return $this;
					}					
				}
			}else{
				$this->sendHttp($params, $this->filename);
			}
			return $this;
		}
		
		private function sendHttp($params, $filename){
			$handle = curl_init ($params["server"]);
			if ($handle)
            {
				// specify custom header
                $customHeader = array(
                    "Content-type: application/text"
                );
				$fh = fopen($this->temp.$filename, 'r');
                $curlOptArr = array(
                    CURLOPT_PUT => TRUE,
                    CURLOPT_HEADER => TRUE,
                    CURLOPT_HTTPHEADER => $customHeader,
                    CURLOPT_INFILESIZE => filesize($this->temp.$filename),
                    CURLOPT_INFILE => $fh,
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => $params["user"]. ':' .$params["pass"],
                    CURLOPT_RETURNTRANSFER => TRUE
                );
                curl_setopt_array($handle, $curlOptArr);
               if (curl_exec($ch)==false){
					$this->errors[] = curl_error($ch);
					return false;
				}
                curl_close($ch);
				return true;
			}
		}
		
		public function save($path){
			if (count ($this->errors)>0){ return $this;}
			if (is_array($this->filename)){
				if (count($this->filename)>0)
				foreach($this->filename as $filename){
					if (!copy($this->temp.$filename, $path.$filename)){
					$this->errors[] = "Copy failed. Wrong path: ".$this->temp.$filename." => ".$path.$filename;
						return $this;
					}
				}
			}else{
				if (!copy($this->temp.$this->filename, $path.$this->filename)){
					$this->errors[] = "Copy failed. Wrong path: ".$this->temp.$this->filename." => ".$path.$this->filename." : ".file_exists($this->temp.$this->filename);
				}
			}
			return $this;
		}
		
		public function ftp($params){
			if (count ($this->errors)>0){ return $this;}
			if (is_array($this->filename)){
				foreach($this->filename as $filename){
					if (!$this->sendFtp($params,$filename)){
						return $this;
					}
				}
			}else{
				$this->sendFtp($params,$this->filename);
			}
			return $this;
		}
		
		private function sendFtp($params, $filename){
			if ($connection = ftp_connect($params['server'])){
				$login = ftp_login($connection, $params['user'], $params['pass']);
				if (!$connection || !$login) { 
					$this->errors[] = 'FTP Connection attempt failed!'; 
				}else{
					$dest = isset($params["dest"])?$params["dest"]:"";
					if (ftp_pasv($connection, true)== false){
						$this->errors[] = "FTP Password error";
					}
					else{
						$upload = ftp_put($connection, $dest."/".$filename, $this->temp.$filename, FTP_BINARY);
						if (!$upload) { 
							$this->errors[] ="FTP upload failed!"; 
						}
						ftp_close($connection);
					}
				}
			}else{
				$this->errors[] = "Ftp connection error";
			}
			if (count($this->errors)>0){ 
				return false;
			}
			return true;
		}
		
		public function webdav($params){
			if (count ($this->errors)>0){ return $this;}
			
			if (is_array($this->filename)){
				foreach($this->filename as $filename){
					$this->sendWebdav($params,$filename);
				}
			}else{
				$this->sendWebdav($params,$this->filename);
			}
			return $this;
		}
		
		private function sendWebdav($params, $filename){
			$filesize = filesize($this->temp.$filename);
			$fh = fopen($this->temp.$filename, 'r');
			$ch = curl_init($params["server"].$filename);
			curl_setopt($ch, CURLOPT_USERPWD, $params["user"].":".(string)$params["pass"]);
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_setopt($ch, CURLOPT_INFILE, $fh);
			curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			if (curl_exec($ch)===false){
				$this->errors[] = "Webdav curl Error: ".curl_error($ch);
				fclose($fh);
				return false;
			}
			curl_close($ch);
			return true;
			
		}
		
		public function zip($dateFlag = true){
			if (count ($this->errors)>0){ return $this;}
			try{
				if (extension_loaded("zip"))
				{
					$date = ($dateFlag)?("_".date("Y.m.d_H.i.s")):"_".date("Y.m.d");
					$zip = new ZipArchive();
					$zipname = "grassblade_lrs-v".grassblade_latest_version()."-database_backup".$date.".zip";
					if (file_exists($this->temp.$zipname)){ unlink($this->temp.$zipname);}
					if ($zip->open($this->temp.$zipname, ZipArchive::CREATE)!==TRUE) {
						$this->errors[] = "Can't create file";
					}
					if (is_array($this->filename)){ 
						foreach($this->filename as $filename){
							$zip->addFile($this->temp.$filename, $filename);
						}
						$zip->close();
						foreach($this->filename as $filename){	unlink($this->temp.$filename);}
						
					}else{
						$zip->addFile($this->temp.$this->filename, $this->filename);
						$zip->close();
						unlink($this->temp.$this->filename);					
					}
					$this->filename = $zipname;
				}else{
					$this->errors[] = "php_zip.dll is not loaded";
				}
			}catch(Exception $e){
				$this->errors[] = "Zipping error";
			}
			return $this;
			
		}
		
		private function execQuery($query){
			switch($this->driver){
			case "mysql":
					$result = array();
					if ($res = mysql_query($query, $this->db)){
						while($row = mysql_fetch_assoc($res)) {
							$result[] = $row;
						}
					}else{
						$this->errors[] = "Mysql query error: ".mysql_error()." QUERY: ".$query;
					}
				return $result;
				break;
			case "mysqli":
				$result = array();
				if ($res = mysqli_query($this->db, $query)){
					while($row = mysqli_fetch_array($res)) {
						$result[] = $row;
					}
				}else{
					$this->errors[] = "Mysqli query error: ".mysql_error()." QUERY: ".$query;
				}
				return $result;
				break;
			case "pdo":
					$stmt=$this->db->prepare($query);
					$stmt->execute();
					while($row = $stmt->fetch()) {
						$result[] = $row;
					}
				return $result;
			}
			return false;
		}
		
		public function remove(){
			if (count ($this->errors)>0){ return $this;}
			try{
				if (is_array($this->filename)){
					foreach($this->filename as $filename){
						unlink ($this->temp.$filename);
					}
				}else{
					unlink ($this->temp.$this->filename);
				}
			}catch(Exception $e){
				$this->errors[] = "Can't delete files ".$e->getMessage();
			}
			return $this;
		}
		
		public function showErrors(){
			if (count($this->errors)>0){
				foreach($this->errors as $error){
					echo $error."<br />\n";
				}
			}
		}
	}
	
	class File_backup{
	public $filename="";
	public $extensions = array();
	public $exclusions = array();
	public $errors = array();
	
	public function zip($params, $dateFlag = true)
	{ 
		$backup = new File_backup();
		$zip = new ZipArchive;
		$date = ($dateFlag)?("_".date("Y.m.d_H.i.s")):"_".date("Y.m.d");
		$backup->filename = "grassblade_lrs-v".grassblade_latest_version()."-files_backup".$date.".zip";
		$backup->extensions = $params["extensions"] ;
		$backup->exclusions = $params["exclusions"];
		$backup->temp = (isset($params["temp"])&& $params["temp"]!="")?$params["temp"]:dirname($_SERVER['REQUEST_URI'])."/";
		if (file_exists(substr($backup->temp, 0, -1))){
			if (file_exists($backup->temp.$backup->filename)){unlink($backup->temp.$backup->filename);}
			$zipAdr = '';
			$res = $zip->open($backup->temp.$backup->filename, ZIPARCHIVE::CREATE|ZipArchive::OVERWRITE);
			if ( $res === TRUE){
				$backup->convToZip($params["path"],$zip, $zipAdr);
				$zip->close();
			}else{
				$backup->errors[] = "Zip error: can't create file: ".$backup->temp.$backup->filename; 
			}
		}else{
			$backup->errors[] = "Zip error: wrong \"temp\" : ".$backup->temp;
		}
		return $backup;  
	}

	public function convToZip($path, $zip, $zipAdr)
	{
		$dir = opendir($path);
		if( $dir === false){ $this->errors[] = "Zip error: can't open dir: ".$path; return false;}
		try{
			while($d = readdir($dir)){
				if ($d == '.' || $d == '..' || $d == ".git" || $d == "backups") continue;
				$path_info = pathinfo($d);
				$file_ext = $path_info['extension'];	
				if (is_file($path.$d) && $file_ext != "zip") 
				{
					if ($zipAdr == ""){
						$zip->addFile($path.$d, $d);
					}
					else{$zip->addFile($path.$d,$zipAdr.'/'.$d);}	
				}
				elseif (is_dir($path.$d) ){
					if ($zipAdr == ""){
						$zip->addEmptyDir($d);
						$this->convToZip($path.$d.'/', $zip, $d);
					}
					else{
						$zip->addEmptyDir($zipAdr.'/'.$d); 
						$this->convToZip($path.$d.'/', $zip, $zipAdr.'/'.$d);
					}
				}
			}			
		}catch(Exception $e){
			$this->errors[] = "Zip function error: ".$e->getMessage();
			return false;
		}
		return true;
	}
	
	public function mail($email, $emailFrom=null){
			if (count ($this->errors)>0){ return $this;}
			$this->sendFile($email,$this->filename, $emailFrom);
			return $this;
		}
		
		private function sendFile($email, $files, $emailFrom=null){
			try{
				
				$from = "Base backuper <noreply@mailer.com>"; 
				if ($emailFrom!=null){
					$from = "Base backuper <".$emailFrom.">"; 
				}
				$subject = "Backup by ".date("d.M H:i"); 
				$message = "Backup by ".date("Y.m.d H:i:s")."\n ".count($files)." attachments";
				$headers = "From: $from";
				$semi_rand = md5(time()); 
				$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
				$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
				/*$message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
				"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
				for($i=0;$i<count($files);$i++){
					if(is_file($files[$i])){
						$message .= "--{$mime_boundary}\n";
						$fp =    @fopen($this->temp.$files[$i],"rb");
					$data =    @fread($fp,filesize($this->temp.$files[$i]));
								@fclose($fp);
						$data = chunk_split(base64_encode($data));
						$message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
						"Content-Description: ".basename($files[$i])."\n" .
						"Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" . 
						"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
						}
				}
				$message .= "--{$mime_boundary}--";*/
				$message = "Backup of GrassBlade LRS (".Router::url('/', true) .") at ". date("Y.m.d H:i:s");	
				$returnpath = "-f noreply@mailer.com";
				if ($emailFrom!=null){
					$returnpath = "-f ".$emailFrom;
				}
				//$ok = @mail($email, $subject, $message, $headers, $returnpath);
				$ok = gb_mail( $email, $subject, $message, $emailFrom, TMP.$files );
			}catch (Exception $e){
				$this->errors[] = "Email function error: ".$e->getMessage();
			}
			if($ok){ 
				return true; 
			}else { 
				$this->errors[] = "Email sending error";
				return false; 
			}
		}
		
	public function http($params){
			if (count ($this->errors)>0){ return $this;}
			if (is_array($this->filename)){
				foreach($this->filename as $filename){
					if (!$this->sendHttp($params, $filename)){
						return $this;
					}					
				}
			}else{
				$this->sendHttp($params, $this->filename);
			}
			return $this;
		}
		
	private function sendHttp($params, $filename){
		$handle = curl_init ($params["server"]);
		if ($handle)
		{
			$customHeader = array("Content-type: application/text");
			$fh = fopen($this->temp.$filename, 'r');
			$curlOptArr = array(
				CURLOPT_PUT => TRUE,
				CURLOPT_HEADER => TRUE,
				CURLOPT_HTTPHEADER => $customHeader,
				CURLOPT_INFILESIZE => filesize($this->temp.$filename),
				CURLOPT_INFILE => $fh,
				CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
				CURLOPT_USERPWD => $params["user"]. ':' .$params["pass"],
                CURLOPT_RETURNTRANSFER => TRUE
			);
			curl_setopt_array($handle, $curlOptArr);
			if (curl_exec($ch)==false){
				$this->errors[] = curl_error($ch);
				return false;
			}
			curl_close($ch);
			return true;
		}
	}
		
	public function save($path){
		if (count ($this->errors)>0){ return $this;}
		if (is_array($this->filename)){
			if (count($this->filename)>0)
			foreach($this->filename as $filename){
				if (!copy($this->temp.$filename, $path.$filename)){
					$this->errors[] = "Copy failed. Wrong path: ".$this->temp.$filename." => ".$path.$filename;
					return $this;
				}
			}
		}else{
			if (!copy($this->temp.$this->filename, $path.$this->filename)){
					$this->errors[] = "Copy failed. Wrong path: ".$this->temp.$this->filename." => ".$path.$this->filename." : ".file_exists($this->temp.$this->filename);
			}
		}
		return $this;
	}
		
	public function ftp($params){
		if (count ($this->errors)>0){ return $this;}
		if (is_array($this->filename)){
			foreach($this->filename as $filename){
				if (!$this->sendFtp($params,$filename)){
					return $this;
				}
			}
		}else{
			$this->sendFtp($params,$this->filename);
		}
		return $this;
	}
		
	private function sendFtp($params, $filename){
		if ($connection = ftp_connect($params['server'])){
			$login = ftp_login($connection, $params['user'], $params['pass']);
			if (!$connection || !$login) { 
				$this->errors[] = 'FTP Connection attempt failed!'; 
			}else{
				$dest = isset($params["dest"])?$params["dest"]:"";
				if (ftp_pasv($connection, true)== false){
					$this->errors[] = "FTP Password error";
				}
				else{
					$upload = ftp_put($connection, $dest."/".$filename, $this->temp.$filename, FTP_BINARY);
					if (!$upload) { 
						$this->errors[] ="FTP upload failed!"; 
					}
					ftp_close($connection);
				}
			}
		}else{
			$this->errors[] = "Ftp connection error";
		}
		if (count($this->errors)>0){ 
			return false;
		}
		return true;
	}
		
	public function webdav($params){
		if (count ($this->errors)>0){ return $this;}
		if (is_array($this->filename)){
			foreach($this->filename as $filename){
				$this->sendWebdav($params,$filename);
			}
		}else{
			$this->sendWebdav($params,$this->filename);
		}
		return $this;
	}
		
	private function sendWebdav($params, $filename){
		$filesize = filesize($this->temp.$filename);
		$fh = fopen($this->temp.$filename, 'r');
		$ch = curl_init($params["server"].$filename);
		curl_setopt($ch, CURLOPT_USERPWD, $params["user"].":".(string)$params["pass"]);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if (curl_exec($ch)===false){
			$this->errors[] = "Webdav curl Error: ".curl_error($ch);
			fclose($fh);
			return false;
		}
		curl_close($ch);
		return true;
	}
		
	public function remove(){
		if (count ($this->errors)>0){ return $this;}
		try{
			if (is_array($this->filename)){
				foreach($this->filename as $filename){
					unlink ($this->temp.$filename);
				}
			}else{
				unlink ($this->temp.$this->filename);
			}
		}catch(Exception $e){
			$this->errors[] = "Can't delete files ".$e->getMessage();
		}
		return $this;
	}
		
	public function showErrors(){
		if (count($this->errors)>0){
			foreach($this->errors as $error){
				echo $error."<br />\n";
			}
		}
	}		
	}

	
	
	
	set_time_limit(2000);
	
	if (isset($_GET["files"]))
	{
		//$path = $_GET["files"]["path"];
		$path = ROOT.DS;
		$extensions = array();//explode(",", str_replace(" ","",$_GET["files"]["extensions"]));
		$exclusions = array();//explode(",", str_replace(array(", "," ,"),"",$_GET["files"]["exclusions"]));
		//$temp = $_GET["files"]["temp"];
		$temp = APP."tmp".DS;

		$params = array("path"=>$path, "extensions"=>$extensions, "exclusions"=>$exclusions, "temp"=>$temp);
		$file_backup = File_backup::zip($params);
		if (isset($_GET["select_to"])){
			switch($_GET["select_to"]){
				case "mail":
					if ($_GET["mail"]["emailfrom"]!=""){
						$file_backup->mail($_GET["mail"]["email"], $_GET["mail"]["emailfrom"]);
					}else{
						$file_backup->mail($_GET["mail"]["email"]);
					}
					break;
				case "http":
					$file_backup->http($_GET["http"]);
					break;
				case "save":
					$file_backup->save($_GET["save"]["path"]);
					break;
				case "webdav":			
					$file_backup->webdav($_GET["webdav"]);
					break;
				case "ftp":
					$file_backup->ftp($_GET["ftp"]);
					break;
			}
		}
		if (isset($_GET["remove"]) && $_GET["remove"]==1){
			$file_backup->remove();
		}
		print_r (json_encode( array("errors"=>$file_backup->errors)));
	}
	
	if (isset($_GET["data"])){
			$params = array();
			$tmp_path = APP."tmp".DS;
			App::uses('ConnectionManager', 'Model');
			$db = ConnectionManager::getDataSource('default');
			
			$params["driver"] = "pdo";
			$params['host'] = $db->config["host"];
			$params['username'] = $db->config["login"];
			$params['password'] = $db->config["password"];
			$params['database'] = $db->config["database"];
			$params["temp"] = $tmp_path;


			//$backup = Backup::init($_GET["data"]);
			$backup = Backup::init($params);
		
		//switch ($_GET["select_as"]){
		//	case "sql":
				$backup = $backup->as_sql()->zip();
		/*		break;
			case "xml":
				$xml_params = array();
				if ($_GET["xml"]["root"]!=""){
					$xml_params["root"] = $_GET["xml"]["root"];
				}
				if ($_GET["xml"]["db_prop"]!=""){
					$xml_params["db_prop"] = $_GET["xml"]["db_prop"];
				}
				$xml_params["db_tag"] = $_GET["xml"]["db_tag"];
				if ($_GET["xml"]["table_prop"]!=""){
					$xml_params["table_prop"] = $_GET["xml"]["table_prop"];
				}
				$xml_params["table_tag"] = $_GET["xml"]["table_tag"];
				if ($xml_params["row_tag"]!=""){
					$xml_params["row_tag"] = $_GET["xml"]["row_tag"];
				}
				if ($_GET["xml"]["field_prop"]!=""){
					$xml_params["field_prop"] = $_GET["xml"]["field_prop"];
				}
				$xml_params["field_tag"] = $_GET["xml"]["field_tag"];
				$backup = $backup->as_xml($xml_params)->zip();
				break;
			case "csv":
				if ($_GET["csv"]["delimeter"]!=""){
					$backup = $backup->as_csv($_GET["csv"]["delimeter"])->zip();
				}else{
					$backup = $backup->as_csv()->zip();
				}
				break;
		}*/
		
		if (isset($_GET["select_to"])){
			switch($_GET["select_to"]){
				case "mail":
					if ($_GET["mail"]["emailfrom"]!=""){
						$backup->mail($_GET["mail"]["email"], $_GET["mail"]["emailfrom"]);
					}else{
						$backup->mail($_GET["mail"]["email"]);
					}
					break;
				case "http":
					$backup->http($_GET["http"]);
					break;
				case "save":
					$backup->save($_GET["save"]["path"]);
					break;
				case "webdav":			
					$backup->webdav($_GET["webdav"]);
					break;
				case "ftp":
					$backup->ftp($_GET["ftp"]);
					break;
			}
		}
		if (isset($_GET["remove"]) && $_GET["remove"]==1){
			$backup->remove();
		}	
		print_r (json_encode( array("errors"=>$backup->errors)));
		
	}
?>