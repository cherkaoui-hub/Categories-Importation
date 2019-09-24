<?php

// Connect to WooCommerce API
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://eyefay.com', 
    'ck_ab12490bf26934737366069b3ee73d17199ff2b1', 
    'cs_fd71e5ae72bcfdb52b098d84f6b2067481733ecc',
    [
        'version' => 'wc/v3',
    ]
);


//Read the file
$filename = 'csv_marque_Modele_290318.csv';

// The nested array to hold all the file content
$marque_modeles = []; 

// Open the file for reading
if (($h = fopen("{$filename}", "r")) !== FALSE) 
{
  // Each line in the file is converted into an individual array that we call $data
  // The items of the array are comma separated
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
    // Each individual array is being pushed into the nested array
    $marque_modeles[] = $data;		
  }

  // Close the file
  fclose($h);
}

//Functions
function getCategorieByName($name,$categories) {
    foreach ($categories as $categorie) {
        if($categorie->name==$name){
            return $categorie;
        }
    }
    return NULL;
}

function getCategorieByID($id,$categories) {
    foreach ($categories as $categorie) {
        if($categorie->id==$id){
            return $categorie;
        }
    }
    return NULL;
}

//get all the categories
$categories = $woocommerce->get('products/categories');

try {
    
$mainCategorie = 'Marques auto';
if(!getCategorieByName($mainCategorie,$categories)){
    //push the category in woocommerce categories
    $c= $woocommerce->post('products/categories', ['name' => $mainCategorie]);  
    array_push($categories,$c);
}

//Get the main categorie object
$mainCategorieObj = getCategorieByName($mainCategorie,$categories);


foreach(array_slice($marque_modeles, 1) as $marque_modele) {
    
    $marque = $marque_modele[0];
    $modele = $marque_modele[1];
    
    //Get the Marque object
    $getMarque = getCategorieByName($marque,$categories);
    if(!$getMarque){
        $marqueObj= $woocommerce->post('products/categories', ['name' => $marque,'parent' => $mainCategorieObj->id]);
        array_push($categories,$marqueObj);
     }
    
    $getMarque = getCategorieByName($marque,$categories); 
    //Get the Modele object
    $getModele = getCategorieByName($modele,$categories);
    if(!$getModele && $getMarque){
        $modeleObj= $woocommerce->post('products/categories', ['name' => $modele,'parent' => $getMarque->id]);
        array_push($categories,$modeleObj);
     }
}

    
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}



/*

*/


