<?php 
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : J�r�me VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/
  session_start();
  include './requete.php';

  if($_GET["id"]!="")
  {
    $verif=exec_requete("select * from citoyen where md5(concat(idcitoyen,nom))='".$_GET["id"]."'");
    if(mysql_num_rows($verif)==1)
    {
      $_SESSION["citoyen"]=mysql_fetch_array($verif);
    }
    else
      echo("<center><b>Nom d'utilisateur ou mot de passe incorrect</b></center>");
  }


  if($_SESSION["citoyen"]["idcitoyen"]=="")
  {
    die("Session perdue. <a href=\"index.php\">Merci de cliquer ici</a>");
  }
 ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Monnaie M - Exp�rimentation d'une monnaie compl�mentaire assortie d'un revenu de base</title>
  <link rel="stylesheet" href="monnaiem.css" typeproduit="text/css">
  <meta name="description" content="Monnaie M est une exp�rimentation visant � faire conna�tre et promouvoir le fonctionnement et le r�le d'une monnaie, 
  les Syst�mes d'Echanges Locaux, le concept de revenu de base, les monnaies compl�mentaires.">
  <meta name="keywords" lang="fr" content="monnaie bitcoin openudc cr�ation mon�taire SEL revenu de base dividende universel">
  </head>
  <body>
<?php 

    echo("<div id=\"accueil\"><a href=\"index.php\"><img border=\"0\" src=\"images/bandeau.png\"></a><br><br>");

    if($_POST["cmail"]!="")
    {
      $citoyens=exec_requete("select mail,md5(concat(idcitoyen,nom)) as code from citoyen where idcitoyen='".$_POST["cmail"]."'");
      if(mysql_num_rows($citoyens)==1)
      {
          $citoyen1=mysql_fetch_array($citoyens);
          if(mail($citoyen1["mail"], "Message de la part de ".$_SESSION["citoyen"]["idcitoyen"]." depuis monnaie M",
                  "Ce message a �t� envoy� par ".$_SESSION["citoyen"]["idcitoyen"]." depuis le site Monnaie M. Merci de ne pas utiliser le bouton 'R�pondre' de votre messagerie, mais ce lien : http://merome.net/monnaiem/mail.php?id=".$citoyen1["code"]."&c=".urlencode($_SESSION["citoyen"]["idcitoyen"])." pour lui faire une r�ponse.\r\n\r\n".stripslashes($_POST["contenu"])."\r\n".
                  "\r\nPour r�pondre � ce message, cliquez ici : http://merome.net/monnaiem/mail.php?id=".$citoyen1["code"]."&c=".urlencode($_SESSION["citoyen"]["idcitoyen"])."\r\n",
                  "From: ".FROM."\r\n"
        					."Reply-To: ".FROM."\r\n"
        					."X-Mailer: PHP/" . phpversion()))
            echo("Message envoy�");
          else
             echo("Erreur lors de l'envoi du message");

      }
    }

    if($_GET["c"]!="")
    {
      $transactions=exec_requete("select * from citoyen,transaction,produit where vendeur=citoyen.idcitoyen and produit.idproduit=transaction.idproduit and statut='Termin�' and citoyen.idcitoyen='".$_GET["c"]."' order by datevente");
      if(mysql_num_rows($transactions)>0)
      {
        echo("<b>Les derni�res transactions de ".$_GET["c"]." :</b><br><br><table border=\"1\" align=\"center\"><tr><td>Date de la transaction</td><td>Cat�gorie de produit</td><td>Note</td><td>Commentaires de l'acheteur</td></tr>");
        while ($transaction=mysql_fetch_array($transactions))
        {
          echo("<tr><td>".$transaction["datevente"]."</td><td>".$transaction["categorie"]."</td><td>".$transaction["note"]."/5</td><td>".$transaction["commentaires"]."</td></tr>");
        }
        echo("</table><br><br>");
      }
      echo("<b>Envoyer un message � ".$_GET["c"]." :</b><br>");
      ?>
        <form method="post" action="mail.php"><input type="hidden" name="cmail" value="<?php  echo($_GET["c"]); ?>">
          <textarea name="contenu" rows="10" cols="80"></textarea><br>
          <input type="submit" value="Envoyer le message">
        </form>
      <?php 

    }
    echo("</div>");



  mysql_close();



?>
  </body>
</html>