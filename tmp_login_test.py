import re
import urllib.parse
import urllib.request
import http.cookiejar

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

login_url = 'http://127.0.0.1:8000/login'
print('GET', login_url)
resp = opener.open(login_url)
html = resp.read().decode('utf-8', errors='ignore')
print('GET status', resp.getcode())

match = re.search(r'name="_token" value="([^"]+)"', html)
if not match:
    print('CSRF token not found')
    print(html[:2000])
    raise SystemExit(1)

csrf_token = match.group(1)
print('CSRF found', csrf_token[:10], '...')

post_data = urllib.parse.urlencode({
    '_token': csrf_token,
    'email': 'clarencejohn@02@gmail.com',
    'password': 'password123',
    'remember': 'on'
}).encode('utf-8')

resp = opener.open(login_url, data=post_data)
print('POST status', resp.getcode())
print('final url', resp.geturl())
content = resp.read(3000).decode('utf-8', errors='ignore')
print('response starts:', content[:2000])
print('cookies', [(c.name, c.value) for c in cj])
