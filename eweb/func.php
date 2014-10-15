<?php 
  function goNewVersion($html)
  {
      $html = str_replace("#dadada", "#fafafa", $html);
      $html = str_replace("14pt", "19px", $html);
      $html = str_replace("13pt", "18px", $html);
      $html = str_replace("12pt", "16px", $html);
      return $html;
  }
	function addSlash($str)
    {
       $str = str_replace("'",'\"',$str);
       return $str;
    }

    function stripSlash($str)
    {
       $str = str_replace('\"',"'",$str);
       return $str;
    }
    function fupper($text)
    {
         $t = explode(" ",$text);
         $cad = "";
         foreach($t as $v)
         {
           $tam = strlen($v);
           $t1 = substr($v,0,1);
           $t2 = substr($v,1,$tam);
           $cad .= strtoupper($t1).$t2." ";
         }
	return $cad;
    }
?>