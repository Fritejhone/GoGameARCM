# GoGameARCM
Jeu de go

#Installation

Déposez le dossier complet zippé en .rar sur un serveur web (cette archive contient déjà les vendors etc...)
Initialiser la BDD avec le script à la racine : gogame.sql

#En bref

Il s'agit d'un jeu de go simplifié, entourés les pierres de votre adversaire pour gagner des points ! 
1 point par pierre

Le premier à 10 points remporte la partie

#Lancement

Rendez-vous sur le lien suivant : http://localhost/html/GoGame_AR_CM_Symfony/web/app_dev.php pour profiter du mode profiler de symfony

Renseignez un pseudo sur la page d'acceuil et patientez qu'un autre joueur se connecte (vous pouvez ouvrir le site sur deux onglets en local)

#Le jeu

Le premier connecté commence et le jeu se déroule jusqu'à atteindre 10 points

#Technologies 

PHP - Symfony / JS / HTML + TWIG / Pusher

Le service tierce de temps réel fonctionne meme en local avec une connection internet

Bonne partie
