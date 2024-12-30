<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('PHPMailer/PHPMailer/src/Exception.php');
require('PHPMailer/PHPMailer/src/PHPMailer.php');
require('PHPMailer/PHPMailer/src/SMTP.php');

include "../email_cred.php";


if(isset($_POST["email"])) {
    global $email_username;
    global $email_password;
    
    $body = "Hey " .$_POST['fisrt_name']."!

We're thrilled to have you on board as one of our early birds here at Tourstify! 🐦✨

First things first, give yourself a pat on the back for being ahead of the game and joining our community of fearless solo travelers. 🙌🗺️

We wanted to pop into your inbox to say hello and officially welcome you to the Tourstify family. 🥳

Here at Tourstify, we're all about turning your solo adventures into unforgettable quests, and we're excited to have you along for the ride! 🌟

You're about to embark on a journey like no other, where you'll connect with fellow explorers, experience cultures, and create memories that will last a lifetime. And guess what? Our AI travel buddy, Petra, is eagerly waiting to become your new best friend and travel companion. She's pretty awesome, if we do say so ourselves! 😄🤖

But hey, we don't just want to be another travel platform. We're building a community of like-minded adventurers who share the same passion for exploring the world and making new friends along the way. Get ready for exciting travel stories, tips, and, of course, some good-natured travel humor! 🌆🤣

Stay tuned for more updates, exclusive content, and a whole lot of fun. In the meantime, why not follow us on Instagram and Facebook to join the Tourstify tribe and be part of our pre-launch excitement? 📸👍

Thank you for choosing Tourstify. We can't wait to share this adventure with you! If you have any questions or just want to chat, feel free to hit us up anytime. We're here for you! 💌

Here's to new friendships, unforgettable journeys, and a world of adventure waiting for you. Buckle up; it's going to be one heck of a ride!

Adventure awaits,
Tourstify Team
Website Link: www.tourstify.com";
    
   $email = $_POST["email"];
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $email_username;
    $mail->Password = $email_password;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom($email_username); // Your gmail
    $mail->addAddress ($email);
    $mail->isHTML(true);
    $mail->Subject =  "Welcome to the Tourstify Adventure!";
    $mail->Body = nl2br($body);
    $success = $mail->send();



// redirect to success page
if ($success && $errorMSG == ""){
   echo "success";
}else{
    if($errorMSG == ""){
        echo "Something went wrong :(";
    } else {
        echo $errorMSG;
    }
}

}
?>