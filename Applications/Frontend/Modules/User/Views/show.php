<p>Par <em><?php echo $news->auteur; ?></em>, <?php echo $news->created_at; ?></p>
<h2><?php echo $news->titre; ?></h2>
<p><?php echo $news->contenu; ?></p>

<?php if ($news->dateajout != $news->updated_at) { ?>
    <p style="text-align: right;"><small><em>Modifiée <?php echo $news->updated_at; ?></em></small></p>
<?php } ?>

<p><a href="commenter-<?php echo $news->id; ?>.html">Ajouter un commentaire</a></p>

<?php
    if (empty($comments))
    {
?>
<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php
    }
    
    foreach ($comments as $comment)
    {
?>
<fieldset>
    <legend>
        Posté par <strong><?php echo htmlspecialchars($comment->auteur); ?></strong> <?php echo $comment->created_at; ?>
        <?php if ($user->isAuthenticated()) { ?> -
            <a href="admin/comment-update-<?php echo $comment->id; ?>.html">Modifier</a> |
            <a href="admin/comment-delete-<?php echo $comment->id; ?>.html">Supprimer</a>
        <?php } ?>
    </legend>
    <p><?php echo nl2br(htmlspecialchars($comment->contenu)); ?></p>
</fieldset>
<?php
    }
?>

<p><a href="commenter-<?php echo $news->id; ?>.html">Ajouter un commentaire</a></p> 
