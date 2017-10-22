<?php

function netejaCsv($csvFile)
    {
    $data2= array();
    $data= array();
    $data=file($csvFile);
    $linies = count($data);
    $j=0;
    for ($i=0;$i<$linies;$i++)
        {
        if (($i>0) && (strlen($data[$i]) > 2)) // Poso 2 perquè el csv posa algun caràcter especial que no es veu
            {
            $data2[$j] = $data[$i];
            //$data2[$j] = substr($data2[$j],1);
            //$data2[$j] = str_replace("\",\"",";",$data2[$j]);
            //echo "<br>>>".$data2[$j];
            $j++;
            }
        }
    return $data2;
    }

function neteja_apostrofs(&$cadena)

	{
	$elements = array("`", "'");
	$cadena = str_replace($elements, " ", $cadena);
	return $cadena;
	}
    
function normaliza ($cadena){
    $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    $cadena = strtolower($cadena);
    return utf8_encode($cadena);
}



?>


