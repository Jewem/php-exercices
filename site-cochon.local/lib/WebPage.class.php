<?php

/*\
--------------------------------------------
    WebPage.class.php
--------------------------------------------
    Cette classe est destinée à afficher la 
    page Web. Si une méthode doit renvoyer 
    du HTML, elle se trouvera sûrement ici.

    Patron de conception : singleton.

    Pour instancier la WebPage : 
    WebPage::createInstance();
--------------------------------------------
\*/

// On utilise le typage strict
declare(strict_types=1);

// On inclu les constantes nécessaires au fonctionnement de la classe
include_once './params.inc.php';

class WebPage
{

    /*\
     | -------------------------------------
     | Attributs
     | -------------------------------------
    \*/

    
    private static $webPageInstance = null; // WebPage
    private static $actualPageCode;         // int
    private static $actualPageDatas;        // array
    private $HTMLStyles = '';               // string
    private $pageContent = '';              // string
    private $alertMessages = '';            // string

    
    /*\
     | -------------------------------------
     | Méthodes
     | -------------------------------------
    \*/

    // __construct()

    // En private car singleton
    private function __construct()
    {
        $this->fetchPageContent();
    }

    /*------------------------------------*/

    // createInstance()

    // Permets d'instancier la page
    public static function createInstance(int $actualPageCode, array $actualPageDatas) : WebPage
    {
        // La classe aura besoin de savoir sur quelle page on est
        self::$actualPageCode = $actualPageCode;

        // Et éventuellement quelques infos venant du traitement de formulaires
        self::$actualPageDatas = $actualPageDatas;

        // Si Il n'existe pas déjà de connexion
        if(!self::$webPageInstance)                 
        {
            // On instancie par la méthode __construct
            self::$webPageInstance = new WebPage;   
        }
        return self::$webPageInstance; 
    }

    /*------------------------------------*/
    
    // getHTMLStyles()
    
    // Permets de retourner les balises styles rentrées en paramètres
    public function getHTMLStyles() : string
    {
        foreach (SITE_STYLES as $this->style)   
        {
            $this->HTMLStyles .= $this->style;
        }
        return $this->HTMLStyles;
    }

    /*------------------------------------*/
    
    
    // setPageContent()
    
    // Remplace tout le contenu principal de la page, à utiliser pour le debug
    public function setPageContent($content)
    {
        $this->pageContent = $content;
    }

    /*------------------------------------*/
    
    
    // fetchPageContent()
    
    // Construit le contenu de la page
    public function fetchPageContent()
    {
        // On récupère le code de la page actuelle
        switch (self::$actualPageCode) {
            case 1:
                // Page principale
                $this->pageContent = 'Page Principale';
                break;
            
            case 3:
                // Page login
                $this->pageContent = '  <form method="post">
                                            <fieldset>
                                            
                                                <!-- Form Name -->
                                                <legend>Se connecter ou créer un compte</legend>
                                                
                                                <!-- Text input-->
                                                <div>
                                                    <label for="login-email">Votre email</label>  
                                                    <div>
                                                        <input name="login-email" type="email" placeholder="" required="">
                                                    </div>
                                                </div>
                                                
                                                <!-- Password input-->
                                                <div>
                                                    <label control-label" for="login-pass">Mot de passe</label>
                                                    <div>
                                                        <input name="login-pass" type="password" placeholder="" required="">
                                                    </div>
                                                </div>
                                                
                                                <!-- Button -->
                                                <div>
                                                    <div>
                                                        <input type="submit" value="Valider">
                                                    </div>
                                                </div>

                                            </fieldset>
                                        </form>';
                break;
            
            case 4:
                // Page de création de compte
                $this->pageContent = '  <form method="post">
                                            <fieldset>
                                            
                                                <!-- Form Name -->
                                                <legend>Créer un compte</legend>
                                                
                                                <!-- Text input-->
                                                <div>
                                                    <label for="signup-email">E-mail</label>  
                                                    <div>
                                                        <input name="signup-email" type="email" placeholder="" required="" value="' . self::$actualPageDatas["login-mail"] . '">
                                                    </div>
                                                </div>
                                                
                                                <!-- Text input-->
                                                <div>
                                                    <label for="signup-name">Nom d\'utilisateur</label>  
                                                    <div>
                                                        <input name="signup-name" type="text" placeholder="" required="" value="' . (isset($_SESSION["signup-name"]) ? $_SESSION["signup-name"] : "" ) . '">
                                                    </div>
                                                </div>
                                                
                                                <!-- Password input-->
                                                <div>
                                                    <label for="signup-pass">Mot de passe</label>
                                                    <div>
                                                        <input name="signup-pass" type="password" placeholder="" required="" value="' . self::$actualPageDatas["login-pass"] . '">
                                                    </div>
                                                </div>
                                                
                                                <!-- Password input-->
                                                <div>
                                                    <label for="signup-pass-confirm">Confirmez votre mot de passe</label>
                                                    <div>
                                                        <input name="signup-pass-confirm" type="password" placeholder="" required="">
                                                    </div>
                                                </div>
                                                
                                                <!-- Button -->
                                                <div>
                                                    <div>
                                                        <input type="submit" value="Créer un compte">
                                                    </div>
                                                </div>
                                            
                                            </fieldset>
                                        </form>
                                        <a href="?page=login">< Page de login</a>';
                break;
            
            default:
                // Page 404
                $this->pageContent = 'Page 404';
        }
    }

    /*------------------------------------*/
    
    
    // getPageContent()
    
    // Renvois le contenu de la page
    public function getPageContent() : string
    {
        return $this->pageContent;
    }

    /*------------------------------------*/
    
    
    // addAlertMessage()
    
    // Met en place les messages d'alertes
    public function addAlertMessage($message)
    {
        $this->alertMessages .= $message;
    }

    /*------------------------------------*/
    
    // displayPage()
    
    // Permets de retourner les balises styles rentrées en paramètres
    public function displayPage() : string
    {

        // Mise en forme des messages d'alert s'il y en a
        $HTMLAlertMessages = ""; // string
        if ($this->alertMessages !== "") {
            $HTMLAlertMessages = '<pre>' . $this->alertMessages . '</pre>';
        }

        // On affiche la page
        return '    <!DOCTYPE html>
                    <html lang="' . SITE_LANG . '">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        ' . $this->getHTMLStyles() . '
                        <title>' . SITE_TITLE . '</title>
                    </head>
                    <body>
                        <h1>' . SITE_TITLE . '</h1>
                        ' . $HTMLAlertMessages . '
                        ' . $this->pageContent . '
                    </body>
                    </html>';
    }

    /*------------------------------------*/
    
    // __destruct()

    public function __destruct()
    {
        echo $this->displayPage();
    }
}