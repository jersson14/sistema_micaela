<?php

$dni = $_POST['dni'];
$token = 'sk_6484.RRP3U7oiRSgcS8VJ2m9sTCKcqaFeVH7e'; // Tu token real

if(strlen($dni) != 8){
    echo json_encode(1); // Validación de 8 dígitos
    exit;
}
// Buscar ruc sunat
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.decolecta.com/v1/sunat/ruc?numero=' . $ruc,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Referer: http://apis.net.pe/api-ruc',
    'Authorization: Bearer ' . $token
  ),
));

$response = curl_exec($curl);

if(curl_errno($curl)){
    echo json_encode(['error' => curl_error($curl)]);
    exit;
}

curl_close($curl);

// Mostrar la respuesta como JSON
echo $response;
