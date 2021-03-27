<?php 
    date_default_timezone_set('Asia/Tashkent');
    define('API_KEY', 'your token');

    $admin = "your id";
    $company = "your company name and web site";
    // bot function
    function bot ($method, $datas = []) {
        $url = "https://api.telegram.org/bot".API_KEY."/".$method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $res = curl_exec($ch);
        if (!curl_error($ch)) return json_decode($res);
    }
    // html function
    function html($text) {
        return str_replace(['<','>'],['&#60;','&#62;'], $text);
    }

    $update = json_decode(file_get_contents('php://input'));

    // log file 
    file_put_contents("log.txt", file_get_contents('php://input'));

    // variables
    $message = $update->message;
    $text = html($message->text);
    $chat_id = $message->chat->id;
    $from_id = $message->from->id;
    $message_id = $message->message_id;
    $first_name = $message->from->first_name;
    $last_name = $message->from->last_name;
    $full_name = html($first_name." ".$last_name);

    //replymessage
    $reply_to_message = $message->reply_to_message;
    $reply_chat_id = $message->reply_to_message->forward_from->id;
    $reply_text = $message->text;

    // Klient 
    if ($chat_id != $admin) {
        if ($text == "/start") {
            $reply = "Hi <b>".$full_name."</b>, Welcome to ".$company."'s official bot. Send your questions 👇";

            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $reply,
                'parse_mode' => "HTML",
            ]);

            // sendMessage Admin
            $reply = "New Customer:\n".$full_name."\n👉 👉 <a href='tg://user?id=".$from_id."'>".$from_id. "</a>\n".date('Y-m-d H:i:s')."";

            bot ('sendMessage', [
                'chat_id' => $admin,
                'text' => $reply,
                'parse_mode' => "HTML",
            ]);

            bot ('forwardMessage', [
                'chat_id' => $admin,
                'from_chat_id' => $chat_id,
                'message_id' => $message_id,
            ]);
        } else if ($text != "/start") {
            bot('forwardMessage', [
                'chat_id' => $admin,
                'from_chat_id' => $chat_id,
                'message_id' => $message_id,
            ]);
        } 
    }  else if ($chat_id == $admin) {
        if (isset($reply_to_message)) {
            bot ('sendMessage', [
                'chat_id' => $reply_chat_id,
                'text' => $reply_text,
                'parse_mode' => "HTML",
            ]);
        }

        if ($text == "hi" or $text == "/start") {
            bot('sendMessage', [
                'chat_id' => $admin,
                'text' => "Hi Admin",
            ]);
        }
    }
?>
