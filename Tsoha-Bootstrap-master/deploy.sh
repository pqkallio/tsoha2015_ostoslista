# Missä kansiossa komento suoritetaan
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

source $DIR/config/environment.sh

echo "Transfering files to server..."

# Tämä komento siirtää tiedostot palvelimelta
rsync -r $DIR/app $DIR/assets $DIR/config $DIR/lib $DIR/sql $DIR/vendor $DIR/index.php $DIR/composer.json $USERNAME@users.cs.helsinki.fi:htdocs/$PROJECT_FOLDER

echo "Done!"

echo "Dumping autoload..."

# Päivitetään autoload_classmap.php-tiedosto
ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER
php composer.phar dump-autoload
exit"

echo "Done! Your application has been deployed to $USERNAME.users.cs.helsinki.fi/$PROJECT_FOLDER"
