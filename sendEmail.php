<?php


$rdir = str_replace("\\", "/", __DIR__);                    //Root Dir
require $rdir.'/src/Exception.php';
require $rdir.'/src/PHPMailer.php';
require $rdir.'/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$conn_string = "host=127.0.0.1 port=5432 dbname=onfro_dev2 user=onfro_v2_u password=MSKAPVNGYG options='--client_encoding=UTF8'";
$smtp =  "smtp.gmail.com";
$email = "ventasTransten@gmail.com";
$password = "gvvagdsiswdbtrys";

view_travel();

function view_travel(){
    $dbconn =null;
    try
    {
    // detalles de la conexion
    global $conn_string;
    $idViaje =0;
    // establecemos una conexion con el servidor postgresSQL
    $dbconn = pg_connect($conn_string);
    // Revisamos el estado de la conexion en caso de errores. 
    if(!$dbconn) {
    echo "Error: No se ha podido conectar a la base de datos\n";
    } else {
    $query = "select id from travel_routes where notification is null";
    $part = pg_query($dbconn, $query);
    while ($row = pg_fetch_array($part)) {
    update_travel($row["id"]);
    $idViaje = $row["id"];
    }
    }
    
    // Close connection

    }catch(Exception $e){
    echo 'Mensaje'.$e;
    }
    //pg_close($dbconn);
    if($idViaje != 0){
        view_all_carrier( $idViaje);
        echo 'Ya fue enviada su cotización'."\n";
    }else{
        echo 'No hay notificaciones por enviar'."\n";
    }
   
}
function update_travel($id){
    $dbconn =null;
    try
    {
        global $conn_string;
        // establecemos una conexion con el servidor postgresSQL
        $dbconn = pg_connect($conn_string);
        // Revisamos el estado de la conexion en caso de errores. 
        if(!$dbconn) {
        echo "Error: No se ha podido conectar a la base de datos\n";
        } else {
        $query = "update travel_routes set notification='Enviada' where id=".$id;
        pg_query($dbconn, $query);
        }      
        // Close connection

        }catch(Exception $e){
        echo 'Mensaje'.$e ."\n";
        }  
        pg_close($dbconn);

}
function view_all_carrier($idViaje){
    $dbconn =null;
    try
    {
    global $conn_string;
    // establecemos una conexion con el servidor postgresSQL
    $dbconn = pg_connect($conn_string);
    // Revisamos el estado de la conexion en caso de errores. 
    if(!$dbconn) {
    echo "Error: No se ha podido conectar a la base de datos\n";
    } else {
    $query = "select t2.email from role_users as t1
    inner join users as t2
    on t1.user_id = t2.id
    where t1.rol_id='3' and t2.deleted_at is null";
    $part = pg_query($dbconn, $query);
    while ($row = pg_fetch_array($part)) {
    send($row["email"],$idViaje);
    }
    }
    
    // Close connection
   
    }catch(Exception $e){
    echo 'Mensaje'.$e ."\n";
    }
    pg_close($dbconn);
    }   

 function Send($emailCarrier,$idViaje){
try{
    global $smtp,$email,$password;
    $mail = new PHPMailer(); // create a new object
    $mail->IsSMTP(); // enable SMTP
    //$mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = $smtp;
    $mail->Port = 465; // or 587
    $mail->Username = $email; // GMAIL username
    $mail->Password = $password; // GMAIL password
    $mail->SetFrom($emailCarrier);
    $mail->Subject = "Cotiza ahora y crece tu negocio ¡Última oportunidad!";
    $mail->AddEmbeddedImage('4.png', 'myImageID', '4.png', 'base64', 'image/png');
    $mail->IsHTML(true);
    $mail->Body = "<html><body><table width='100%'><tr><td style='font-style:arial;font-size:15px;text-align:center;'><p style='line-height:200%;'><b>¡Hola Transportista!</b></p><p style='line-height: 30 %;'><b>Viaje #".$idViaje."</b></p><p style='line-height: 200 %;'><b>El viaje comienza aquí..</b></p><a href='https://app.transten.com.mx/login'><img src=cid:myImageID ></a></td></tr></table></body></html>";
    $mail->CharSet = 'UTF-8';
    $mail->AddAddress($emailCarrier);
   if(!$mail->Send()) {
   echo "Mailer Error: " . $mail->ErrorInfo;
} else {
   echo "Message has been sent\n";
}
}catch(Exception $e){
    echo "Mailer Error: " . $e . "\n";
}
}
?>