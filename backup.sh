export TIMESTAMP=`date +%Y%m%d`
zip -r gomexsi-html-${TIMESTAMP}.zip html
mysqldump --add-drop-table -h owl.tamucc.edu -u CCS_gomexsi -p CCS_gomexsi | gzip > gomexsi-mysqldump-${TIMESTAMP}.sql.gz
