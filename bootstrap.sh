source config/environment.sh

echo "Initializing..."

# Luodaan projektin kansio
ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs
touch favicon.ico
mkdir $PROJECT_FOLDER
exit"

echo "Done!"

echo "Transfering files to server..."

# Siirretään tiedostot palvelimelle
scp -r app config lib vendor sql assets .htaccess index.php composer.json $USERNAME@users.cs.helsinki.fi:htdocs/$PROJECT_FOLDER

echo "Done!"

echo "Setting permissions and installing Composer..."

# Asetetaan oikeudet ja asennetaan Composer
ssh $USERNAME@users.cs.helsinki.fi "
chmod -R a+rX htdocs
cd htdocs/$PROJECT_FOLDER
curl -sS https://getcomposer.org/installer | php
php composer.phar install
exit"

echo "Done! Your application is now ready at $USERNAME.users.cs.helsinki.fi/$PROJECT_FOLDER"
