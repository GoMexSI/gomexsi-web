# example backup scripts for wordpress instance hosted at gomexsi.tamucc.edu . 
export TIMESTAMP=`date +%Y%m%d`

# dump database
mysqldump --add-drop-table -h owl.tamucc.edu -u CCS_gomexsi -p CCS_gomexsi | gzip > gomexsi-mysqldump-${TIMESTAMP}.sql.gz

# archive wp files
zip -r gomexsi-html-${TIMESTAMP}.zip html
