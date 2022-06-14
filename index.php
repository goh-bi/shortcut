<!-- is sending a form -->
 <!-- verification si url valide | shortcut | has been send? -->
<?php

$bdd = new PDO('mysql:host=localhost;dbname=bitly', 'root', '');
if(isset($_GET['q'])){
    $shortcut = htmlspecialchars($_GET['q']);
    
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));
    // var_dump($req->fetch());
    // die();
    while($result = $req->fetch()) {
        if($result['x'] != 1){
            header('location: index.php?error=true&message=Adresse url non connue');
            exit();
    }
}
    $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

while($result = $req->fetch()) {
    header('location: '.$result['url']);
    exit();
    }
}


if(isset($_POST['url'])) {
    $url = $_POST['url'];
    if(!filter_var($url, FILTER_VALIDATE_URL)) {
        header('location: index.php?error=true&message=Adresse url non valide');
        exit();
    }
    $shortcut = crypt($url, rand());
    $req = $bdd->prepare('SELECT * FROM links WHERE url = ? OR shortcut = ?');
    $req->execute(array($url, $shortcut));
    while($result = $req->fetch()) {
        if($result["url"] == $url) {
            header('location: index.php?error=true&message=Adresse deja raccourci');
            exit();
        }elseif($result["shorcut"] == $shortcut) {
            $shortcut = crypt($url, rand());
        }
    }
    $req= $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
    $req->execute(array($url, $shortcut));
    header('location: index.php?q='.$shortcut);
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Raccourcisseur d'URL express</title>
        <link rel="stylesheet" type="text/css" href="design/default.css" />
        <link rel="icon" type="image/png" href="pictures/favico.png" />
    </head>
    <body> 
        
        <section id="hello">
           
            <div class="container">   
                <header >
                    <img src="pictures/logo.png" alt="logo" id="logo"/>
                </header>
                <h1>Une url longue ? raccourcissez-la</h1>
                <h2>Largement meilleur et plus court que les autres.</h2>
                <form method="post" action="index.php">
                    <input type="url" name="url" id="url" placeholder="coller un lien à raccourcir">
                    <input type="submit" value="raccourcir" />
                </form>
                <?php 
                    if(isset($_GET['error']) && isset($_GET['message'])) { ?>
                      <div class="center">
                          <div id="result">
                                <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                          </div>
                      </div>  
                <?php } else if(isset($_GET['short'])) { 
                    ?>
                    <div class="center">
                          <div id="result">
                                <b>URL RACCOURCIE : </b>
                                   http://localhost/q=<?php echo htmlspecialchars($_GET['short']); ?>
                          </div>
                      </div>

                <?php } ?>
            </div>
             
        </section>
        <section id="brands">
            <div class="container">
                <h3>Ces marques nous font confiances</h3>
                <div class="picture">
                    <img src="pictures/1.png" alt="1" class="pictures" />
                    <img src="pictures/2.png" alt="2" class="pictures" />
                    <img src="pictures/3.png" alt="3" class="pictures" />
                    <img src="pictures/4.png" alt="4" class="pictures" />

                </div>
            </div>
            
        </section>
        <footer>
            <img src="pictures/logo2.png" alt="logo2" id="logo">
            <p>2018 &copy; Bitly</p> <br>
            <a href="">Contact</a> - <a href="">A propos</a>
        </footer>
    </body>
</html>