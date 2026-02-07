<?php
$wa = urldecode($_GET['wa'] ?? '');
$sent = isset($_GET['sent']) && $_GET['sent'] === '1';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Message envoyé</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;background:#f6f9ff;color:#10243a;padding:24px} .box{max-width:720px;margin:40px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 8px 24px rgba(3,32,80,0.06)} a.btn{display:inline-block;background:#0b66b2;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none}</style>
</head>
<body>
  <div class="box">
    <h2><?php echo $sent ? 'Merci — message envoyé' : 'Message envoyé (email non confirmé)'; ?></h2>
    <?php if($sent): ?>
      <p>Votre message a été transmis par email à <strong>projet1325@gmail.com</strong>.</p>
    <?php else: ?>
      <p>Votre message a été préparé mais l'envoi par email a échoué. Le message est prêt à être envoyé via WhatsApp :</p>
    <?php endif; ?>
    <p>Vous pouvez également envoyer ce message via WhatsApp en cliquant sur le bouton ci-dessous si vous utilisez un appareil avec WhatsApp :</p>
    <p><a class="btn" id="waBtn" href="#" target="_blank">Envoyer via WhatsApp</a></p>
    <p>Si la fenêtre WhatsApp ne s'est pas ouverte automatiquement, cliquez sur le bouton ci-dessus.</p>
    <p><a href="../pagesweb/contact.php">Retour à la page Contact</a></p>
  </div>
<script>
(function(){
  var wa = decodeURIComponent("<?php echo rawurlencode($wa); ?>");
  var btn = document.getElementById('waBtn');
  if(wa && btn){
    btn.href = wa;
    // try to open automatically
    try{ window.open(wa,'_blank'); }catch(e){}
  }
})();
</script>
</body>
</html>
