from datetime import date, timedelta
import csv


f = open('html.txt', 'w')
#life_expect = []

#with open('behavior.csv') as csvfile:
#	reader = csv.DictReader(csvfile)
#	for row in reader:
#		life_expect.append(row)

#f.write("INSERT INTO genetic VALUES")

code = 2018

while code >= 1900:
	stCode = str(code)
	f.write("<option value=\"" + stCode + "\">" + stCode + "</option>\n")
	code -= 1

f.close()
