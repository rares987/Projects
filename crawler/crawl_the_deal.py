import requests
from bs4 import BeautifulSoup
import pandas as pd

url = 'https://store.playstation.com/en-us/category/30826e82-088f-4cc2-aaa4-81507aa31353/'
page = 1

games_on_sale = []
while 1:
    result = requests.get(url + str(page))
    soup = BeautifulSoup(result.text, "html.parser")
    game_listings = soup.find_all('section', {'class': 'psw-product-tile__details psw-m-t-2'})
    if not game_listings:
        break
    for games in game_listings:
        if games.find('span', class_='psw-product-tile__product-type psw-t-bold psw-t-size-1 psw-t-truncate-1 '
                                     'psw-c-t-2 psw-t-uppercase psw-m-b-1') is None:

            title = games.find('span', class_='psw-t-body psw-c-t-1 psw-t-truncate-2 psw-m-b-2').text.strip()
            if games.find('span', class_='psw-body-2 psw-badge__text psw-badge--none psw-text-bold psw-p-y-0 psw-p-2 '
                                         'psw-r-1 psw-l-anchor') is not None:
                discount = games.find('span', class_='psw-body-2 psw-badge__text psw-badge--none psw-text-bold '
                                                     'psw-p-y-0 psw-p-2 psw-r-1 psw-l-anchor').text.strip()
            if games.find('span', class_='psw-m-r-3') is not None:
                discounted_price = games.find('span', class_='psw-m-r-3').text.strip()
            if games.find('s', class_='psw-c-t-2') is not None:
                original_price = games.find('s', class_='psw-c-t-2').text.strip()
            game = {
                'title': title,
                'discount': discount,
                'discounted_price': discounted_price,
                'original_price': original_price
            }
            if game in games_on_sale or game.get("discounted_price") == 'Unavailable' or game.get("title").endswith(
                    "Bundle"):
                continue
            else:
                games_on_sale.append(game)
    # break
    page = page + 1
# for game in games_on_sale:
#     print(game)

df = pd.DataFrame(games_on_sale)
df.to_csv('games_on_sale.csv', index=False)
