source config/environment.sh

echo "Dropping tables..."

ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER/sql
psql < drop_tables.sql
exit"