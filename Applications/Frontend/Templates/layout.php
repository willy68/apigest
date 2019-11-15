<?php echo $this->helpers()->doctype()->getDoctype()->render();?>
<html lang="fr">
    <head>
        <title>
            <?php if (!isset($title)) { ?>
                Mon super site en MVC
            <?php } else { echo $title; } ?>
        </title>
        
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        
        <link rel="stylesheet" href="/css/Envision.css" type="text/css" />
        
        <?php if (isset($headScript)) echo $headScript->render();?>
    </head>
    
    <body>
        <div id="wrap">
            <div id="header">
                <h1 id="logo-text"><a href="/">Mon super site en MVC</a></h1>
                <p id="slogan">Comment ça il n'y a presque rien?</p>
            </div>
            
            <div  id="menu">
                <ul>
                    <li><a href="/">Accueil</a></li>
                    <li><a href="/formTest.html">Test de Formulaire</a></li>
                    <li><a href="/bootstrap.html">Test de bootstrap</a></li>
                    <li><a href="/admin/">Admin</a></li>
                    <?php if ($user->isAuthenticated()) { ?>
                    <li><a href="/admin/news-insert.html">Ajouter une news</a></li>
                    <?php } ?>
                </ul>
            </div>
            
            <div id="content-wrap">
                <div id="main">
                    <?php if ($user->hasFlash()) echo '<p style="text-align: center;">', $user->getFlash(), '</p>'; ?>
                    
                    <?php echo $content; ?>
                </div>
            </div>
        
            <div id="footer"></div>
        </div>
    </body>
</html> 
