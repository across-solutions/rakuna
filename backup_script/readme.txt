/**
 *  バックアップスクリプト
 *  @author Hajime.Ando
 *  @version 1.0
 *  @date 2015/05/11
 */

■手順
１．「backup.sh」を任意のパスに配置します

２．「backup.sh」の環境依存値を各環境に合わせて変更します

３．crontabに設定します

例：crontab -e
#### backup
0 3 * * * /root/backup/backup.sh

■フォルダ構成(初回起動時は自動的にフォルダが作成されます)
/root/backup
|--app
|  |--[ドキュメントルートDIR名].YYYYMMDD.tar.gz
|--dump
|  |--[DB名].YYYYMMDD.tar.gz
|--log
|  |--backup.log

■backup.shの権限
[root@ip-172-31-4-127 ~]# chown root. backup.sh
[root@ip-172-31-4-127 ~]# chmod u+x ./backup.sh



