import pandas as pd


def refactor2():
    games_on_sale = pd.read_csv("games_on_sale.csv")

    # Apply refactoring logic to 'title' column
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.lower())
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.replace(' ps4 & ps5', ''))
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.replace(' ps4™ edition', ''))
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.replace(' super bundle', ''))
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.replace(' complete season', ''))
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.replace(' ultimate edition bundle', ''))
    games_on_sale['title'] = games_on_sale['title'].apply(lambda x: x.replace(' premium edition', ''))
    games_on_sale['title'] = games_on_sale['title'].replace('®', '')
    games_on_sale['title'] = games_on_sale['title'].replace('™', '')
    games_on_sale['title'] = games_on_sale['title'].replace('–', '-')
    games_on_sale['title'] = games_on_sale['title'].replace('’', "'")

    # Create new dataframe with refactored 'title' column and any other columns you want to keep
    # refactored_games = games_on_sale[['title', 'discount', 'discounted_price', 'original_price']]

    return games_on_sale


# print(refactor2())

def id_to_name(id_game):
    if id_game == 2:
        return 'Point-and-click'
    if id_game == 4:
        return 'Fighting'
    elif id_game == 5:
        return 'Shooter'
    elif id_game == 7:
        return 'Music'
    elif id_game == 8:
        return 'Platform'
    elif id_game == 9:
        return 'Puzzle'
    elif id_game == 10:
        return 'Racing'
    elif id_game == 11:
        return 'Real Time Strategy (RTS)'
    elif id_game == 12:
        return 'Role-playing (RPG)'
    elif id_game == 13:
        return 'Simulator'
    elif id_game == 14:
        return 'Sport'
    elif id_game == 15:
        return 'Strategy'
    elif id_game == 16:
        return 'Turn-based strategy (TBS)'
    if id_game == 24:
        return 'Tactical'
    if id_game == 25:
        return "Hack and slash/Beat 'em up"
    if id_game == 26:
        return 'Quiz/Trivia'
    elif id_game == 31:
        return 'Adventure'
    elif id_game == 32:
        return 'Indie'
    elif id_game == 33:
        return 'Arcade'
    if id_game == 34:
        return 'Visual Novel'
    elif id_game == 35:
        return 'Card & Board Game'
    else:
        return 'Unknown' + str(id_game)
