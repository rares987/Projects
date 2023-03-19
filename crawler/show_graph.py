import matplotlib.pyplot as plt
import pandas as pd

df = pd.read_csv("games_on_sale_info.csv")

cmap = plt.get_cmap('tab20b')

df['genres'] = df['genres'].str.strip('[]').str.replace("'", "").str.split(", ")

df = df.explode('genres')

genre_discount_counts = df.groupby(['genres', 'discount']).size().reset_index(name='count')

pivot_table = pd.pivot_table(genre_discount_counts, values='count', index='genres', columns='discount', fill_value=0)

pivot_table.plot(kind='bar', stacked=True, cmap=cmap)
plt.show()