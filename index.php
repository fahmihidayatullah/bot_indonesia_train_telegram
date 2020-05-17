<?php
/**
 * Telegram Bot example.
 *
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
require_once 'Telegram.php';
// Set the bot TOKEN
$bot_token = 'your bot telegram token';
// Instances the class
$telegram = new Telegram($bot_token);

$text = $telegram->Text();
$text_origin = $text;
$chat_id = $telegram->ChatID();
$first_name = $telegram->FirstName();
$last_name = $telegram->LastName();
$username_telegram = $telegram->Username();
$message_id = $telegram->MessageID();
// Check if the text is a command

$connection = mysqli_connect("localhost", "root", "t", "database_name", "3306");
// tampilkan JSON kosong jika chat_id kosong
if(is_null($chat_id)){
	
	if (!$connection) {
		$reply = "Debugging errno: " . mysqli_connect_errno() . " Debugging error: " . mysqli_connect_error();
	} else {
		$reply = "404 Not Found";
	}
	header("Connection: close\r\n");
	header("Content-Type: application/json\r\n");
	echo json_encode(array('status'=>'success','reply'=>$reply));
	die();
}

if ($connection) {
	mysqli_query($connection, "INSERT INTO `hist_messages` (`chat_id`, `message_id`, `text_message`) VALUES ('".$chat_id."', '".$message_id."', '".$text."')");
	$error = false;
} else {
    $content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => 'Silahkan masukkan command. Ketik **/help** untuk bantuan.'];
	$telegram->sendMessage($content);
}


$text = preg_replace('/\s+/', '', $text);
$textonly = preg_replace("[^a-zA-Z0-9.,]", "", $text);
$first = substr($text, 0, 1);
$check = explode(',', $text, 2);
$sql0 = "";

if($first=="/"){
	$cmd = true;
	$command = $check[0];
	$command = strtolower($command);
}else{
	$cmd = false;
	$query = array();
	$rt = explode(",", $text);
	$query = array_merge($query, $rt);
}

if(count($check)>1) {
    $args = true;
    // split text by comma
	$r = array();
	$rt = explode(",", $text);
	$r = array_merge($r, $rt);

	// remove command from array
	$argument = array();
	$argument = $r;
	$del = array_shift($argument);
}else{
	$args = false;
}

