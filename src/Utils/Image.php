<?php
	namespace Library\Utils;
	
	class Image
	{

		// $Hauteur_Miniatures provient par exemple d'un fichier de configuration
		// où est stocké la hauteur de la miniature (en px).
		// ex.: $Hauteur_Miniatures = 150;

		public static function creer_miniature($img_big, $dirSrc, $dirDest, $Hauteur_Miniatures) 
		{
		$tnH = $Hauteur_Miniatures;
		$t_rename = 0;
		$th_quality = 1;
		$img_big = $dirSrc . "/" . $img_big;
		$size = @getimagesize($img_big);
		switch($size[2]){
			case 1:
				if (imagetypes() & IMG_GIF)
					$src = imagecreatefromgif($img_big); 
				break;
			case 2:
				if (imagetypes() & IMG_JPG)
					$src = imagecreatefromjpeg($img_big); 
				break;
			case 3:
				if (imagetypes() & IMG_PNG)
					$src = imagecreatefrompng($img_big);  
				break;
			default :
				if (preg_match("/\.wbmp$/",$img_big) && (imagetypes() & IMG_WBMP)){
					$src = imagecreatefromwbmp($img_big);
					$size[0] = imagesx($src);
					$size[1] = imagesy($src);
					if (!isset($format))
						$format = 4;
				}
		}
	
		if (!$src){
			$thumbs[$img_big] = "Format NON SUPPORTE !";
		}
		else
		{
			$destW = $size[0]*$tnH/$size[1];
			$destH = $tnH;
			if ($th_quality == 1){
				$dest = imagecreatetruecolor($destW,$destH);                     
				imagecopyresampled($dest,$src,0,0,0,0,$destW,$destH,$size[0],$size[1]);      
			} 
			else 
			{
				$dest = imagecreatetruecolor($destW,$destH);
				imagecopyresized($dest,$src,0,0,0,0,$destW,$destH,$size[0],$size[1]);
			}
			$tn_name = $img_big;
		
			// ICI VOUS POUVEZ DEFINIR DE RENOMMER LE FICHIER
			// Exemple: "_m" renommera : fichier.jpg en fichier_m.jpg
		
			$tn_name = preg_replace("/\.(gif|jpe|jpg|jpeg|png|wbmp|bmp)$/i","_m",$tn_name);
			$tn_name = preg_replace("/.*\/([^\/]+)$/i","$dirDest\\1",$tn_name);
			if (isset($format))
				$type = $format;
			else
				$type = $size[2];
			switch($type){
				case 1 :
					if (imagetypes() & IMG_GIF){
						imagegif($dest,$tn_name.".gif");
						$thumbs[$img_big] = "$tn_name.gif";
					}
					break;
				case 2:
					if (imagetypes() & IMG_JPG){
						imagejpeg($dest,$tn_name.".jpg");
						$thumbs[$img_big] = "$tn_name.jpg";
					}
					break;
				case 3:
					if (imagetypes() & IMG_PNG){
						imagepng($dest,$tn_name.".png");
						$thumbs[$img_big] = "$tn_name.png";
					}
					break;
				default:
					if (imagetypes() & IMG_WBMP){
						imagewbmp($dest,$tn_name.".wbmp");
						$thumbs[$img_big] = "$tn_name.wbmp";
					}
			}
			
			if (!($thumbs[$img_big])){
				$thumbs[$img_big] = "Format NON SUPPORTE !";
			}
			// FIN CREATION
		 }
	}

		// $Hauteur_Miniatures provient par exemple d'un fichier de configuration
		// où est stocké la hauteur de la miniature (en px).
		// ex.: $Hauteur_Miniatures = 150;
		// $img_big provient d'un formulaire $_FILES['FILE_PHOTO'], le nom du champ doit etre renseigner
		// valeur de retour
		// $file_photo['URL_PHOTO'] = $url_upload . $dest_fichier;
		// $file_photo['URL_PHOTO_THUMB'] = $url_upload.'thumb/'.$Fichier_mini;
		// $file_photo['ERROR'] = $erreur; si erreur  == 1 probleme!


		public static function upload_photo($img_big, $dossier_upload, $url_upload, $Hauteur_Miniatures) 
		{
			$file_photo = array();
			$erreur = 0;
			//Extension autorisé
			$extensions_ok = array('png', 'gif', 'jpg', 'jpe', 'jpeg', 'JPG', 'bmp', 'wbmp');
			//Chemin du dossier image
			// ajout du slash a la fin du chemin s'il n'y est pas
			if( !preg_match( "/^.*\/$/", $dossier_upload ) ) $dossier_upload .= '/';
			// ajout du slash a la fin du chemin s'il n'y est pas
			if( !preg_match( "/^.*\/$/", $url_upload ) ) $url_upload .= '/';
			// vérifications
			if( !in_array( substr(strrchr($img_big['name'], '.'), 1), $extensions_ok ) )
			{
				$erreur=1;
			}
			// copie du fichier
			if($erreur == 0)
			{
				$dest_fichier = basename($img_big['name']);
				// formatage nom fichier
				$dest_fichier = strtr($dest_fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				// remplacer les caracteres autres que lettres, chiffres et point par _
				$dest_fichier = preg_replace('/([^.a-z0-9]+)/i', '_', $dest_fichier);
				// copie du fichier
				move_uploaded_file($img_big['tmp_name'], $dossier_upload . $dest_fichier);
				chmod($dossier_upload . $dest_fichier, 0777);

				// Création de la miniature a la volée
				//REDIMENSIONNEMENT DES AFFICHES SI LES MINIATURES N'EXISTENT PAS
				$Fichier_mini = preg_replace("/\.(gif|jpe|jpg|jpeg|png|bmp|wbmp)$/i","_m",$dest_fichier).'.'.strtolower(substr(strrchr($dest_fichier, "."), 1));
				if(!file_exists($dossier_upload.'thumb/'.$Fichier_mini)){ 
					self::creer_miniature($dest_fichier, $dossier_upload, $dossier_upload.'thumb/', $Hauteur_Miniatures);
					chmod($dossier_upload.'thumb/'.$Fichier_mini, 0777);
				}
				$file_photo['URL_PHOTO'] = $url_upload. $dest_fichier;
				$file_photo['URL_PHOTO_THUMB'] = $url_upload.'thumb/'.$Fichier_mini;
				$file_photo['ERROR'] = $erreur;
			}
			else
			{
				$file_photo['URL_PHOTO'] = '';
				$file_photo['URL_PHOTO_THUMB'] = '';
				$file_photo['ERROR'] = $erreur;
			}
			return $file_photo;
		}
			
        public static function supprimerPhotos( $urlPhoto, $urlPhotoThumb, $dir)
        {
			$array_photo = explode( "/", $urlPhoto );
			$array_thumb = explode( "/", $urlPhotoThumb );
			$photo = $array_photo[sizeof($array_photo)-1];
			$thumb = $array_thumb[sizeof($array_thumb)-1];
					
			if(strtolower(substr(strrchr($photo, "."), 1)) == "gif" || strtolower(substr(strrchr($photo, "."), 1)) == "jpg" 
			|| strtolower(substr(strrchr($photo, "."), 1)) == "jpe" || strtolower(substr(strrchr($photo, "."), 1)) == "jpeg" 
			|| strtolower(substr(strrchr($photo, "."), 1)) == "png" || strtolower(substr(strrchr($photo, "."), 1)) == "bmp"
			|| strtolower(substr(strrchr($photo, "."), 1)) == "wbmp")
			{
				$nom = $dir.'/'.$photo;
   				@unlink($nom);
				// On efface la miniature
	  			$nom_mini = $dir.'/thumb/'.$thumb;
	  			@unlink($nom_mini);
	  			return true;        
        	}
        	return false;
        }	
	}

