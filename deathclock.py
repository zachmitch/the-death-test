#  Death Clock v1.0 by Zach Mitchell 11/22/15
#
# Death Clock story:  I was listening to music while working through some programming #book.  heard the song “When” by grachan moncur and thought about the time I got a tattoo #that says “remember your death” while listening to that song.  Then I thought, “Wow, how #cool would it be to just make a death calculator.  So I’ll know “When”.
#
# This version makes the assumption that you are an American and that you will live an average lifespan.  Future versions will take into account country of residence, lifestyle choices, and risk. 
#
# - - - - - - - - - - - - - - - - - - - -
from datetime import date, timedelta

m_f = raw_input("Are you Male(M) or Female(F)?: ")
print "Please enter your birthday: "
y = int(raw_input("Year (ex: 1988, 1939, 2004): "))
m = int(raw_input("Month (ex: 1 - Jan, 2 - Feb, etc...): "))
d = int(raw_input("Day (ex: 1-31):  "))
now = date.today()
bday = date(y, m, d)
life_expect = [27503, 29386, 28459]
if m_f.lower() == "m":
	life_expect = life_expect[0]
elif m_f.lower() == "f":
	life_expect = life_expect[1]
else:
	life_expect = life_expect[2]
age = now - bday
age = int(age.days)
age_year = (age / 365)
age_month = (age % 365) / 30
age_week = ((age % 365) % 30) / 7
age_day = ((age % 365) % 30) % 7
time_left = life_expect - age
dod = now + timedelta(days=time_left)
death_year = (time_left / 365)
death_month = (time_left % 365) / 30
death_week = ((time_left % 365) % 30) / 7
death_day = ((time_left % 365) % 30) % 7
spacer = 3 * "\n*"

print spacer, "You are", age_year, "years,", age_month, "months,", age_week, "weeks, and", age_day, "days old today.", spacer

print "You should live for about", death_year, "years,", death_month, "months,", death_week, "weeks, and", death_day, "more days after today.", spacer

print "Your expected date of death: ", repr(dod.strftime("%m %d %Y")), spacer