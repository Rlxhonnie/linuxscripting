# lib

import sys
import xmlrpclib
from datetime import date, timedelta

# Tanggal


nargs = len(sys.argv)

if nargs != 2:
	yesterday = date.today() - timedelta(days=+0)
	date_start = yesterday.strftime('%Y-%m-%d')
	date_stop = yesterday.strftime('%Y-%m-%d')
else:
	date_start = sys.argv[1]
	date_stop = sys.argv[1]

print('Tanggal diambil : ', date_start)

# Configuration


#wh_id = 2
wh_id = 183
url = 'https://new.ewaroeng.com'
db = 'WAROENG_2023'
username = 'audit_pajak_medan1'
password = 'pajakmedan1@123'

common = xmlrpclib.ServerProxy('{}/xmlrpc/2/common'.format(url))

uid = common.authenticate(db, username, password, {})

models = xmlrpclib.ServerProxy('{}/xmlrpc/2/object'.format(url))
record = models.execute_kw(db, uid, password,
   'pos.order', 'api_pos_tax_all',
   ['read',wh_id,date_start,date_stop],{})
filename = date_start + '.json'
outF = open(filename, "a")
for line in record:
   print >>outF, line
outF.close()

count = 0
with open(filename) as fpct:
	for line in fpct:
		if line.strip():
			count += 1
print('Jumlah struk    : ', count)

filename = 'row_' + date_start
outS = open(filename, "a")
print >>outS, count
outS.close()
