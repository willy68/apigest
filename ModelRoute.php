<?php  
  return '  <route url="/entreprise/([0-9]+)/'.$model_name.'(\\?.+=.+)*" module="'.$model_name.'" action="list" vars="entreprise_id,params"/>
  <route url="/entreprise/([0-9]+)/'.$model_name.'/([0-9]+)" module="'.$model_name.'" action="by_id" vars="entreprise_id,id"/>
  <route url="/'.$model_name.'(\\?.+=.+)*" module="'.$model_name.'" action="list" vars="params"/>
  <route url="/'.$model_name.'/([0-9+])(\\?.+=.+)*" module="'.$model_name.'" action="by_id" vars="id,params"/>
';