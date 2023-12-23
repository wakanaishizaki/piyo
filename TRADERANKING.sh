#!/usr/bin/bash
set -xv

# curl params.
URL='https://www.sbisec.co.jp/ETGate/?_ControlID=WPLETmgR001Control'
URL+='&_PageID=WPLETmgR001Mdtl20&_DataStoreID=DSWPLETmgR001Control'
URL+='&_ActionID=DefaultAID&burl=iris_ranking&cat1=market&cat2=ranking'
URL+='&dir=tl1-rnk%7Ctl2-stock%7Ctl3-thisrank%7Ctl4-salesval&file=index.html&getFlg=on'

# ignore url?
URL='https://www.sbisec.co.jp/ETGate/?_ControlID=WPLETmgR001Control&_PageID=WPLETmgR001Mdtl20&_DataStoreID=DSWPLETmgR001Control&_ActionID=DefaultAID&burl=iris_ranking&cat1=market&cat2=ranking&dir=tl1-rnk%7Ctl2-stock%7Ctl3-thisrank%7Ctl4-turnover&file=index.html&getFlg=on'

# proxy (retail)
#PROXY='http://user:password@host:port'

# dirs.
BINDIR=$(cd $(dirname ${BASH_SOURCE:-$0}); pwd)
cd ${BINDIR}

HOME=`dirname ${BINDIR}`

LOGDIR=${HOME}/'log'
TMPDIR=${HOME}/'tmp'
ETCDIR=${HOME}/'etc'
DATDIR=${HOME}/'dat'

SELF=$0
SELF=${SELF##*/}

#
DATE=`date +%Y%m%d`
TIME=`date +%H%M%S`
DATETIME=`date +"%Y-%m-%d %H:%M:%S"`

# file
LOGFILE="${LOGDIR}/trade_ranking_${DATE}.log"
OUTFILE="${DATDIR}/trade_ranking_${DATE}.html"

# main
echo "[BEGIN] trade ranking ${DATETIME}" >> ${LOGFILE}

mkdir -p ${LOGDIR}
mkdir -p ${TMPDIR}
mkdir -p ${ETCDIR}
mkdir -p ${DATDIR}

#/bin/curl -x ${PROXY} ${URL} 2>>${LOGFILE} | nkf -w -Lu | sed -e 's/charset=Shift_JIS/charset=UTF-8/g' > ${OUTFILE}
/bin/curl ${URL} 2>>${LOGFILE} | nkf -w -Lu | sed -e 's/charset=Shift_JIS/charset=UTF-8/g' > ${OUTFILE}

`php TRADERANKING.php ${OUTFILE} csv/buy_yyyymmdd.csv csv/sell_yyyymmdd.csv`

echo "[END] trade ranking ${DATETIME}" >> ${LOGFILE}
