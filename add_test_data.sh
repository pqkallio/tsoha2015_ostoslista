source config/environment.sh

echo "Adding test data..."

ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER/sql
psql < add_test_data.sql
exit"
