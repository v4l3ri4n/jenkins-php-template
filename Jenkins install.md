
1. Install Jenkins
------------------

wget -q -O - http://pkg.jenkins-ci.org/debian/jenkins-ci.org.key | sudo apt-key add -
sudo sh -c 'echo deb http://pkg.jenkins-ci.org/debian binary/ > /etc/apt/sources.list.d/jenkins.list'
sudo apt-get update
sudo apt-get install jenkins


2. Install Jenkins Plugins
--------------------------

curl -L http://updates.jenkins-ci.org/update-center.json | sed '1d;$d' | curl -X POST -H 'Accept: application/json' -d @- http://127.0.0.1:8080/updateCenter/byId/default/postBack
wget http://127.0.0.1:8080/jnlpJars/jenkins-cli.jar
java -jar jenkins-cli.jar -s http://127.0.0.1:8080 install-plugin greenballs htmlpublisher publish-over-ssh email-ext \
    checkstyle cloverphp dry jdepend plot pmd tasks violations warnings xunit phing postbuild-task crap4j audit-trail \
    cucumber-reports
    
# install plot pipeline plugin manually : https://github.com/MarkusDNC/plot-plugin
    
java -jar jenkins-cli.jar -s http://127.0.0.1:8080 safe-restart

# Green Balls : Indique la réussite du build par une bulle verte au lieu de blue (par défaut).
# HTML Publisher Plugin : Ce plugin permet de générer des rapports HTML.
# Publish Over SSH Plugin : Publier des fichier et exécuter des commandes sous ssh et (scp en utilisant FTPS)
# Audit Trail Plugin : Garder une trace de n’importe quelle opération effectuée sous jenkins (configuration d’un job)
# Email-ext plugin : Configuration d’un email de notification
# checkstyle : C’est un analyseur du code qui génère un rapport de résultat
# Clover PHP Plugin : Ce plugin permet de présenter une interface basé sur les résultats de PHPUnit.
# DRY Plugin : Ce plugin génère un rapport des tendances pour la duplication du code .
# JDepend Plugin :  JDepend génère le rapport des builds.
# Plot Plugin :Ce plugin fournit le traçage générique (et graphique)   de Jenkins.
# PMD Plugin : Ce plugin permet la génération un rapport de tendance pour PMD et detecte toutes les défaillance du code.
# Task Scanner Plugin : Ce plugin scan les fichiers du répertoire du projet pour ouvrir les tâches et générer un rapport .
# Violations :Ce plugin génère des rapports statiques détecteurs de violation de code tels que checkstyle, pmd, cpd, findbugs, fxcop, stylecop et simian.
# xUnit Plugin : Ce plugin permet la publication des résultats de de l’execution de l’outil de test.
# Phing Plugin : Ce plugin permet l’utilisation de phing pour le build de votre projet php.
# Post build task :  Ce plugin permet aux utilisateurs d’exécuter des tâches shell/batch en fonction des logs de sortie du build .
# Crap4J (for processing PHPUnit's Crap4J XML logfile)
# Warnings (for processing PHP compiler warnings in the console log)
# Git
# Github


3. Install PHP QA Tools
-----------------------

sudo apt-get install php-mysql php-pear php-curl php-zip php-mbstring php-sqlite3

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
#echo "export COMPOSER_HOME=/usr/local/composer" > /etc/profile.d/composer.sh
#mkdir /user/local/composer

# Mise à jour des extensions PEAR déjà installées
sudo pear upgrade-all
sudo pear config-set auto_discover 1

# PHPUnit
wget https://phar.phpunit.de/phpunit.phar
    chmod +x phpunit.phar
    mv phpunit.phar /usr/local/bin/phpunit
    
# Autres outils "QA"
wget https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
    chmod +x phpcs.phar
    mv phpcs.phar /usr/local/bin/phpcs

wget https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar
    chmod +x phpcbf.phar
    mv phpcbf.phar /usr/local/bin/phpcbf
    
wget https://phar.phpunit.de/phploc.phar
    chmod +x phploc.phar
    mv phploc.phar /usr/local/bin/phploc
    
wget https://phar.phpunit.de/phpcpd.phar
    chmod +x phpcpd.phar
    mv phpcpd.phar /usr/local/bin/phpcpd
    
wget http://phpdox.de/releases/phpdox.phar
    chmod +x phpdox.phar
    sudo mv phpdox.phar /usr/local/bin/phpdox
    
wget http://phpdoc.org/phpDocumentor.phar  
    chmod +x phpDocumentor.phar
    sudo mv phpDocumentor.phar /usr/local/bin/phpDocumentor  
    
wget -c http://static.phpmd.org/php/latest/phpmd.phar
    chmod +x phpmd.phar
    sudo mv phpmd.phar /usr/local/bin/phpmd

wget http://static.pdepend.org/php/latest/pdepend.phar
    chmod +x pdepend.phar
    sudo mv pdepend.phar /usr/local/bin/pdepend
    
wget http://bartlett.laurent-laville.org/get/phpcompatinfo-5.0.0.phar
    chmod +x phpcompatinfo.phar
    sudo mv phpcompatinfo.phar /usr/local/bin/phpcompatinfo
    
wget https://github.com/phpmetrics/PhpMetrics/releases/download/v2.2.0/phpmetrics.phar
    chmod +x phpmetrics.phar
    sudo mv phpmetrics.phar /usr/local/bin/phpmetrics
    
wget https://github.com/Behat/Behat/releases/download/v3.3.0/behat.phar
    chmod +x behat.phar
    sudo mv behat.phar /usr/local/bin/behat
    
composer global require mayflower/php-codebrowser

# Phing
    
wget http://www.phing.info/get/phing-latest.phar
    chmod +x phing.phar
    sudo mv phing.phar /usr/local/bin/phing

4. Configure PHP project
------------------------

Install Behat Json formatter
https://packagist.org/packages/vanare/behat-cucumber-json-formatter
