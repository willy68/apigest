<?php
	namespace Library\Utils;
	
	class Upload_dir
	{
		//fonction pour créer les dossier d'upload des photos
		//$dir = dossier principal
		//$dir_thumb = dossier des miniatures
		//return: boolean
		public static function creer_dir_upload( $dir, $dir_thumb, $recursive=false )
		{
			//si le dossier n'existe pas, on le créé
			if (!file_exists($dir)) {
				//on créé automatiquement un dossier d'upload d'image 
				$old = umask(0); 
				if( !mkdir ("$dir", 0777, $recursive)){
					umask($old);
					return false;
				}
				umask($old);
			}

			if (!file_exists($dir_thumb)) {
				//on créé automatiquement un dossier d'upload d'image 
				$old = umask(0); 
				if(!mkdir ("$dir_thumb", 0777, $recursive)){
					umask($old);
					return false;
				}
				umask($old);
			}
			return true;
		}


		//fonction pour supprimer un dossier et son contenu
		public static function advRmDir( $dir )
		{
 			// ajout du slash a la fin du chemin s'il n'y est pas
			if( !preg_match( "/^.*\/$/", $dir ) ) $dir .= '/';
 
			// Ouverture du répertoire demande
 			$handle = @opendir( $dir );
 
 			// si pas d'erreur d'ouverture du dossier on lance le scan
 			if( $handle != false )
 			{ 
  				// Parcours du répertoire
  				while( $item = readdir($handle) )
  				{
   					if($item != "." && $item != "..")
   					{
    					if( is_dir( $dir.$item ) )
     						self::advRmDir( $dir.$item );
    					else unlink( $dir.$item );
   					}
  				}
 
  				// Fermeture du répertoire
  				closedir($handle);
 
  				// suppression du répertoire
  				$res = rmdir( $dir );
  				//pour les utilisateur de free.fr, on renomme le répertoire
  				if(file_exists($dir))
  					rename($dir,"../supprime-moi"); 
 			}
 			else $res = false;
 
 			return $res;
 
		}
		
		public static function getBaseUploadUrl($id_eleveur)
		{
			return 'userfiles/eleveur/'.$id_eleveur.'/userfiles/';
		}

	}