if($cmd==true){
	$username = $username_telegram;
	// with argument
	if (!is_null($command) && !is_null($chat_id)) {
		// command welcome
    	if ($command == '/start') {
	        $reply  = "Welcome to bot of Jadwal Kereta Api Indonesia. \xF0\x9F\x8E\x89 \n Berisikan informasi seputar jadwal perjalanan Kereta Api di Indonesia. \n Silakan Pilih menu yang anda inginkan:";
			$sql = "Select 1 from mst_profile where username = '".$username."' and chat_id = '".$chat_id."' ";
			$result = $connection->query($sql);
			if ($result->num_rows == 0) {
				mysqli_query($connection, "INSERT INTO `mst_profile` (`username`, `name`, `chat_id`) VALUES ('".$username."', '".$first_name." ".$last_name."', '".$chat_id."')") or die("Maaf Pendaftaran Gagal");
			}
	        $keyboard = array("inline_keyboard"=>array(
							array(array("text"=>"Cek Posisi Kereta Api","callback_data"=>"/train_cek")),
							array(array("text"=>"Cek Jadwal Kereta Api","callback_data"=>"/train_schedule")),
							array(array("text"=>"Cek Jadwal Keberangkatan di Stasiun","callback_data"=>"/train_station")),
							array(array("text"=>"Mau naik Kereta apa?","callback_data"=>"/train_route")),
							array(array("text"=>"About","callback_data"=>"/about")),
							array(array("text"=>"Help","callback_data"=>"/help"))
						));
	        $content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
	        $telegram->sendMessage($content);
	    } else if ($command == '/about') {
	        $reply  = file_get_contents("about.md");
	        $content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
	        $telegram->sendMessage($content);
	    } else if ($command == '/help') {
	        $reply  = file_get_contents("help.md");
	        $content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
	        $telegram->sendMessage($content);
	    } else if ($command == '/train_cek') {
			//state 1
			if ($connection) {
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '1' WHERE `mst_profile`.`username` = '".$username."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				$reply  = "Silakan masukkan nama dan nomor Kereta Api yang anda inginkan dengan format 'Nama KA', 'Nomor KA' (Tanpa tanda petik dan gunakan spasi setelah tanda baca koma)";
	        	$content = ['chat_id' => $chat_id, 'text' => $reply];
	        	$telegram->sendMessage($content);
			} else {
				$error = true;
			}
	    } else if ($command == '/train_schedule') {
			//state 2
			if ($connection) {
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '2' WHERE `mst_profile`.`username` = '".$username."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				$reply  = "Silakan masukkan nama dan nomor Kereta Api yang anda inginkan dengan format 'Nama KA', 'Nomor KA' (Tanpa tanda petik dan gunakan spasi setelah tanda baca koma)";
	        	$content = ['chat_id' => $chat_id, 'text' => $reply];
	        	$telegram->sendMessage($content);
			} else {
				$error = true;
			}
	    } else if ($command == '/train_station') {
			//state 3
			if ($connection) {
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '3' WHERE `mst_profile`.`username` = '".$username."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				$reply  = "Silakan masukkan nama stasiun yang anda inginkan dengan format 'Nama Stasiun' (tanpa tanda petik)";
	        	$content = ['chat_id' => $chat_id, 'text' => $reply];
		        $telegram->sendMessage($content);
			} else {
				$error = true;
			}
	    } else if ($command == '/train_route') {
			//state 4
			if ($connection) {
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '4' WHERE `mst_profile`.`username` = '".$username."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				$reply  = "Silakan masukkan nama stasiun keberangkatan yang anda inginkan dengan format 'Stasiun Keberangkatan' (tanpa tanda petik)";
	        	$content = ['chat_id' => $chat_id, 'text' => $reply];
		        $telegram->sendMessage($content);
			} else {
				$error = true;
			}
	    } else if ($command == '/menu') {
			if ($connection) {
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '0' WHERE `mst_profile`.`username` = '".$username."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
			}
		    $reply  = 'Silakan pilih menu berikut';
			$keyboard = array("inline_keyboard"=>array(
				array(array("text"=>"Cek Posisi Kereta Api","callback_data"=>"/train_cek")),
				array(array("text"=>"Cek Jadwal Kereta Api","callback_data"=>"/train_schedule")),
				array(array("text"=>"Cek Jadwal Keberangkatan di Stasiun","callback_data"=>"/train_station")),
				array(array("text"=>"Mau naik Kereta apa?","callback_data"=>"/train_route")),
				array(array("text"=>"About","callback_data"=>"/about")),
				array(array("text"=>"Help","callback_data"=>"/help"))
			));
			$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
			$telegram->sendMessage($content);
	    } else {
			$error = true;
		}
	}
}else{
	$sql = "Select state from mst_profile where username = '".$username_telegram."' and chat_id = '".$chat_id."' ";
	$result = $connection->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$state = $row["state"];
		}
		if ($state == '1'){
			if(count($query) == 2){
				$name_train = $query[0];
				$no_train = $query[1];
				$sql0 = "select distinct no_train, name_train, name_train_spaceout from mst_schedule where name_train_spaceout like '%".$name_train."%' and no_train like '%".$no_train."%' ";
				$train_cek = true;
			} else if(count($query)==1){
				//cek train schedule from train_name or train_no
				$input = $query[0];
				$sql0 = "select distinct no_train, name_train, name_train_spaceout from mst_schedule where name_train_spaceout = '".$input."' or no_train = '".$input."' ";
				$train_cek = true;
			} else {
				$error = true;
			}
		} else if ($state == '2'){
			if(count($query) == 2){
				$name_train = $query[0];
				$no_train = $query[1];
				$sql0 = "select distinct no_train, name_train, name_train_spaceout from mst_schedule where name_train_spaceout like '%".$name_train."%' and no_train like '%".$no_train."%' ";
				$train_schedule = true;
			} else if(count($query)==1){
				$input = $query[0];
				$sql0 = "select distinct no_train, name_train, name_train_spaceout from mst_schedule where name_train_spaceout = '".$input."' or no_train = '".$input."' ";
				$train_schedule = true;
			} else {
				$error = true;
			}
		} else if ($state == '3'){
			if(count($query)==2){
				$req_code_station = $query[1];
				$req_name_station = $query[0];
				$sqlStation = "select distinct name_station, name_station_spaceout, code_station from mst_schedule where name_station_spaceout like '%".$req_name_station."%' and code_station = '".$req_code_station."' ";
				$station_schedule = true;
			} else if(count($query)==1){
				$station = $query[0];
				// $sqlStation = "select distinct name_station, name_station_spaceout, code_station from mst_schedule where name_station_spaceout like '%".$textonly."%' ";
				$sqlStation = "select distinct name_station, name_station_spaceout, code_station from mst_schedule where name_station_spaceout like '%".$station."%' ";
				$station_schedule = true;
			} else {
				$error = true;
			}
		} else if ($state == '4'){
			$sql0 = "select distinct name_station, name_station_spaceout from mst_schedule where name_station_spaceout like '%".$textonly."%' ";
			$result0 = $connection->query($sql0);
			if ($result0->num_rows == 0) {
				$reply  = 'Maaf Terjadi Kesalahan, Harap kirimkan argumen'. PHP_EOL . "'Nama Stasiun' ". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
				$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
				$telegram->sendMessage($content);
			} else if($result0->num_rows == 1) { 
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '5' WHERE `mst_profile`.`username` = '".$username_telegram."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				$reply  = "Silakan masukkan Stasiun Tujuan anda dengan format 'Nama Stasiun' (tanpa tanda petik)\n";
				$content = ['chat_id' => $chat_id, 'text' => $reply];
				$telegram->sendMessage($content);
			} else {
				$reply  = "Ups, Apakah maksud anda stasiun dibawah ini?\nAtau ketik **/menu** untuk kembali ke menu yang tersedia";
				$a = [];
				while($row = $result0->fetch_row()) {
					array_push($a,array(array("text"=>$row[0], "callback_data"=>$row[0])));
				}
				$keyboard = array("inline_keyboard"=> $a);
				$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
				$telegram->sendMessage($content);
			}
		} else if ($state == '5'){
			$sql0 = "select distinct name_station, name_station_spaceout from mst_schedule where name_station_spaceout like '%".$textonly."%' ";
			$result0 = $connection->query($sql0);
			if ($result0->num_rows == 0) {
				mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '4' WHERE `mst_profile`.`username` = '".$username_telegram."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				$reply  = "Silakan masukkan kembali nama stasiun keberangkatan yang anda inginkan dengan format 'Stasiun Keberangkatan' (tanpa tanda petik)". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
				$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
				$telegram->sendMessage($content);
			} else if($result0->num_rows == 1) { 
				$sql_hist = "select text_message from hist_messages where chat_id = '".$chat_id."' order by id_hist_message desc limit 2 ";
				$result_hist = $connection->query($sql_hist);
				if($result_hist->num_rows == 0){
					mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '4' WHERE `mst_profile`.`username` = '".$username_telegram."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
					$reply  = "Silakan masukkan kembali nama stasiun keberangkatan yang anda inginkan dengan format 'Stasiun Keberangkatan' (tanpa tanda petik)". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
					$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
					$telegram->sendMessage($content);
				} else {
					while($row = $result_hist->fetch_assoc()) {
						$old_message = $row["text_message"];
					}
					$sql1 = "select seq_station, no_train, name_train, name_station, IF(time_dep='-', time_arr, time_dep) as time_dep, code_station, CAST(no_train as integer) as nomor from mst_schedule where no_train in (select train_no from mst_train where train_route_spaceout like '%".$old_message."%".$textonly."%') and name_station_spaceout in('".$old_message."','".$textonly."') order by nomor, seq_station";
					$result = $connection->query($sql1);
					$i = 1;
					$j = 1;
					$reply2 = "Daftar Kereta Api dari Stasiun ".$old_message." Ke Stasiun ".$text_origin."\n \n";
					while($row = $result->fetch_row()) {
						if($i%2 == 0){
							$reply2 = $reply2." Datang: ".$row[4]."\n";
						} else {
							$reply2 = $reply2.$j.". KA ".$row[2]." (".$row[1].") Berangkat: ".$row[4];
							$j++;
						}
						$i++; 
					}
					$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply2];
					$telegram->sendMessage($content);
					mysqli_query($connection, "UPDATE `mst_profile` SET `state` = '4' WHERE `mst_profile`.`username` = '".$username_telegram."' and `mst_profile`.`chat_id` = '".$chat_id."' ");
				}
			} else {
				$reply  = "Ups, Apakah maksud anda stasiun dibawah ini?\nAtau ketik **/menu** untuk kembali ke menu yang tersedia";
				$a = [];
				while($row = $result0->fetch_row()) {
					array_push($a,array(array("text"=>$row[0], "callback_data"=>$row[0])));
				}
				$keyboard = array("inline_keyboard"=> $a);
				$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
				$telegram->sendMessage($content);
			}
		} else {
			$error = true;
		}
	} else {
		$error = true;
	}
}

