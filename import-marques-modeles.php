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


try {
    
//Creat the main category
$main_category = 'Marques auto';
//Search for the Main category
$main_cat_obj = $woocommerce->get('products/categories',['search' => $main_category]);
if(empty($main_cat_obj)){
    $main_cat_obj = array($woocommerce->post('products/categories', ['name' => $main_category]));  
}


//loop the file lines in Csv
foreach(array_slice($marque_modeles, 1) as $marque_modele) {

    $marque = $marque_modele[0];
    $modele = $marque_modele[1];
    
    //Search for the Marque object
    $get_marque = $woocommerce->get('products/categories',['search' => $marque]);
    if(empty($get_marque)){
        $get_marque = array($woocommerce->post('products/categories', ['name' => $marque,'parent' => $main_cat_obj[0]->id]));
     }
    
    //Search for the Modele object
    $get_modele = $woocommerce->get('products/categories',['search' => $modele]);
    if(empty($getModele) && !empty($get_marque)){
        $woocommerce->post('products/categories', ['name' => $modele,'parent' => $get_marque[0]->id]);
     }
}


} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}


/*
//How to delete categories 
$cs = $woocommerce->get('products/categories',['per_page' => 200]);
foreach($cs as $c) {
    print_r($woocommerce->delete('products/categories/'.$c->id, ['force' => true]));
}
*/


