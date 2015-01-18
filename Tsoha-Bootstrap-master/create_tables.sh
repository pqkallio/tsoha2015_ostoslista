source config/environment.sh

echo "Creating tables..."

ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER/sql
psql < create_tables.sql
exit"
