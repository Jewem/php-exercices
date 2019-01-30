<?php

/*\
--------------------------------------------
    DataBase.class.php
--------------------------------------------
    Cette classe est destinée à traiter 
    toutes les requêtes en base de donnée. 
    S'il y a du SQL, c'est ici que ça se 
    passe.

    Patron de conception : singleton.

    Pour instancier la DataBase : 
    DataBase::connect();

    Pour utiliser une méthode :
    DataBase::connect()->checkEmail('email');
--------------------------------------------
\*/

// On utilise le typage strict
declare(strict_types=1);

// On inclu les constantes nécessaires au fonctionnement de la classe
include_once './params.inc.php';

class DataBase
{

    /*\
     | -------------------------------------
     | Attributs
     | -------------------------------------
    \*/

    
    private static $dataBaseInstance = null;// DataBase
    private $connectionPDO;                 // Object PDO                    

    /*\
     | -------------------------------------
     | Méthodes
     | -------------------------------------
    \*/

    // __construct()

    // En private car singleton
    private function __construct()
    {
        try 
        {
            $this->connectionPDO = new PDO(DB_TYPE     . 
                                        ':host='    . DB_HOST . 
                                        ';dbname='  . DB_NAME . 
                                        ';charset=' . DB_CHAR, 
                                        DB_USER, 
                                        DB_PASS);
        }
        catch (PDOException $error)
        {
            // On envoie le message d'erreur dans la section alerte de la page Web
            WebPage::createInstance()->addAlertMessage(DB_CONNECTION_ERROR_MESSAGE . $error);
        }
    }

    /*------------------------------------*/

    // createInstance()

    // Permets d'instancier la page
    public static function connect() : DataBase
    {
        // Si Il n'existe pas déjà de connexion
        if(!self::$dataBaseInstance)                 
        {
            // On instancie par la méthode __construct
            self::$dataBaseInstance = new DataBase;   
        }
        return self::$dataBaseInstance; 
    }

    /*------------------------------------*/

    // checkEmail()

    // Retourne true si l'email existe en base, false sinon
    public function checkEmail(string $email) : int
    {
        try
        {
            $PDOStatement = $this->connectionPDO->prepare(' SELECT COUNT(1) 
                                                            FROM ' . DB_USER_TABLE . ' 
                                                            WHERE ' . DB_USER_EMAIL_FIELD . ' 
                                                            LIKE :email');
        }
        catch (PDOException $error)
        {
            // On envoie le message d'erreur dans la section alerte de la page Web
            WebPage::createInstance()->addAlertMessage($error->getMessage());
            return 2;
        }
        if($PDOStatement === false)
        {
            // On envoie le message d'erreur dans la section alerte de la page Web
            WebPage::createInstance()->addAlertMessage($this->connectionPDO->errorInfo()[2]);
            return 3;
        }
        if ($PDOStatement->bindValue(':email',$email,PDO::PARAM_STR) === false) {
            // On envoie le message d'erreur dans la section alerte de la page Web
            WebPage::createInstance()->addAlertMessage($PDOStatement->errorInfo()[2]);
            return 4;
        } 
        if($PDOStatement->execute() === false)
        {
            // On envoie le message d'erreur dans la section alerte de la page Web
            WebPage::createInstance()->addAlertMessage($this->connectionPDO->errorInfo()[2]);
            return 5;
        }
        // La requete s'est bien effectuée
        $response = $PDOStatement->fetch(PDO::FETCH_NUM);
        if($response[0] === '1')
        {
            return 1;
        };
        return 0;
    }

    /*------------------------------------*/

    // createNewUser()

    // Retourne true si le user a bien été enregistré, false s'il y a eu un problème
    public function createNewUser(string $email, string $name, string $pass) : bool
    {
        try
        {
            $PDOStatement = $this->connectionPDO->prepare(  'INSERT INTO ' . DB_USER_TABLE . 
                                                            ' ( ' . DB_USER_NAME_FIELD . ', ' . DB_USER_EMAIL_FIELD . ', ' . DB_USER_PASS_FIELD . ') 
                                                            VALUES (:name, :email, :pass)');
        }
        catch (PDOException $error)
        {
            // Erreur lors de la préparation
            return false;
        }
        if($PDOStatement === false)
        {
            // Erreur
            return false;
        }
        if (($PDOStatement->bindValue(':name', $name, PDO::PARAM_STR) === false) OR
            ($PDOStatement->bindValue(':email',$email,PDO::PARAM_STR) === false) OR
            ($PDOStatement->bindValue(':pass', $pass, PDO::PARAM_STR) === false)) 
        {
            // Erreur pendant le bindValue
            return false;
        } 
        if($PDOStatement->execute() === false)
        {
            // Erreur d'exécution
            return false;
        }
        // La requete s'est bien effectuée
        $PDOStatement->fetch(PDO::FETCH_NUM);
        return true;
    }

    /*------------------------------------*/

    // getPassHashByEmail()

    // Retourne le hash du mot de passe correspondant a l'email
    public function getPassHashByEmail(string $email) : array
    {
        try
        {
            $PDOStatement = $this->connectionPDO->prepare(  'SELECT ' . DB_USER_PASS_FIELD . 
                                                            ' FROM '  . DB_USER_TABLE . 
                                                            ' WHERE ' . DB_USER_EMAIL_FIELD . 
                                                            ' LIKE :email');
        }
        catch (PDOException $error)
        {
            // Erreur lors de la préparation
            // TODO : Renvoyer un message d'erreur
        }
        if($PDOStatement === false)
        {
            // Erreur
            // TODO : Renvoyer un message d'erreur
        }
        if (($PDOStatement->bindValue(':email', $email, PDO::PARAM_STR) === false)) 
        {
            // Erreur pendant le bindValue
            // TODO : Renvoyer un message d'erreur
        } 
        if($PDOStatement->execute() === false)
        {
            // Erreur d'exécution
            // TODO : Renvoyer un message d'erreur
        }
        // La requete s'est bien effectuée
        $response = $PDOStatement->fetch(PDO::FETCH_NUM);
        return $response;
    }
}