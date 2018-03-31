from datetime import date, timedelta
import csv


f = open('countrySql.txt', 'w')
life_expect = []

with open('behavior.csv') as csvfile:
	reader = csv.DictReader(csvfile)
	for row in reader:
		life_expect.append(row)

code = 1

f.write("INSERT INTO behavior VALUES")

for row in life_expect:
	behavior = (row['Behavior'])
	effect = (row['Effect'])
	cod = (row['COD'])
	cod_chance = (row['COD Chance'])
	codeStr = str(code)
	f.write("(" + codeStr + ",\"" + behavior + "\"," + effect + ",\"" + cod + "\"," + cod_chance + "),")
	code += 1

f.close()
