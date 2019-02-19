<?php 
date_default_timezone_set('Europe/Madrid');
header('Content-Type: text/html; charset=UTF-8');

function send($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

$result = file_get_contents("php://input");
if ($result){
	include("settings.php");
	$message = json_decode($result, true);
	$chat_id = $message["message"]["chat"]["id"];
	if ($message["channel_post"]){
		$user = $message["channel_post"]["chat"]["id"];
	}else{
		$user = $message["message"]["from"]["id"];
	}
	if (in_array($user,WHITELIST)){
		if (!$message["channel_post"]){
			if (in_array($user,WHITELIST)){
				if (isset($message["message"]["animation"])){ //GIF
					$id_gif = $message["message"]["animation"]["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendAnimation?chat_id='.CHANNEL_ID.'&animation='.$id_gif.'&caption='.CAPTION);							
				}else if (isset($message["message"]["photo"])){ //PHOTO
					$photo = end($message["message"]["photo"]);
					$id_photo = $photo["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendPhoto?chat_id='.CHANNEL_ID.'&photo='.$id_photo.'&caption='.CAPTION);
				}else if (isset($message["message"]["sticker"])){ //STICKER
					$id_sticker = $message["message"]["sticker"]["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendSticker?chat_id='.CHANNEL_ID.'&sticker='.$id_sticker.'&caption='.CAPTION);
				}else if (isset($message["message"]["audio"])){ //AUDIO
					$id_audio = $message["message"]["audio"]["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendAudio?chat_id='.CHANNEL_ID.'&audio='.$id_audio.'&caption='.CAPTION);
				}else if (isset($message["message"]["video"])){ //VIDEO
					$id_video = $message["message"]["video"]["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendVideo?chat_id='.CHANNEL_ID.'&video='.$id_video.'&caption='.CAPTION);
				}else if (isset($message["message"]["voice"])){ //VOICE_NOTE
					$id_voice = $message["message"]["voice"]["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendVoice?chat_id='.CHANNEL_ID.'&voice='.$id_voice.'&caption='.CAPTION);
				}else if (isset($message["message"]["video_note"])){ //VIDEO_NOTE
					$id_videonote = $message["message"]["video_note"]["file_id"];
					send('https://api.telegram.org/bot'.BOT_ID.'/sendVideoNote?chat_id='.CHANNEL_ID.'&video_note='.$id_videonote.'&caption='.CAPTION);
				}else if (isset($message["message"]["text"])){ //TEXT
					$text = $message["message"]["text"]."\n\n@HumorGoRhY";
					send('https://api.telegram.org/bot'.BOT_ID.'/sendMessage?chat_id='.CHANNEL_ID.'&text='.urlencode($text).'&parse_mode=HTML');
				}else{
					send('https://api.telegram.org/bot'.BOT_ID.'/sendMessage?text='.urlencode("Ha habido un problema al procesar el mensaje: ".$result." - ".$chat_id).'&chat_id='.ADMIN_ID);
				}
			}else{
				send('https://api.telegram.org/bot'.BOT_ID.'/sendMessage?text='.urlencode("Intento de uso de bot en un grupo/canal no autorizado: ".$result." - ".$chat_id).'&chat_id='.ADMIN_ID);
				send('https://api.telegram.org/bot'.BOT_ID.'/sendMessage?text='.urlencode("Lo siento, este bot sólo sirve para ser usado por su creador").'&chat_id='.$chat_id);
			}
		}
	}else{
		if ($message["channel_post"]){
			$chat_id = $message["channel_post"]["chat"]["id"];
		}
		send('https://api.telegram.org/bot'.BOT_ID.'/sendMessage?text='.urlencode("Intento de uso de bot en un grupo/canal no autorizado: ".$result." - ".$chat_id).'&chat_id='.ADMIN_ID);
		send('https://api.telegram.org/bot'.BOT_ID.'/sendMessage?text='.urlencode("Lo siento, este bot sólo sirve para ser usado por su creador").'&chat_id='.$chat_id);
		send('https://api.telegram.org/bot'.BOT_ID.'/leaveChat?chat_id='.$chat_id);
	}
}
echo "fin ejecución";
?>