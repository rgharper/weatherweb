import requests, json
import matplotlib.pyplot as plot

def lobf(x,y):
    #stolen from https://stackoverflow.com/questions/22239691/code-for-best-fit-straight-line-of-a-scatter-plot
    xbar = sum(x)/len(x)
    ybar = sum(y)/len(y)
    n = len(x) # or len(Y)

    numer = sum([xi*yi for xi,yi in zip(x, y)]) - n * xbar * ybar
    denum = sum([xi**2 for xi in x]) - n * xbar**2

    b = numer / denum
    # a = ybar - b * xbar # not really necessary because it approaches 0 
    a = 0
    return a,b

# load bom data
with open('data.json') as f:
    data = json.load(f)

# fetch bom data
api_url = 'http://www.bom.gov.au/fwo/IDN60801/IDN60801.95896.json'

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0'
}

response = requests.get(api_url, headers=headers)
current = response.json()['observations']['data'][0]
bom_gust = current['gust_kmh']
bom_speed = current['wind_spd_kmh']

# fetch local data
response = requests.get('http://192.168.1.164:8080/chartData.php')
current = response.json()['outside'][-3]
local_gust = current['windgust']
local_speed = current['windspeed']

data['bom']['gust'].append(float(bom_gust))
data['bom']['reg'].append(float(bom_speed))
data['local']['gust'].append(float(local_gust))
data['local']['reg'].append(float(local_speed))

with open('data.json', 'w') as f:
    json.dump(data, f)

print(data)

all_bom=data['bom']['gust']+data['bom']['reg']
all_local=data['local']['gust']+data['local']['reg']

a,b = lobf(all_local, all_bom)
c,d = lobf(data['local']['gust'], data['bom']['gust'])
e,f = lobf(data['local']['reg'], data['bom']['reg'])

text = f'best fit line all:\ny = {a} + {b}x'
text += f'\nbest fit line gust:\ny = {c} + {d}x'
text += f'\nbest fit line normal:\ny = {e} + {f}x'

with open('lobf.text', 'w') as file:
    file.write(text)

with open('factor.text', 'w') as file:
    file.write(str(f))

plot.scatter(data['local']['gust'], data['bom']['gust'])
plot.scatter(data['local']['reg'], data['bom']['reg'])
yfit = [a + b * xi for xi in range(150)]
yfitg = [c + d * xi for xi in range(150)]
yfitr = [e + f * xi for xi in range(150)]
plot.plot(range(150), yfit)
plot.plot(range(150), yfitg)
plot.plot(range(150), yfitr)

plot.savefig('calibrate.png')

