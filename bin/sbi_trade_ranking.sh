#!/bin/sh
set -xv

BASE=/opt/SBI_TRADE_RANKING

BIN=${BASE}/bin
LOG=${BASE}/log
DAT=${BASE}/dat
CSV=${BASE}/csv
ETC=${BASE}/etc

# SBI証券 マーケット ランキング 国内株式 当社ランキング 売買代金上位
URL='https://www.sbisec.co.jp/ETGate/?_ControlID=WPLETmgR001Control&_PageID=WPLETmgR001Mdtl20&_DataStoreID=DSWPLETmgR001Control&_ActionID=DefaultAID&burl=iris_ranking&cat1=market&cat2=ranking&dir=tl1-rnk%7Ctl2-stock%7Ctl3-thisrank%7Ctl4-salesval&file=index.html&getFlg=on'


DATE=`date +%Y%m%d`

curl -Ss -# $URL -o ${DAT}/sbi_trade_ranking_${DATE}.html

nkf -w -Lu --overwrite ${DAT}/sbi_trade_ranking_${DATE}.html
