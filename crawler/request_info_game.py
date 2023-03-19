import pandas as pd
import requests
import refactorCSV

all_the_games = []
# Set your access token
access_token = 'n6s3g5mwxzusgqgpgo83g7sgu992ky'

# Set the endpoint URL
url = 'https://api.igdb.com/v4/games'

# Set the request headers
headers = {
    'Authorization': f'Bearer {access_token}',
    'Client-ID': '4nelfcwb5ggowgcmki2axo02bnc7ys'
}

games_list = refactorCSV.refactor2()

for game in range(0, len(games_list['title'])):
    body = 'fields name,genres; search ' + '"' + games_list['title'][game] + '";' + 'where genres != null;'
    # Make a GET request to the endpoint with the specified parameters and headers
    response = requests.post(url, headers=headers, data=body.encode('utf-8'))
    # Print the response content (list of games)
    if response.json():
        dict = response.json()[0]
        del dict['id']
        print(dict['genres'])
        print(dict['name'])
        print()
        lista = []
        for test in dict['genres']:
            lista.append(refactorCSV.id_to_name(test))
        dict['genres'] = lista
        dict['discount'] = games_list['discount'][game]
        dict['discounted_price'] = games_list['discounted_price'][game]
        dict['original_price'] = games_list['original_price'][game]
        all_the_games.append(dict)
    #break

df = pd.DataFrame(all_the_games)
df.to_csv('games_on_sale_info.csv', index=False)