if($train_cek == true){
	$result0 = $connection->query($sql0);
	if ($result0->num_rows == 0) {
		$reply  = 'Maaf Terjadi Kesalahan, Harap kirimkan argumen'. PHP_EOL . "'Nama Kereta', 'No KA' ". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
		$telegram->sendMessage($content);
	} else if($result0->num_rows == 1) {
		while($row = $result0->fetch_row()) {
			$res_name_train_spaceout = $row[2];
			$res_name_train = $row[1];
			$res_no_train = $row[0];
		}
		$diff_time_now = 0;
		$reply = "Posisi Kereta Api ".$res_name_train." (".$res_no_train.") \n";
		$sql1 = "select ROUND(IF((SELECT DATE_FORMAT( NOW() ,  '%H.%i' ))<=train_time_arr, (SELECT DATE_FORMAT( NOW() ,  '%H.%i' )) + add_day*'24.00', (SELECT DATE_FORMAT( NOW() ,  '%H.%i' )))-train_time_dep, 2)*100 as 'diff_time_now' from mst_train where  mst_train.train_no = '".$res_no_train."' and mst_train.train_name_spaceout = '".$res_name_train_spaceout."' ";
		$result1 = $connection->query($sql1);
		if ($result1->num_rows > 0) {
			while($row = $result1->fetch_assoc()) {
				$diff_time_now = $row["diff_time_now"];
			}
			$sql2 = "select name_station, time_arr, time_dep, arr_diff, dep_diff, IF(arr_diff <= ".$diff_time_now.", IF(dep_diff <= ".$diff_time_now.", 'Berangkat', 'Tiba'), 'Else') as 'Note' from mst_schedule where no_train = '".$res_no_train."' and name_train_spaceout = '".$res_name_train_spaceout."' and (dep_diff <= ".$diff_time_now." or arr_diff <= ".$diff_time_now.") order by seq_station desc limit 1";
			$result = $connection->query($sql2);
			if ($result->num_rows == 0) {
				$reply = $reply."Saat ini diluar Jadwal, Kereta api ".$res_name_train." Masih Belum Berangkat atau sudah Tiba di Stasiun Akhir. \n";
			} else {
				while($row = $result->fetch_row()) {
					if($row[2]=='-' && $row[5]=='Berangkat'){
						$reply = $reply."Kereta Api ".$res_name_train." Telah tiba di stasiun akhir ".$row[0]." pada Pukul ".$row[1]." \n";
					} else if ($row[2] != '-' && $row[5]=='Berangkat') {							
						$reply = $reply."Telah Berangkat dari Stasiun ".$row[0]." pada Pukul ".$row[2]." \n";
					} else if ($row[5] == 'Tiba'){
						$reply = $reply."Telah Tiba di Stasiun ".$row[0]." pada Pukul ".$row[1]." dan akan Berangkat kembali Pukul ".$row[2]." \n";
					} else {
						$reply  = 'Maaf Terjadi Kesalahan, Harap kirimkan argumen'. PHP_EOL . "'Nama Kereta', 'No KA' \nAtau ketik **/menu** untuk kembali ke menu yang tersedia";
					}
				}					
			}
			$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
			$telegram->sendMessage($content);
		} else {
			$error = true;
		}
	} else {
		// tampilkan pilihan kereta dari hasil result0
		$reply  = "Ups, Apakah maksud anda kereta dibawah ini?\nAtau ketik **/menu** untuk kembali ke menu yang tersedia";
		$a = [];
		while($row = $result0->fetch_row()) {
			array_push($a,array(array("text"=>$row[1]." (".$row[0].") ","callback_data"=>$row[2].",".$row[0])));
		}
		$keyboard = array("inline_keyboard"=> $a);
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
		$telegram->sendMessage($content);
	}
	$train_cek = false;
}
if($train_schedule == true){
	$result0 = $connection->query($sql0);
	if ($result0->num_rows == 0) {
		$reply  = 'Maaf Terjadi Kesalahan, Harap kirimkan argumen'. PHP_EOL . "'Nama Kereta', 'No KA' ". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
		$telegram->sendMessage($content);
	} else if($result0->num_rows == 1) {
		while($row = $result0->fetch_row()) {
			$res_name_train_spaceout = $row[2];
			$res_name_train = $row[1];
			$res_no_train = $row[0];
		}
		$sql = "select name_station as 'Stasiun', time_arr as 'Datang', time_dep as 'Berangkat' from mst_schedule where name_train_spaceout = '".$res_name_train_spaceout."' and no_train = '".$res_no_train."' ";
		$result = $connection->query($sql);
		$i=1;
		if ($result->num_rows == 0) {
			$reply  = 'Maaf Terjadi Kesalahan, Harap kirimkan argumen'. PHP_EOL . "'Nama Kereta', 'No KA' ". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
		} else {
			$reply = "Jadwal Perjalanan Kereta Api ".$res_name_train." (".$res_no_train.") \n \n";
			while($row = $result->fetch_row()) {
				if($row[1]=='-'){
					$reply = $reply.$i.". Stasiun ".$row[0].", Berangkat ".$row[2]." (Stasiun Awal) \n";
				} else if($row[2]=='-'){
					$reply = $reply.$i.". Stasiun ".$row[0].", Datang ".$row[1]." (Stasiun Akhir) \n";
				} else {
					$reply = $reply.$i.". Stasiun ".$row[0].", Datang ".$row[1].", Berangkat ".$row[2]." \n";
				}
				$i++;
			}
		}
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
		$telegram->sendMessage($content);
	} else {
		// tampilkan pilihan kereta dari hasil result0
		$reply  = "Ups, Apakah maksud anda kereta dibawah ini?\nAtau ketik **/menu** untuk kembali ke menu yang tersedia";
		$a = [];
		while($row = $result0->fetch_row()) {
			array_push($a,array(array("text"=>$row[1]." (".$row[0].") ","callback_data"=>$row[2].",".$row[0])));
		}
		$keyboard = array("inline_keyboard"=> $a);
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
		$telegram->sendMessage($content);
	}
	$train_schedule = false;
}
if($station_schedule == true){
	$result0 = $connection->query($sqlStation);
	if ($result0->num_rows == 0) {
		$reply  = 'Maaf Terjadi Kesalahan, Harap kirimkan argumen'. PHP_EOL . "'Nama Stasiun' ". PHP_EOL ."Atau ketik **/menu** untuk kembali ke menu yang tersedia";
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply];
		$telegram->sendMessage($content);
	} else if($result0->num_rows == 1) {
		while($row = $result0->fetch_row()) {
			$res_name_station_spaceout = $row[1];
			$res_name_station = $row[0];
		}
		$sql = "select * from (select `no_train` as 'No KA', `name_train` as 'KA', `origin_train` as 'Asal', `destination_train` as 'Tujuan', `time_arr` as 'Datang', `time_dep` as 'Berangkat', IF(time_arr = '-', time_dep, time_arr) AS waktu from `mst_schedule` where `name_station_spaceout` = '".$res_name_station_spaceout."') new_table order by new_table.waktu";
		$result = $connection->query($sql);
		if ($result->num_rows > 40) {
			$i = 1;
			$y = 1;
			$reply2 = "Daftar Keberangkatan dan Kedatangan Kereta Api di Stasiun ".$res_name_station." (Part 1) \n \n";
			while($row = $result->fetch_row()) {
				if($row[5] == '-'){
					$reply2 = $reply2.$i.". KA ".$row[1]." (".$row[0].") Dari ".$row[2].". Datang: ".$row[4]." (Tujuan Akhir) \n";
				} else if ($row[4] == '-') {
					$reply2 = $reply2.$i.". KA ".$row[1]." (".$row[0].") Ke ".$row[3].". Berangkat: ".$row[5]."\n";
				} else {
					$reply2 = $reply2.$i.". KA ".$row[1]." (".$row[0].") Ke ".$row[3].". Datang: ".$row[4].", Berangkat:".$row[5]."\n";
				}
				if ($i%40==0){
					$y++;
					$content = ['chat_id' => $chat_id, 'text' => $reply2];
					$telegram->sendMessage($content);
					$reply2 = "Daftar Keberangkatan dan Kedatangan Kereta Api di Stasiun ".$res_name_station." (Part ".$y.")\n \n";
				}
				$i++;
			}
			$content = ['chat_id' => $chat_id, 'text' => $reply2];
			$telegram->sendMessage($content);
		} else if ($result->num_rows > 0 && $result->num_rows <= 40) {
			$i = 1;
			$reply2 = "Daftar Keberangkatan dan Kedatangan Kereta Api di Stasiun ".$res_name_station."\n \n";
			while($row = $result->fetch_row()) {
				if($row[5] == '-'){
					$reply2 = $reply2.$i.". KA ".$row[1]." (".$row[0].") Dari ".$row[2].". Datang: ".$row[4]." (Tujuan Akhir) \n";
				} else if ($row[4] == '-') {
					$reply2 = $reply2.$i.". KA ".$row[1]." (".$row[0].") Ke ".$row[3].". Berangkat: ".$row[5]."\n";
				} else {
					$reply2 = $reply2.$i.". KA ".$row[1]." (".$row[0].") Ke ".$row[3].". Datang: ".$row[4].", Berangkat:".$row[5]."\n";
				}
				$i++;
			}
			$content = ['chat_id' => $chat_id, 'text' => $reply2];
			$telegram->sendMessage($content);
		} else {
			$error = true;
		}
	} else {
		$reply  = "Ups, Apakah maksud anda stasiun dibawah ini?\nAtau ketik **/menu** untuk kembali ke menu yang tersedia";
		$a = [];
		while($row = $result0->fetch_row()) {
			$textStation = $row[1].','.$row[2];
			// array_push($a,array(array("text"=>$row[0], "callback_data"=>$row[1])));
			array_push($a,array(array("text"=>$row[0], "callback_data"=>$textStation)));
		}
		$keyboard = array("inline_keyboard"=> $a);
		$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => $reply, 'reply_markup'=>json_encode($keyboard)];
		$telegram->sendMessage($content);
	}
	$station_schedule = false;
}
if($error == true){
	$content = ['chat_id' => $chat_id, 'parse_mode'=>'Markdown', 'text' => 'Silahkan Ketik **/menu** untuk menu yang tersedia atau Ketik **/help** untuk bantuan'];
	$telegram->sendMessage($content);
}

$connection->close();
