#!/bin/sh

###### 環境依存(以下を各環境によって修正) ######
DB_USER="mos"
DB_PASS=("awFAxKq7")
DB_NAMES=("rakuna")

APP_DIR=/home/mos/
APP_DIR_NAME="public_html"
################################################

PATH_BK=/root/backup/

if [ ! -d $PATH_BK ] ; then
	mkdir $PATH_BK
fi

PATH_BK_LOG=${PATH_BK}log/

if [ ! -d $PATH_BK_LOG ] ; then
	mkdir $PATH_BK_LOG
fi

PATH_BK_DB=${PATH_BK}dump/

if [ ! -d $PATH_BK_DB ] ; then
	mkdir $PATH_BK_DB
fi

PATH_BK_APP=${PATH_BK}app/

if [ ! -d $PATH_BK_APP ] ; then
	mkdir $PATH_BK_APP
fi

LOGFILE=${PATH_BK_LOG}backup.log

DT_NOW=`date '+%Y%m%d%H%M%S'`

MAX_BACKUPS=7

echo `date` >> $LOGFILE
echo 'db backup start.'  >> $LOGFILE

# DBバックアップ実行
for DB_NAME in "${DB_NAMES[@]}"; do
	DB_BK_TMP=$DB_NAME.$DT_NOW.sql
	DB_BK_FILE=$DB_NAME.$DT_NOW.tar.gz

	/usr/bin/mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $PATH_BK_DB$DB_BK_TMP
	tar -zcvf $PATH_BK_DB$DB_BK_FILE -C $PATH_BK_DB $DB_BK_TMP >> $LOGFILE

	if [ -e $PATH_BK_DB$DB_BK_TMP ] ; then
		rm -rf $PATH_BK_DB$DB_BK_TMP >> $LOGFILE
	fi

	# DBバックアップ数取得
	CNT_BK_DB=$(ls -1lt ${PATH_BK_DB}${DB_NAME}.*.tar.gz | wc -l)
	if [ -z $CNT_BK_DB ]; then
		CNT_BK_DB=0;
	fi

	# DBバックアップ削除
	if [ $CNT_BK_DB -gt $MAX_BACKUPS ]; then
		((CNT_RM_DB = "$CNT_BK_DB - $MAX_BACKUPS"))
		RM_DB=$(ls -1rt ${PATH_BK_DB}${DB_NAME}.*.tar.gz | head -n $CNT_RM_DB | tr '\n' ' ')
		echo "Remove DB $RM_DB" >> $LOGFILE
		rm -rf $RM_DB
	fi

done

echo `date` >> $LOGFILE
echo 'db backup end.'  >> $LOGFILE

echo `date` >> $LOGFILE
echo 'app backup start.'  >> $LOGFILE

# アプリバックアップ実行
tar -zcf $PATH_BK_APP$APP_DIR_NAME.$DT_NOW.tar.gz -C $APP_DIR $APP_DIR_NAME >> $LOGFILE

# アプリバックアップ数取得
CNT_BK_APP=$(ls -1lt ${PATH_BK_APP}${APP_DIR_NAME}.*.tar.gz | wc -l)
if [ -z $CNT_BK_APP ]; then
	CNT_BK_APP=0;
fi

# アプリバックアップ削除
if [ $CNT_BK_APP -gt $MAX_BACKUPS ]; then
	((CNT_RM_APP = "$CNT_BK_APP - $MAX_BACKUPS"))
	RM_APP=$(ls -1rt ${PATH_BK_APP}${APP_DIR_NAME}.*.tar.gz | head -n $CNT_RM_APP | tr '\n' ' ')
	echo "Remove APP $RM_APP" >> $LOGFILE
	rm -rf $RM_APP
fi

echo `date` >> $LOGFILE
echo 'app backup end.'  >> $LOGFILE
echo '------------------------------' >> $LOGFILE

exit 0
